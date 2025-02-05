<?php
session_start();    
include 'header.php';
include 'db.php';
$query = 'SELECT * FROM products';
$db_result = $database->query($query);
$row = $db_result->fetch_all();
foreach($row as $product_info){
    $product_id = $product_info[0];
    $product_name = $product_info[1];
    $product_price = $product_info[2];
    $product_image = $product_info[3];
}

include "static/html/index.html"
?>