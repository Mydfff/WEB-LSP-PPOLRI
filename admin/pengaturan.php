```php
<?php

// ==========================================================
// PENGATURAN ADMIN - LSP PPPOLRI
// File: admin/pengaturan.php
// ==========================================================

session_start();


// ==========================================================
// CEK LOGIN
// ==========================================================

if (!isset($_SESSION["admin_id"])) {
    header("Location: auth/login.php");
    exit;
}


// ==========================================================
// DATA ADMIN
// ==========================================================

$adminNama = $_SESSION["admin_nama"] ?? "Administrator";
$adminRole = $_SESSION["admin_role"] ?? "Administrator";


// ==========================================================
// PAGE INFORMATION
// ==========================================================

$pageTitle = "Pengaturan";
$pageSubtitle = "Informasi dan pengaturan dasar LSP PPPOLRI";

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
        Pengaturan Admin | LSP PPPOLRI
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
             PENGATURAN CONTENT
        ================================================== -->

        <main class="dashboard-content">


            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <section class="dashboard-welcome">

                <span class="dashboard-label">
                    ADMIN PANEL
                </span>

                <h1>
                    Pengaturan
                </h1>

                <p>
                    Informasi dan pengaturan dasar
                    sistem administrasi LSP PPPOLRI.
                </p>

            </section>


            <!-- =================================================
                 INFORMASI SISTEM
            ================================================== -->

            <section class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <span class="card-label">
                            SYSTEM
                        </span>

                        <h3>
                            Informasi Sistem
                        </h3>

                    </div>

                </div>


                <div class="p-4">

                    <p class="mb-3">
                        Halaman ini berisi informasi dasar mengenai
                        sistem administrasi LSP PPPOLRI.
                    </p>

                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="p-3 border rounded">

                                <small class="text-muted d-block mb-1">
                                    Nama Sistem
                                </small>

                                <strong>
                                    Sistem Informasi LSP PPPOLRI
                                </strong>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="p-3 border rounded">

                                <small class="text-muted d-block mb-1">
                                    Panel
                                </small>

                                <strong>
                                    Administrator
                                </strong>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="p-3 border rounded">

                                <small class="text-muted d-block mb-1">
                                    Status Sistem
                                </small>

                                <strong>
                                    Aktif
                                </strong>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="p-3 border rounded">

                                <small class="text-muted d-block mb-1">
                                    Teknologi
                                </small>

                                <strong>
                                    PHP Native & MySQL
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 ADMIN YANG LOGIN
            ================================================== -->

            <section class="dashboard-card mt-4">

                <div class="dashboard-card-header">

                    <div>

                        <span class="card-label">
                            ADMINISTRATOR
                        </span>

                        <h3>
                            Administrator Aktif
                        </h3>

                    </div>

                </div>


                <div class="p-4">

                    <p class="mb-3">
                        Informasi administrator yang sedang
                        masuk ke dalam sistem.
                    </p>


                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="p-3 border rounded">

                                <small class="text-muted d-block mb-1">
                                    Nama Administrator
                                </small>

                                <strong>
                                    <?= htmlspecialchars($adminNama); ?>
                                </strong>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="p-3 border rounded">

                                <small class="text-muted d-block mb-1">
                                    Role
                                </small>

                                <strong>
                                    <?= htmlspecialchars($adminRole); ?>
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 CATATAN
            ================================================== -->

            <section class="dashboard-card mt-4">

                <div class="dashboard-card-header">

                    <div>

                        <span class="card-label">
                            INFORMATION
                        </span>

                        <h3>
                            Catatan
                        </h3>

                    </div>

                </div>


                <div class="p-4">

                    <p class="mb-0">
                        Pengaturan lanjutan seperti perubahan
                        informasi LSP, logo, alamat, kontak,
                        dan konfigurasi website dapat
                        dikembangkan pada tahap berikutnya
                        sesuai kebutuhan pengelolaan sistem.
                    </p>

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
```
