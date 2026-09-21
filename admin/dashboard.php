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
// DATA SEMENTARA
// Nanti diganti query MySQL
// ==========================================================

$totalBerita  = 19;
$totalGaleri  = 24;
$totalSkema   = 42;
$totalPeserta = 125;


// ==========================================================
// DATA BERITA SEMENTARA
// ==========================================================

$beritaTerbaru = [

    [
        'judul'  => 'LSP PPPOLRI Menyelenggarakan Sertifikasi Digital Forensik',
        'tanggal' => '28 Juli 2026',
        'status' => 'Published'
    ],

    [
        'judul'  => 'Pelaksanaan Sertifikasi Bidang Cyber Security',
        'tanggal' => '24 Juli 2026',
        'status' => 'Published'
    ],

    [
        'judul'  => 'Pembukaan Pendaftaran Asesor Kompetensi Tahun 2026',
        'tanggal' => '20 Juli 2026',
        'status' => 'Draft'
    ]

];


// ==========================================================
// AKTIVITAS SEMENTARA
// ==========================================================

$aktivitasTerbaru = [

    [
        'icon' => 'bi-newspaper',
        'judul' => 'Berita baru ditambahkan',
        'deskripsi' => 'Sertifikasi Digital Forensik 2026',
        'waktu' => '10 menit lalu'
    ],

    [
        'icon' => 'bi-images',
        'judul' => 'Galeri diperbarui',
        'deskripsi' => 'Dokumentasi kegiatan asesmen',
        'waktu' => '1 jam lalu'
    ],

    [
        'icon' => 'bi-person-plus',
        'judul' => 'Peserta baru mendaftar',
        'deskripsi' => 'Pendaftaran skema Cyber Security',
        'waktu' => '2 jam lalu'
    ]

];

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


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >


    <!-- Google Font -->
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


    <!-- Admin CSS -->
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
                    <?php echo htmlspecialchars($adminNama); ?>
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


                <!-- Total Berita -->
                <div class="stat-card">

                    <div class="stat-content">

                        <span class="stat-label">
                            Total Berita
                        </span>

                        <strong class="stat-number">
                            <?php echo $totalBerita; ?>
                        </strong>

                        <span class="stat-description">
                            Konten berita
                        </span>

                    </div>

                    <div class="stat-icon stat-icon-news">
                        <i class="bi bi-newspaper"></i>
                    </div>

                </div>


                <!-- Total Galeri -->
                <div class="stat-card">

                    <div class="stat-content">

                        <span class="stat-label">
                            Total Galeri
                        </span>

                        <strong class="stat-number">
                            <?php echo $totalGaleri; ?>
                        </strong>

                        <span class="stat-description">
                            Dokumentasi
                        </span>

                    </div>

                    <div class="stat-icon stat-icon-gallery">
                        <i class="bi bi-images"></i>
                    </div>

                </div>


                <!-- Total Skema -->
                <div class="stat-card">

                    <div class="stat-content">

                        <span class="stat-label">
                            Total Skema
                        </span>

                        <strong class="stat-number">
                            <?php echo $totalSkema; ?>
                        </strong>

                        <span class="stat-description">
                            Skema sertifikasi
                        </span>

                    </div>

                    <div class="stat-icon stat-icon-scheme">
                        <i class="bi bi-award"></i>
                    </div>

                </div>


                <!-- Total Peserta -->
                <div class="stat-card">

                    <div class="stat-content">

                        <span class="stat-label">
                            Total Peserta
                        </span>

                        <strong class="stat-number">
                            <?php echo $totalPeserta; ?>
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

                        <?php foreach ($beritaTerbaru as $berita): ?>

                            <div class="news-admin-item">

                                <div class="news-admin-icon">
                                    <i class="bi bi-newspaper"></i>
                                </div>

                                <div class="news-admin-info">

                                    <h4>
                                        <?php
                                        echo htmlspecialchars(
                                            $berita['judul']
                                        );
                                        ?>
                                    </h4>

                                    <span>

                                        <i class="bi bi-calendar3"></i>

                                        <?php
                                        echo htmlspecialchars(
                                            $berita['tanggal']
                                        );
                                        ?>

                                    </span>

                                </div>

                                <span
                                    class="status-badge
                                    <?php
                                    echo strtolower(
                                        $berita['status']
                                    );
                                    ?>"
                                >
                                    <?php
                                    echo htmlspecialchars(
                                        $berita['status']
                                    );
                                    ?>
                                </span>

                            </div>

                        <?php endforeach; ?>

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
                                        class="bi <?php
                                        echo htmlspecialchars(
                                            $aktivitas['icon']
                                        );
                                        ?>"
                                    ></i>

                                </div>

                                <div class="activity-content">

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $aktivitas['judul']
                                        );
                                        ?>
                                    </strong>

                                    <span>
                                        <?php
                                        echo htmlspecialchars(
                                            $aktivitas['deskripsi']
                                        );
                                        ?>
                                    </span>

                                    <small>
                                        <?php
                                        echo htmlspecialchars(
                                            $aktivitas['waktu']
                                        );
                                        ?>
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


                    <!-- Tambah Berita -->
                    <a
                        href="berita/tambah.php"
                        class="quick-access-item"
                    >

                        <div class="quick-access-icon">
                            <i class="bi bi-plus-lg"></i>
                        </div>

                        <div>

                            <strong>
                                Tambah Berita
                            </strong>

                            <span>
                                Publikasikan berita baru
                            </span>

                        </div>

                        <i class="bi bi-arrow-right"></i>

                    </a>


                    <!-- Galeri -->
                    <a
                        href="galeri/"
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
                                Upload dokumentasi
                            </span>

                        </div>

                        <i class="bi bi-arrow-right"></i>

                    </a>


                    <!-- Skema -->
                    <a
                        href="skema/"
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


                    <!-- Peserta -->
                    <a
                        href="peserta/"
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