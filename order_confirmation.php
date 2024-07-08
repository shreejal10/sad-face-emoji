<?php
if (!isset($_GET['total_amount'])) {
    header("Location: home.php");
    exit();
}
$total_amount = $_GET['total_amount'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
</head>

<body>
    <h2>Order Placed Successfully</h2>
    <p>Total Amount: Rs.<?php echo $total_amount; ?></p>
    <p><a href="home.php">Continue Shopping</a></p>
</body>

</html>