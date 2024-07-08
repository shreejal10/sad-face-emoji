<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['checkout']) || isset($_POST['update_cart'])) {
        // Update quantities in the cart
        foreach ($_POST['quantities'] as $product_id => $quantity) {
            $quantity = intval($quantity);
            if ($quantity > 0) {
                $update_query = "UPDATE cart SET quantity = $quantity WHERE user_id = $user_id AND product_id = $product_id";
                mysqli_query($conn, $update_query);
            } else {
                $remove_query = "DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id";
                mysqli_query($conn, $remove_query);
            }
        }
    }

    if (isset($_POST['remove'])) {
        // Remove a specific item from the cart
        $product_id = intval($_POST['product_id']);
        $remove_query = "DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id";
        mysqli_query($conn, $remove_query);
    } elseif (isset($_POST['clear_cart'])) {
        // Clear the entire cart
        $clear_cart_query = "DELETE FROM cart WHERE user_id = $user_id";
        mysqli_query($conn, $clear_cart_query);
    } elseif (isset($_POST['checkout'])) {
        // Fetch updated cart items for the user
        $query = "SELECT cart.*, product.name, product.price FROM cart 
                  JOIN product ON cart.product_id = product.id 
                  WHERE cart.user_id = $user_id";
        $result = mysqli_query($conn, $query);
        $cart_items = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Calculate total amount
        $total_amount = 0;
        foreach ($cart_items as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }

        // Save order into orders table
        $order_query = "INSERT INTO orders (user_id, total_amount, order_date) 
                        VALUES ($user_id, $total_amount, NOW())";
        mysqli_query($conn, $order_query);

        // Clear user's cart after placing order
        $clear_cart_query = "DELETE FROM cart WHERE user_id = $user_id";
        mysqli_query($conn, $clear_cart_query);

        mysqli_close($conn);

        header("Location: order_confirmation.php?total_amount=$total_amount");
        exit();
    }
}

// Fetch cart items for the user
$query = "SELECT cart.*, product.name, product.price FROM cart 
          JOIN product ON cart.product_id = product.id 
          WHERE cart.user_id = $user_id";
$result = mysqli_query($conn, $query);
$cart_items = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
</head>

<body>
    <h2>Your Cart</h2>
    <a href="home.php">Continue Shopping</a>
    <?php if (count($cart_items) > 0) : ?>
        <form method="POST" action="checkout.php">
            <table>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
                <?php
                $total_price = 0;
                foreach ($cart_items as $item) :
                    $item_total = $item['price'] * $item['quantity'];
                    $total_price += $item_total;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['price']); ?></td>
                        <td>
                            <input type="number" name="quantities[<?php echo $item['product_id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1">
                        </td>
                        <td><?php echo $item_total; ?></td>
                        <td>
                            <button type="submit" name="remove" value="Remove">Remove</button>
                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <p>Total Price: <?php echo $total_price; ?></p>
            <button type="submit" name="update_cart">Update Cart</button>
            <button type="submit" name="checkout">Checkout</button>
            <button type="submit" name="clear_cart">Clear Cart</button>
        </form>
    <?php else : ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</body>

</html>