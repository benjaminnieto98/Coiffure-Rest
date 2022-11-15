<?php

class ProductModel
{

    function __construct()
    {
        $this->db = new PDO('mysql:host=localhost;' . 'dbname=db-coiffure;charset=utf8', 'root', '');
    }

    //DEVUELVE TODOS LOS PRODUCTOS
    function getAll()
    {
        $sentence = $this->db->prepare(
            "SELECT productos.*, categorias.nombre AS categoria
            FROM productos
            JOIN categorias ON productos.id_categoria = categorias.id_categoria"
        );
        $sentence->execute();
        $products = $sentence->fetchAll(PDO::FETCH_OBJ);
        return $products;
    }

    //DEVUELVE EL PORDUCTO CON EL ID PASADO POR PARAMETRO
    function get($id_producto)
    {
        $sentence = $this->db->prepare(
            "SELECT productos.*, categorias.nombre AS categoria
            FROM productos
            JOIN categorias ON productos.id_categoria = categorias.id_categoria
            WHERE id_producto=?"
        );
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
    function insert($marca, $modelo, $precio, $id_categoria)
    {
        $sentence = $this->db->prepare("INSERT INTO productos(marca, modelo, precio, id_categoria) VALUES(?,?,?,?)");
        $sentence->execute(array($marca, $modelo, $precio, $id_categoria));
    }

    //ACTUALIZA LOS DATOS DE UN PRODUCTO
    function update($marca, $modelo, $precio, $id_categoria, $id_producto)
    {
        $sentence = $this->db->prepare("UPDATE productos SET marca=?, modelo=?, precio=?, id_categoria=? WHERE id_producto=?");
        $sentence->execute(array($marca, $modelo, $precio, $id_categoria, $id_producto));
    }
}
