<?php

namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\User;
use Config;
use Dingo\Api\Exception\ValidationHttpException;
use Illuminate\Mail\Message;
use JWTAuth;
use Password;
use Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

/**
 * AuthController.
 *
 * @author Mayur Patel <mayurpatel3209@gmail.com>
 */
class AuthController extends ApiController
{

    public function login(Request $request)
    {
        $this->validateRequest('login');
        
        $credentials = $request->only(['email', 'password']);
        
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                $this->response->errorUnauthorized();
                return null;
            }
        } catch (JWTException $e) {
            $this->response->error('could_not_create_token', 500);
            exit;
        }

        return [
            'token' => $token
        ];

    }

    public function signup(Request $request)
    {
        $signupFields = Config::get('boilerplate.signup_fields');
        $hasToReleaseToken = Config::get('boilerplate.signup_token_release');

        $userData = $request->only($signupFields);

        $validator = Validator::make($userData, Config::get('boilerplate.signup_fields_rules'));

        if ($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        User::unguard();
        $user = User::create($userData);
        User::reguard();

        if (!$user->id) {
            $this->response->error('could_not_create_user', 500);
        }

        if ($hasToReleaseToken) {
            return $this->login($request);
        }

        return $this->response->created();
    }

    public function recovery(Request $request)
    {
        $validator = Validator::make($request->only('email'), [
            'email' => 'required'
        ]);

        if ($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject(Config::get('boilerplate.recovery_email_subject'));
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return $this->response->noContent();
            case Password::INVALID_USER:
                $this->response->errorNotFound();
        }
    }

    public function reset(Request $request)
    {
        $credentials = $request->only(['email', 'password', 'password_confirmation', 'token']);

        $validator = Validator::make($credentials, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        $response = Password::reset($credentials, function (User $user, $password) {
            $user->password = $password;
            $user->save();
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                if (Config::get('boilerplate.reset_token_release')) {
                    return $this->login($request);
                }
                return $this->response->noContent();

            default:
                $this->response->error('could_not_reset_password', 500);
        }
    }
}