<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'plush_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$session_id = session_id();
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : NULL;

if ($user_id) {
    $sql = "SELECT c.id AS cart_id, p.id, p.name, p.price, p.image, c.quantity
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = $user_id";
} else {
    $sql = "SELECT c.id AS cart_id, p.id, p.name, p.price, p.image, c.quantity
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.session_id = '$session_id'";
}

$result = $conn->query($sql);
$cart_items = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
}

if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    $conn->query("DELETE FROM cart WHERE id=$remove_id AND (session_id='$session_id' OR user_id=$user_id)");
    header("Location: cart.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Shopping Cart - Plush</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
<header>
  <div class="container">
    <h1 class="logo">Plush</h1>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <?php if (isset($_SESSION['username'])): ?>
          <li><a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a></li>
        <?php else: ?>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>

<main class="container">
  <h2>Your Cart</h2>
  <?php if (count($cart_items) == 0): ?>
    <p>Your cart is empty.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Subtotal</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $total = 0;
        foreach ($cart_items as $item):
          $subtotal = $item['price'] * $item['quantity'];
          $total += $subtotal;
        ?>
        <tr>
          <td>
            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width:50px; vertical-align:middle;" />
            <?= htmlspecialchars($item['name']) ?>
          </td>
          <td>$<?= number_format($item['price'], 2) ?></td>
          <td><?= $item['quantity'] ?></td>
          <td>$<?= number_format($subtotal, 2) ?></td>
          <td><a href="cart.php?remove=<?= $item['cart_id'] ?>" onclick="return confirm('Remove item?');">Remove</a></td>
        </tr>
        <?php endforeach; ?>
        <tr>
          <td colspan="3" style="text-align:right;"><strong>Total:</strong></td>
          <td colspan="2"><strong>$<?= number_format($total, 2) ?></strong></td>
        </tr>
      </tbody>
    </table>
  <?php endif; ?>
</main>

<footer>
  <p>&copy; 2025 Plush. All rights reserved.</p>
</footer>
</body>
</html>
