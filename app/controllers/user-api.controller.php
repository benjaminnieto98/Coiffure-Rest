<?php

require_once './app/models/userModel.php';
require_once './app/views/api.view.php';
require_once './app/helpers/auth-api.helper.php';

class UserApiController{
    // private $model;
    private $view; 
    private $authHelper; 

    function __construct(){
        //$this->model = new UserModel();
        $this->view = new ApiView();
        $this->authHelper = new AuthApiHelper();
    }

    public function getToken(){
        $userpass = $this->authHelper->getBasic();        
        //obtengo el usuario de la bbdd
        //$user = $this->model->getUser($userName);

        $user = array("user"=>$userpass["user"]);

        //if usuario existe y contrasena coincide
        if(true /*$user && password_verify($password, $user->password)*/){
            $token = $this->authHelper->createToken($user);
            //devolver un token
            $this->view->response(["token"=>$token], 200);
        }
        else{
            $this->view->response("Wrong username or password", 401);
        }
    }

    function getUser($params = null)
    {
        if ($params) {
            $id = $params[':ID'];
            $user = $this->authApiHelper->getUser();
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
