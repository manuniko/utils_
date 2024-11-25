<?php
include 'conexion_db.php';

$legajo = $_GET['legajo'] ?? null;
if ($legajo) {
    try {
        $stmt = $conn->prepare("SELECT nombre_completo, mail FROM estudiantes WHERE legajo = :legajo");
        $stmt->bindParam(':legajo', $legajo, PDO::PARAM_INT);
        $stmt->execute();
        $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($estudiante) {
            echo json_encode(['status' => 'success', 'nombre_completo' => $estudiante['nombre_completo'], 'mail' => $estudiante['mail']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Estudiante no encontrado']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error al cargar estudiante']);
    }
}
