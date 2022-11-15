<?php

function base64url_encode($data){
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

class AuthApiHelper{
    private $key;

    function __construct(){
        $this->key = "123";
    }

    public function isLoggedIn(){
        $payload = $this->getUser();
        if(isset($payload->sub))
            return true;
        else
            return false;
    }

    public function getBasic(){
        $header = $this->getHeader();
        //Basic base64(user:pass)
        if(strpos($header,"Basic ")===0){
            // base64(user:pass)
            $userpass = explode(" ",$header)[1];
            //user:pass
            $userpass = base64_decode($userpass);
            $userpass = explode(":",$userpass);
            if(count($userpass)==2){
                $user = $userpass[0];
                $pass = $userpass[1];
                return array(
                    "user" => $user,
                    "pass" => $pass
                );
            }
        }
        return null;
    }

    public function createToken($user){
        $header = array(
            "alg" => 'HS256',
            "typ" => 'JWT'
        );
        $payload = array(
            "sub"=>1,
            "name"=>$user["user"],
            "rol"=>["admin","other"]
        );
        $header = json_encode($header);
        $payload = json_encode($payload);
        $header = base64url_encode($header);
        $payload = base64url_encode($payload);

        $signature = hash_hmac('SHA256', "$header . $payload", $this->key, true);
        $signature = base64url_encode($signature);

        return "$header.$payload.$signature";
    }

    public function getHeader(){
        if(isset($_SERVER["REDIRECT_HTTP_AUTHORIZATION"])){
            return $_SERVER["REDIRECT_HTTP_AUTHORIZATION"];
        }
        if(isset($_SERVER["HTTP_AUTHORIZATION"])){
            return $_SERVER["HTTP_AUTHORIZATION"];
        }
        return null;
    }

    public function getUser(){
        $header = $this->getHeader();
        if(strpos($header,"Bearer ")===0){
            $token = explode(" ",$header)[1];
            $parts = explode(".",$token);
            if(count($parts)===3){
                $header = $parts[0];
                $payload = $parts[1];
                $signature = $parts[2];
                $new_signature = hash_hmac('SHA256', "$header . $payload", $this->key, true);
                $new_signature = base64url_encode($new_signature);
                if($signature == $new_signature){
                    $payload = base64_decode($payload);
                    $payload = json_decode($payload);
                    return $payload;
                }
            }
        }
        return null;
    }
}