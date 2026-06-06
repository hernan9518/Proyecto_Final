<?php
// php/crear_reporte.php — Recibe POST multipart con datos + foto opcional
// Migrado de mysqli → PDO (PostgreSQL / Supabase)

session_start();
header("Content-Type: application/json");

if (empty($_SESSION["user"])) {
    echo json_encode(["ok" => false, "msg" => "No autenticado"]); exit;
}

require_once __DIR__ . "/../conexion.php";   // $pdo disponible

$id_usuario        = (int) $_SESSION["user"]["id"];
$tipo_falla        = trim($_POST["tipo_falla"]        ?? "");
$bloque            = trim($_POST["bloque"]             ?? "");
$ubicacion_detalle = trim($_POST["ubicacion_detalle"]  ?? "");
$descripcion       = trim($_POST["descripcion"]        ?? "");

// Validación básica
$tipos_validos   = ["estructural","electrica","iluminacion","red","sistema","mobiliario","otro"];
$bloques_validos = ["A","B","C"];

if (!in_array($tipo_falla, $tipos_validos) || !in_array($bloque, $bloques_validos)
    || $ubicacion_detalle === "" || $descripcion === "") {
    echo json_encode(["ok" => false, "msg" => "Completa todos los campos obligatorios"]); exit;
}

// Manejo de foto opcional
$foto_path = null;
if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === UPLOAD_ERR_OK) {
    $ext_permitidas = ["jpg","jpeg","png","webp"];
    $info = pathinfo($_FILES["foto"]["name"]);
    $ext  = strtolower($info["extension"] ?? "");

    if (!in_array($ext, $ext_permitidas)) {
        echo json_encode(["ok" => false, "msg" => "Solo se permiten imágenes JPG, PNG o WEBP"]); exit;
    }
    if ($_FILES["foto"]["size"] > 5 * 1024 * 1024) {
        echo json_encode(["ok" => false, "msg" => "La imagen no debe superar 5 MB"]); exit;
    }

    $uploads_dir = __DIR__ . "/../uploads/reportes/";
    if (!is_dir($uploads_dir)) mkdir($uploads_dir, 0755, true);

    $nombre_archivo = uniqid("rep_", true) . "." . $ext;
    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $uploads_dir . $nombre_archivo)) {
        $foto_path = "uploads/reportes/" . $nombre_archivo;
    }
}

// Insertar reporte
$sql = "
    INSERT INTO reportes (id_usuario, tipo_falla, bloque, ubicacion_detalle, descripcion, foto)
    VALUES (:id_usuario, :tipo_falla, :bloque, :ubicacion_detalle, :descripcion, :foto)
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":id_usuario"        => $id_usuario,
        ":tipo_falla"        => $tipo_falla,
        ":bloque"            => $bloque,
        ":ubicacion_detalle" => $ubicacion_detalle,
        ":descripcion"       => $descripcion,
        ":foto"              => $foto_path,
    ]);
    echo json_encode(["ok" => true, "msg" => "Reporte enviado correctamente"]);
} catch (PDOException $e) {
    echo json_encode(["ok" => false, "msg" => "Error al guardar: " . $e->getMessage()]);
}
