<?php
// php/mis_reportes.php — Devuelve los reportes del estudiante autenticado
// Migrado de mysqli → PDO (PostgreSQL / Supabase)

session_start();
header("Content-Type: application/json");

if (empty($_SESSION["user"])) {
    echo json_encode(["ok" => false, "msg" => "No autenticado"]); exit;
}

require_once __DIR__ . "/../conexion.php";   // $pdo disponible

$id_usuario = (int) $_SESSION["user"]["id"];

// En PostgreSQL se usa una subconsulta correlacionada (igual que en MySQL)
$sql = "
    SELECT
        r.id,
        r.tipo_falla,
        r.bloque,
        r.ubicacion_detalle,
        r.descripcion,
        r.foto,
        r.nivel_amenaza,
        r.estado,
        r.creado,
        r.actualizado,
        (
            SELECT h.nota
            FROM historial_estados h
            WHERE h.id_reporte = r.id
            ORDER BY h.fecha DESC
            LIMIT 1
        ) AS ultima_nota
    FROM reportes r
    WHERE r.id_usuario = :id_usuario
    ORDER BY r.creado DESC
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":id_usuario" => $id_usuario]);
    $reportes = $stmt->fetchAll();
    echo json_encode(["ok" => true, "reportes" => $reportes]);
} catch (PDOException $e) {
    echo json_encode(["ok" => false, "msg" => "Error: " . $e->getMessage()]);
}
