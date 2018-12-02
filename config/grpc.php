<?php
return [
    'helloworld.Greeter' => [
        'method' => [
            'SayHello' => [
                'request'  => 'Helloworld\\HelloRequest' ,
                'response' => 'Helloworld\\HelloReply' ,
            ] ,
        ] ,
    ] ,
    'grpc.UserScore'     => [
        'method' => [
            'updateScore' => [
                'request'  => 'Grpc\\UserScoreGrpcRequest' ,
                'response' => 'Grpc\\GrpcResult' ,
            ] ,
        ] ,
    ] ,
    'Userservice'        => [
        'method' => [
            'GetByUid'        => [
                'request'  => 'Userservice\\Request' ,
                'response' => 'Userservice\\Result' ,
            ] ,
            'GetByToken'      => [
                'request'  => 'Userservice\\Request' ,
                'response' => 'Userservice\\Result' ,
            ] ,
            'IsExist'         => [
                'request'  => 'Userservice\\Request' ,
                'response' => 'Userservice\\Result' ,
            ] ,
            'SignIn'          => [
                'request'  => 'Userservice\\Request' ,
                'response' => 'Userservice\\Result' ,
            ] ,
            'OAuthSignIn'     => [
                'request'  => 'Userservice\\Request' ,
                'response' => 'Userservice\\Result' ,
            ] ,
            'Register'        => [
                'request'  => 'Userservice\\Request' ,
                'response' => 'Userservice\\Result' ,
            ] ,
            'OAuthRegister'   => [
                'request'  => 'Userservice\\Request' ,
                'response' => 'Userservice\\Result' ,
            ] ,
            'OAuthBindMobile' => [
                'request'  => 'Userservice\\Request' ,
                'response' => 'Userservice\\Result' ,
            ] ,
            'CheckToken'      => [
                'request'  => 'Userservice\\Request' ,
                'response' => 'Userservice\\Result' ,
            ] ,
            'Handle'          => [
                'request'  => 'Userservice\\Request' ,
                'response' => 'Userservice\\Result' ,
            ] ,
        ] ,
    ] ,
];