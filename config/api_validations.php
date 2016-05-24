<?php

return [

    'login' => [
        'v1' => [
            'rules' => [
                'email' => 'required|email',
                'password' => 'required',
            ],
            'messages' => [
                'email.required' => 'Email is required',
                'email.email' => 'Email is not valid',
                'password.required' => 'Password is required',
            ]
        ]
    ]

];
