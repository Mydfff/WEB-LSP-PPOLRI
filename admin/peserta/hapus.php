
<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../../config/database.php";


/* =====================================================
   AMBIL ID PESERTA
===================================================== */

$id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;


if ($id <= 0) {
    header("Location: daftar.php");
    exit;
}


/* =====================================================
   CEK PESERTA
===================================================== */

$stmtCheck = $pdo->prepare("
    SELECT id, email
    FROM akun_peserta
    WHERE id = ?
    LIMIT 1
");

$stmtCheck->execute([$id]);

$peserta = $stmtCheck->fetch(PDO::FETCH_ASSOC);


if (!$peserta) {
    header("Location: daftar.php?error=peserta_not_found");
    exit;
}


/* =====================================================
   HAPUS PESERTA
===================================================== */

try {

    $stmtDelete = $pdo->prepare("
        DELETE FROM akun_peserta
        WHERE id = ?
    ");

    $stmtDelete->execute([$id]);


    if ($stmtDelete->rowCount() > 0) {
        header("Location: daftar.php?success=peserta_deleted");
        exit;
    }


    header("Location: daftar.php?error=delete_failed");
    exit;


} catch (PDOException $e) {

    /*
     * Jika peserta masih mempunyai data yang
     * terhubung dengan tabel lain, database dapat
     * menolak proses DELETE.
     */

    header("Location: daftar.php?error=peserta_masih_terhubung");
    exit;
}
?>