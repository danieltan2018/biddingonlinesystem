<?php

class ConnectionManager
{

    public function getConnection()
    {

        $host = "localhost";
        $username = "root";
        if ($_SERVER['SERVER_NAME'] == "54.169.91.131") {
            $password = "3Ps9yPiNA4tm";
        } else {
            $password = "";
        }
        $dbname = "g8t8";
        $port = 3306;

        $url  = "mysql:host={$host};dbname={$dbname};port={$port}";

        $conn = new PDO($url, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
}
