<?php


header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
session_start();
//process.php

$connect = new PDO("mysql:host=localhost; dbname=stock_db", "root", "");

$query = "SELECT * FROM stocks WHERE id > ?";
//var_dump($_SESSION['latest_id']);
$clause = [$_SESSION['latest_id']];

$statement = $connect->prepare($query);

$statement->execute($clause);

echo $statement->rowCount();


?>