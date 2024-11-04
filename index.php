<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leguizamon";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$categories_query = "SELECT id_categoria, nombre FROM categorias ORDER BY nombre";
$categories_result = $conn->query($categories_query);


$featured_offers_query = "SELECT id_producto, nombre, descripcion FROM productos WHERE destacado = 1 LIMIT 3";
$featured_offers_result = $conn->query($featured_offers_query);


$products_query = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.imagen, p.marca 
                  FROM productos p 
                  ORDER BY p.fecha_creacion DESC 
                  LIMIT 6";
$products_result = $conn->query($products_query);

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
    <title>Leguizamón Grifería</title>
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
    <section class="featured-offers">
    <h2 id="ofertas">Ofertas Destacadas</h2>
    <div class="offers-grid">
        <?php while($offer = $featured_offers_result->fetch_assoc()): ?>
            <a href="producto.php?id=<?php echo $offer['id_producto']; ?>" class="offer-card">
                <div class="offer-image">
                    <img src="<?php echo ($offer['imagen'] ?? 'img/placeholder.svg'); ?>"
                         alt="<?php echo ($offer['nombre']); ?>"
                         class="offer-img">
                </div>
                <div class="offer-info">
                    <h3><?php echo htmlspecialchars($offer['nombre']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($offer['descripcion'], 0, 100)) . '...'; ?></p>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
</section>

        <div class="catalog-container">
            <aside class="filters">
                <h2>Filtros</h2>
                <div class="filter-group">
                <form id="filtroForm">
    <h3>Categorías</h3>
    <label><input type="checkbox" name="categoria[]" value="1"> Grifos de baño</label>
    <label><input type="checkbox" name="categoria[]" value="2"> Grifos de cocina</label>
    <label><input type="checkbox" name="categoria[]" value="3"> Llaves mezcladoras</label>
    <label><input type="checkbox" name="categoria[]" value="4"> Llaves monomando</label>
    <label><input type="checkbox" name="categoria[]" value="5"> Llaves de paso</label>
    <label><input type="checkbox" name="categoria[]" value="6"> Duchas</label>
    <label><input type="checkbox" name="categoria[]" value="7"> Accesorios de grifería</label>

    <h3>Rango de Precio Máximo</h3>
    <input type="range" id="precio_max" name="precio_max" min="4000" max="25000" step="1000" value="25000" oninput="document.getElementById('precioMaxLabel').innerText = '$' + this.value;">
    <span id="precioMaxLabel">$25000</span>

    <button type="button" onclick="aplicarFiltros()">Aplicar Filtros</button>
    <button type="button" onclick="resetearFiltros()">Restablecer Filtros</button>
</form>
            </aside>
            <section class="catalog">
            <div class="products-grid" id="productsGrid">
    <?php while($product = $products_result->fetch_assoc()): ?>
        
        <div class="product-card">
        <a href="producto.php?id=<?php echo $product['id_producto']; ?>">
            <div class="product-image">
                <?php 
                echo "<!-- Imagen: " . htmlspecialchars($product['imagen']) . " -->"; 
                ?>
                <img src="<?php echo ($product['imagen'] ?? 'img/placeholder.svg'); ?>"
                     alt="<?php echo htmlspecialchars($product['nombre']); ?>"
                     class="product-img">
            </div>
            <div class="product-info">
                <h3 class="product-title"><?php echo htmlspecialchars($product['nombre']); ?></h3>
                <p class="product-brand"><?php echo htmlspecialchars($product['marca']); ?></p>
                <p class="product-description"><?php echo htmlspecialchars(substr($product['descripcion'], 0, 100)) . '...'; ?></p>
                <p class="product-price">$<?php echo number_format($product['precio'], 2); ?></p>
                </a>
                <button class="add-to-cart" data-product-id="<?php echo $product['id_producto']; ?>">
                    Agregar al carrito
                </button>
            </div>
   
        </div>
    <?php endwhile; ?>
</div>
<script>
// Función para aplicar filtros
function aplicarFiltros() {
    const form = document.getElementById('filtroForm');
    const formData = new FormData(form);

    // Realizar una solicitud AJAX a filtros.php
    fetch('filtros.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('productsGrid').innerHTML = data;
    })
    .catch(error => console.error('Error:', error));
}

// Función para restablecer filtros
function resetearFiltros() {
    document.getElementById('filtroForm').reset();
    document.getElementById('precioMaxLabel').innerText = '$150000';

    // Restablecer el valor de precio máximo del control deslizante
    document.getElementById('precio_max').value = 150000;

    // Llamar a aplicarFiltros sin ningún parámetro para mostrar productos en orden predeterminado
    aplicarFiltros();
}

// Escuchar cambios en los filtros y aplicar automáticamente
document.querySelectorAll('#filtroForm input').forEach(input => {
    input.addEventListener('change', aplicarFiltros);
});
</script>
</section>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Grifería. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>