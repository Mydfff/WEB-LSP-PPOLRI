<?php
// ==========================================================
// HAPUS SKEMA SERTIFIKASI - ADMIN LSP PPPOLRI
// File: admin/skema/hapus.php
// ==========================================================

session_start();

// ==========================================================
// CEK LOGIN
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
// AMBIL ID SKEMA
// ==========================================================
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: daftar.php");
    exit;
}

// ==========================================================
// CEK DATA SKEMA
// ==========================================================
$stmt = $pdo->prepare("
    SELECT id
    FROM skema
    WHERE id = :id
");

$stmt->execute([
    ":id" => $id
]);

$skema = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$skema) {
    header("Location: daftar.php");
    exit;
}

// ==========================================================
// PROSES HAPUS
// ==========================================================
try {

    $stmt = $pdo->prepare("
        DELETE FROM skema
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $id
    ]);

    // Kembali ke daftar
    header("Location: daftar.php?success=deleted");
    exit;

} catch (PDOException $e) {

    // Jika gagal hapus
    header("Location: daftar.php?error=delete");
    exit;
}