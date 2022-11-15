<?php
require_once './app/models/ProductModel.php';
require_once './app/view/api.view.php';
require_once './app/helpers/auth-api.helper.php';

class ApiProductController
{
    private $model;
    private $view;
    private $authHelper;

    function __construct()
    {
        $this->model = new ProductModel();
        $this->view = new ApiView();
        $this->authHelper = new AuthApiHelper();

        // lee el body del request
        $this->data = file_get_contents("php://input");
    }

    private function getData()
    {
        return json_decode($this->data);
    }

    public function getAll($params = null)
    {
        $products = $this->model->getAll();
        $this->view->response($products);
    }

    public function get($params = null)
    {
        $id = $params[':ID'];
        $product = $this->model->get($id);
        if ($product)
            $this->view->response($product);
        else
            $this->view->response("Product id= $id not found", 404);
    }

    public function remove($params = null)
    {
        $id = $params[':ID'];

        if (!$this->authHelper->isLoggedIn()) {
            $this->view->response("You are not logged in", 401);
            return;
        }

        $product = $this->model->get($id);
        if ($product) {
            $product = $this->model->delete($id);
            $this->view->response("Product id= $id remove successfully");
        } else {
            $this->view->response("Product id= $id not found", 404);
        }
    }

    public function insert($params = null)
    {
        $product = $this->getData();
        if (empty($product->marca) || empty($product->modelo) || empty($product->precio) || empty($product->id_categoria)) {
            $this->view->response("Complete all the fields", 400);
        } else {
            $body = $this->getData();
            $marca = $body->marca;
            $modelo = $body->modelo;
            $precio = $body->precio;
            $id_categoria = $body->id_categoria;
            $product = $this->model->insert($marca, $modelo, $precio, $id_categoria);
            $this->view->response("Product created successfully ", 201);
        }
    }



    public function update($params = null)
    {
        $id = $params[':ID'];
        $product = $this->model->get($id);
        if ($product) {
            $body = $this->getData();
            $marca = $body->marca;
            $modelo = $body->modelo;
            $precio = $body->precio;
            $id_categoria = $body->id_categoria;
            $product = $this->model->update($marca, $modelo, $precio, $id_categoria, $id);
            $this->view->response("Product updated successfully ", 200);
        } else {
            $this->view->response("Complete all the fields", 400);
        }
    }
}
