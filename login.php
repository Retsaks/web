<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    $sql = "SELECT id_usuario, contrasena FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id_usuario, $hash_contrasena);

    if ($stmt->num_rows == 1) {
        $stmt->fetch();
        if (password_verify($contrasena, $hash_contrasena)) {
            $_SESSION['user_id'] = $id_usuario;
            header("Location: index.php");
            exit;
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "No existe una cuenta con este correo.";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <div class="header-content">
        <h2 class="title">Iniciar Sesión</h2>
    </div>
    <form action="login.php" method="post" class="form-container">
        <input type="email" name="email" placeholder="Correo electrónico" required class="form-input">
        <input type="password" name="contrasena" placeholder="Contraseña" required class="form-input">
        <button type="submit" class="login-button">Iniciar Sesión</button>
        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>.</p>
    </form>
</div>
</body>
</html>
