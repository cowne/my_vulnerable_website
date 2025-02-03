<?php
ob_start();
session_start();
if (isset($_SESSION['username'])){
    unset($_SESSION['username']);
    header('Location:/index.php');
}
else{
    header('Refresh:2; url=/index.php');
}
?>