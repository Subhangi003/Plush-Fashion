<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'plush_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all products
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Plush - Fashion for You</title>
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

<main class="container">
  <h2>Featured Products</h2>
  <div class="products">
    <?php foreach ($products as $product): ?>
      <div class="product" data-category="<?= htmlspecialchars($product['category']) ?>" data-price="<?= $product['price'] ?>">
        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
        <h3><?= htmlspecialchars($product['name']) ?></h3>
        <p>$<?= number_format($product['price'], 2) ?></p>
        <a href="product.php?id=<?= $product['id'] ?>" class="btn">View</a>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<footer>
  <p>&copy; 2025 Plush. All rights reserved.</p>
</footer>
</body>
</html>
