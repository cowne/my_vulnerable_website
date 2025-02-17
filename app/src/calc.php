<?php
ob_start();
session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calculate'])) {
        $num_order = $_POST['num_order'];

        $totalPrice = eval("return $price * $num_order;"); // insecure
        
        echo "<div class=\"container mt-5\">";
        echo "<div class=\"mt-4\">";
        echo "<p>The total price is: " . $totalPrice . "</p>";
        echo '<form action="/cart.php?action=add" method="post">';
        echo '<input type="hidden" name="product_id" value="' . $id . '">';
        echo '<input type="hidden" name="num_order" value="' . $num_order . '">';
        echo '<button type="submit" name="submit">Add to cart</button>';
        echo '</form>';
        echo '</div>';
        echo "</div>";
    }
?>