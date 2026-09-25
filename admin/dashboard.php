
<?php

// ==========================================================
// DASHBOARD ADMIN - LSP PPPOLRI
// File: admin/dashboard.php
// ==========================================================

session_start();


// ==========================================================
// CEK LOGIN
// ==========================================================

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit;
}


// ==========================================================
// KONEKSI DATABASE
// ==========================================================

require_once "../config/database.php";


// ==========================================================
// DATA ADMIN
// ==========================================================

$adminNama = $_SESSION["admin_nama"] ?? "Administrator";
$adminRole = $_SESSION["admin_role"] ?? "Administrator";


// ==========================================================
// PAGE INFORMATION
// ==========================================================

$pageTitle = "Dashboard";
$pageSubtitle = "Panel Administrasi LSP PPPOLRI";


// ==========================================================
// TOTAL BERITA
// ==========================================================

$totalBerita = 0;

try {

    $stmtBerita = $pdo->query("
        SELECT COUNT(*)
        FROM berita
    ");

    $totalBerita = (int) $stmtBerita->fetchColumn();

} catch (PDOException $e) {

    $totalBerita = 0;

}


// ==========================================================
// TOTAL GALERI
// ==========================================================
// Tabel galeri belum tersedia di database.
// Untuk sementara bernilai 0.
// Nanti tinggal disambungkan jika tabel galeri sudah dibuat.
// ==========================================================

$totalGaleri = 0;


// ==========================================================
// TOTAL SKEMA
// ==========================================================

$totalSkema = 0;

try {

    $stmtSkema = $pdo->query("
        SELECT COUNT(*)
        FROM skema
        WHERE status = 'aktif'
    ");

    $totalSkema = (int) $stmtSkema->fetchColumn();

} catch (PDOException $e) {

    $totalSkema = 0;

}


// ==========================================================
// TOTAL PESERTA
// ==========================================================

$totalPeserta = 0;

try {

    $stmtPeserta = $pdo->query("
        SELECT COUNT(*)
        FROM akun_peserta
    ");

    $totalPeserta = (int) $stmtPeserta->fetchColumn();

} catch (PDOException $e) {

    $totalPeserta = 0;

}


// ==========================================================
// BERITA TERBARU
// ==========================================================

$beritaTerbaru = [];

try {

    $stmtBeritaTerbaru = $pdo->query("
        SELECT
            id,
            judul,
            status,
            created_at
        FROM berita
        ORDER BY created_at DESC, id DESC
        LIMIT 3
    ");

    $beritaTerbaru =
        $stmtBeritaTerbaru->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $beritaTerbaru = [];

}


// ==========================================================
// PENDAFTARAN TERBARU
// ==========================================================

$pendaftaranTerbaru = [];

try {

    $stmtPendaftaranTerbaru = $pdo->query("
        SELECT
            p.id,
            p.nama_lengkap,
            p.nomor_pendaftaran,
            p.created_at,
            s.nama_skema
        FROM pendaftaran p
        LEFT JOIN skema s
            ON s.id = p.skema_id
        ORDER BY p.created_at DESC, p.id DESC
        LIMIT 3
    ");

    $pendaftaranTerbaru =
        $stmtPendaftaranTerbaru->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $pendaftaranTerbaru = [];

}


// ==========================================================
// FORMAT TANGGAL
// ==========================================================

function formatTanggalDashboard($tanggal)
{
    if (empty($tanggal)) {
        return "-";
    }

    $timestamp = strtotime($tanggal);

    if ($timestamp === false) {
        return "-";
    }

    return date("d M Y", $timestamp);
}


// ==========================================================
// AKTIVITAS TERBARU
// ==========================================================

$aktivitasTerbaru = [];


// ----------------------------------------------------------
// AKTIVITAS PENDAFTARAN
// ----------------------------------------------------------

foreach ($pendaftaranTerbaru as $pendaftaran) {

    $namaPeserta =
        $pendaftaran["nama_lengkap"]
        ?? "Peserta";

    $namaSkema =
        $pendaftaran["nama_skema"]
        ?? "Skema belum dipilih";


    $aktivitasTerbaru[] = [

        "icon" => "bi-person-plus",

        "judul" => "Peserta baru mendaftar",

        "deskripsi" =>
            $namaPeserta
            . " - "
            . $namaSkema,

        "waktu" =>
            formatTanggalDashboard(
                $pendaftaran["created_at"]
            )
    ];
}


// ----------------------------------------------------------
// JIKA BELUM ADA PENDAFTARAN
// ----------------------------------------------------------

if (empty($aktivitasTerbaru)) {

    $aktivitasTerbaru[] = [

        "icon" => "bi-info-circle",

        "judul" => "Belum ada aktivitas",

        "deskripsi" =>
            "Belum terdapat pendaftaran terbaru.",

        "waktu" => "-"
    ];
}

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Dashboard Admin | LSP PPPOLRI
    </title>


    <!-- =====================================================
         BOOTSTRAP
    ====================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- =====================================================
         BOOTSTRAP ICONS
    ====================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >


    <!-- =====================================================
         GOOGLE FONT
    ====================================================== -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <!-- =====================================================
         ADMIN CSS
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >

</head>


<body>


<div class="admin-wrapper">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <?php require_once "components/sidebar.php"; ?>


    <!-- =====================================================
         MAIN AREA
    ====================================================== -->

    <div class="admin-main">


        <!-- =================================================
             HEADER
        ================================================== -->

        <?php require_once "components/header.php"; ?>


        <!-- =================================================
             DASHBOARD CONTENT
        ================================================== -->

        <main class="dashboard-content">


            <!-- =================================================
                 WELCOME
            ================================================== -->

            <section class="dashboard-welcome">

                <span class="dashboard-label">
                    ADMIN PANEL
                </span>

                <h1>

                    Selamat Datang,

                    <?= htmlspecialchars(
                        $adminNama,
                        ENT_QUOTES,
                        "UTF-8"
                    ); ?>

                </h1>

                <p>

                    Kelola informasi dan konten website
                    LSP PPPOLRI melalui dashboard administrasi.

                </p>

            </section>


            <!-- =================================================
                 STATISTICS
            ================================================== -->

            <section class="dashboard-statistics">


                <!-- =================================================
                     TOTAL BERITA
                ================================================== -->

                <div class="stat-card">

                    <div class="stat-content">

                        <span class="stat-label">
                            Total Berita
                        </span>

                        <strong class="stat-number">

                            <?= $totalBerita; ?>

                        </strong>

                        <span class="stat-description">
                            Konten berita
                        </span>

                    </div>


                    <div class="stat-icon stat-icon-news">

                        <i class="bi bi-newspaper"></i>

                    </div>

                </div>


                <!-- =================================================
                     TOTAL GALERI
                ================================================== -->

                <div class="stat-card">

                    <div class="stat-content">

                        <span class="stat-label">
                            Total Galeri
                        </span>

                        <strong class="stat-number">

                            <?= $totalGaleri; ?>

                        </strong>

                        <span class="stat-description">
                            Dokumentasi
                        </span>

                    </div>


                    <div class="stat-icon stat-icon-gallery">

                        <i class="bi bi-images"></i>

                    </div>

                </div>


                <!-- =================================================
                     TOTAL SKEMA
                ================================================== -->

                <div class="stat-card">

                    <div class="stat-content">

                        <span class="stat-label">
                            Total Skema
                        </span>

                        <strong class="stat-number">

                            <?= $totalSkema; ?>

                        </strong>

                        <span class="stat-description">
                            Skema sertifikasi aktif
                        </span>

                    </div>


                    <div class="stat-icon stat-icon-scheme">

                        <i class="bi bi-award"></i>

                    </div>

                </div>


                <!-- =================================================
                     TOTAL PESERTA
                ================================================== -->

                <div class="stat-card">

                    <div class="stat-content">

                        <span class="stat-label">
                            Total Peserta
                        </span>

                        <strong class="stat-number">

                            <?= $totalPeserta; ?>

                        </strong>

                        <span class="stat-description">
                            Peserta terdaftar
                        </span>

                    </div>


                    <div class="stat-icon stat-icon-users">

                        <i class="bi bi-people"></i>

                    </div>

                </div>


            </section>


            <!-- =================================================
                 DASHBOARD GRID
            ================================================== -->

            <section class="dashboard-grid">


                <!-- =================================================
                     BERITA TERBARU
                ================================================== -->

                <div class="dashboard-card news-admin-card">


                    <div class="dashboard-card-header">

                        <div>

                            <span class="card-label">
                                CONTENT
                            </span>

                            <h3>
                                Berita Terbaru
                            </h3>

                        </div>


                        <a
                            href="berita/daftar.php"
                            class="card-action"
                        >

                            Kelola Berita

                            <i class="bi bi-arrow-right"></i>

                        </a>

                    </div>


                    <div class="news-admin-list">


                        <?php if (!empty($beritaTerbaru)): ?>


                            <?php foreach ($beritaTerbaru as $berita): ?>


                                <div class="news-admin-item">


                                    <div class="news-admin-icon">

                                        <i class="bi bi-newspaper"></i>

                                    </div>


                                    <div class="news-admin-info">


                                        <h4>

                                            <?= htmlspecialchars(
                                                $berita["judul"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ); ?>

                                        </h4>


                                        <span>

                                            <i class="bi bi-calendar3"></i>

                                            <?= formatTanggalDashboard(
                                                $berita["created_at"]
                                            ); ?>

                                        </span>


                                    </div>


                                    <span
                                        class="status-badge
                                        <?= strtolower(
                                            htmlspecialchars(
                                                $berita["status"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            )
                                        ); ?>"
                                    >

                                        <?= htmlspecialchars(
                                            ucfirst(
                                                $berita["status"]
                                            ),
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ); ?>

                                    </span>


                                </div>


                            <?php endforeach; ?>


                        <?php else: ?>


                            <div class="news-admin-item">


                                <div class="news-admin-icon">

                                    <i class="bi bi-newspaper"></i>

                                </div>


                                <div class="news-admin-info">

                                    <h4>
                                        Belum ada berita
                                    </h4>

                                    <span>
                                        Belum terdapat data berita.
                                    </span>

                                </div>


                            </div>


                        <?php endif; ?>


                    </div>

                </div>


                <!-- =================================================
                     AKTIVITAS TERBARU
                ================================================== -->

                <div class="dashboard-card activity-card">


                    <div class="dashboard-card-header">

                        <div>

                            <span class="card-label">
                                SYSTEM
                            </span>

                            <h3>
                                Aktivitas Terbaru
                            </h3>

                        </div>

                    </div>


                    <div class="activity-list">


                        <?php foreach ($aktivitasTerbaru as $aktivitas): ?>


                            <div class="activity-item">


                                <div class="activity-icon">

                                    <i
                                        class="bi <?= htmlspecialchars(
                                            $aktivitas["icon"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ); ?>"
                                    ></i>

                                </div>


                                <div class="activity-content">


                                    <strong>

                                        <?= htmlspecialchars(
                                            $aktivitas["judul"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ); ?>

                                    </strong>


                                    <span>

                                        <?= htmlspecialchars(
                                            $aktivitas["deskripsi"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ); ?>

                                    </span>


                                    <small>

                                        <?= htmlspecialchars(
                                            $aktivitas["waktu"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ); ?>

                                    </small>


                                </div>


                            </div>


                        <?php endforeach; ?>


                    </div>

                </div>


            </section>


            <!-- =================================================
                 QUICK ACCESS
            ================================================== -->

            <section class="dashboard-card quick-access-card">


                <div class="dashboard-card-header">

                    <div>

                        <span class="card-label">
                            QUICK ACCESS
                        </span>

                        <h3>
                            Akses Cepat
                        </h3>

                    </div>

                </div>


                <div class="quick-access-grid">


                    <!-- =================================================
                         BERITA
                    ================================================== -->

                    <a
                        href="berita/daftar.php"
                        class="quick-access-item"
                    >

                        <div class="quick-access-icon">

                            <i class="bi bi-newspaper"></i>

                        </div>


                        <div>

                            <strong>
                                Kelola Berita
                            </strong>

                            <span>
                                Kelola konten berita
                            </span>

                        </div>


                        <i class="bi bi-arrow-right"></i>

                    </a>


                    <!-- =================================================
                         GALERI
                    ================================================== -->

                    <a
                        href="galeri/daftar.php"
                        class="quick-access-item"
                    >

                        <div class="quick-access-icon">

                            <i class="bi bi-images"></i>

                        </div>


                        <div>

                            <strong>
                                Kelola Galeri
                            </strong>

                            <span>
                                Kelola dokumentasi
                            </span>

                        </div>


                        <i class="bi bi-arrow-right"></i>

                    </a>


                    <!-- =================================================
                         SKEMA
                    ================================================== -->

                    <a
                        href="skema/daftar.php"
                        class="quick-access-item"
                    >

                        <div class="quick-access-icon">

                            <i class="bi bi-award"></i>

                        </div>


                        <div>

                            <strong>
                                Kelola Skema
                            </strong>

                            <span>
                                Kelola skema sertifikasi
                            </span>

                        </div>


                        <i class="bi bi-arrow-right"></i>

                    </a>


                    <!-- =================================================
                         PESERTA
                    ================================================== -->

                    <a
                        href="peserta/daftar.php"
                        class="quick-access-item"
                    >

                        <div class="quick-access-icon">

                            <i class="bi bi-people"></i>

                        </div>


                        <div>

                            <strong>
                                Data Peserta
                            </strong>

                            <span>
                                Lihat peserta terdaftar
                            </span>

                        </div>


                        <i class="bi bi-arrow-right"></i>

                    </a>


                </div>

            </section>


        </main>


        <!-- =====================================================
             FOOTER
        ====================================================== -->

        <?php require_once "components/footer.php"; ?>


    </div>

</div>


<!-- =========================================================
     BOOTSTRAP JS
========================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>


<!-- =========================================================
     ADMIN JS
========================================================== -->

<script
    src="../assets/js/admin.js"
></script>


</body>

</html>