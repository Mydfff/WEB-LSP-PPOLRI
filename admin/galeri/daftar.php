
<?php

// ==========================================================
// DAFTAR GALERI - LSP PPPOLRI
// File: admin/galeri/daftar.php
// ==========================================================

session_start();

// ==========================================================
// CEK LOGIN
// ==========================================================

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

// ==========================================================
// KONEKSI DATABASE
// ==========================================================

require_once "../../config/database.php";

// ==========================================================
// DATA ADMIN
// ==========================================================

$adminNama = $_SESSION["admin_nama"] ?? "Administrator";
$adminRole = $_SESSION["admin_role"] ?? "Administrator";

// ==========================================================
// PAGE INFORMATION
// ==========================================================

$pageTitle = "Galeri";
$pageSubtitle = "Kelola dokumentasi kegiatan LSP PPPOLRI";

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
// AMBIL DATA GALERI
// ==========================================================

$galeriList = [];
$databaseError = "";

try {

    $stmt = $pdo->query("
        SELECT
            id,
            judul,
            deskripsi,
            created_at
        FROM galeri
        ORDER BY created_at DESC
    ");

    $galeriList = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $databaseError = "Data galeri belum tersedia atau tabel galeri belum dibuat.";

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

    <title>
        Galeri | LSP PPPOLRI
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
        href="../../assets/css/admin.css"
    >

</head>

<body>

<div class="admin-wrapper">

    <!-- =====================================================
         SIDEBAR COMPONENT
    ====================================================== -->

    <?php require_once "../components/sidebar.php"; ?>


    <!-- =====================================================
         MAIN AREA
    ====================================================== -->

    <div class="admin-main">

        <!-- =================================================
             HEADER COMPONENT
        ================================================== -->

        <?php require_once "../components/header.php"; ?>


        <!-- =================================================
             CONTENT GALERI
        ================================================== -->

        <main class="dashboard-content">

            <!-- =================================================
                 HEADER HALAMAN
            ================================================== -->

            <section class="dashboard-welcome">

                <span class="dashboard-label">
                    CONTENT MANAGEMENT
                </span>

                <h1>
                    Galeri
                </h1>

                <p>
                    Kelola dokumentasi kegiatan dan informasi
                    visual LSP PPPOLRI.
                </p>

            </section>


            <!-- =================================================
                 INFORMASI
            ================================================== -->

            <section class="dashboard-card">

                <div class="d-flex align-items-center gap-3">

                    <div
                        class="d-flex align-items-center justify-content-center"
                        style="
                            width:52px;
                            height:52px;
                            border-radius:12px;
                            background:#f1f3f5;
                        "
                    >

                        <i
                            class="bi bi-images fs-4"
                        ></i>

                    </div>

                    <div>

                        <span class="card-label">
                            GALERI LSP PPPOLRI
                        </span>

                        <h3 class="mb-1">
                            Dokumentasi Kegiatan
                        </h3>

                        <p class="mb-0 text-muted">
                            Halaman ini digunakan untuk melihat
                            dokumentasi kegiatan LSP PPPOLRI
                            yang tersimpan pada sistem.
                        </p>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 DAFTAR GALERI
            ================================================== -->

            <section class="dashboard-card mt-4">

                <div
                    class="d-flex justify-content-between align-items-center flex-wrap gap-3"
                >

                    <div>

                        <span class="card-label">
                            GALERI
                        </span>

                        <h3 class="mb-0">
                            Daftar Dokumentasi
                        </h3>

                    </div>

                    <!--
                        Untuk sementara tombol upload belum dibuat
                        karena fitur galeri dibatasi sesuai budget.
                    -->

                    <span class="badge text-bg-secondary">
                        Fitur dasar
                    </span>

                </div>


                <!-- =================================================
                     PESAN ERROR DATABASE
                ================================================== -->

                <?php if ($databaseError !== ""): ?>

                    <div
                        class="alert alert-warning mt-4"
                        role="alert"
                    >

                        <i class="bi bi-info-circle me-2"></i>

                        <?php echo e($databaseError); ?>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                     TABLE
                ================================================== -->

                <div class="table-responsive mt-4">

                    <table class="table align-middle">

                        <thead>

                            <tr>

                                <th style="width:70px;">
                                    No
                                </th>

                                <th>
                                    Judul Dokumentasi
                                </th>

                                <th>
                                    Deskripsi
                                </th>

                                <th>
                                    Tanggal
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php if (!empty($galeriList)): ?>

                                <?php foreach ($galeriList as $index => $galeri): ?>

                                    <tr>

                                        <td>
                                            <?php echo $index + 1; ?>
                                        </td>

                                        <td>

                                            <strong>
                                                <?php
                                                echo e(
                                                    $galeri["judul"]
                                                );
                                                ?>
                                            </strong>

                                        </td>

                                        <td>

                                            <?php
                                            echo e(
                                                $galeri["deskripsi"] ?? "-"
                                            );
                                            ?>

                                        </td>

                                        <td>

                                            <?php
                                            echo date(
                                                "d M Y",
                                                strtotime(
                                                    $galeri["created_at"]
                                                )
                                            );
                                            ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>

                                    <td
                                        colspan="4"
                                        class="text-center py-5"
                                    >

                                        <i
                                            class="bi bi-images fs-1 d-block mb-3 text-muted"
                                        ></i>

                                        <strong>
                                            Belum Ada Dokumentasi
                                        </strong>

                                        <p class="mb-0 text-muted">
                                            Data dokumentasi kegiatan
                                            belum tersedia.
                                        </p>

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>

        </main>


        <!-- =====================================================
             FOOTER COMPONENT
        ====================================================== -->

        <?php require_once "../components/footer.php"; ?>

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
    src="../../assets/js/admin.js"
></script>

</body>

</html>

