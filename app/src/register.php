<?php
session_start();
include 'header.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        include "db.php";
        $sql = "select username from users where username=?";
        $sth = $database->prepare($sql);
        $sth->bind_param('s', $_POST['username']);
        $sth->execute();
        $sth->store_result();
        if ($sth->num_rows() > 0) {
            $message = "Sorry this username already registered";
        } else {
            $money = '1000';
            $sql = "insert into users(username, password, address,phone, money) values (?, ?, ?, ?, ?)";
            $sth = $database->prepare($sql);
            $sth->bind_param('sssss', $_POST['username'], $_POST['password'], $_POST['address'],$_POST['phone'], $money);
            $sth->execute();
            $message = "Create successful";
        }
    } catch (mysqli_sql_exception $e) {
        $message = $e->getMessage();
    }
}
include "static/html/register.html";
