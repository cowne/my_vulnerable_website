<?php 
ob_start();
$file_name = $_GET['file_name']; #../../../../etc/passwd
$file_path = '/var/www/html/static/images/' . $file_name; #/var/www/html/images/etc/shadow
if (file_exists($file_path)) {
    readfile($file_path);
}
else { // Image file not found
    echo " 404 Not Found";
}