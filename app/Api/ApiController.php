<?php

namespace App\Api;

use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;

/**
 * ApiController.
 *
 * @author Mayur Patel <mayurpatel3209@gmail.com>
 */
class ApiController extends Controller
{

    use Helpers;

    public function validateRequest($api)
    {
        $version = app('request')->version();
        $rules = app('config')->get("api_validations.{$api}.{$version}.rules");
        $messages = app('config')->get("api_validations.{$api}.{$version}.messages");

        if ($rules && $messages) {
            $payload = app('request')->only(array_keys($rules));
            $validator = app('validator')->make($payload, $rules, $messages);

            if ($validator->fails()) {
                throw new ApiException($validator->errors()->first(), 0, 400);
            }
        }
    }

}