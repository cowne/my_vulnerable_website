<?php
ob_start();
session_start();
include 'header.php';
//Get infor product by id
if ($_GET['id']){
    include "db.php";
    $id = $_GET['id'];

    $query = "SELECT name,price,image_product,description FROM products WHERE id=".$id; //insecure
    $db_result = $database->query($query);
    if($db_result->num_rows > 0){
        $row = $db_result->fetch_assoc();
        
        $name = $row['name'];
        $price = $row['price'];
        $image_product = $row['image_product'];
        $description = $row['description'];
    } else{
        die('Cannot search product.');
    }
}else{
    header('Location: /index.php');
}
include 'static/html/product.html';
include 'calc.php';
?>