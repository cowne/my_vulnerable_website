<?php
ob_start();
session_start();
include 'header.php';
if (!isset($_SESSION['username'])){
    $message = "You should login first to use the function!";
    header('Location:/login.php');
}
if (isset($_GET['action'])){
    switch ($_GET['action']){
        case "add": // create a new cart
            if(isset($_POST['product_id']) && isset($_POST['num_order']) ){
                $product_id = $_POST['product_id'];
                $user_id = $_SESSION['user_id'];
                $num_order = $_POST['num_order'];
                add_to_cart($product_id,$user_id,$num_order);
                $message = 'Order successful';
                header('Location: cart.php');
            }else{
                header('Location: cart.php');
            }
            break;
        case "buy": // calculate 
            if(isset($_POST['totalPrice'])){
                $money = $_SESSION['money'];
                $price = $_POST['totalPrice'];
                if($money > $price){
                    //echo 'You can buy';
                    include 'db.php';
                    $money = $money - $price;
                    $query = "UPDATE users SET money =? WHERE username =?";
                    $sth = $database->prepare($query);
                    $sth->bind_param('ss',$money,$_SESSION['username']);
                    $sth->execute();
                    $_SESSION['money'] = $money;
                    $message = "<script>alert(\"Buy successful! Continue to shopping?\")</script>";
                    header('Refresh:1 ; url=/index.php');
                }
                else{
                    $message = "<script>alert(\"You don't have enough money\")</script>";
                    header('Refresh:1 ; url=/index.php');
                }
            }
            break;
        case "delete": // remove cart
            echo "delete order";
            break;
        default:
            echo "nothing";
    }
} else{
    include 'db.php';
    //$id = $_GET['id'];
    $id = $_SESSION['user_id'];
    try{
        $query = "select p.id,p.name,p.price,cp.number_of_ordered_product,p.image_product from cart as c, users as u ,products as p , cart_product as cp where c.user_id= u.id and c.id=cp.id_cart and cp.id_product=p.id and u.id=".$id;
        $db_result = $database->query($query);
    }catch(mysqli_sql_exception $e){
        $message = $e->getMessage();
    }
    if($db_result->num_rows > 0){
        $row = $db_result->fetch_all();
    } else{
        if(is_cart_created($id)){
            $message ="Your cart here";
        }else{
            create_new_cart($id);
            $message ="Create new cart successful";
        }
    }
}
function add_to_cart($product_id, $user_id, $number_order){
    try{
        include 'db.php';
        $cart_id = get_cart_id($user_id); //get cart id by user id
        //check if product is ordered
        if(is_product_ordered($cart_id,$product_id)){
            $query = "UPDATE cart_product SET number_of_ordered_product=number_of_ordered_product + ? WHERE id_cart=? and id_product=?";
            $sth = $database->prepare($query);
            $sth->bind_param('sss',$number_order,$cart_id,$product_id);
            $sth->execute();
        }else{
            $query = "INSERT INTO cart_product(id_cart, id_product, number_of_ordered_product) VALUES (?,?,?)";     
            $sth = $database->prepare($query);      
            $sth->bind_param('sss',$cart_id,$product_id,$number_order);
            $sth->execute();
        }
         
        //$message = "Add to cart successful.";
    }catch(mysqli_sql_exception $e){
        return $message = $e->getMessage();
    }

}

//check is the product oredered or not
function is_product_ordered($cart_id,$product_id){
    include 'db.php';
    $query = "SELECT id_product FROM cart_product where id_cart=? and id_product=?";
    $sth = $database->prepare($query);
    $sth->bind_param('ss',$cart_id,$product_id);
    $sth->execute();
    $sth->store_result();
    if($sth->num_rows() > 0) {
        return true;
    }
    return false;
}

//get cart id from user id
function get_cart_id($user_id){
    include 'db.php';
    $sql = "SELECT id FROM cart WHERE user_id=?";
    $sth = $database->prepare($sql);
    $sth->bind_param('s', $user_id);
    $sth->execute();
    $result = $sth->get_result();
    $row = $result->fetch_assoc();
    return $row['id'];
}


function create_new_cart($user_id){
    try{
        include 'db.php';
        $query = "INSERT INTO cart(user_id) VALUES(?)";
        $sth = $database->prepare($query);
        $sth->bind_param('s',$user_id);
        $sth->execute();
    }catch(mysqli_sql_exception $e){
        $message = $e->getMessage();
    }
}

//check is user_id have cart
function is_cart_created($user_id){
    include 'db.php';
    $query = "select id from cart where user_id=?";
    $sth = $database->prepare($query);
    $sth->bind_param('s', $user_id);
    $sth->execute();
    $sth->store_result();
    if ($sth->num_rows() > 0) return true;
    else return false;
}

include 'static/html/cart.html';