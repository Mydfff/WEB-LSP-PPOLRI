<?php
// ==========================================================
// HALAMAN KONTAK - LSP PPPOLRI
// File: kontak.php
// ==========================================================
//
// Halaman kontak bersifat statis.
// Tidak menggunakan database atau admin khusus.
// ==========================================================


// ==========================================================
// HELPER HTML
// ==========================================================

function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    );
}


// ==========================================================
// DATA KONTAK LSP PPPOLRI
// ==========================================================
//
// Data sementara ditulis langsung di halaman.
// Nanti tinggal kita sesuaikan dengan data resmi LSP PPPOLRI.
//

$namaInstansi = "LSP PPPOLRI";

$alamat = "Jakarta Selatan";

$telepon = "Nomor Telepon LSP PPPOLRI";

$whatsapp = "628xxxxxxxxxx";

$email = "email@lsp-pppolri.id";

$jamPelayanan = "Senin - Jumat, 08.00 - 16.00 WIB";


// ==========================================================
// LINK KONTAK
// ==========================================================

$linkWhatsApp =
    "https://wa.me/" . $whatsapp
    . "?text="
    . urlencode(
        "Halo LSP PPPOLRI, saya ingin mendapatkan informasi mengenai layanan sertifikasi."
    );


$linkEmail =
    "mailto:" . $email;


// ==========================================================
// GOOGLE MAPS
// ==========================================================
//
// Untuk sementara menggunakan placeholder.
// Nanti diganti dengan embed Google Maps lokasi resmi.
//

$googleMapsEmbed = "";

?>

<!doctype html>

<html lang="id">


<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >


    <title>
        Kontak | LSP PPPOLRI
    </title>


    <link
        rel="icon"
        type="image/png"
        href="assets/img/Logo-ppolri.jpg"
    >


    <!-- =====================================================
         BOOTSTRAP CSS
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
         CSS HEADER
    ====================================================== -->

    <link
        rel="stylesheet"
        href="assets/css/header.css"
    >


    <!-- =====================================================
         CSS KONTAK
    ====================================================== -->

    <link
        rel="stylesheet"
        href="assets/css/kontak.css"
    >


    <!-- =====================================================
         CSS UTAMA
    ====================================================== -->

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/responsive.css"
    >

</head>


<body>


<!-- =========================================================
     TOP HEADER
========================================================== -->

<div id="top-header"></div>


<!-- =========================================================
     NAVBAR
========================================================== -->

<div id="navbar"></div>



<!-- =========================================================
     SECTION 1
     HERO / PAGE HEADER KONTAK
========================================================== -->

<section class="contact-page-header">

    <div class="container">

        <div class="contact-page-header-content">


            <span class="section-subtitle">

                LSP PPPOLRI

            </span>


            <h1>

                Hubungi Kami

            </h1>


            <p>

                Dapatkan informasi mengenai layanan sertifikasi
                profesi, skema sertifikasi, pendaftaran, dan
                layanan LSP PPPOLRI.

            </p>


        </div>

    </div>

</section>



<!-- =========================================================
     SECTION 2
     INFORMASI KONTAK + GOOGLE MAPS
========================================================== -->

<section class="contact-information-section">

    <div class="container">


        <!-- =================================================
             SECTION HEADER
        ================================================== -->

        <div class="contact-section-heading">


            <span class="section-subtitle">

                Informasi Kontak

            </span>


            <h2>

                Hubungi LSP PPPOLRI

            </h2>


            <p>

                Silakan gunakan informasi berikut untuk
                menghubungi LSP PPPOLRI atau mendapatkan
                informasi lebih lanjut mengenai layanan kami.

            </p>


        </div>



        <!-- =================================================
             CONTACT CONTENT
        ================================================== -->

        <div class="row g-4 align-items-stretch">


            <!-- =================================================
                 INFORMASI KONTAK
            ================================================== -->

            <div class="col-lg-5">


                <div class="contact-information-card">


                    <!-- ALAMAT -->

                    <div class="contact-info-item">


                        <div class="contact-info-icon">

                            <i class="bi bi-geo-alt"></i>

                        </div>


                        <div class="contact-info-content">

                            <span>

                                Alamat

                            </span>


                            <p>

                                <?php echo e($alamat); ?>

                            </p>

                        </div>


                    </div>



                    <!-- TELEPON -->

                    <div class="contact-info-item">


                        <div class="contact-info-icon">

                            <i class="bi bi-telephone"></i>

                        </div>


                        <div class="contact-info-content">

                            <span>

                                Telepon

                            </span>


                            <p>

                                <?php echo e($telepon); ?>

                            </p>

                        </div>


                    </div>



                    <!-- EMAIL -->

                    <div class="contact-info-item">


                        <div class="contact-info-icon">

                            <i class="bi bi-envelope"></i>

                        </div>


                        <div class="contact-info-content">

                            <span>

                                Email

                            </span>


                            <p>

                                <a
                                    href="<?php echo e($linkEmail); ?>"
                                >

                                    <?php echo e($email); ?>

                                </a>

                            </p>

                        </div>


                    </div>



                    <!-- JAM PELAYANAN -->

                    <div class="contact-info-item">


                        <div class="contact-info-icon">

                            <i class="bi bi-clock"></i>

                        </div>


                        <div class="contact-info-content">

                            <span>

                                Jam Pelayanan

                            </span>


                            <p>

                                <?php echo e($jamPelayanan); ?>

                            </p>

                        </div>


                    </div>



                    <!-- WHATSAPP -->

                    <div class="contact-whatsapp-wrapper">


                        <a
                            href="<?php echo e($linkWhatsApp); ?>"
                            class="contact-whatsapp-btn"
                            target="_blank"
                            rel="noopener noreferrer"
                        >

                            <i class="bi bi-whatsapp"></i>


                            <span>

                                Hubungi via WhatsApp

                            </span>


                            <i class="bi bi-arrow-up-right"></i>

                        </a>


                    </div>


                </div>

            </div>



            <!-- =================================================
                 GOOGLE MAPS
            ================================================== -->

            <div class="col-lg-7">


                <div class="contact-map-card">


                    <div class="contact-map-header">


                        <div>

                            <span class="section-subtitle">

                                Lokasi Kami

                            </span>


                            <h3>

                                Lokasi LSP PPPOLRI

                            </h3>

                        </div>


                        <i class="bi bi-map"></i>


                    </div>



                    <div class="contact-map-wrapper">


                        <?php if (!empty($googleMapsEmbed)): ?>

                            <iframe
                                src="<?php echo e($googleMapsEmbed); ?>"
                                width="100%"
                                height="100%"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Lokasi LSP PPPOLRI"
                            >
                            </iframe>

                        <?php else: ?>


                            <div class="contact-map-placeholder">


                                <i class="bi bi-geo-alt-fill"></i>


                                <h4>

                                    Lokasi LSP PPPOLRI

                                </h4>


                                <p>

                                    Google Maps akan ditampilkan
                                    setelah alamat resmi LSP PPPOLRI
                                    ditentukan.

                                </p>


                            </div>


                        <?php endif; ?>


                    </div>


                </div>

            </div>


        </div>

    </div>

</section>



<!-- =========================================================
     SECTION 3
     CTA KONTAK
========================================================== -->

<section class="contact-page-cta">

    <div class="container">


        <div class="contact-page-cta-content">


            <div class="contact-page-cta-text">


                <span class="section-subtitle">

                    LSP PPPOLRI

                </span>


                <h2>

                    Masih Memiliki Pertanyaan?

                </h2>


                <p>

                    Jangan ragu untuk menghubungi LSP PPPOLRI
                    untuk mendapatkan informasi lebih lanjut
                    mengenai layanan sertifikasi.

                </p>


            </div>



            <div class="contact-page-cta-action">


                <a
                    href="<?php echo e($linkWhatsApp); ?>"
                    class="contact-cta-btn"
                    target="_blank"
                    rel="noopener noreferrer"
                >

                    <i class="bi bi-whatsapp"></i>


                    Hubungi Kami via WhatsApp


                    <i class="bi bi-arrow-right"></i>

                </a>


            </div>


        </div>

    </div>

</section>



<!-- =========================================================
     FOOTER
========================================================== -->

<div id="footer"></div>



<!-- =========================================================
     POPUP SECURITY
========================================================== -->





<!-- =========================================================
     JAVASCRIPT
========================================================== -->

<!-- Bootstrap -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>


<!-- Components -->

<script
    src="assets/js/include.js"
></script>


<!-- Navbar -->

<script
    src="assets/js/navbar.js"
></script>


<!-- Main -->

<script
    src="assets/js/main.js"
></script>


<!-- Popup Security -->

<!--
<script
    src="assets/js/popup-security.js"
></script>
-->


</body>

</html>