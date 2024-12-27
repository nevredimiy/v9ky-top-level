<?php

class Db 
{

    private $conn;

    function __construct(array $db_config) 
    {
        $dsn = "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charste={$db_config['charset']}";

        try {
            $this->conn = new PDO($dsn, $db_coonfig['username'], $db_coonfig['password'], $db_coonfig['options']);
        } catch (PDOExeption $e) {
            echo "DB Error: {$e->getMessage()}";
            die;
        }
    }

}