<?php
ob_start();
session_start();
include 'header.php';
if (!isset($_SESSION['username'])){
    header('Location: /login.php');
}
else{
    $username = $_SESSION['username'];
    $address = $_SESSION['address'];
    $phone = $_SESSION['phone'];
    $money = $_SESSION['money'];
    $message = "Welcome back, ".htmlspecialchars($username)."!";
    include 'static/html/profile.html';
}

?>