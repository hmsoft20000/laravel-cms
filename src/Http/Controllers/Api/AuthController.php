<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Requests\Auth\{LoginRequest, RegisterRequest, ForgotPasswordRequest, ResetPasswordRequest, UpdateProfileRequest, VerifyOtpRequest};
use HMsoft\Cms\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Resources\Api\UserResource;
use HMsoft\Cms\Exceptions\CredentialsDoNotMatchException;


class AuthController extends Controller
{

    public function __construct(protected AuthRepositoryInterface $repo) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->repo->register($request->validated());
            return successResponse(
                __('cms::auth.registered'),
                [
                    'user' => resolve(UserResource::class, ['resource' => $user])->withFields(request()->get('fields')),
                ],
                201
            );
        } catch (\Exception $e) {
            return errorResponse(__('cms::auth.registration_failed'), 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->repo->login($request->validated());
            $user = $result['user'];
            return successResponse(__('cms::auth.logged_in_successfully'), [
                'user' => resolve(UserResource::class, ['resource' => $user])->withFields(request()->get('fields')),
                'token' => $result['token'],
            ]);
        } catch (CredentialsDoNotMatchException $e) {
            return errorResponse($e->getMessage(), 422);
        } catch (\Exception $e) {
            throw $e;
            return errorResponse(__('cms::auth.login_failed'), 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->repo->logout($request);
            return successResponse(__('cms::auth.logged_out_successfully'));
        } catch (\Exception $e) {
            return errorResponse(__('cms::auth.logout_failed'), 500);
        }
    }

    public function user(Request $request): JsonResponse
    {
        try {
            $user = $this->repo->user($request);
            return successResponse(__('cms::auth.user_retrieved'), [
                'user' => resolve(UserResource::class, ['resource' => $user])->withFields(request()->get('fields')),
                'token' => $request->bearerToken(),
            ]);
        } catch (\Exception $e) {
            throw $e;
            return errorResponse(__('cms::auth.user_retrieval_failed'), 500);
        }
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $this->repo->sendResetLinkEmail($request->validated());
            return successResponse(__('cms::auth.password_reset_code_sent'));
        } catch (\Exception $e) {
            return errorResponse(__('cms::auth.password_reset_failed'), 500);
        }
    }

    public function verifyResetLinkEmail(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $this->repo->sendResetLinkEmail($request->validated());
            return successResponse(__('cms::auth.password_reset_code_sent'));
        } catch (\Exception $e) {
            return errorResponse(__('cms::auth.password_reset_failed'), 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $this->repo->resetPassword($request->validated());
            return successResponse(__('cms::auth.password_reset_successful'));
        } catch (\Exception $e) {
            return errorResponse(__('cms::auth.password_reset_failed'), 500);
        }
    }

    public function profile(Request $request): JsonResponse
    {
        try {
            $user = $this->repo->user($request);
            return successResponse(__('cms::auth.profile_retrieved'), [
                'user' => resolve(UserResource::class, ['resource' => $user])->withFields(request()->get('fields')),
                'token' => $request->bearerToken(),
            ]);
        } catch (\Exception $e) {
            return errorResponse(__('cms::auth.profile_retrieval_failed'), 500);
        }
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {

            $data = collect($request->validated())->except([
                'old_password'
            ])->toArray();

            $data['id'] = $request->user()->getKey();
            $user = $this->repo->updateProfile($data);
            return successResponse(
                __('cms::auth.profile_updated'),
                [
                    'user' => resolve(UserResource::class, ['resource' => $user])->withFields(request()->get('fields')),
                ]
            );
        } catch (\Exception $e) {
            return errorResponse(__('cms::auth.profile_update_failed'), 500);
        }
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $this->repo->verifyOtp($request->validated());
            return successResponse(__('cms::auth.otp_verified_successfully'));
        } catch (\Exception $e) {
            return errorResponse(__('cms::auth.otp_verification_failed'), 400);
        }
    }
}
