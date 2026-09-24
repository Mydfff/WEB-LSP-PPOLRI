<?php
require_once "../../config/database.php";

/*
|--------------------------------------------------------------------------
| Ambil Data Skema Sertifikasi
|--------------------------------------------------------------------------
| Mengambil skema yang berstatus aktif dari database.
*/

$stmt = $pdo->prepare("
    SELECT 
        id,
        nama_skema,
        deskripsi
    FROM skema
    WHERE status = 'aktif'
    ORDER BY id ASC
");

$stmt->execute();
$skemaList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Pendaftaran Sertifikasi - LSP PPPOLRI</title>

    <!-- =====================================================
         GOOGLE FONT
    ====================================================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

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
         SWIPER
    ====================================================== -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
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
         CSS PAGE PENDAFTARAN
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
         MAIN CONTENT
    ====================================================== -->

    <main class="pendaftaran-page">

        <div class="container">

            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <div class="pendaftaran-header text-center">

                <span class="pendaftaran-subtitle">
                    PENDAFTARAN SERTIFIKASI
                </span>

                <h1>
                    Pilih Skema Sertifikasi
                </h1>

                <p>
                    Pilih skema sertifikasi yang sesuai dengan
                    kompetensi dan latar belakang Anda untuk
                    melanjutkan proses pendaftaran.
                </p>

            </div>


            <!-- =================================================
                 PROGRESS
            ================================================== -->

            <div class="registration-progress">

                <div class="progress-item active">

                    <div class="progress-number">
                        1
                    </div>

                    <span>
                        Pilih Skema
                    </span>

                </div>


                <div class="progress-line"></div>


                <div class="progress-item">

                    <div class="progress-number">
                        2
                    </div>

                    <span>
                        Buat Akun
                    </span>

                </div>


                <div class="progress-line"></div>


                <div class="progress-item">

                    <div class="progress-number">
                        3
                    </div>

                    <span>
                        Isi & Unggah
                    </span>

                </div>


                <div class="progress-line"></div>


                <div class="progress-item">

                    <div class="progress-number">
                        4
                    </div>

                    <span>
                        Verifikasi
                    </span>

                </div>


                <div class="progress-line"></div>


                <div class="progress-item">

                    <div class="progress-number">
                        5
                    </div>

                    <span>
                        Jadwal Asesmen
                    </span>

                </div>


                <div class="progress-line"></div>


                <div class="progress-item">

                    <div class="progress-number">
                        6
                    </div>

                    <span>
                        Keputusan
                    </span>

                </div>


                <div class="progress-line"></div>


                <div class="progress-item">

                    <div class="progress-number">
                        7
                    </div>

                    <span>
                        Sertifikat
                    </span>

                </div>

            </div>


            <!-- =================================================
                 INFORMASI
            ================================================== -->

            <div class="registration-info">

                <div class="registration-info-icon">
                    <i class="bi bi-info-circle-fill"></i>
                </div>

                <div>

                    <h5>
                        Sebelum melakukan pendaftaran
                    </h5>

                    <p>
                        Pastikan Anda memilih skema sertifikasi
                        yang sesuai. Data dan dokumen yang
                        diberikan pada proses pendaftaran akan
                        digunakan untuk proses verifikasi
                        administrasi oleh LSP PPPOLRI.
                    </p>

                </div>

            </div>


            <!-- =================================================
                 DAFTAR SKEMA
            ================================================== -->

            <div class="row g-4">

                <?php if (!empty($skemaList)): ?>

                    <?php foreach ($skemaList as $skema): ?>

                        <div class="col-lg-4 col-md-6">

                            <div class="skema-registration-card">

                                <div class="skema-card-icon">

                                    <i class="bi bi-patch-check-fill"></i>

                                </div>


                                <div class="skema-card-content">

                                    <h3>
                                        <?= htmlspecialchars(
                                            $skema['nama_skema']
                                        ); ?>
                                    </h3>

                                    <?php if (!empty($skema['deskripsi'])): ?>

                                        <p>
                                            <?= htmlspecialchars(
                                                $skema['deskripsi']
                                            ); ?>
                                        </p>

                                    <?php else: ?>

                                        <p>
                                            Skema sertifikasi kompetensi
                                            LSP PPPOLRI.
                                        </p>

                                    <?php endif; ?>


                                    <a
                                        href="akun.php?skema_id=<?= (int) $skema['id']; ?>"
                                        class="btn btn-pilih-skema"
                                    >
                                        Pilih Skema
                                        <i class="bi bi-arrow-right"></i>
                                    </a>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <!-- =========================================
                         EMPTY STATE
                    ========================================== -->

                    <div class="col-12">

                        <div class="skema-empty">

                            <i class="bi bi-folder-x"></i>

                            <h3>
                                Belum Ada Skema
                            </h3>

                            <p>
                                Belum terdapat skema sertifikasi
                                yang tersedia untuk pendaftaran.
                            </p>

                        </div>

                    </div>

                <?php endif; ?>

            </div>


            <!-- =================================================
                 CATATAN
            ================================================== -->

            <div class="registration-note">

                <i class="bi bi-shield-check"></i>

                <p>
                    Pastikan data yang Anda masukkan sesuai dengan
                    dokumen resmi. Setiap pendaftaran akan melalui
                    proses verifikasi administrasi sebelum peserta
                    dapat melanjutkan ke tahap berikutnya.
                </p>

            </div>

        </div>

    </main>


    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <div id="footer"></div>


    <!-- =====================================================
         JAVASCRIPT
    ====================================================== -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
    </script>

    <script
        src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js">
    </script>

    <script src="../../assets/js/include.js"></script>

    <script src="../../assets/js/navbar.js"></script>

    <script src="../../assets/js/main.js"></script>

</body>

</html>