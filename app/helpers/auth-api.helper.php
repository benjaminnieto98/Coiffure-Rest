<?php

function base64url_encode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

class AuthApiHelper
{
    private $key;

    function __construct()
    {
        $this->key = "admin123";
    }

    public function isLoggedIn()
    {
        $payload = $this->getUser();
        if (isset($payload->sub))
            return true;
        else
            return false;
    }

    public function getBasic()
    {
        $header = $this->getHeader();
        //Basic base64(user:pass)
        if (strpos($header, "Basic ") === 0) {
            // base64(user:pass)
            $userpass = explode(" ", $header)[1];
            //user:pass
            $userpass = base64_decode($userpass);
            $userpass = explode(":", $userpass);
            if (count($userpass) == 2) {
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

    public function createToken($user)
    {
        $header = array(
            "alg" => 'HS256',
            "typ" => 'JWT'
        );
        $user = json_decode(json_encode($user), true);
        $payload = array(
            "sub" => 1,
            "name" => $user["email"],
            "exp" => time() + 3600
        );
        $header = base64url_encode(json_encode($header));
        $payload = base64url_encode(json_encode($payload));
        $signature = hash_hmac('SHA256', "$header.$payload", $this->key, true);
        $signature = base64url_encode($signature);

        return "$header.$payload.$signature";
    }

    public function getHeader()
    {
        if (isset($_SERVER["REDIRECT_HTTP_AUTHORIZATION"])) {
            return $_SERVER["REDIRECT_HTTP_AUTHORIZATION"];
        }
        if (isset($_SERVER["HTTP_AUTHORIZATION"])) {
            return $_SERVER["HTTP_AUTHORIZATION"];
        }
        return null;
    }

    public function getUser()
    {
        $auth = $this->getHeader(); // Bearer header.payload.signature
        $auth = explode(" ", $auth);
        if ($auth[0] != "Bearer" || count($auth) != 2) {
            return array();
        }
        $token = explode(".", $auth[1]);
        $header = $token[0];
        $payload = $token[1];
        $signature = $token[2];

        $new_signature = hash_hmac('SHA256', "$header.$payload", $this->key, true);
        $new_signature = base64url_encode($new_signature);

        if ($signature != $new_signature) {
            return array();
        }

        $payload = json_decode(base64_decode($payload));
        if (!isset($payload->exp) || $payload->exp < time()) {
            return array();
        }

        return $payload;
    }
}
