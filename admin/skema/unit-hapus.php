<?php
session_start();

// ==========================================================
// CEK LOGIN ADMIN
// ==========================================================
if (!isset($_SESSION["admin_id"])) {
    header("Location: ../../auth/login.php");
    exit;
}

// ==========================================================
// KONEKSI DATABASE
// ==========================================================
require_once "../../config/database.php";

// ==========================================================
// AMBIL ID UNIT
// ==========================================================
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: daftar.php");
    exit;
}

// ==========================================================
// AMBIL DATA UNIT
// ==========================================================
$stmt = $pdo->prepare("
    SELECT id, skema_id
    FROM unit_skema
    WHERE id = :id
    LIMIT 1
");

$stmt->execute([
    ":id" => $id
]);

$unit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$unit) {
    header("Location: daftar.php");
    exit;
}

$skemaId = $unit["skema_id"];

// ==========================================================
// HAPUS UNIT
// ==========================================================
try {

    $stmtDelete = $pdo->prepare("
        DELETE FROM unit_skema
        WHERE id = :id
    ");

    $stmtDelete->execute([
        ":id" => $id
    ]);

    header(
        "Location: detail.php?id=" .
        $skemaId .
        "&success=unit_deleted"
    );
    exit;

} catch (PDOException $e) {

    header(
        "Location: detail.php?id=" .
        $skemaId .
        "&error=unit_delete"
    );
    exit;
}