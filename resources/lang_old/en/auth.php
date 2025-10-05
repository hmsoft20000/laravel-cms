<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    // General Messages
    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    // Authentication Messages
    'unauthenticated' => 'You must be logged in',
    'logged_in_successfully' => 'Logged in successfully',
    'login_failed' => 'Login failed. Please try again.',
    'registered' => 'Account created successfully',
    'credentials_do_not_match' => 'The provided credentials do not match our records.',
    'registration_successful_logged_in' => 'Account created and logged in successfully',
    'logged_out_successfully' => 'Logged out successfully',
    'old_password_not_match' => 'Old password does not match',
    'password_reset_code_sent' => 'Password reset code sent successfully',
    'password_reset_successful' => 'Password reset successfully',
    'password_reset_failed' => 'Failed to reset password',
    'registration_failed' => 'User registration failed.',
    'logout_failed' => 'Logout failed.',
    'user_retrieved' => 'User retrieved successfully.',
    'user_retrieval_failed' => 'Failed to retrieve user.',
    'profile_retrieved' => 'Profile retrieved successfully.',
    'profile_retrieval_failed' => 'Failed to retrieve profile.',
    'profile_updated' => 'Profile updated successfully.',
    'profile_update_failed' => 'Failed to update profile.',

    // Email Messages
    'password_reset_code_subject' => 'Password Reset Code',
    'password_reset_code_greeting' => 'Hello,',
    'password_reset_code_message' => 'Your password reset code is',
    'password_reset_code_footer' => 'If you did not request a password reset, please ignore this email.',

    // OTP Messages
    'otp_verified_successfully' => 'OTP verified successfully.',
    'otp_verification_failed' => 'OTP verification failed.',
    'otp_invalid_or_expired' => 'OTP is invalid or expired.',
    'too_many_wrong_otp_attempts' => 'Too many wrong OTP attempts. Please try again later.',
    'too_many_otp_requests_email' => 'Too many OTP requests from this email. Please try again later.',
    'too_many_otp_requests_ip' => 'Too many OTP requests from this IP address. Please try again later.',

    // Login Request
    'login' => [
        'messages' => [
            'login_identifier.required' => 'The email/mobile field is required.',
            'login_identifier.string' => 'The email/mobile must be a string.',
            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a string.',
        ],
        'attributes' => [
            'login_identifier' => 'email/mobile',
            'password' => 'password',
        ],
    ],

    // Register Request
    'register' => [
        'messages' => [
            'first_name.required' => 'The first name field is required.',
            'first_name.string' => 'The first name must be a string.',
            'first_name.max' => 'The first name may not be greater than 255 characters.',
            'first_name.regex' => 'The first name may only contain letters.',
            'last_name.required' => 'The last name field is required.',
            'last_name.string' => 'The last name must be a string.',
            'last_name.max' => 'The last name may not be greater than 255 characters.',
            'last_name.regex' => 'The last name may only contain letters.',
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'email.unique' => 'The email has already been taken.',
            'mobile.required' => 'The mobile field is required.',
            'mobile.string' => 'The mobile must be a string.',
            'mobile.unique' => 'The mobile has already been taken.',
            'password.required' => 'The password field is required.',
            'password.uncompromised' => 'The given password has appeared in a data leak. Please choose a different password.',
            'password_confirmation.required' => 'The password confirmation field is required.',
            'password_confirmation.same' => 'The password confirmation does not match.',
            'agreeTerms.accepted' => 'You must accept the terms and conditions.',
        ],
        'attributes' => [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'email' => 'email',
            'mobile' => 'mobile',
            'password' => 'password',
            'password_confirmation' => 'password confirmation',
            'agreeTerms' => 'terms and conditions',
        ],
    ],

    // Forgot Password Request
    'forgot_password' => [
        'messages' => [
            'login_identifier.required' => 'The email/mobile field is required.',
            'login_identifier.string' => 'The email/mobile must be a string.',
        ],
        'attributes' => [
            'login_identifier' => 'email/mobile',
        ],
    ],

    // Reset Password Request
    'reset_password' => [
        'messages' => [
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'token.required' => 'The token field is required.',
            'password.required' => 'The password field is required.',
            'password.uncompromised' => 'The given password has appeared in a data leak. Please choose a different password.',
            'password_confirmation.required' => 'The password confirmation field is required.',
            'password_confirmation.same' => 'The password confirmation does not match.',
        ],
        'attributes' => [
            'email' => 'email',
            'token' => 'token',
            'password' => 'password',
            'password_confirmation' => 'password confirmation',
        ],
    ],

    // Update Password Request
    'update_password' => [
        'messages' => [
            'old_password.required' => 'The old password field is required.',
            'old_password.string' => 'The old password must be a string.',
            'new_password.required' => 'The new password field is required.',
            'new_password.confirmed' => 'The password confirmation does not match.',
            'new_password.different' => 'The new password must be different from the old one.',
            'new_password.min' => 'The password must be at least 8 characters.',
            'new_password.regex' => 'The password must contain uppercase, lowercase, numbers and symbols.',
        ],
        'attributes' => [
            'old_password' => 'old password',
            'new_password' => 'new password',
        ],
    ],

    // Update Profile Request
    'update_profile' => [
        'messages' => [
            'first_name.string' => 'The first name must be a string.',
            'first_name.max' => 'The first name may not be greater than 255 characters.',
            'first_name.regex' => 'The first name may only contain letters.',
            'last_name.string' => 'The last name must be a string.',
            'last_name.max' => 'The last name may not be greater than 255 characters.',
            'last_name.regex' => 'The last name may only contain letters.',
            'mobile.unique' => 'The mobile has already been taken.',
            'mobile.phone' => 'The mobile number is invalid.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The file must be of type: jpeg, png, jpg.',
            'image.max' => 'The file may not be greater than 2048 kilobytes.',
            'password_confirmation.same' => 'The password confirmation does not match.',
        ],
        'attributes' => [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'mobile' => 'mobile',
            'image' => 'image',
            'password' => 'password',
            'password_confirmation' => 'password confirmation',
        ],
    ],

    // Verify OTP Request
    'verify_otp' => [
        'messages' => [
            'otp.required' => 'The OTP field is required.',
            'otp.string' => 'The OTP must be a string.',
            'login_identifier.required' => 'The email field is required.',
            'login_identifier.email' => 'The email must be a valid email address.',
            'login_identifier.exists' => 'The email does not exist.',
        ],
        'attributes' => [
            'otp' => 'OTP',
            'login_identifier' => 'email',
        ],
    ],
];