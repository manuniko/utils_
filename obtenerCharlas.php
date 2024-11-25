<?php
include 'conexion_db.php';

try {
    $stmt = $conn->prepare("SELECT id, titulo FROM charlas WHERE fecha > NOW()");
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error al cargar charlas']);
}
