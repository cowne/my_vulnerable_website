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
            }
            else{
                header('Location: cart.php');
            }
            break;
        case "buy": 
            include 'db.php';
            $id = $_SESSION['user_id'];
            try{
                $query = "select p.id, p.name, p.price, cp.number_of_ordered_product, p.image_product, cp.id 
                from cart as c, users as u ,products as p , cart_product as cp where c.user_id= u.id and c.id=cp.id_cart and cp.id_product=p.id and u.id=".$id;
                $db_result = $database->query($query);
            }catch(mysqli_sql_exception $e){
                $message = $e->getMessage();
            }
            if($db_result->num_rows > 0){
                $row = $db_result->fetch_all();
            }
            if(isset($_POST['pay']) and $_POST['pay'] == 1){
                $money_user = $_SESSION['money'];
                $money_total = $_SESSION['totalMoney'];
                if ($money_total > $money_user){
                    echo '<script>alert("Money khong du, moi nap them")</script>';
                }else{
                    $money = $money_user - $money_total;
                    $_SESSION['money'] = $money;
                    $sql = "UPDATE users SET money =" .$money."where id =".$id;
                    $database->query($sql);

                    echo '<script>alert("Da thanh toan xong")</script>';
                    $id_cart = get_cart_id($id);
                    $query_delete = "DELETE FROM cart_product WHERE id_cart =".$id_cart;
                    $database->query($query_delete);
                    header( "refresh:1;url=index.php" );
                }
            }
            break;

        case "delete": // delete product ordered
            if(isset($_GET['cart_product'])){
                $username = $_SESSION['username'];
                $cart_product_id = $_GET['cart_product'];
                include 'db.php';
                // prevent IDOR
                $query = 'select cp.id, u.username 
                from users as u, cart_product as cp, cart as c 
                where u.id = c.user_id and c.id = cp.id_cart and cp.id= ? and u.username = ?';
                $sql = $database->prepare($query);
                $sql->bind_param("ss",$cart_product_id,$username);
                $sql->execute();
                $sql->store_result();
                if ($sql->num_rows() > 0){
                    $delete_query = 'delete FROM cart_product WHERE id = ?';
                    $sth = $database->prepare($delete_query);
                    $sth->bind_param('s', $cart_product_id);
                    $sth->execute();
                    $message_func = "DELETE YOUR ORDER.";
                } else{
                    $message_func = "IDOR DETECTED.";
                }
                header('Location: /cart.php');
            }
            break;
        default:
            echo "nothing";
    }
} else{
    include 'db.php';
    //$id = $_GET['id'];
    $id = $_SESSION['user_id'];
    try{
        $query = "select p.id, p.name, p.price, cp.number_of_ordered_product, p.image_product, cp.id 
        from cart as c, users as u ,products as p , cart_product as cp where c.user_id= u.id and c.id=cp.id_cart and cp.id_product=p.id and u.id=".$id;
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