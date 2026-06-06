<?php
// php/historial_reporte.php — Timeline de cambios de un reporte
// Migrado de mysqli → PDO (PostgreSQL / Supabase)

session_start();
header("Content-Type: application/json");

if (empty($_SESSION["user"])) {
    echo json_encode(["ok" => false, "msg" => "No autenticado"]); exit;
}

require_once __DIR__ . "/../conexion.php";   // $pdo disponible

$id_reporte = (int) ($_GET["id"] ?? 0);
$id_usuario = (int) $_SESSION["user"]["id"];
$rol        = $_SESSION["user"]["rol"];

if (!$id_reporte) {
    echo json_encode(["ok" => false, "msg" => "ID inválido"]); exit;
}

try {
    // Si es estudiante, verificar que el reporte le pertenece
    if ($rol !== "admin") {
        $check = $pdo->prepare(
            "SELECT id FROM reportes WHERE id = :id AND id_usuario = :uid LIMIT 1"
        );
        $check->execute([":id" => $id_reporte, ":uid" => $id_usuario]);
        if (!$check->fetch()) {
            echo json_encode(["ok" => false, "msg" => "Sin permisos"]); exit;
        }
    }

    $sql = "
        SELECT
            h.estado_anterior,
            h.estado_nuevo,
            h.nivel_amenaza,
            h.nota,
            h.fecha,
            u.nombres || ' ' || u.apellidos AS admin_nombre
        FROM historial_estados h
        JOIN users u ON u.id = h.id_admin
        WHERE h.id_reporte = :id_reporte
        ORDER BY h.fecha ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([":id_reporte" => $id_reporte]);
    $historial = $stmt->fetchAll();

    echo json_encode(["ok" => true, "historial" => $historial]);

} catch (PDOException $e) {
    echo json_encode(["ok" => false, "msg" => "Error: " . $e->getMessage()]);
}
