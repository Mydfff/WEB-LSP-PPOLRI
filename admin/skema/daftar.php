<?php
// ==========================================================
// DAFTAR SKEMA SERTIFIKASI - ADMIN LSP PPPOLRI
// File: admin/skema/daftar.php
// ==========================================================

session_start();

// ==========================================================
// CEK LOGIN
// ==========================================================
if (!isset($_SESSION["admin_id"])) {
    header("Location: ../../auth/login.php");
    exit;
}

// ==========================================================
// KONEKSI DATABASE
// ==========================================================
require_once "../../config/database.php";

// ==========================================================
// PAGE INFORMATION
// ==========================================================
$pageTitle = "Skema Sertifikasi";
$pageSubtitle = "Kelola data skema sertifikasi LSP PPPOLRI";

// ==========================================================
// AMBIL DATA SKEMA
// ==========================================================
$stmt = $pdo->query("
    SELECT *
    FROM skema
    ORDER BY id ASC
");

$skemaList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================================================
// TOTAL SKEMA
// ==========================================================
$totalSkema = count($skemaList);

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
        Skema Sertifikasi | LSP SEKURITI PPPOLRI
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
         SIDEBAR
    ====================================================== -->
    <?php require_once "../components/sidebar.php"; ?>


    <!-- =====================================================
         MAIN AREA
    ====================================================== -->
    <div class="admin-main">

        <!-- =================================================
             HEADER
        ================================================== -->
        <?php require_once "../components/header.php"; ?>


        <!-- =================================================
             SKEMA CONTENT
        ================================================== -->
        <main class="dashboard-content">

            <!-- =================================================
                 PAGE HEADER
            ================================================== -->
            <section class="dashboard-welcome">

                <span class="dashboard-label">
                    SERTIFIKASI
                </span>

                <h1>
                    Skema Sertifikasi
                </h1>

                <p>
                    Kelola data skema sertifikasi yang tersedia
                    pada LSP PPPOLRI.
                </p>

            </section>


            <!-- =================================================
                 TOOLBAR
            ================================================== -->
            <section class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <span class="card-label">
                            DATA SKEMA
                        </span>

                        <h3>
                            Daftar Skema Sertifikasi
                        </h3>

                    </div>

                    <a
                        href="tambah.php"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-plus-lg"></i>
                        Tambah Skema
                    </a>

                </div>


                <!-- =================================================
                     INFO TOTAL
                ================================================== -->
                <div class="mb-4">

                    <span class="text-muted">
                        Total skema:
                    </span>

                    <strong>
                        <?php echo $totalSkema; ?>
                    </strong>

                </div>


                <!-- =================================================
                     TABLE
                ================================================== -->
                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                            <tr>

                                <th width="60">
                                    No
                                </th>

                                <th>
                                    Nama Skema
                                </th>

                                <th>
                                    Kode Skema
                                </th>

                                <th>
                                    Acuan / Standar
                                </th>

                                <th width="120">
                                    Status
                                </th>

                                <th width="180">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php if (!empty($skemaList)): ?>

                            <?php foreach ($skemaList as $index => $skema): ?>

                                <tr>

                                    <!-- NO -->
                                    <td>
                                        <?php echo $index + 1; ?>
                                    </td>


                                    <!-- NAMA SKEMA -->
                                    <td>

                                        <strong>
                                            <?php
                                            echo htmlspecialchars(
                                                $skema['nama_skema']
                                            );
                                            ?>
                                        </strong>

                                    </td>


                                    <!-- KODE -->
                                    <td>

                                        <?php if (!empty($skema['kode_skema'])): ?>

                                            <span class="badge bg-secondary">
                                                <?php
                                                echo htmlspecialchars(
                                                    $skema['kode_skema']
                                                );
                                                ?>
                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                -
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- ACUAN -->
                                    <td>

                                        <div
                                            style="
                                                max-width: 400px;
                                                font-size: 13px;
                                                line-height: 1.5;
                                            "
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $skema['acuan']
                                            );
                                            ?>

                                        </div>

                                    </td>


                                    <!-- STATUS -->
                                    <td>

                                        <?php if ($skema['status'] === 'aktif'): ?>

                                            <span class="badge bg-success">
                                                Aktif
                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-secondary">
                                                Nonaktif
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- AKSI -->
                                    <td>

                                        <div class="d-flex gap-2">

                                            <a
                                                href="detail.php?id=<?php echo $skema['id']; ?>"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Detail"
                                            >
                                                <i class="bi bi-eye"></i>
                                            </a>


                                            <a
                                                href="edit.php?id=<?php echo $skema['id']; ?>"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Edit"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </a>


                                            <a
                                                href="hapus.php?id=<?php echo $skema['id']; ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Hapus"
                                                onclick="return confirm('Yakin ingin menghapus skema ini?');"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center py-5"
                                >

                                    <i
                                        class="bi bi-award"
                                        style="font-size: 40px;"
                                    ></i>

                                    <p class="mt-3 mb-0">
                                        Belum ada data skema sertifikasi.
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
             FOOTER
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