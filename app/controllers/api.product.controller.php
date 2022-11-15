<?php
require_once './app/models/ProductModel.php';
require_once './app/views/api.view.php';
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

    public function get($params = null)
    {
        if ($params != null) {
            $id = $params[':ID']; // Si vienen parametros, llama al modelo y busca por id
            $product = $this->model->get($id);
            if ($product) {
                return $product = $this->view->response($product, 200);
            } else {
                $this->view->response("Product id= $id not found", 404);
            }
        } else { // no viene un parámetro :ID, entonces obtiene la coleccion entera
            // Ordenado
            $orderBy = $_GET['orderBy'] ?? "id_producto";
            $orderMode = $_GET['orderMode'] ?? "asc";
            // Paginado
            $page = (int)($_GET['page'] ?? 1);
            $elements = (int)($_GET['elements'] ?? 5);
            // Filtrado
            $filterBy = $_GET['filterBy'] ?? null;
            $equalTo = $_GET['equalTo'] ?? null;

            $columns = $this->getHeaderColumns(); //Obtiene los nombres de las columnas de la tabla productos.
            if (($orderBy == 'categoria' || in_array(strtolower($orderBy), $columns)) && (strtolower($orderMode == "asc") || strtolower($orderMode == "desc"))) { // Verifica si los parámetros de ordenado son válidos
                if ($orderBy == 'categoria') {  //Asigna un valor $order para pasar al modelo en funcion del campo por el que se quiere ordenar
                    $order = 'categorias.categoria';
                } else {
                    $order = 'productos.' . $orderBy;
                }
                if ((is_numeric($page) && $page > 0) && (is_numeric($elements) && $elements > 0)) {  // Verifica si los parámetros de paginado son válidos
                    $startAt = ($page * $elements) - $elements; //Calcula cuál es el primer elemento a mostrar del paginado y lo almacena en $startAt
                    if ($filterBy != null && $equalTo != null) { // Verifica si existen los parámetros de filtrado
                        if ($filterBy == 'categoria' || in_array(strtolower($filterBy), $columns)) { //Verifica que el campo $filterBy exista en la tabla (comparando con $columns)
                            if ($filterBy == 'categoria') { //Asigna un valor $filter para pasar al modelo en funcion del campo por el que se quiere ordenar
                                $filter = 'categorias.nombre';
                            } else {
                                $filter = 'productos.' . $filterBy;
                            }
                            $result = $this->model->getAllWithFilter($order, $orderMode, $elements, $startAt, $filter, $equalTo); //Obtiene todos los productos del modelo y pasa los parametros de ordenamiento, paginado y filtrado.
                            if (isset($result)) {   //Verifica si la consulta se realizó correctamente
                                if (empty($result)) { //Verifica si el resultado de la consulta está vacío.
                                    $this->view->response("The query performed did not return any results.", 204);
                                } else {
                                    $this->view->response($result, 200); //Envía el/los producto/s a la vista para ser mostrado/s.
                                }
                            } else {
                                $result = $this->view->response("The specified query could not be performed.", 500); //Informa error interno de servidor
                            }
                        } else {
                            $result = $this->view->response("Invalid filter parameter.", 400); //Informa error de parámetro no válido
                        }
                    } else {
                        $result = $this->model->getAll($order, $orderMode, $elements, $startAt); //Obtiene todos los productos del modelo y pasa los parametros de ordenamiento y paginado.
                        $this->view->response($result, 200);
                    }
                } else {
                    $result = $this->view->response("Invalid paging parameter.", 400); //Informa error de parámetro no válido
                }
            } else if ($orderBy == '') { //Si no se le agrega ningun parametro, obtiene todos los productos.
                $result = $this->model->getAll("id_producto", $orderMode, $elements, 0);
                $this->view->response($result, 200);
            } else {
                $result = $this->view->response("Invalid sort parameter", 400); //Informa error de parámetro no válido
            }
        }
    }


    //Método que devuelve un arreglo con los nombres de las columnas de una tabla
    function getHeaderColumns($params = null)
    {
        $columns = []; //Se define un arreglo vacío para almacenar los nombres de las columnas.
        $result = $this->model->getColumns(); // Obtiene toda la información de las columnas de la tabla. Devuelve un arreglo de objetos con toda la info
        foreach ($result as $column) { //Recorre el arreglo y por cada elemento, extrae el nombre de la columna y lo agrega al arreglo $columns.
            array_push($columns, $column->Field);
        }
        return $columns;
    }

    public function insert($params = null)
    {
        $body = $this->getData();
        if (
            empty($body->marca) ||
            empty($body->modelo) ||
            empty($body->precio) ||
            empty($body->id_categoria)
        ) {
            $this->view->response("Complete all the fields", 400);
        } else {
            $id = $this->model->insert($body);
            $product = $this->model->get($id);
            $this->view->response($product, 201);
        }
    }

    public function update($params = null)
    {
        if (!$this->authHelper->isLoggedIn()) {
            $this->view->response("You are not logged in", 401);
        } else {
            $body = $this->getData();
            if ($params != null) {
                $id = $params[':ID'];
                $product = $this->model->get($id);
                if ($product) {
                    $this->model->update($id, $body);
                    $this->view->response("Product updated successfully ", 200);
                } else {
                    $this->view->response("Product id= $id not found", 400);
                }
            }
        }
    }

    public function remove($params = null)
    {
        if (!$this->authHelper->isLoggedIn()) {
            $this->view->response("You are not logged in", 401);
        } else {
            if ($params != null) {
                $id = $params[':ID'];
                $product = $this->model->get($id);
                $this->model->delete($id);
                if ($product) {
                    $this->view->response($product, 200);
                } else {
                    $this->view->response("Product id= $id not found", 404);
                }
            } else {
                $this->view->response("Missing parameters", 400);
            }
        }
    }
}
