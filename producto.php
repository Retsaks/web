<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leguizamon";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product ID from URL parameter
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details
$product_query = "SELECT p.*, c.nombre AS categoria_nombre 
                  FROM productos p 
                  LEFT JOIN producto_categorias pc ON p.id_producto = pc.id_producto
                  LEFT JOIN categorias c ON pc.id_categoria = c.id_categoria
                  WHERE p.id_producto = ?";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

$conn->close();

if (!$product) {
    // Redirect to index if product not found
    header("Location: index.php");
    exit();
}
?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['nombre']); ?> - Leguizamón Grifería</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">
                    <img src="Logo.png" alt="Leguizamón Grifería" class="logo-image">
                    <span>Leguizamón Grifería</span>
                </a>
                <div class="search-bar">
                    <input type="text" placeholder="Buscar productos...">
                    <button type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </div>
                <a href="logout.php" class="login-button">Cerrar Sesión</a>
                <div class="cart-icon">
    <a href="carrito.php">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <span id="cart-count">0</span>
    </a>
</div>
    </header>

    <main class="container">
        <div class="product-detail">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['imagen']); ?>" 
                     alt="<?php echo htmlspecialchars($product['nombre']); ?>"
                     class="product-img-large">
            </div>
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['nombre']); ?></h1>
                <p class="product-category">Categoría: <?php echo htmlspecialchars($product['categoria_nombre'] ?? 'No especificada'); ?></p>
                <p class="product-brand">Marca: <?php echo htmlspecialchars($product['marca']); ?></p>
                <p class="product-description"><?php echo htmlspecialchars($product['descripcion']); ?></p>
                <p class="product-price">Precio: $<?php echo number_format($product['precio'], 2); ?></p>
                <p class="product-stock">Stock: <?php echo $product['stock']; ?> unidades</p>
                <button class="add-to-cart" data-product-id="<?php echo $product['id_producto']; ?>">
                    Agregar al carrito
                </button>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Leguizamón Grifería. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>