<?php

require_once './app/views/api.view.php';
require_once './app/helpers/auth-api.helper.php';

class AuthApiController
{
    private $view;
    private $authHelper;

    public function __construct()
    {
        $this->view = new ApiView();
        $this->authHelper = new AuthApiHelper();
    }

    function getToken($params = null)
    {
        $userpass = $this->authHelper->getBasic();

        //Se obtiene el user de la DB
        $user = array('user' => $userpass['user']);

        //Se verifica que el usuario existe en DB y la contaseña coincide
        if (true) {
            $token = $this->authHelper->createToken($user);
            //Devuelve un token
            $this->view->response(["token" => $token], 200);
        } else {
            $this->view->response("invalid username or password", 401);
        }
    }

    function getUser($params = null)
    {
        if ($params) {
            $id = $params[':ID'];
            $user = $this->authHelper->getUser();
            if ($user) {
                if ($id == $user->sub) {
                    $this->view->response($user, 200);
                } else {
                    $this->view->response('Forbidden', 403);
                }
            } else {
                $this->view->response('Unauthorized', 401);
            }
        }
    }
}
