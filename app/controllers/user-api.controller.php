<?php

require_once './app/views/api.view.php';
require_once './app/helpers/auth-api.helper.php';
require_once './app/models/UserModel.php';

class UserApiController
{
    private $model;
    private $view;
    private $authHelper;

    function __construct()
    {
        $this->model = new UserModel();
        $this->view = new ApiView();
        $this->authHelper = new AuthApiHelper();
    }

    public function getToken()
    {
        $userpass = $this->authHelper->getBasic();
        $user = $userpass["user"];
        $pass = $userpass["pass"];
        //obtengo el usuario de la bbdd
        $userModel = $this->model->getUser($user);

        if ($userModel && password_verify($pass, $userModel->contraseña)) {
            //     //  crear un token
            $token = $this->authHelper->createToken($userModel);
            $this->view->response('Token' . $token, 200);
        } else {
            $this->view->response('Wrong username or password', 401);
        }
    }

    function getUser($params = null)
    {
        if ($params) {
            $id = $params[':ID'];
            $user = $this->authHelper->getUser();
            var_dump($user);
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
