<?php
// ==========================================================
// HALAMAN BERITA - LSP PPPOLRI
// File: berita.php
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
// AMBIL BERITA YANG PUBLISHED
// ==========================================================
//
// Berita diambil dari tabel berita yang SUDAH ADA.
// Tidak membuat database atau tabel baru.
//

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
        WHERE status = 'publish'
        ORDER BY created_at DESC
    ";

    $stmt = $pdo->prepare($query);

    $stmt->execute();

    $beritaList = $stmt->fetchAll();

} catch (PDOException $e) {

    $beritaList = [];

}


// ==========================================================
// BERITA UTAMA
// ==========================================================

$beritaUtama = $beritaList[0] ?? null;


// ==========================================================
// BERITA LAIN
// ==========================================================

$beritaLain = [];

if (count($beritaList) > 1) {

    $beritaLain = array_slice(
        $beritaList,
        1
    );

}


// ==========================================================
// FUNGSI GAMBAR
// ==========================================================

function getGambarBerita($gambar)
{
    if (empty($gambar)) {

        return "assets/img/placeholder-news.jpg";

    }

    return "uploads/berita/" . $gambar;
}


// ==========================================================
// POTONG ISI BERITA
// ==========================================================

function excerpt($text, $length = 150)
{
    $text = strip_tags($text);

    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr(
        $text,
        0,
        $length
    ) . "...";
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
        Berita & Informasi | LSP PPPOLRI
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
         CSS BERITA
    ====================================================== -->
    <link rel="stylesheet" href="assets/css/berita-page.css">


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
     HERO / PAGE HEADER BERITA
========================================================== -->

<section class="news-page-header">

    <div class="container">

        <div class="news-page-header-content">

            <span class="section-subtitle">

                Berita & Pengumuman

            </span>


            <h1>

                Informasi Terbaru
                LSP PPPOLRI

            </h1>


            <p>

                Temukan berbagai informasi terbaru mengenai
                kegiatan, sertifikasi, pengumuman, dan agenda
                LSP PPPOLRI.

            </p>

        </div>

    </div>

</section>



<!-- =========================================================
     CONTENT BERITA
========================================================== -->

<section class="news-page-section">

    <div class="container">


        <!-- =================================================
             FILTER KATEGORI
        ================================================== -->

        <div class="news-filter">

            <button
                type="button"
                class="news-filter-btn active"
                data-filter="all"
            >

                Semua

            </button>


            <button
                type="button"
                class="news-filter-btn"
                data-filter="Kegiatan"
            >

                Kegiatan

            </button>


            <button
                type="button"
                class="news-filter-btn"
                data-filter="Sertifikasi"
            >

                Sertifikasi

            </button>


            <button
                type="button"
                class="news-filter-btn"
                data-filter="Informasi"
            >

                Informasi

            </button>


            <button
                type="button"
                class="news-filter-btn"
                data-filter="Pengumuman"
            >

                Pengumuman

            </button>

        </div>



        <!-- =================================================
             BERITA UTAMA
        ================================================== -->

        <?php if ($beritaUtama): ?>

            <div
                class="featured-news-page"
                data-category="<?php echo e($beritaUtama['kategori']); ?>"
            >


                <!-- FOTO -->

                <div class="featured-news-page-image">

                    <img
                        src="<?php echo e(
                            getGambarBerita(
                                $beritaUtama['gambar']
                            )
                        ); ?>"
                        alt="<?php echo e($beritaUtama['judul']); ?>"
                    >

                </div>


                <!-- CONTENT -->

                <div class="featured-news-page-content">


                    <div class="news-meta">

                        <span class="news-category">

                            <?php
                            echo e(
                                $beritaUtama['kategori']
                            );
                            ?>

                        </span>


                        <span class="news-date">

                            <i class="bi bi-calendar-event"></i>

                            <?php
                            echo formatTanggal(
                                $beritaUtama['created_at']
                            );
                            ?>

                        </span>

                    </div>


                    <h2>

                        <?php
                        echo e(
                            $beritaUtama['judul']
                        );
                        ?>

                    </h2>


                    <p>

                        <?php
                        echo e(
                            excerpt(
                                $beritaUtama['isi'],
                                220
                            )
                        );
                        ?>

                    </p>


                    <a
                        href="detail-berita.php?id=<?php echo (int) $beritaUtama['id']; ?>"
                        class="news-detail-btn"
                    >

                        Baca Selengkapnya

                        <i class="bi bi-arrow-right"></i>

                    </a>

                </div>

            </div>

        <?php endif; ?>



        <!-- =================================================
             JUDUL BERITA TERBARU
        ================================================== -->

        <?php if (!empty($beritaLain)): ?>

            <div class="news-list-header">

                <div>

                    <span class="section-subtitle">

                        Update Terbaru

                    </span>


                    <h2>

                        Berita Lainnya

                    </h2>

                </div>

            </div>



            <!-- =================================================
                 GRID BERITA
            ================================================== -->

            <div class="row g-4">


                <?php foreach ($beritaLain as $berita): ?>


                    <div
                        class="col-lg-4 col-md-6 news-item"
                        data-category="<?php echo e($berita['kategori']); ?>"
                    >


                        <article class="news-card-page">


                            <!-- FOTO -->

                            <div class="news-card-page-image">

                                <img
                                    src="<?php echo e(
                                        getGambarBerita(
                                            $berita['gambar']
                                        )
                                    ); ?>"
                                    alt="<?php echo e($berita['judul']); ?>"
                                >


                                <!-- KATEGORI -->

                                <span class="news-card-category">

                                    <?php
                                    echo e(
                                        $berita['kategori']
                                    );
                                    ?>

                                </span>

                            </div>



                            <!-- BODY -->

                            <div class="news-card-page-body">


                                <!-- TANGGAL -->

                                <span class="news-card-date">

                                    <i class="bi bi-calendar3"></i>

                                    <?php
                                    echo formatTanggal(
                                        $berita['created_at']
                                    );
                                    ?>

                                </span>


                                <!-- JUDUL -->

                                <h3>

                                    <?php
                                    echo e(
                                        $berita['judul']
                                    );
                                    ?>

                                </h3>


                                <!-- DESKRIPSI -->

                                <p>

                                    <?php
                                    echo e(
                                        excerpt(
                                            $berita['isi'],
                                            120
                                        )
                                    );
                                    ?>

                                </p>


                                <!-- LINK -->

                                <a
                                    href="detail-berita.php?id=<?php echo (int) $berita['id']; ?>"
                                    class="news-card-link"
                                >

                                    Baca Selengkapnya

                                    <i class="bi bi-arrow-right"></i>

                                </a>


                            </div>

                        </article>

                    </div>


                <?php endforeach; ?>


            </div>


        <?php elseif (!$beritaUtama): ?>


            <!-- =================================================
                 EMPTY STATE
            ================================================== -->

            <div class="news-empty">

                <div class="news-empty-icon">

                    <i class="bi bi-newspaper"></i>

                </div>


                <h3>

                    Belum Ada Berita

                </h3>


                <p>

                    Saat ini belum ada berita atau informasi
                    yang dipublikasikan oleh LSP PPPOLRI.

                </p>

            </div>


        <?php endif; ?>


    </div>

</section>



<!-- =========================================================
     CTA
========================================================== -->

<section class="news-page-cta">

    <div class="container">

        <div class="news-page-cta-content">


            <div>

                <span class="section-subtitle">

                    LSP PPPOLRI

                </span>


                <h2>

                    Jangan Lewatkan Informasi Terbaru

                </h2>


                <p>

                    Ikuti informasi dan kegiatan terbaru
                    dari LSP PPPOLRI.

                </p>

            </div>


            <div>

                <a
                    href="kontak.php"
                    class="btn-news-contact"
                >

                    Hubungi Kami

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

<!-- <script
    src="assets/js/popup-security.js"
></script> -->



<!-- =========================================================
     FILTER BERITA
========================================================== -->

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const filterButtons =
            document.querySelectorAll(
                ".news-filter-btn"
            );


        const newsItems =
            document.querySelectorAll(
                ".news-item"
            );


        const featuredNews =
            document.querySelector(
                ".featured-news-page"
            );


        filterButtons.forEach(
            function (button) {

                button.addEventListener(
                    "click",
                    function () {


                        /*
                        |--------------------------------------------------
                        | BUTTON ACTIVE
                        |--------------------------------------------------
                        */

                        filterButtons.forEach(
                            function (btn) {

                                btn.classList.remove(
                                    "active"
                                );

                            }
                        );


                        this.classList.add(
                            "active"
                        );


                        /*
                        |--------------------------------------------------
                        | FILTER
                        |--------------------------------------------------
                        */

                        const filter =
                            this.dataset.filter;


                        /*
                        |--------------------------------------------------
                        | BERITA UTAMA
                        |--------------------------------------------------
                        */

                        if (featuredNews) {

                            const category =
                                featuredNews.dataset.category;


                            if (
                                filter === "all" ||
                                category === filter
                            ) {

                                featuredNews.style.display =
                                    "";

                            } else {

                                featuredNews.style.display =
                                    "none";

                            }

                        }


                        /*
                        |--------------------------------------------------
                        | BERITA LAIN
                        |--------------------------------------------------
                        */

                        newsItems.forEach(
                            function (item) {

                                const category =
                                    item.dataset.category;


                                if (
                                    filter === "all" ||
                                    category === filter
                                ) {

                                    item.style.display =
                                        "";

                                } else {

                                    item.style.display =
                                        "none";

                                }

                            }
                        );

                    }
                );

            }
        );

    }
);

</script>


</body>

</html>