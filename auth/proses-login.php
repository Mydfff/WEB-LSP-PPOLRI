<?php

session_start();

require_once "../config/database.php";


// =====================================================
// CEK METHOD REQUEST
// =====================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: login.php");
    exit;

}


// =====================================================
// AMBIL DATA DARI FORM LOGIN
// =====================================================

$username = trim($_POST["username"] ?? "");

$password = $_POST["password"] ?? "";


// =====================================================
// VALIDASI INPUT
// =====================================================

if ($username === "" || $password === "") {

    $_SESSION["login_error"] =
        "Username dan password wajib diisi.";

    header("Location: login.php");
    exit;

}


// =====================================================
// CARI ADMIN BERDASARKAN USERNAME
// =====================================================

try {

    $stmt = $pdo->prepare("
        SELECT
            id,
            nama_lengkap,
            username,
            email,
            password,
            foto,
            role,
            status
        FROM admin
        WHERE username = ?
        LIMIT 1
    ");

    $stmt->execute([
        $username
    ]);

    $admin = $stmt->fetch(PDO::FETCH_ASSOC);


} catch (PDOException $e) {

    $_SESSION["login_error"] =
        "Terjadi kesalahan saat menghubungkan ke database.";

    header("Location: login.php");
    exit;

}


// =====================================================
// ADMIN TIDAK DITEMUKAN
// =====================================================

if (!$admin) {

    $_SESSION["login_error"] =
        "Username atau password salah.";

    header("Location: login.php");
    exit;

}


// =====================================================
// CEK STATUS ADMIN
// =====================================================

if (strtolower($admin["status"]) !== "aktif") {

    $_SESSION["login_error"] =
        "Akun administrator sedang tidak aktif.";

    header("Location: login.php");
    exit;

}


// =====================================================
// CEK PASSWORD
// =====================================================

if (!password_verify(
    $password,
    $admin["password"]
)) {

    $_SESSION["login_error"] =
        "Username atau password salah.";

    header("Location: login.php");
    exit;

}


// =====================================================
// LOGIN BERHASIL
// =====================================================

// Regenerasi session ID untuk keamanan
session_regenerate_id(true);


// =====================================================
// SIMPAN DATA ADMIN KE SESSION
// =====================================================

$_SESSION["admin_id"] =
    $admin["id"];

$_SESSION["admin_nama"] =
    $admin["nama_lengkap"];

$_SESSION["admin_username"] =
    $admin["username"];

$_SESSION["admin_email"] =
    $admin["email"];

$_SESSION["admin_foto"] =
    $admin["foto"];

$_SESSION["admin_role"] =
    $admin["role"];


// =====================================================
// UPDATE LAST LOGIN
// =====================================================

try {

    $update = $pdo->prepare("
        UPDATE admin
        SET last_login = NOW()
        WHERE id = ?
    ");

    $update->execute([
        $admin["id"]
    ]);

} catch (PDOException $e) {

    // Tidak menggagalkan login
    // jika update last_login gagal.

}


// =====================================================
// REDIRECT KE DASHBOARD
// =====================================================

header(
    "Location: ../admin/dashboard.php"
);

exit;