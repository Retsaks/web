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

// Fetch all products
$products_query = "SELECT id_producto, nombre, precio, imagen FROM productos";
$products_result = $conn->query($products_query);

$products = [];
while ($row = $products_result->fetch_assoc()) {
    $products[$row['id_producto']] = $row;
}

$conn->close();
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
    <title>Carrito de Compras - Leguizamón Grifería</title>
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
        <h1>Carrito de Compras</h1>
        <div id="cart-items" class="cart-items">
           
        </div>
        <div class="cart-total">
            <h2>Total: $<span id="cart-total">0.00</span></h2>
            <button id="checkout-button">Proceder al pago</button>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Grifería. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cartItemsContainer = document.getElementById('cart-items');
            const cartTotalElement = document.getElementById('cart-total');
            const products = <?php echo json_encode($products); ?>;
            
            function updateCartDisplay() {
                const cart = JSON.parse(localStorage.getItem('cart')) || {};
                let cartContent = '';
                let total = 0;

                for (const [productId, quantity] of Object.entries(cart)) {
                    if (products[productId]) {
                        const product = products[productId];
                        const subtotal = product.precio * quantity;
                        total += subtotal;

                        cartContent += `
                            <div class="cart-item" data-product-id="${productId}">
                                <img src="${product.imagen}" alt="${product.nombre}" class="cart-item-img">
                                <div class="cart-item-info">
                                    <h3>${product.nombre}</h3>
                                    <p class="cart-item-price">$${product.precio}</p>
                                    <p class="cart-item-quantity">Cantidad: ${quantity}</p>
                                    <p class="cart-item-subtotal">Subtotal: $${subtotal.toFixed(2)}</p>
                                </div>
                                <button class="remove-from-cart" data-product-id="${productId}">Eliminar</button>
                            </div>
                        `;
                    }
                }

                cartItemsContainer.innerHTML = cartContent || '<p>Tu carrito está vacío.</p>';
                cartTotalElement.textContent = total.toFixed(2);

                // Add event listeners for remove buttons
                const removeButtons = document.querySelectorAll('.remove-from-cart');
                removeButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const productId = this.getAttribute('data-product-id');
                        removeFromCart(productId);
                    });
                });
            }

            function removeFromCart(productId) {
                let cart = JSON.parse(localStorage.getItem('cart')) || {};
                delete cart[productId];
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartDisplay();
                updateCartCount(); // Assuming this function is defined in script.js
            }

            updateCartDisplay();
        });
    </script>
</body>
</html>