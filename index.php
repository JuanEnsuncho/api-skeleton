<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require 'vendor/autoload.php';


Flight::set('keySecret', '9wWuBHzNI5xjkldq83tn6_N74eGOHeJoUjzT7x4mgBY');
Flight::set('algoritm', 'HS256');
Flight::set('vigencia', 60);


Flight::route('POST /login' , function(){
    
    if(Flight::request()->data->user == 'juan.ensuncho' && Flight::request()->data->pass == '123456789'){
    
        $key = Flight::get('keySecret');
        
        $payload = array(
            "user_id" => 1234,
            "username" => Flight::request()->data->user,
            "createAT" => time()
        );  
        
        $token = JWT::encode($payload, $key, Flight::get('algoritm'));
        Flight::json(array("token" => $token));
    }
});





Flight::map('tokenValid', function(){

    try {

        $headers    = apache_request_headers();
        $token      = str_replace('Bearer ', '', $headers['Authorization']);
        $decoded    = JWT::decode($token, new Key(Flight::get('keySecret'), Flight::get('algoritm')));

        if((time() - $decoded->createAT) > Flight::get('vigencia')){
            Flight::json(['message' => '401 Unauthorized', 'data' => null], 401);
            exit;
        }
        
    } catch (\Throwable $th) {
        Flight::json(['message' => $th->getMessage(), 'data' => null], 500);
        exit;
    }
    
    
    
});



Flight::route('POST /', function(){
    Flight::tokenValid();
    Flight::json(['message' => 'Hola mundo', 'data' => null], 200);
});



Flight::start();