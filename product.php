<?php
// Connect to the database
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "inventory_system";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Retrieve products from the database
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);

// Initialize the cart array
$cart = array();

// Handle adding items to the cart
if(isset($_POST['add_to_cart'])) {
    $product_id = $_POST['id'];
    $product_name = $_POST['name'];
    $product_price = $_POST['sale_price'];
    $quantity = $_POST['quantity'];
    
    // Check if the product is already in the cart
    $index = -1;
    for($i = 0; $i < count($cart); $i++) {
        if($cart[$i]['id'] == $product_id) {
            $index = $i;
            break;
        }
    }
    
    // If the product is already in the cart, update the quantity
    if($index != -1) {
        $cart[$index]['quantity'] += $quantity;
    } else {
        // Otherwise, add the product to the cart
        $item = array(
            'id' => $product_id,
            'name' => $product_name,
            'sale_price' => $product_price,
            'quantity' => $quantity
        );
        array_push($cart, $item);
    }
}

// Handle removing items from the cart
if(isset($_POST['remove_from_cart'])) {
    $product_id = $_POST['id'];
    
    // Remove the item from the cart
    for($i = 0; $i < count($cart); $i++) {
        if($cart[$i]['id'] == $product_id) {
            array_splice($cart, $i, 1);
            break;
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart Example</title>
</head>
<body>
    <h1>Products</h1>
    <?php
    // Display products on the webpage
    if(mysqli_num_rows($result) > 0) {
        echo "<table>";
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['sale_price'] . "</td>";
            echo "<td>
                <form method=\"post\">
                    <input type=\"hidden\" name=\"id\" value=\"" . $row['id'] . "\">
                    <input type=\"hidden\" name=\"name\" value=\"" . $row['name'] . "\">
                    <input type=\"hidden\" name=\"sale_price\" value=\"" . $row['sale_price'] . "\">
                    <input type=\"number\" name=\"quantity\" value=\"1\">
                    <button type=\"submit\" name=\"add_to_cart\">Add to Cart</button>
                </form>
            </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No products found.";
    }
    ?>
    
    <h1>Cart</h1>
    <?php
    // Display items in the cart
    if(count($cart) > 0) {
        $total = 0;
        echo "<table>";
        echo "<tr><th>Product Name</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th></tr>";
        foreach($cart as $item) {
            $subtotal = $item['sale_price'] * $item['quantity'];
            $total += $subtotal;
            
            echo "<tr>";
            echo "<td>" . $item['name'] . "</td>";
            echo "<td>" . $item['sale_price'] . "</td>";
            echo "<td>" . $item['quantity'] . "</td>";
            echo "<td>
                <form method=\"post\">
                    <input type=\"hidden\" name=\"id\" value=\"" . $item['id'] . "\">
                    <button type=\"submit\" name=\"remove_from_cart\">Remove</button>
                </form>
            </td>";
            echo "</tr>";
        
        echo "</table>";
    } 
    echo "<p>Total: " . $total . "</p>";
} else {
    echo "Your cart is empty.";
    }
    ?>
</body>
</html>

