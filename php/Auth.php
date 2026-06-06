<?php
// php/Auth.php — Endpoint de autenticación
// Migrado de mysqli → PDO (PostgreSQL / Supabase)

session_start();
header("Content-Type: application/json");

require_once __DIR__ . "/../conexion.php";   // $pdo disponible

// Solo acepta POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

// Leer JSON del body
$body   = json_decode(file_get_contents("php://input"), true);
$codigo = trim($body["codigo"]   ?? "");
$cedula = trim($body["password"] ?? "");

if ($codigo === "" || $cedula === "") {
    echo json_encode(["ok" => false, "msg" => "Completa todos los campos"]);
    exit;
}

// Buscar usuario con su rol
$sql = "
    SELECT
        u.id,
        u.codigo,
        u.nombres,
        u.apellidos,
        u.correo,
        u.telefono,
        u.cedula,
        r.nombre AS rol
    FROM users u
    JOIN roles r ON u.id_rol = r.id
    WHERE u.codigo = :codigo AND u.activo = TRUE
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([":codigo" => $codigo]);
$user = $stmt->fetch();

// Validar existencia y contraseña (cédula en texto plano)
if (!$user || $user["cedula"] !== $cedula) {
    echo json_encode(["ok" => false, "msg" => "Código o contraseña incorrectos"]);
    exit;
}

// Guardar sesión
$_SESSION["user"] = [
    "id"        => $user["id"],
    "codigo"    => $user["codigo"],
    "nombres"   => $user["nombres"],
    "apellidos" => $user["apellidos"],
    "correo"    => $user["correo"],
    "telefono"  => $user["telefono"],
    "rol"       => $user["rol"],
];

echo json_encode([
    "ok"  => true,
    "rol" => $user["rol"],
    "msg" => "Bienvenido, " . $user["nombres"]
]);
