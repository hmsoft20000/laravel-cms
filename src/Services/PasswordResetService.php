<?php

namespace HMsoft\Cms\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use HMsoft\Cms\Mail\ResetCodeMail;
use Carbon\Carbon;
use Exception;

class PasswordResetService
{
    protected int $otpLength;
    protected int $otpExpiryMinutes;
    protected int $maxOtpRequests;
    protected int $maxWrongOtpAttempts;

    public function __construct()
    {
        $this->otpLength = config('passwordreset.otp_length', 6);
        $this->otpExpiryMinutes = config('passwordreset.otp_expiry_minutes', 15);
        $this->maxOtpRequests = config('passwordreset.max_otp_requests', 3);
        $this->maxWrongOtpAttempts = config('passwordreset.max_wrong_otp_attempts', 5);
    }

    /**
     * Send Otp to the given email with rate limiting.
     *
     * @param string $email
     * @param string $ip
     * @return void
     * @throws Exception
     */
    public function sendOtp(string $email, string $ip): void
    {
        $emailKey = $this->emailCacheKey($email);
        $ipKey = $this->IPCacheKey($ip);

        // Check rate limit for email
        if (Cache::get($emailKey, 0) >= $this->maxOtpRequests) {
            throw new Exception(__('cms::auth.too_many_otp_requests_email'));
        }

        // Check rate limit for IP
        if (Cache::get($ipKey, 0) >= $this->maxOtpRequests) {
            throw new Exception(__('cms::auth.too_many_otp_requests_ip'));
        }

        // Generate Otp
        $otp = $this->generateOtp();

        // Hash Otp
        $hashedOtp = Hash::make($otp);

        // Store hashed Otp in DB with created_at
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => $hashedOtp, 'created_at' => now()]
        );

        // Send Otp email
        Mail::to($email)->send(new ResetCodeMail($otp));

        // Increment rate limit counters with expiration
        Cache::put($emailKey, Cache::get($emailKey, 0) + 1, now()->addMinutes(15));
        Cache::put($ipKey, Cache::get($ipKey, 0) + 1, now()->addMinutes(15));

        // Reset wrong attempts counter on new Otp send
        $this->resetWrongAttempts($email);
        $this->resetEmail($email);
        $this->resetIP($email);
    }

    /**
     * Validate the Otp for the given email.
     *
     * @param string $email
     * @param string $otp
     * @return bool
     * @throws Exception
     */
    public function validateOtp(string $email, string $otp): bool
    {
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $record) {
            throw new Exception(__('cms::auth.otp_invalid_or_expired'));
        }

        // Check expiry
        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes($this->otpExpiryMinutes)->isPast()) {
            $this->deleteToken($email);
            throw new Exception(__('cms::auth.otp_invalid_or_expired'));
        }

        // Check wrong attempts
        if ($this->getWrongAttempts($email) >= $this->maxWrongOtpAttempts) {
            $this->deleteToken($email);
            throw new Exception(__('cms::auth.too_many_wrong_otp_attempts'));
        }

        // Check Otp hash
        if (! Hash::check($otp, $record->token)) {
            $this->incrementWrongAttempts($email);
            throw new Exception(__('cms::auth.otp_invalid_or_expired'));
        }

        return true;
    }

    /**
     * Reset the password and delete the token.
     *
     * @param string $email
     * @param string $newPassword
     * @return void
     * @throws Exception
     */
    public function resetPassword(string $email, string $newPassword): void
    {
        $user = DB::table('tbUsers')->where('Email', $email)->first();

        if (! $user) {
            throw new Exception(__('cms::auth.password_reset_failed'));
        }

        DB::table('tbUsers')->where('Email', $email)->update([
            'Password' => Hash::make($newPassword),
        ]);

        $this->deleteToken($email);
        $this->resetWrongAttempts($email);
        $this->resetEmail($email);
        $this->resetIP($email);

        // Reset rate limit counters for email and IP
        $emailKey = 'password_reset_otp_requests_email_' . sha1($email);
        Cache::forget($emailKey);
        // IP key is not available here, so cannot reset it here
    }

    /**
     * Generate a numeric Otp of configured length.
     *
     * @return string
     */
    protected function generateOtp(): string
    {
        $digits = $this->otpLength;
        $min = (int) str_pad('1', $digits, '0', STR_PAD_RIGHT);
        $max = (int) str_pad('', $digits, '9', STR_PAD_RIGHT);
        return (string) random_int($min, $max);
    }

    /**
     * Delete the token for the email.
     *
     * @param string $email
     * @return void
     */
    protected function deleteToken(string $email): void
    {
        DB::table('password_reset_tokens')->where('email', $email)->delete();
    }

    /**
     * Get the number of wrong Otp attempts for the email.
     *
     * @param string $email
     * @return int
     */
    protected function getWrongAttempts(string $email): int
    {
        return Cache::get($this->wrongAttemptsCacheKey($email), 0);
    }

    /**
     * Increment the wrong Otp attempts counter for the email.
     *
     * @param string $email
     * @return void
     */
    protected function incrementWrongAttempts(string $email): void
    {
        $key = $this->wrongAttemptsCacheKey($email);
        $count = Cache::get($key, 0) + 1;
        Cache::put($key, $count, now()->addHour());
    }

    /**
     * Reset the wrong Otp attempts counter for the email.
     *
     * @param string $email
     * @return void
     */
    protected function resetWrongAttempts(string $email): void
    {
        Cache::forget($this->wrongAttemptsCacheKey($email));
    }


    protected function resetEmail(string $email): void
    {
        Cache::forget($this->emailCacheKey($email));
    }

    protected function resetIP(string $email): void
    {
        Cache::forget($this->IPCacheKey($email));
    }

    /**
     * Get the cache key for wrong Otp attempts.
     *
     * @param string $email
     * @return string
     */
    protected function wrongAttemptsCacheKey(string $email): string
    {
        return 'password_reset_wrong_attempts_' . sha1($email);
    }

    protected function emailCacheKey(string $email): string
    {
        return 'password_reset_otp_requests_email_' . sha1($email);
    }

    protected function IPCacheKey(string $email): string
    {
        return 'password_reset_otp_requests_ip_' . sha1($email);
    }
}
