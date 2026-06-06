<?php
// php/session_check.php — Incluir al inicio de cualquier página privada
// Uso: require_once __DIR__ . "/../php/session_check.php";

session_start();

if (empty($_SESSION["user"])) {
    header("Location: ../index.php");
    exit;
}

// La variable $user queda disponible en la página que haga el require
$user = $_SESSION["user"];
