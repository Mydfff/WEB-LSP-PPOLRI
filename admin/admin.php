<?php

session_start();

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| CEK SESSION LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| DATA ADMIN YANG SEDANG LOGIN
|--------------------------------------------------------------------------
*/

$admin_id = $_SESSION['admin_id'];

$stmtLogin = $pdo->prepare("
    SELECT
        id,
        nama_lengkap,
        username,
        email,
        role,
        status
    FROM admin
    WHERE id = ?
    LIMIT 1
");

$stmtLogin->execute([$admin_id]);

$currentAdmin = $stmtLogin->fetch();


/*
|--------------------------------------------------------------------------
| JIKA DATA ADMIN TIDAK DITEMUKAN
|--------------------------------------------------------------------------
*/

if (!$currentAdmin) {

    session_unset();
    session_destroy();

    header("Location: ../auth/login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL SEMUA DATA ADMIN
|--------------------------------------------------------------------------
*/

$stmtAdmin = $pdo->query("
    SELECT
        id,
        nama_lengkap,
        username,
        email,
        role,
        status,
        created_at
    FROM admin
    ORDER BY id ASC
");

$admins = $stmtAdmin->fetchAll();


/*
|--------------------------------------------------------------------------
| STATISTIK ADMIN
|--------------------------------------------------------------------------
*/

$totalAdmin = count($admins);

$adminAktif = 0;
$adminNonaktif = 0;

foreach ($admins as $admin) {

    if ($admin['status'] === 'aktif') {
        $adminAktif++;
    } else {
        $adminNonaktif++;
    }
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

    <title>Kelola Admin | LSP PPPOLRI</title>


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
         
         Sidebar + Topbar
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >


    <!-- =====================================================
         KELOLA ADMIN CSS
         
         Khusus isi halaman Kelola Admin
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../assets/css/kelola-admin.css"
    >

</head>


<body>


<!-- =========================================================
     SIDEBAR
========================================================= -->

<aside
    class="admin-sidebar"
    id="adminSidebar"
>


    <!-- =====================================================
         SIDEBAR BRAND
    ====================================================== -->

    <div class="sidebar-brand">

        <div class="brand-logo">

            <i class="bi bi-shield-check"></i>

        </div>


        <div class="brand-text">

            <strong>
                LSP PPPOLRI
            </strong>

            <span>
                Admin Panel
            </span>

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
                href="dashboard.php"
                class="sidebar-link"
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



        <!-- =================================================
             MANAGEMENT
        ================================================== -->

        <div class="nav-section">

            <span class="nav-section-title">
                MANAGEMENT
            </span>


            <!-- Kelola Admin -->

            <a
                href="admin.php"
                class="sidebar-link active"
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



    <!-- =====================================================
         SIDEBAR FOOTER
    ====================================================== -->

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



<!-- =========================================================
     MOBILE OVERLAY
========================================================= -->

<div
    class="sidebar-overlay"
    id="sidebarOverlay"
></div>



<!-- =========================================================
     MAIN AREA
========================================================= -->

<div class="admin-main">


    <!-- =====================================================
         TOPBAR
    ====================================================== -->

    <header class="admin-topbar">


        <!-- =================================================
             HAMBURGER
        ================================================== -->

        <button
            type="button"
            class="sidebar-toggle"
            id="sidebarToggle"
            aria-label="Buka menu"
            aria-expanded="false"
        >

            <i class="bi bi-list"></i>

        </button>



        <!-- =================================================
             TOPBAR TITLE
        ================================================== -->

        <div class="topbar-title">

            <h2>
                Kelola Admin
            </h2>

            <span>
                Panel Administrasi LSP PPPOLRI
            </span>

        </div>



        <!-- =================================================
             TOPBAR ACTIONS
        ================================================== -->

        <div class="topbar-actions">


            <!-- NOTIFICATION -->

            <button
                type="button"
                class="notification-btn"
                aria-label="Notifikasi"
            >

                <i class="bi bi-bell"></i>

                <span class="notification-badge">
                    0
                </span>

            </button>



            <!-- ADMIN PROFILE -->

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
                        <?= htmlspecialchars(
                            $currentAdmin['nama_lengkap']
                        ); ?>
                    </strong>

                    <span>
                        <?= htmlspecialchars(
                            $currentAdmin['role']
                        ); ?>
                    </span>

                </div>


                <i class="bi bi-chevron-down profile-arrow"></i>

            </a>

        </div>

    </header>



    <!-- =====================================================
         CONTENT
    ====================================================== -->

    <main class="admin-content">


        <!-- =================================================
             PAGE HEADER
             
             SESUAI DENGAN kelola-admin.css
        ================================================== -->

        <section class="admin-page-header">


            <div>

                <span class="admin-page-label">
                    MANAJEMEN SISTEM
                </span>


                <h1>
                    Kelola Administrator
                </h1>


                <p>
                    Kelola akun administrator yang memiliki
                    akses ke sistem LSP PPPOLRI.
                </p>

            </div>



            <!-- TAMBAH ADMIN -->

            <a
                href="admin-tambah.php"
                class="btn-add-admin"
            >

                <i class="bi bi-plus-lg"></i>

                Tambah Admin

            </a>

        </section>



        <!-- =================================================
             STATISTICS
             
             SESUAI DENGAN kelola-admin.css
        ================================================== -->

        <section class="admin-statistics">


            <!-- TOTAL -->

            <div class="admin-stat-card">

                <div class="admin-stat-icon">

                    <i class="bi bi-people-fill"></i>

                </div>


                <div class="admin-stat-info">

                    <span>
                        Total Admin
                    </span>

                    <strong>
                        <?= $totalAdmin; ?>
                    </strong>

                    <small>
                        Administrator terdaftar
                    </small>

                </div>

            </div>



            <!-- AKTIF -->

            <div class="admin-stat-card">

                <div class="admin-stat-icon success">

                    <i class="bi bi-person-check-fill"></i>

                </div>


                <div class="admin-stat-info">

                    <span>
                        Admin Aktif
                    </span>

                    <strong>
                        <?= $adminAktif; ?>
                    </strong>

                    <small>
                        Akun aktif
                    </small>

                </div>

            </div>



            <!-- NONAKTIF -->

            <div class="admin-stat-card">

                <div class="admin-stat-icon danger">

                    <i class="bi bi-person-x-fill"></i>

                </div>


                <div class="admin-stat-info">

                    <span>
                        Admin Nonaktif
                    </span>

                    <strong>
                        <?= $adminNonaktif; ?>
                    </strong>

                    <small>
                        Akun nonaktif
                    </small>

                </div>

            </div>

        </section>



        <!-- =================================================
             TABLE CARD
        ================================================== -->

        <section class="admin-table-card">


            <!-- =================================================
                 TABLE HEADER
            ================================================== -->

            <div class="admin-table-header">


                <div>

                    <span>
                        ADMINISTRATOR
                    </span>


                    <h2>
                        Daftar Administrator
                    </h2>


                    <p>
                        Daftar seluruh akun administrator
                        yang tersimpan dalam database.
                    </p>

                </div>



                <!-- TOTAL -->

                <div class="admin-table-count">

                    <i class="bi bi-people"></i>

                    <span>
                        <?= $totalAdmin; ?> Admin
                    </span>

                </div>

            </div>



            <!-- =================================================
                 TABLE WRAPPER
            ================================================== -->

            <div class="admin-table-wrapper">


                <table class="admin-table">


                    <!-- =================================================
                         TABLE HEAD
                    ================================================== -->

                    <thead>

                        <tr>

                            <th>
                                No
                            </th>

                            <th>
                                Administrator
                            </th>

                            <th>
                                Username
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Role
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Tanggal Dibuat
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>

                    </thead>



                    <!-- =================================================
                         TABLE BODY
                    ================================================== -->

                    <tbody>


                    <?php if (!empty($admins)): ?>


                        <?php $no = 1; ?>


                        <?php foreach ($admins as $admin): ?>


                            <tr>


                                <!-- =================================================
                                     NOMOR
                                ================================================== -->

                                <td>

                                    <span class="table-number">
                                        <?= $no++; ?>
                                    </span>

                                </td>



                                <!-- =================================================
                                     ADMINISTRATOR
                                ================================================== -->

                                <td>

                                    <div class="admin-table-user">


                                        <!-- AVATAR -->

                                        <div class="admin-table-avatar">

                                            <i class="bi bi-person-fill"></i>

                                        </div>


                                        <!-- INFO -->

                                        <div class="admin-table-user-info">

                                            <strong>
                                                <?= htmlspecialchars(
                                                    $admin['nama_lengkap']
                                                ); ?>
                                            </strong>


                                            <?php if (
                                                (int)$admin['id']
                                                ===
                                                (int)$currentAdmin['id']
                                            ): ?>

                                                <span class="current-admin">
                                                    Anda
                                                </span>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </td>



                                <!-- =================================================
                                     USERNAME
                                ================================================== -->

                                <td>

                                    <span class="admin-username">

                                        <?= htmlspecialchars(
                                            $admin['username']
                                        ); ?>

                                    </span>

                                </td>



                                <!-- =================================================
                                     EMAIL
                                ================================================== -->

                                <td>

                                    <span class="admin-email">

                                        <?php

                                        if (!empty($admin['email'])) {

                                            echo htmlspecialchars(
                                                $admin['email']
                                            );

                                        } else {

                                            echo '-';

                                        }

                                        ?>

                                    </span>

                                </td>



                                <!-- =================================================
                                     ROLE
                                ================================================== -->

                                <td>

                                    <span class="admin-role">

                                        <?= htmlspecialchars(
                                            $admin['role']
                                        ); ?>

                                    </span>

                                </td>



                                <!-- =================================================
                                     STATUS
                                ================================================== -->

                                <td>


                                    <?php if (
                                        $admin['status'] === 'aktif'
                                    ): ?>


                                        <span
                                            class="admin-status active"
                                        >

                                            <i
                                                class="bi bi-check-circle-fill"
                                            ></i>

                                            Aktif

                                        </span>


                                    <?php else: ?>


                                        <span
                                            class="admin-status inactive"
                                        >

                                            <i
                                                class="bi bi-x-circle-fill"
                                            ></i>

                                            Nonaktif

                                        </span>


                                    <?php endif; ?>

                                </td>



                                <!-- =================================================
                                     TANGGAL
                                ================================================== -->

                                <td>

                                    <span class="admin-date">

                                        <?php

                                        if (
                                            !empty(
                                                $admin['created_at']
                                            )
                                        ) {

                                            echo date(
                                                'd M Y',
                                                strtotime(
                                                    $admin['created_at']
                                                )
                                            );

                                        } else {

                                            echo '-';

                                        }

                                        ?>

                                    </span>

                                </td>



                                <!-- =================================================
                                     AKSI
                                ================================================== -->

                                <td>

                                    <div class="admin-actions">


                                        <!-- EDIT -->

                                        <a
                                            href="admin-edit.php?id=<?= (int)$admin['id']; ?>"
                                            class="admin-action-btn edit"
                                            title="Edit Admin"
                                        >

                                            <i
                                                class="bi bi-pencil-square"
                                            ></i>

                                        </a>



                                        <?php if (
                                            (int)$admin['id']
                                            !==
                                            (int)$currentAdmin['id']
                                        ): ?>


                                            <!-- DELETE -->

                                            <a
                                                href="admin-hapus.php?id=<?= (int)$admin['id']; ?>"
                                                class="admin-action-btn delete"
                                                title="Hapus Admin"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus admin ini?');"
                                            >

                                                <i
                                                    class="bi bi-trash3-fill"
                                                ></i>

                                            </a>


                                        <?php else: ?>


                                            <!-- AKUN SENDIRI -->

                                            <button
                                                type="button"
                                                class="admin-action-btn disabled"
                                                disabled
                                                title="Akun yang sedang digunakan tidak dapat dihapus"
                                            >

                                                <i
                                                    class="bi bi-shield-lock-fill"
                                                ></i>

                                            </button>


                                        <?php endif; ?>


                                    </div>

                                </td>


                            </tr>


                        <?php endforeach; ?>


                    <?php else: ?>


                        <!-- =================================================
                             EMPTY STATE
                        ================================================== -->

                        <tr>

                            <td
                                colspan="8"
                                class="admin-table-empty"
                            >


                                <div class="admin-empty-state">


                                    <div class="empty-icon">

                                        <i class="bi bi-people"></i>

                                    </div>


                                    <h3>
                                        Belum Ada Admin
                                    </h3>


                                    <p>
                                        Belum terdapat akun
                                        administrator dalam sistem.
                                    </p>


                                    <a
                                        href="admin-tambah.php"
                                        class="empty-add-btn"
                                    >

                                        <i class="bi bi-plus-lg"></i>

                                        Tambah Admin

                                    </a>


                                </div>


                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </section>


    </main>



    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <footer class="admin-footer">

        <p>

            &copy;
            <?= date('Y'); ?>

            LSP PPPOLRI.
            Admin Dashboard.

        </p>

    </footer>


</div>



<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script
    src="../assets/js/admin.js"
></script>


</body>

</html>