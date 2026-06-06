<?php
// conexion.php — Conexión a Supabase via PDO (PostgreSQL)
// Reemplaza el archivo original conexion.php

$host = getenv('DB_HOST') ?: 'aws-0-us-east-1.pooler.supabase.com';
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_NAME') ?: 'postgres';
$user = getenv('DB_USER') ?: '';
$pass = getenv('DB_PASS') ?: '';

$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(["ok" => false, "msg" => "Error de conexión: " . $e->getMessage()]));
}

/*
 * NOTA: Los archivos PHP que usaban mysqli_* deben migrarse a PDO.
 * Ejemplo rápido de migración:
 *
 *   ANTES (mysqli):
 *     $stmt = mysqli_prepare($conexion, $sql);
 *     mysqli_stmt_bind_param($stmt, "s", $valor);
 *     mysqli_stmt_execute($stmt);
 *     $res = mysqli_stmt_get_result($stmt);
 *
 *   DESPUÉS (PDO):
 *     $stmt = $pdo->prepare($sql);
 *     $stmt->execute([$valor]);
 *     $res = $stmt->fetchAll();
 */
