<?php
session_start();
ob_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calculate'])) {
        $num_order = $_POST['num_order'];
        $pricePerUnit = 10; // Assuming the price per unit is 10

        $totalPrice = eval("return $price * $num_order;"); // insecure
        
        echo "<p>The total price is: " . $totalPrice . "</p>";
        echo '<form action="/cart.php?action=add" method="post">';
        echo '<input type="hidden" name="product_id" value="' . $id . '">';
        echo '<input type="hidden" name="num_order" value="' . $num_order . '">';
        echo '<button type="submit" name="submit">Add to cart</button>';
        echo '</form>';
        echo '<form action="/cart.php?action=buy" method="post">';
        echo '<input type="hidden" name="totalPrice" value="' . $totalPrice . '">';
        echo '<button type="submit" name="submit">Buy</button>';
        echo '</form>';
    }
?>