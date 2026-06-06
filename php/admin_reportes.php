<?php
// php/admin_reportes.php — Lista reportes para el admin con filtros opcionales
// Migrado de mysqli → PDO (PostgreSQL / Supabase)
// Nota: MySQL FIELD() no existe en PostgreSQL → se reemplaza por CASE WHEN

session_start();
header("Content-Type: application/json");

if (empty($_SESSION["user"]) || $_SESSION["user"]["rol"] !== "admin") {
    echo json_encode(["ok" => false, "msg" => "Acceso denegado"]); exit;
}

require_once __DIR__ . "/../conexion.php";   // $pdo disponible

$estado          = trim($_GET["estado"] ?? "");
$estados_validos = ["pendiente","en_proceso","completado",""];

if (!in_array($estado, $estados_validos)) {
    echo json_encode(["ok" => false, "msg" => "Estado inválido"]); exit;
}

// PostgreSQL no tiene FIELD(): se usa CASE WHEN para el mismo orden personalizado
$where  = $estado !== "" ? "WHERE r.estado = :estado" : "";
$params = $estado !== "" ? [":estado" => $estado] : [];

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
        u.nombres,
        u.apellidos,
        u.codigo,
        u.correo,
        u.telefono,
        (
            SELECT h.nota
            FROM historial_estados h
            WHERE h.id_reporte = r.id
            ORDER BY h.fecha DESC
            LIMIT 1
        ) AS ultima_nota
    FROM reportes r
    JOIN users u ON u.id = r.id_usuario
    $where
    ORDER BY
        CASE r.estado
            WHEN 'pendiente'   THEN 1
            WHEN 'en_proceso'  THEN 2
            WHEN 'completado'  THEN 3
            ELSE 4
        END,
        CASE r.nivel_amenaza
            WHEN 'alto'  THEN 1
            WHEN 'medio' THEN 2
            WHEN 'bajo'  THEN 3
            ELSE 4
        END,
        r.creado DESC
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $reportes = $stmt->fetchAll();
    echo json_encode(["ok" => true, "reportes" => $reportes]);
} catch (PDOException $e) {
    echo json_encode(["ok" => false, "msg" => "Error: " . $e->getMessage()]);
}
