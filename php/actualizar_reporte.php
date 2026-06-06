<?php
// php/actualizar_reporte.php — Admin actualiza estado, nivel_amenaza y nota
// Migrado de mysqli → PDO (PostgreSQL / Supabase)

session_start();
header("Content-Type: application/json");

if (empty($_SESSION["user"]) || $_SESSION["user"]["rol"] !== "admin") {
    echo json_encode(["ok" => false, "msg" => "Acceso denegado"]); exit;
}

require_once __DIR__ . "/../conexion.php";   // $pdo disponible

$body          = json_decode(file_get_contents("php://input"), true);
$id_reporte    = (int) ($body["id_reporte"]    ?? 0);
$estado_nuevo  = trim($body["estado_nuevo"]    ?? "");
$nivel_amenaza = trim($body["nivel_amenaza"]   ?? "");
$nota          = trim($body["nota"]            ?? "");
$id_admin      = (int) $_SESSION["user"]["id"];

$estados_validos = ["pendiente","en_proceso","completado"];
$niveles_validos = ["bajo","medio","alto",""];

if (!$id_reporte || !in_array($estado_nuevo, $estados_validos)
    || !in_array($nivel_amenaza, $niveles_validos)) {
    echo json_encode(["ok" => false, "msg" => "Datos inválidos"]); exit;
}

try {
    // Obtener estado actual
    $cur = $pdo->prepare("SELECT estado FROM reportes WHERE id = :id LIMIT 1");
    $cur->execute([":id" => $id_reporte]);
    $row = $cur->fetch();
    if (!$row) {
        echo json_encode(["ok" => false, "msg" => "Reporte no encontrado"]); exit;
    }
    $estado_anterior = $row["estado"];

    // Actualizar reporte — nivel_amenaza es opcional (NULL si vacío)
    $nivel_val = $nivel_amenaza !== "" ? $nivel_amenaza : null;

    $sql_upd = "
        UPDATE reportes
        SET estado = :estado,
            nivel_amenaza = COALESCE(:nivel_amenaza, nivel_amenaza)
        WHERE id = :id
    ";
    // Si nivel vacío se mantiene el valor anterior con COALESCE;
    // si se envía un valor, se sobreescribe.
    $stmt = $pdo->prepare($sql_upd);
    $stmt->execute([
        ":estado"        => $estado_nuevo,
        ":nivel_amenaza" => $nivel_val,
        ":id"            => $id_reporte,
    ]);

    // Insertar en historial
    $nota_val = $nota !== "" ? $nota : null;

    $sql_hist = "
        INSERT INTO historial_estados
            (id_reporte, id_admin, estado_anterior, estado_nuevo, nivel_amenaza, nota)
        VALUES
            (:id_reporte, :id_admin, :estado_anterior, :estado_nuevo, :nivel_amenaza, :nota)
    ";
    $stmt2 = $pdo->prepare($sql_hist);
    $stmt2->execute([
        ":id_reporte"     => $id_reporte,
        ":id_admin"       => $id_admin,
        ":estado_anterior"=> $estado_anterior,
        ":estado_nuevo"   => $estado_nuevo,
        ":nivel_amenaza"  => $nivel_val,
        ":nota"           => $nota_val,
    ]);

    echo json_encode(["ok" => true, "msg" => "Reporte actualizado correctamente"]);

} catch (PDOException $e) {
    echo json_encode(["ok" => false, "msg" => "Error: " . $e->getMessage()]);
}
