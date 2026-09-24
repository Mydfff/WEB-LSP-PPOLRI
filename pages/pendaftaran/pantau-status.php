<?php

session_start();

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
        Pantau Status Pendaftaran - LSP PPPOLRI
    </title>


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
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <!-- =====================================================
         CSS GLOBAL
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../../assets/css/header.css"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/style.css"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/responsive.css"
    >


    <!-- =====================================================
         CSS PENDAFTARAN
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../../assets/css/pendaftaran.css"
    >

</head>


<body>


<!-- =====================================================
     TOP HEADER
====================================================== -->

<div id="top-header"></div>


<!-- =====================================================
     NAVBAR
====================================================== -->

<div id="navbar"></div>


<!-- =====================================================
     MAIN
====================================================== -->

<main class="pendaftaran-page">

    <div class="container">


        <!-- =================================================
             HEADER
        ================================================== -->

        <div class="pendaftaran-header text-center">

            <span class="pendaftaran-subtitle">
                LAYANAN SERTIFIKASI
            </span>

            <h1>
                Pantau Status Pendaftaran
            </h1>

            <p>
                Pantau proses pendaftaran sertifikasi Anda
                dengan menggunakan nomor pendaftaran dan
                email yang digunakan saat melakukan pendaftaran.
            </p>

        </div>


        <!-- =================================================
             INFO
        ================================================== -->

        <div class="registration-info">

            <div class="registration-info-icon">

                <i class="bi bi-info-circle"></i>

            </div>

            <div>

                <h5>
                    Informasi Pemantauan
                </h5>

                <p>
                    Pastikan Anda memasukkan nomor pendaftaran
                    dan email yang sama dengan data yang digunakan
                    saat melakukan pendaftaran sertifikasi.
                </p>

            </div>

        </div>


        <!-- =================================================
             FORM PANTAU STATUS
        ================================================== -->

        <div class="status-wrapper">

            <div class="status-card">


                <!-- =============================================
                     ICON
                ============================================== -->

                <div class="text-center mb-4">

                    <div
                        class="registration-info-icon mx-auto mb-3"
                    >

                        <i class="bi bi-search"></i>

                    </div>

                    <h2 class="status-title mb-2">
                        Cek Status Pendaftaran
                    </h2>

                    <p class="status-subtitle mb-0">
                        Masukkan data berikut untuk melihat
                        perkembangan proses sertifikasi Anda.
                    </p>

                </div>


                <!-- =============================================
                     FORM
                ============================================== -->

                <form
                    action="status.php"
                    method="POST"
                >


                    <!-- NOMOR PENDAFTARAN -->

                    <div class="mb-3">

                        <label
                            for="nomor_pendaftaran"
                            class="form-label"
                        >
                            Nomor Pendaftaran
                        </label>

                        <input
                            type="text"
                            id="nomor_pendaftaran"
                            name="nomor_pendaftaran"
                            class="form-control"
                            placeholder="Contoh: REG-20260923-BEBA19"
                            required
                        >

                        <div class="form-text">
                            Masukkan nomor pendaftaran yang
                            Anda terima setelah mengirim formulir.
                        </div>

                    </div>


                    <!-- EMAIL -->

                    <div class="mb-4">

                        <label
                            for="email"
                            class="form-label"
                        >
                            Email Pendaftaran
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="Masukkan email yang digunakan saat mendaftar"
                            required
                        >

                    </div>


                    <!-- BUTTON -->

                    <button
                        type="submit"
                        class="btn btn-primary-custom w-100"
                    >

                        <i class="bi bi-search me-1"></i>

                        Cek Status Pendaftaran

                    </button>

                </form>


                <!-- =============================================
                     CATATAN
                ============================================== -->

                <div class="text-center mt-4">

                    <small class="text-muted">

                        Belum melakukan pendaftaran?

                        <a
                            href="pendaftaran.php"
                            class="back-link"
                        >
                            Daftar Sertifikasi
                        </a>

                    </small>

                </div>


                <!-- =============================================
                     KEMBALI
                ============================================== -->

                <div class="text-center mt-3">

                    <a
                        href="../../index.php"
                        class="back-link"
                    >

                        <i class="bi bi-arrow-left me-1"></i>

                        Kembali ke Beranda

                    </a>

                </div>

            </div>

        </div>

    </div>

</main>


<!-- =====================================================
     FOOTER
====================================================== -->

<div id="footer"></div>


<!-- =====================================================
     BOOTSTRAP JS
====================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>


<!-- =====================================================
     COMPONENT LOADER
====================================================== -->

<script
    src="../../assets/js/include.js"
></script>


<!-- =====================================================
     NAVBAR
====================================================== -->

<script
    src="../../assets/js/navbar.js"
></script>


<!-- =====================================================
     MAIN JS
====================================================== -->

<script
    src="../../assets/js/main.js"
></script>

</body>

</html>