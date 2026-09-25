
<?php

session_start();

/* ==========================================================
   CEK LOGIN
========================================================== */

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit;
}


/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";


/* ==========================================================
   DATA ADMIN YANG SEDANG LOGIN
========================================================== */

$currentAdminId = (int) $_SESSION["admin_id"];


/* ==========================================================
   AMBIL ID ADMIN YANG AKAN DIHAPUS
========================================================== */

$id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;


/* ==========================================================
   VALIDASI ID
========================================================== */

if ($id <= 0) {
    header("Location: admin.php");
    exit;
}


/* ==========================================================
   CEGAH HAPUS AKUN SENDIRI
========================================================== */

if ($id === $currentAdminId) {
    header("Location: admin.php?error=cannot_delete_self");
    exit;
}


/* ==========================================================
   CEK DATA ADMIN
========================================================== */

$stmtCheck = $pdo->prepare("
    SELECT
        id,
        nama_lengkap,
        username
    FROM admin
    WHERE id = ?
    LIMIT 1
");

$stmtCheck->execute([$id]);

$admin = $stmtCheck->fetch(PDO::FETCH_ASSOC);


/* ==========================================================
   JIKA ADMIN TIDAK DITEMUKAN
========================================================== */

if (!$admin) {
    header("Location: admin.php?error=admin_not_found");
    exit;
}


/* ==========================================================
   HAPUS ADMIN
========================================================== */

try {

    $stmtDelete = $pdo->prepare("
        DELETE FROM admin
        WHERE id = ?
    ");

    $stmtDelete->execute([$id]);


/* ==========================================================
   CEK HASIL HAPUS
========================================================== */

    if ($stmtDelete->rowCount() > 0) {
        header("Location: admin.php?success=admin_deleted");
        exit;
    }


/* ==========================================================
   JIKA DATA TIDAK TERHAPUS
========================================================== */

    header("Location: admin.php?error=delete_failed");
    exit;


} catch (PDOException $e) {

    /*
     * Jika admin masih digunakan oleh data lain
     * dan database memiliki foreign key,
     * proses hapus akan masuk ke sini.
     */

    header("Location: admin.php?error=delete_failed");
    exit;
}

?>