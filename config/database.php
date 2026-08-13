<?php

/**
 * Koneksi Database LSP PPPOLRI
 * Database: lsp_ppolri
 */

$host = "localhost";
$dbname = "lsp_ppolri";
$username = "root";
$password = "";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

} catch (PDOException $e) {

    die("Koneksi database gagal: " . $e->getMessage());

}