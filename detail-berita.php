<?php
// ==========================================================
// DETAIL BERITA - LSP PPPOLRI
// File: detail-berita.php
// ==========================================================

require_once "config/database.php";


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
// FORMAT TANGGAL
// ==========================================================

function formatTanggal($tanggal)
{
    if (empty($tanggal)) {
        return "-";
    }

    $bulan = [
        1 => "Januari",
        2 => "Februari",
        3 => "Maret",
        4 => "April",
        5 => "Mei",
        6 => "Juni",
        7 => "Juli",
        8 => "Agustus",
        9 => "September",
        10 => "Oktober",
        11 => "November",
        12 => "Desember"
    ];

    $timestamp = strtotime($tanggal);

    if ($timestamp === false) {
        return $tanggal;
    }

    return date("d", $timestamp)
        . " "
        . $bulan[(int) date("m", $timestamp)]
        . " "
        . date("Y", $timestamp);
}


// ==========================================================
// FUNGSI GAMBAR BERITA
// ==========================================================

function getGambarBerita($gambar)
{
    if (empty($gambar)) {
        return "assets/img/placeholder-news.jpg";
    }

    return "uploads/berita/" . $gambar;
}


// ==========================================================
// AMBIL ID BERITA
// ==========================================================

$id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;


// ==========================================================
// VALIDASI ID
// ==========================================================

if ($id <= 0) {

    header("Location: berita.php");
    exit;

}


// ==========================================================
// AMBIL DATA BERITA
// ==========================================================

try {

    $query = "
        SELECT
            id,
            judul,
            kategori,
            gambar,
            isi,
            status,
            created_at
        FROM berita
        WHERE id = :id
        AND status = 'publish'
        LIMIT 1
    ";

    $stmt = $pdo->prepare($query);

    $stmt->execute([
        ":id" => $id
    ]);

    $berita = $stmt->fetch();

} catch (PDOException $e) {

    $berita = false;

}


// ==========================================================
// JIKA BERITA TIDAK DITEMUKAN
// ==========================================================

if (!$berita) {

    header("Location: berita.php");
    exit;

}

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
        <?php echo e($berita["judul"]); ?> | LSP PPPOLRI
    </title>


    <!-- =====================================================
         FAVICON
    ====================================================== -->

    <link
        rel="icon"
        type="image/png"
        href="assets/img/Logo-ppolri.jpg"
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
         CSS DETAIL BERITA
    ====================================================== -->

    <link
        rel="stylesheet"
        href="assets/css/detail-berita.css"
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
     DETAIL BERITA
========================================================== -->

<main class="detail-news-section">

    <div class="container">


        <!-- =================================================
             BREADCRUMB
        ================================================== -->

        <div class="detail-news-breadcrumb">

            <a href="index.php">

                <i class="bi bi-house-door-fill"></i>

                Beranda

            </a>

            <i class="bi bi-chevron-right"></i>

            <a href="berita.php">

                Berita

            </a>

            <i class="bi bi-chevron-right"></i>

            <span>

                Detail

            </span>

        </div>



        <!-- =================================================
             HEADER BERITA
        ================================================== -->

        <article class="detail-news">


            <!-- =================================================
                 KATEGORI & TANGGAL
            ================================================== -->

            <div class="detail-news-meta">

                <span class="detail-news-category">

                    <?php
                    echo e($berita["kategori"]);
                    ?>

                </span>


                <span class="detail-news-date">

                    <i class="bi bi-calendar-event"></i>

                    <?php
                    echo formatTanggal(
                        $berita["created_at"]
                    );
                    ?>

                </span>

            </div>



            <!-- =================================================
                 JUDUL
            ================================================== -->

            <h1 class="detail-news-title">

                <?php
                echo e($berita["judul"]);
                ?>

            </h1>



            <!-- =================================================
                 GAMBAR UTAMA
            ================================================== -->

            <div class="detail-news-image">

                <img
                    src="<?php
                        echo e(
                            getGambarBerita(
                                $berita["gambar"]
                            )
                        );
                    ?>"
                    alt="<?php
                        echo e(
                            $berita["judul"]
                        );
                    ?>"
                >

            </div>



            <!-- =================================================
                 ISI BERITA
            ================================================== -->

            <div class="detail-news-content">

                <?php

                /*
                --------------------------------------------------
                Menampilkan isi berita.
                
                nl2br digunakan agar enter/paragraf dari textarea
                admin tetap terlihat pada halaman berita.
                --------------------------------------------------
                */

                echo nl2br(
                    e($berita["isi"])
                );

                ?>

            </div>



            <!-- =================================================
                 TOMBOL KEMBALI
            ================================================== -->

            <div class="detail-news-footer">

                <a
                    href="berita.php"
                    class="detail-news-back"
                >

                    <i class="bi bi-arrow-left"></i>

                    Kembali ke Berita

                </a>

            </div>


        </article>

    </div>

</main>



<!-- =========================================================
     CTA
========================================================== -->

<section class="detail-news-cta">

    <div class="container">

        <div class="detail-news-cta-wrapper">

            <div>

                <span class="section-subtitle">

                    LSP PPPOLRI

                </span>

                <h2>

                    Ingin Mengetahui Informasi Lainnya?

                </h2>

                <p>

                    Lihat berita dan informasi terbaru
                    dari LSP PPPOLRI.

                </p>

            </div>


            <div>

                <a
                    href="berita.php"
                    class="detail-news-cta-btn"
                >

                    Lihat Berita Lainnya

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
     JAVASCRIPT
========================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>


<script
    src="assets/js/include.js"
></script>


<script
    src="assets/js/navbar.js"
></script>


<script
    src="assets/js/main.js"
></script>


</body>

</html>