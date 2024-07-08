<?php
// Include database connection
include('db_connection.php');

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Hash the password (use stronger hashing methods in production)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into database
    $query = "INSERT INTO users (username, password, email) VALUES ('$username', '$hashed_password', '$email')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "Registration successful. <a href='login.php'>Login here</a>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
</head>

<body>
    <h2>User Registration</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        Email: <input type="email" name="email" required><br><br>
        <input type="submit" value="Register">
    </form>
</body>

</html>