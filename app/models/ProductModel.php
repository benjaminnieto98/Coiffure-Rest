<?php

class ProductModel
{

    function __construct()
    {
        $this->db = new PDO('mysql:host=localhost;' . 'dbname=db-coiffure;charset=utf8', 'root', '');
    }

    //DEVUELVE TODOS LOS PRODUCTOS
    function getAll($order, $orderMode, $elements, $startAt)
    {
        $sentence = $this->db->prepare("SELECT productos.*, categorias.nombre AS categoria
                                        FROM productos
                                        JOIN categorias 
                                        ON productos.id_categoria = categorias.id_categoria
                                        ORDER BY $order  $orderMode
                                        LIMIT $elements 
                                        OFFSET $startAt");
        $sentence->execute();
        $products = $sentence->fetchAll(PDO::FETCH_OBJ);
        return $products;
    }

    function getAllWithFilter($order, $orderMode, $elements, $startAt, $filterBy, $equalTo)
    {
        $sentence = $this->db->prepare("SELECT productos.*, categorias.nombre AS categoria
                                     FROM productos
                                     JOIN categorias
                                     ON productos.id_categoria = categorias.id_categoria
                                     WHERE $filterBy = ?
                                     ORDER BY $order $orderMode
                                     LIMIT $elements 
                                     OFFSET $startAt");
        $sentence->execute([$equalTo]);
        $products = $sentence->fetchAll(PDO::FETCH_OBJ);
        return $products;
    }

    //DEVUELVE EL PORDUCTO CON EL ID PASADO POR PARAMETRO
    function get($id_producto)
    {
        $sentence = $this->db->prepare("SELECT productos.*, categorias.nombre AS categoria
                                        FROM productos
                                        JOIN categorias 
                                        ON productos.id_categoria = categorias.id_categoria
                                        WHERE id_producto=?");
        $sentence->execute(array($id_producto));
        $product = $sentence->fetch(PDO::FETCH_OBJ);
        return $product;
    }

    //ELIMINA EL PRODUCTO
    function delete($id_producto)
    {
        $sentence = $this->db->prepare("DELETE FROM `productos` WHERE id_producto=?");
        $sentence->execute(array($id_producto));
    }

    //AÑADE UN PRODUCTO
    function insert($product)
    {
        $sentence = $this->db->prepare("INSERT INTO productos(marca, modelo, precio, id_categoria) VALUES(?,?,?,?)");
        $sentence->execute(array($product->marca, $product->modelo, $product->precio, $product->id_categoria));
    }

    //ACTUALIZA LOS DATOS DE UN PRODUCTO
    function update($id_producto, $product)
    {
        $sentence = $this->db->prepare("UPDATE productos SET marca=?, modelo=?, precio=?, id_categoria=? WHERE id_producto=?");
        $sentence->execute(array($product->marca, $product->modelo, $product->precio, $product->id_categoria, $id_producto));
    }

    //OBTIENE LOS NOMBRES DE LAS COLUMNAS
    function getColumns()
    {
        $sentence = $this->db->prepare('DESCRIBE productos');
        $sentence->execute();

        $columns = $sentence->fetchAll(PDO::FETCH_OBJ);
        return $columns;
    }
}
