<?php

ini_set("display_errors",1);
ini_set("display_startip_errors",1);
error_reporting(E_ALL);


$hostname="localhost";
$username="xvalachovicl";
$password="vRHnEC9gN6mxJFE";
$dbname="restaurants";


try{
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch (PDOException $e) {
    echo $e->getMessage();
}


?>