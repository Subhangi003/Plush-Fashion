<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'plush_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT * FROM products WHERE id = $product_id LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    die("Product not found.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($product['name']) ?> - Plush</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
<header>
  <div class="container">
    <h1 class="logo">Plush</h1>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="cart.php">Cart 🛒</a></li>
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

<main class="product-detail container">
  <div class="product-image">
    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
  </div>
  <div class="product-info">
    <h2><?= htmlspecialchars($product['name']) ?></h2>
    <p class="price">$<?= number_format($product['price'], 2) ?></p>
    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
    <form method="post" action="add_to_cart.php">
      <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
      <label for="quantity">Quantity:</label>
      <input type="number" id="quantity" name="quantity" value="1" min="1" max="10" />
      <button type="submit" class="btn">Add to Cart</button>
    </form>
  </div>
</main>

<footer>
  <p>&copy; 2025 Plush. All rights reserved.</p>
</footer>
</body>
</html>
