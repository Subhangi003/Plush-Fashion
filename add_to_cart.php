<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'plush_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$session_id = session_id();
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : NULL;
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Check if product already in cart for this session/user
if ($user_id) {
    $sql = "SELECT id, quantity FROM cart WHERE user_id=$user_id AND product_id=$product_id";
} else {
    $sql = "SELECT id, quantity FROM cart WHERE session_id='$session_id' AND product_id=$product_id";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;
    $update_sql = "UPDATE cart SET quantity=$new_quantity WHERE id=" . $row['id'];
    $conn->query($update_sql);
} else {
    if ($user_id) {
        $insert_sql = "INSERT INTO cart (session_id, user_id, product_id, quantity) VALUES ('$session_id', $user_id, $product_id, $quantity)";
    } else {
        $insert_sql = "INSERT INTO cart (session_id, product_id, quantity) VALUES ('$session_id', $product_id, $quantity)";
    }
    $conn->query($insert_sql);
}

$conn->close();
header("Location: cart.php");
exit;
