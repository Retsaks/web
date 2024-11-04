<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leguizamon";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function obtenerProductosFiltrados($conn, $categorias = [], $precio_max = 150000) {
    // Iniciar la consulta con un orden predeterminado
    $sql = "SELECT p.* FROM productos p ";
    
    // Verificación de categorías seleccionadas para aplicar filtro
    if (!empty($categorias)) {
        $sql .= "INNER JOIN producto_categorias pc ON p.id_producto = pc.id_producto ";
    }

    $sql .= "WHERE p.precio <= ? ";

    // Aplicar filtro de categorías si existen categorías seleccionadas
    if (!empty($categorias)) {
        $categoria_placeholders = implode(',', array_fill(0, count($categorias), '?'));
        $sql .= "AND pc.id_categoria IN ($categoria_placeholders) ";
    }

    // Aplicar un orden predeterminado por nombre al inicio
    $sql .= "GROUP BY p.id_producto ORDER BY p.nombre ASC";

    // Preparar la consulta y vincular los parámetros
    $stmt = $conn->prepare($sql);

    $params = [$precio_max];

    // Agregar categorías a los parámetros si están seleccionadas
    if (!empty($categorias)) {
        $params = array_merge($params, $categorias);
    }

    $stmt->bind_param(str_repeat('i', count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Capturar los datos del filtro de categorías y precio máximo
$categorias = $_POST['categoria'] ?? [];
$precio_max = $_POST['precio_max'] ?? 150000;

// Obtener los productos filtrados
$productos_filtrados = obtenerProductosFiltrados($conn, $categorias, $precio_max);

// Generar el HTML de productos para devolver en la respuesta AJAX
foreach ($productos_filtrados as $producto): ?>
    <div class="product-card">
        <a href="producto.php?id=<?php echo $producto['id_producto']; ?>">
            <div class="product-image">
                <img src="<?php echo ($producto['imagen'] ?? 'img/placeholder.svg'); ?>"
                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                     class="product-img">
            </div>
            <div class="product-info">
                <h3 class="product-title"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                <p class="product-brand"><?php echo htmlspecialchars($producto['marca']); ?></p>
                <p class="product-description"><?php echo htmlspecialchars(substr($producto['descripcion'], 0, 100)) . '...'; ?></p>
                <p class="product-price">$<?php echo number_format($producto['precio'], 2); ?></p>
            </div>
        </a>
        <button class="add-to-cart" data-product-id="<?php echo $producto['id_producto']; ?>">
            Agregar al carrito
        </button>
    </div>
<?php endforeach; ?>