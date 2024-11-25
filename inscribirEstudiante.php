<?php
include 'conexion_db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit();
}

$errores = [];

if (isset($_POST["legajo"])) {
    $nombre = trim($_POST["nombre"]);
    $legajo = $_POST["legajo"];
    $mail = $_POST["mail"];
    $charla = $_POST["charla"];

    // Validaciones
    if (preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+\s[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/', $nombre) === 0 || strlen($nombre) > 60) {
        $errores['nombre'] = "El nombre debe contener exactamente dos palabras y hasta 60 caracteres.";
    }

    if ($legajo < 1 || $legajo > 100000) {
        $errores['legajo'] = "El Legajo debe estar entre 1 y 100.000.";
    }

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errores['mail'] = "El correo electrónico no es válido.";
    }

    if (empty($charla)) {
        $errores['charla'] = "El campo charla es obligatorio.";
    }
}

if (empty($errores)) {
    try {
        // Comprobar si el estudiante ya existe por legajo
        $query = $conn->prepare("SELECT id FROM estudiantes WHERE legajo = :legajo");
        $query->bindParam(':legajo', $legajo, PDO::PARAM_INT);
        $query->execute();
        $estudiante = $query->fetch(PDO::FETCH_ASSOC);

        // Si el estudiante no existe, insertarlo      
        if (!$estudiante) {
            $insertEstudiante = $conn->prepare("INSERT INTO estudiantes (legajo, nombre_completo, mail) VALUES (:legajo, :nombre_completo, :mail)");
            $insertEstudiante->bindParam(':legajo', $legajo, PDO::PARAM_INT);
            $insertEstudiante->bindParam(':nombre_completo', $nombre, PDO::PARAM_STR);
            $insertEstudiante->bindParam(':mail', $mail, PDO::PARAM_STR);
            $insertEstudiante->execute();
            $idEstudiante = $conn->lastInsertId();
        } else {
            $idEstudiante = $estudiante['id'];
        }

        // Verificar que la charla seleccionada aún no ha ocurrido
        $queryCharla = $conn->prepare("SELECT id FROM charlas WHERE id = :charla AND fecha > NOW()");
        $queryCharla->bindParam(':charla', $charla, PDO::PARAM_INT);
        $queryCharla->execute();
        $charlaExistente = $queryCharla->fetch(PDO::FETCH_ASSOC);

        if (!$charlaExistente) {
            $errores['charla'] = "La charla seleccionada ya ha ocurrido o no existe.";
        }

        // Si no hay errores, proceder con la inscripción
        if (empty($errores)) {
            // Insertar en la tabla de inscripciones
            $insertInscripcion = $conn->prepare("INSERT INTO inscripciones (id_charla, id_estudiante, fecha_inscripcion) VALUES (:id_charla, :id_estudiante, NOW())");
            $insertInscripcion->bindParam(':id_charla', $charla, PDO::PARAM_INT);
            $insertInscripcion->bindParam(':id_estudiante', $idEstudiante, PDO::PARAM_INT);
            $insertInscripcion->execute();

            echo json_encode(['status' => 'success', 'message' => 'Inscripción realizada con éxito.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $errores]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
    }
} else {
    // Enviar los errores de validación
    echo json_encode(['status' => 'error', 'message' => $errores]);
}
?>
