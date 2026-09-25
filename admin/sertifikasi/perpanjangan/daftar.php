```php
<?php

// ==========================================================
// PERPANJANGAN SERTIFIKASI - ADMIN LSP PPPOLRI
// File: admin/sertifikasi/perpanjangan/daftar.php
// ==========================================================

session_start();


// ==========================================================
// CEK LOGIN
// ==========================================================

if (!isset($_SESSION["admin_id"])) {

    header("Location: ../../../auth/login.php");

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

$pageTitle = "Perpanjangan Sertifikasi";
$pageSubtitle = "Informasi layanan perpanjangan sertifikasi LSP PPPOLRI";

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
        Perpanjangan Sertifikasi | LSP PPPOLRI
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
        href="../../../assets/css/admin.css"
    >

</head>


<body>


<div class="admin-wrapper">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <?php require_once "../../components/sidebar.php"; ?>


    <!-- =====================================================
         MAIN AREA
    ====================================================== -->

    <div class="admin-main">


        <!-- =================================================
             HEADER
        ================================================== -->

        <?php require_once "../../components/header.php"; ?>


        <!-- =================================================
             CONTENT
        ================================================== -->

        <main class="dashboard-content">


            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <section class="page-heading">

                <span class="page-label">
                    PERPANJANGAN SERTIFIKASI
                </span>

                <h1>
                    Perpanjangan Sertifikasi
                </h1>

                <p>
                    Informasi layanan perpanjangan sertifikasi
                    peserta LSP PPPOLRI.
                </p>

            </section>


            <!-- =================================================
                 INFORMASI UTAMA
            ================================================== -->

            <section class="row g-4">


                <!-- INFORMASI LAYANAN -->

                <div class="col-lg-8">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body p-4">

                            <div class="d-flex align-items-center mb-4">

                                <div class="me-3">

                                    <i
                                        class="bi bi-calendar-check fs-1"
                                    ></i>

                                </div>

                                <div>

                                    <span class="text-muted small">
                                        LAYANAN SERTIFIKASI
                                    </span>

                                    <h3 class="fw-bold mb-0">
                                        Perpanjangan Sertifikasi
                                    </h3>

                                </div>

                            </div>


                            <p class="text-muted">

                                Perpanjangan sertifikasi merupakan layanan
                                bagi peserta yang memerlukan perpanjangan
                                masa berlaku sertifikasi sesuai dengan
                                ketentuan dan persyaratan yang berlaku
                                di LSP PPPOLRI.

                            </p>


                            <p class="text-muted mb-0">

                                Halaman ini digunakan sebagai informasi awal
                                mengenai layanan perpanjangan sertifikasi.
                                Pengembangan proses pengajuan dan pengelolaan
                                perpanjangan dapat dilakukan pada tahap
                                pengembangan berikutnya.

                            </p>

                        </div>

                    </div>

                </div>


                <!-- INFORMASI FITUR -->

                <div class="col-lg-4">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body p-4">

                            <span class="text-muted small">
                                INFORMASI
                            </span>

                            <h5 class="fw-bold mt-1 mb-3">
                                Pengelolaan
                            </h5>


                            <div class="d-flex mb-3">

                                <i
                                    class="bi bi-info-circle fs-4 me-3"
                                ></i>

                                <div>

                                    <strong>
                                        Fitur Dasar
                                    </strong>

                                    <p class="text-muted small mb-0 mt-1">

                                        Halaman informasi dasar layanan
                                        perpanjangan sertifikasi.

                                    </p>

                                </div>

                            </div>


                            <hr>


                            <p class="text-muted small mb-0">

                                Fitur pengajuan, pemeriksaan dokumen,
                                verifikasi pembayaran, dan persetujuan
                                dapat dikembangkan pada tahap berikutnya.

                            </p>

                        </div>

                    </div>

                </div>


            </section>


        </main>


        <!-- =====================================================
             FOOTER
        ====================================================== -->

        <?php require_once "../../components/footer.php"; ?>


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
    src="../../../assets/js/admin.js"
></script>


</body>

</html>
```
