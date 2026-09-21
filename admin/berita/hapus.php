<?php
// ==========================================================
// HAPUS BERITA - LSP PPPOLRI
// File: admin/berita/hapus.php
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
// AMBIL ID
// ==========================================================

$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

// ==========================================================
// VALIDASI ID
// ==========================================================

if (!$id) {

    $_SESSION["error"] =
        "ID berita tidak valid.";

    header("Location: daftar.php");
    exit;

}

// ==========================================================
// AMBIL DATA BERITA
// ==========================================================

try {

    $stmt = $pdo->prepare("
        SELECT
            id,
            gambar
        FROM berita
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);

    $berita = $stmt->fetch();

} catch (PDOException $e) {

    $_SESSION["error"] =
        "Data berita gagal ditemukan.";

    header("Location: daftar.php");
    exit;

}

// ==========================================================
// CEK DATA
// ==========================================================

if (!$berita) {

    $_SESSION["error"] =
        "Berita tidak ditemukan.";

    header("Location: daftar.php");
    exit;

}

// ==========================================================
// HAPUS DATA
// ==========================================================

try {

    // ======================================================
    // MULAI TRANSAKSI
    // ======================================================

    $pdo->beginTransaction();


    // ======================================================
    // HAPUS DATA DARI DATABASE
    // ======================================================

    $stmt = $pdo->prepare("
        DELETE FROM berita
        WHERE id = ?
    ");

    $stmt->execute([$id]);


    // ======================================================
    // COMMIT
    // ======================================================

    $pdo->commit();


    // ======================================================
    // HAPUS FILE GAMBAR
    // ======================================================

    if (!empty($berita["gambar"])) {

        $fileGambar =
            "../../uploads/berita/" .
            $berita["gambar"];


        if (
            file_exists($fileGambar)
        ) {

            unlink($fileGambar);

        }

    }


    // ======================================================
    // PESAN BERHASIL
    // ======================================================

    $_SESSION["success"] =
        "Berita berhasil dihapus.";

} catch (PDOException $e) {

    // ======================================================
    // ROLLBACK JIKA GAGAL
    // ======================================================

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }


    $_SESSION["error"] =
        "Berita gagal dihapus.";

}

// ==========================================================
// KEMBALI KE DAFTAR
// ==========================================================

header("Location: daftar.php");
exit;