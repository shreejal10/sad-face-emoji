<?php
session_start();
include('db_connection.php');

// Fetch products from database
$query = "SELECT * FROM product";
$result = mysqli_query($conn, $query);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle add to cart functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];


    // Insert into cart table
    // $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, 1)";
    // mysqli_query($conn, $insert_query);

    //dont insert if already exists
    $check_query = "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id";
    $check_result = mysqli_query($conn, $check_query);
    $check_row = mysqli_fetch_assoc($check_result);

    if ($check_row) {
    } else {
        $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, 1)";
        mysqli_query($conn, $insert_query);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Browse Products</title>
</head>

<body>
    <h2>Browse Products</h2>
    <ul>


        <?php if (isset($_SESSION['user_id'])) : ?>
            <li><a href="checkout.php">Checkout</a></li>

        <?php endif; ?>

        <?php foreach ($products as $product) : ?>
            <li>
                <?php echo $product['name']; ?> - Rs <?php echo $product['price']; ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="submit" name="add_to_cart" value="Add to Cart">
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>

</html>