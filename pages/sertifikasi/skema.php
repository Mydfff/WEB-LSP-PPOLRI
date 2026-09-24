<?php
// ==========================================================
// HALAMAN DAFTAR SKEMA SERTIFIKASI
// LSP PPPOLRI
// ==========================================================

// Koneksi database
require_once "../../config/database.php";

// ==========================================================
// AMBIL DATA SKEMA DARI DATABASE
// HANYA MENAMPILKAN SKEMA YANG AKTIF
// ==========================================================
$stmt = $pdo->prepare("
    SELECT *
    FROM skema
    WHERE status = 'aktif'
    ORDER BY id ASC
");

$stmt->execute();

$skemaList = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ==========================================================
// ICON UNTUK SETIAP CARD
// ==========================================================
$icons = [
    "bi-shield-check",
    "bi-person-badge",
    "bi-award",
    "bi-briefcase",
    "bi-diagram-3",
    "bi-patch-check"
];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar Skema Sertifikasi - LSP PPPOLRI</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    >

    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- CSS Global -->
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/style.css">

    <!-- CSS Halaman Skema -->
    <link rel="stylesheet" href="../../assets/css/skema.css">

    <!-- Responsive -->
    <link rel="stylesheet" href="../../assets/css/responsive.css">

</head>

<body>

    <!-- ==================================================
         TOP HEADER
         ================================================== -->
    <div id="top-header"></div>


    <!-- ==================================================
         NAVBAR
         ================================================== -->
    <div id="navbar"></div>


    <!-- ==================================================
         CONTENT
         ================================================== -->
    <main>

        <section class="skema-section">

            <div class="container">

                <!-- Header -->
                <div class="skema-header">

                    <span class="section-label">
                        Sertifikasi Profesi
                    </span>

                    <h1>
                        DAFTAR SKEMA SERTIFIKASI OKUPASI
                    </h1>

                    <p>
                        Pilih skema sertifikasi yang sesuai dengan
                        kompetensi dan kebutuhan sertifikasi Anda.
                    </p>

                </div>


                <!-- ==================================================
                     GRID SKEMA
                     ================================================== -->

                <div class="row skema-grid g-4">

                    <?php if (count($skemaList) > 0): ?>

                        <?php foreach ($skemaList as $index => $skema): ?>

                            <?php
                            // Menentukan icon berdasarkan urutan card
                            $icon = $icons[$index] ?? "bi-patch-check";
                            ?>

                            <div class="col-lg-4 col-md-6">

                                <div class="skema-card">

                                    <!-- Nomor Skema -->
                                    <span class="skema-number">
                                        SKEMA <?php echo str_pad($index + 1, 2, "0", STR_PAD_LEFT); ?>
                                    </span>


                                    <!-- Icon -->
                                    <div class="skema-icon">

                                        <i class="bi <?php echo htmlspecialchars($icon); ?>"></i>

                                    </div>


                                    <!-- Nama Skema -->
                                    <h3>

                                        <?php
                                        echo htmlspecialchars($skema["nama_skema"]);
                                        ?>

                                    </h3>


                                    <!-- Deskripsi -->
                                    <p>

                                        <?php

                                        if (!empty($skema["deskripsi"])) {

                                            echo htmlspecialchars($skema["deskripsi"]);

                                        } else {

                                            echo "Informasi skema sertifikasi LSP PPPOLRI.";

                                        }

                                        ?>

                                    </p>


                                    <!-- Detail -->
                                    <a
                                        href="detail-skema.php?id=<?php echo $skema["id"]; ?>"
                                        class="skema-btn"
                                    >

                                        Lihat Detail

                                        <i class="bi bi-arrow-right"></i>

                                    </a>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <!-- Jika belum ada data -->

                        <div class="col-12">

                            <div class="text-center py-5">

                                <i
                                    class="bi bi-info-circle fs-1 text-muted"
                                ></i>

                                <h4 class="mt-3">
                                    Belum Ada Skema Sertifikasi
                                </h4>

                                <p class="text-muted">
                                    Data skema sertifikasi belum tersedia.
                                </p>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </section>

    </main>


    <!-- ==================================================
         FOOTER
         ================================================== -->
    <div id="footer"></div>


    <!-- ==================================================
         JAVASCRIPT
         ================================================== -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
    ></script>

    <script src="../../assets/js/include.js"></script>
    <script src="../../assets/js/navbar.js"></script>
    <script src="../../assets/js/main.js"></script>

</body>

</html>