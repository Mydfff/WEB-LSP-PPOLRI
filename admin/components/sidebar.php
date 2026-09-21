<?php
// ==========================================================
// SIDEBAR ADMIN - LSP PPPOLRI
// File: admin/components/sidebar.php
// ==========================================================
?>

<aside class="admin-sidebar" id="adminSidebar">

    <!-- =====================================================
         SIDEBAR BRAND
    ====================================================== -->
    <div class="sidebar-brand">

        <div class="brand-logo">
            <i class="bi bi-shield-check"></i>
        </div>

        <div class="brand-text">
            <strong>LSP PPPOLRI</strong>
            <span>Admin Panel</span>
        </div>

    </div>


    <!-- =====================================================
         SIDEBAR NAVIGATION
    ====================================================== -->
    <nav class="sidebar-nav">


        <!-- =================================================
             MENU UTAMA
        ================================================== -->
        <div class="nav-section">

            <span class="nav-section-title">
                MENU UTAMA
            </span>


            <!-- Dashboard -->
            <a
                href="admin/dashboard.php"
                class="sidebar-link"
            >
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>


            <!-- Berita -->
            <a
                href="berita/daftar.php"
                class="sidebar-link"
            >
                <i class="bi bi-newspaper"></i>
                <span>Berita</span>
            </a>


            <!-- Galeri -->
            <a
                href="../galeri/"
                class="sidebar-link"
            >
                <i class="bi bi-images"></i>
                <span>Galeri</span>
            </a>


            <!-- Skema Sertifikasi -->
            <a
                href="skema/daftar.php"
                class="sidebar-link"
            >
                <i class="bi bi-award"></i>
                <span>Skema Sertifikasi</span>
            </a>


            <!-- Data Peserta -->
            <a
                href="../peserta/"
                class="sidebar-link"
            >
                <i class="bi bi-people"></i>
                <span>Data Peserta</span>
            </a>

        </div>



        <!-- =================================================
             SERTIFIKASI
        ================================================== -->
        <div class="nav-section">

            <span class="nav-section-title">
                SERTIFIKASI
            </span>


            <!-- Pendaftaran Sertifikasi -->
            <a
                href="../sertifikasi/pendaftaran.php"
                class="sidebar-link"
            >
                <i class="bi bi-file-earmark-person"></i>
                <span>Pendaftaran Sertifikasi</span>
            </a>


            <!-- Sertifikasi Ulang -->
            <a
                href="../sertifikasi/ulang.php"
                class="sidebar-link"
            >
                <i class="bi bi-arrow-repeat"></i>
                <span>Sertifikasi Ulang</span>
            </a>


            <!-- Perpanjangan Sertifikasi -->
            <a
                href="../sertifikasi/perpanjangan.php"
                class="sidebar-link"
            >
                <i class="bi bi-arrow-clockwise"></i>
                <span>Perpanjangan Sertifikasi</span>
            </a>

        </div>



        <!-- =================================================
             MANAGEMENT
        ================================================== -->
        <div class="nav-section">

            <span class="nav-section-title">
                MANAGEMENT
            </span>


            <!-- Kelola Admin -->
            <a
                href="../admin.php"
                class="sidebar-link"
            >
                <i class="bi bi-person-gear"></i>
                <span>Kelola Admin</span>
            </a>


            <!-- Pengaturan -->
            <a
                href="../pengaturan.php"
                class="sidebar-link"
            >
                <i class="bi bi-gear"></i>
                <span>Pengaturan</span>
            </a>

        </div>

    </nav>



    <!-- =====================================================
         SIDEBAR FOOTER
    ====================================================== -->
    <div class="sidebar-footer">

        <a
            href="../../auth/logout.php"
            class="sidebar-link"
        >
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>

    </div>

</aside>


<!-- =========================================================
     MOBILE OVERLAY
========================================================== -->
<div
    class="sidebar-overlay"
    id="sidebarOverlay"
></div>