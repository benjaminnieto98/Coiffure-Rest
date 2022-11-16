<?php

class UserModel
{

    private $db;

    function __construct()
    {
        $this->db = new PDO('mysql:host=localhost;' . 'dbname=db-coiffure;charset=utf8', 'root', '');
    }

    function getUser($email)
    {
        $sentence = $this->db->prepare(
            "SELECT *
            FROM usuarios
            WHERE email=?"
        );
        $sentence->execute([$email]);
        $user = $sentence->fetch(PDO::FETCH_OBJ);
        return $user;
    }
}
