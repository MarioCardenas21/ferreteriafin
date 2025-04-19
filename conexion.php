<?php
$servername = "localhost";
$username = "root";
$password = "2820";
$dbname = "ferreteria";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
