<?php
session_start();    
include 'header.php';
if (isset($_SESSION['username'])){
    $username = $_SESSION['username'];
    $message = "Welcome back, ".htmlspecialchars($username)."!";
} else{
    $message = "Welcome to my shop lo";
}
include "static/html/index.html"
?>