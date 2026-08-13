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

    header("Location: ../auth/login.php ");
    exit;

}

// ==========================================================
// KONEKSI DATABASE
// ==========================================================

require_once "../config/database.php";

// ==========================================================
// DATA ADMIN DARI SESSION
// ==========================================================

$adminNama = $_SESSION["admin_nama"] ?? "Administrator";
$adminRole = $_SESSION["admin_role"] ?? "admin";

// ==========================================================
// DATA SEMENTARA
// Nanti diganti query MySQL
// ==========================================================
$totalBerita  = 19;
$totalGaleri  = 24;
$totalSkema   = 42;
$totalPeserta = 125;

// Data berita sementara
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

// Aktivitas sementara
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


// ----------------------------------------------------------
// DATA ADMIN
// Nanti dapat diambil dari session login
// ----------------------------------------------------------

$adminNama = 'Administrator';
$adminRole = 'Administrator';

?>


<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard Admin | LSP PPPOLRI</title>


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

    <link rel="preconnect" href="https://fonts.googleapis.com">

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
         
         Karena dashboard.php berada di folder:
         
         WEB-LSP-PPPOLRI/
         ├── admin/
         │   └── dashboard.php
         │
         └── assets/
             └── css/
                 └── admin.css
         
         maka path yang benar:
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >

</head>


<body>


<!-- =========================================================
     ADMIN WRAPPER
========================================================== -->

<div class="admin-wrapper">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside
        class="admin-sidebar"
        id="adminSidebar"
    >


        <!-- =================================================
             SIDEBAR BRAND
        ================================================== -->

        <div class="sidebar-brand">

            <div class="brand-logo">
                <i class="bi bi-shield-check"></i>
            </div>

            <div class="brand-text">
                <strong>LSP PPPOLRI</strong>
                <span>Admin Panel</span>
            </div>

        </div>


        <!-- =================================================
             SIDEBAR NAVIGATION
        ================================================== -->

        <nav class="sidebar-nav">


            <!-- ===============================
                 MENU UTAMA
            ================================ -->

            <div class="nav-section">

                <span class="nav-section-title">
                    MENU UTAMA
                </span>


                <!-- Dashboard -->

                <a
                    href="dashboard.php"
                    class="sidebar-link active"
                >
                    <i class="bi bi-grid-fill"></i>

                    <span>
                        Dashboard
                    </span>
                </a>


                <!-- Berita -->

                <a
                    href="berita.php"
                    class="sidebar-link"
                >
                    <i class="bi bi-newspaper"></i>

                    <span>
                        Berita
                    </span>
                </a>


                <!-- Galeri -->

                <a
                    href="galeri.php"
                    class="sidebar-link"
                >
                    <i class="bi bi-images"></i>

                    <span>
                        Galeri
                    </span>
                </a>


                <!-- Skema -->

                <a
                    href="skema.php"
                    class="sidebar-link"
                >
                    <i class="bi bi-award"></i>

                    <span>
                        Skema Sertifikasi
                    </span>
                </a>


                <!-- FAQ -->

                <a
                    href="faq.php"
                    class="sidebar-link"
                >
                    <i class="bi bi-question-circle"></i>

                    <span>
                        FAQ
                    </span>
                </a>


                <!-- Peserta -->

                <a
                    href="peserta.php"
                    class="sidebar-link"
                >
                    <i class="bi bi-people"></i>

                    <span>
                        Data Peserta
                    </span>
                </a>

            </div>


            <!-- ===============================
                 MANAGEMENT
            ================================ -->

            <div class="nav-section">

                <span class="nav-section-title">
                    MANAGEMENT
                </span>


                <!-- Kelola Admin -->

                <a
                    href="admin.php"
                    class="sidebar-link"
                >
                    <i class="bi bi-person-gear"></i>

                    <span>
                        Kelola Admin
                    </span>
                </a>


                <!-- Pengaturan -->

                <a
                    href="#"
                    class="sidebar-link"
                >
                    <i class="bi bi-gear"></i>

                    <span>
                        Pengaturan
                    </span>
                </a>

            </div>

        </nav>


        <!-- =================================================
             SIDEBAR FOOTER
        ================================================== -->

        <div class="sidebar-footer">

            <a
                href="../auth/logout.php"
                class="sidebar-link"
            >

                <i class="bi bi-box-arrow-right"></i>

                <span>
                    Logout
                </span>

            </a>

        </div>

    </aside>


    <!-- =====================================================
         OVERLAY MOBILE
         
         Akan muncul ketika sidebar dibuka di mobile.
    ====================================================== -->

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>



    <!-- =====================================================
         MAIN AREA
    ====================================================== -->

    <div class="admin-main">


        <!-- =================================================
             TOPBAR
        ================================================== -->

        <header class="admin-topbar">


            <!-- =============================================
                 HAMBURGER BUTTON
                 
                 INI YANG SEBELUMNYA BELUM ADA.
                 JS akan membaca class .sidebar-toggle
            ============================================== -->

            <button
                type="button"
                class="sidebar-toggle"
                id="sidebarToggle"
                aria-label="Buka menu"
                aria-expanded="false"
            >

                <i class="bi bi-list"></i>

            </button>


            <!-- =============================================
                 TOPBAR TITLE
            ============================================== -->

            <div class="topbar-title">

                <h2>
                    Dashboard
                </h2>

                <span>
                    Panel Administrasi LSP PPPOLRI
                </span>

            </div>


            <!-- =============================================
                 TOPBAR RIGHT
            ============================================== -->

            <div class="topbar-actions">


                <!-- Notification -->

                <button
                    type="button"
                    class="notification-btn"
                    aria-label="Notifikasi"
                >

                    <i class="bi bi-bell"></i>

                    <span class="notification-badge">
                        3
                    </span>

                </button>


                <!-- Admin Profile -->

                                <a
                    href="profile.php"
                    class="admin-profile"
                    title="Profil Admin"
                >

                    <div class="admin-avatar">

                        <i class="bi bi-person-fill"></i>

                    </div>


                    <div class="admin-profile-info">

                        <strong>
                            <?php echo htmlspecialchars($adminNama); ?>
                        </strong>

                        <span>
                            <?php echo htmlspecialchars($adminRole); ?>
                        </span>

                    </div>


                    <i class="bi bi-chevron-down profile-arrow"></i>

                </a>

            </div>

        </header>



        <!-- =================================================
             DASHBOARD CONTENT
        ================================================== -->

        <main class="dashboard-content">


            <!-- =============================================
                 WELCOME
            ============================================== -->

            <section class="dashboard-welcome">

                <span class="dashboard-label">
                    ADMIN PANEL
                </span>

                <h1>
                    Selamat Datang, <?php echo htmlspecialchars($adminNama); ?>
                </h1>

                <p>
                    Kelola informasi dan konten website
                    LSP PPPOLRI melalui dashboard administrasi.
                </p>

            </section>



            <!-- =============================================
                 STATISTICS
            ============================================== -->

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


                <!-- =============================================
                     BERITA TERBARU
                ============================================== -->

                <div class="dashboard-card news-admin-card">


                    <!-- Card Header -->

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
                            href="berita.php"
                            class="card-action"
                        >

                            Kelola Berita

                            <i class="bi bi-arrow-right"></i>

                        </a>

                    </div>


                    <!-- News List -->

                    <div class="news-admin-list">


                        <?php foreach ($beritaTerbaru as $berita): ?>

                            <div class="news-admin-item">


                                <!-- Icon -->

                                <div class="news-admin-icon">

                                    <i class="bi bi-newspaper"></i>

                                </div>


                                <!-- Information -->

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


                                <!-- Status -->

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



                <!-- =============================================
                     AKTIVITAS TERBARU
                ============================================== -->

                <div class="dashboard-card activity-card">


                    <!-- Header -->

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


                    <!-- Activity List -->

                    <div class="activity-list">


                        <?php foreach ($aktivitasTerbaru as $aktivitas): ?>

                            <div class="activity-item">


                                <!-- Icon -->

                                <div class="activity-icon">

                                    <i
                                        class="bi <?php
                                        echo htmlspecialchars(
                                            $aktivitas['icon']
                                        );
                                        ?>"
                                    ></i>

                                </div>


                                <!-- Content -->

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


                <!-- Header -->

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


                <!-- Quick Access -->

                <div class="quick-access-grid">


                    <!-- Tambah Berita -->

                    <a
                        href="berita.php"
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



                    <!-- Tambah Galeri -->

                    <a
                        href="galeri.php"
                        class="quick-access-item"
                    >

                        <div class="quick-access-icon">

                            <i class="bi bi-images"></i>

                        </div>

                        <div>

                            <strong>
                                Tambah Galeri
                            </strong>

                            <span>
                                Upload dokumentasi
                            </span>

                        </div>

                        <i class="bi bi-arrow-right"></i>

                    </a>



                    <!-- Tambah Skema -->

                    <a
                        href="skema.php"
                        class="quick-access-item"
                    >

                        <div class="quick-access-icon">

                            <i class="bi bi-award"></i>

                        </div>

                        <div>

                            <strong>
                                Tambah Skema
                            </strong>

                            <span>
                                Tambahkan skema sertifikasi
                            </span>

                        </div>

                        <i class="bi bi-arrow-right"></i>

                    </a>



                    <!-- Data Peserta -->

                    <a
                        href="peserta.php"
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
                                Lihat pendaftaran terbaru
                            </span>

                        </div>

                        <i class="bi bi-arrow-right"></i>

                    </a>

                </div>

            </section>


        </main>



        <!-- =================================================
             FOOTER
        ================================================== -->

        <footer class="admin-footer">

            <p>
                &copy;
                <?php echo date('Y'); ?>
                LSP PPPOLRI.
                Admin Dashboard.
            </p>

        </footer>


    </div>

</div>



<!-- =========================================================
     JAVASCRIPT
     
     Bootstrap JS
========================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>


<!-- =========================================================
     ADMIN JAVASCRIPT

     Lokasi:
     WEB-LSP-PPPOLRI/assets/js/admin.js
     
     Dari admin/dashboard.php:
     ../assets/js/admin.js
========================================================== -->

<script
    src="../assets/js/admin.js"
></script>


</body>
</html>