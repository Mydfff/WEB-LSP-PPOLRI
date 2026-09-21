<?php
// ==========================================================
// DETAIL SKEMA SERTIFIKASI - ADMIN LSP PPPOLRI
// File: admin/skema/detail.php
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
// AMBIL ID SKEMA
// ==========================================================
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: daftar.php");
    exit;
}

// ==========================================================
// AMBIL DATA SKEMA
// ==========================================================
$stmt = $pdo->prepare("
    SELECT *
    FROM skema
    WHERE id = :id
");

$stmt->execute([
    ":id" => $id
]);

$skema = $stmt->fetch(PDO::FETCH_ASSOC);

// ==========================================================
// CEK DATA
// ==========================================================
if (!$skema) {
    header("Location: daftar.php");
    exit;
}

// ==========================================================
// AMBIL UNIT KOMPETENSI
// ==========================================================
$stmtUnit = $pdo->prepare("
    SELECT *
    FROM unit_skema
    WHERE skema_id = :skema_id
    ORDER BY id ASC
");

$stmtUnit->execute([
    ":skema_id" => $id
]);

$unitList = $stmtUnit->fetchAll(PDO::FETCH_ASSOC);

// ==========================================================
// PAGE INFORMATION
// ==========================================================
$pageTitle = "Detail Skema";
$pageSubtitle = "Detail informasi skema sertifikasi";

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
        Detail Skema | LSP PPPOLRI
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
             CONTENT
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
                    Detail Skema Sertifikasi
                </h1>

                <p>
                    Informasi lengkap mengenai skema sertifikasi
                    yang dipilih.
                </p>

            </section>


            <!-- =================================================
                 INFORMASI SKEMA
            ================================================== -->
            <section class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <span class="card-label">
                            INFORMASI SKEMA
                        </span>

                        <h3>
                            <?php
                            echo htmlspecialchars(
                                $skema["nama_skema"]
                            );
                            ?>
                        </h3>

                    </div>

                    <div class="d-flex gap-2">

                        <a
                            href="edit.php?id=<?php echo $skema["id"]; ?>"
                            class="btn btn-warning"
                        >
                            <i class="bi bi-pencil me-1"></i>
                            Edit
                        </a>

                        <a
                            href="daftar.php"
                            class="btn btn-secondary"
                        >
                            <i class="bi bi-arrow-left me-1"></i>
                            Kembali
                        </a>

                    </div>

                </div>


                <!-- =================================================
                     DATA SKEMA
                ================================================== -->
                <div class="row g-4">

                    <!-- KODE -->
                    <div class="col-md-6">

                        <div class="border rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                Kode Skema
                            </small>

                            <strong>

                                <?php if (!empty($skema["kode_skema"])): ?>

                                    <?php
                                    echo htmlspecialchars(
                                        $skema["kode_skema"]
                                    );
                                    ?>

                                <?php else: ?>

                                    <span class="text-muted">
                                        Belum tersedia
                                    </span>

                                <?php endif; ?>

                            </strong>

                        </div>

                    </div>


                    <!-- STATUS -->
                    <div class="col-md-6">

                        <div class="border rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                Status
                            </small>

                            <?php if ($skema["status"] === "aktif"): ?>

                                <span class="badge bg-success">
                                    Aktif
                                </span>

                            <?php else: ?>

                                <span class="badge bg-secondary">
                                    Nonaktif
                                </span>

                            <?php endif; ?>

                        </div>

                    </div>


                    <!-- ACUAN -->
                    <div class="col-12">

                        <div class="border rounded p-3">

                            <small class="text-muted d-block mb-2">
                                Acuan / Standar
                            </small>

                            <div style="line-height: 1.7;">

                                <?php if (!empty($skema["acuan"])): ?>

                                    <?php
                                    echo nl2br(
                                        htmlspecialchars(
                                            $skema["acuan"]
                                        )
                                    );
                                    ?>

                                <?php else: ?>

                                    <span class="text-muted">
                                        Belum ada acuan / standar.
                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>


                    <!-- DESKRIPSI -->
                    <div class="col-12">

                        <div class="border rounded p-3">

                            <small class="text-muted d-block mb-2">
                                Deskripsi
                            </small>

                            <div style="line-height: 1.7;">

                                <?php if (!empty($skema["deskripsi"])): ?>

                                    <?php
                                    echo nl2br(
                                        htmlspecialchars(
                                            $skema["deskripsi"]
                                        )
                                    );
                                    ?>

                                <?php else: ?>

                                    <span class="text-muted">
                                        Belum ada deskripsi.
                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 UNIT KOMPETENSI
            ================================================== -->
            <section class="dashboard-card mt-4">

                <div class="dashboard-card-header">

                    <div>

                        <span class="card-label">
                            KOMPETENSI
                        </span>

                        <h3>
                            Unit Kompetensi
                        </h3>

                    </div>

                    <a
                        href="unit-tambah.php?skema_id=<?php echo $skema["id"]; ?>"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-plus-lg me-1"></i>
                        Tambah Unit
                    </a>

                </div>


                <!-- =================================================
                     TABLE UNIT
                ================================================== -->
                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                            <tr>

                                <th width="60">
                                    No
                                </th>

                                <th width="220">
                                    Kode Unit
                                </th>

                                <th>
                                    Judul Unit Kompetensi
                                </th>

                                <th width="100">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php if (!empty($unitList)): ?>

                            <?php foreach ($unitList as $index => $unit): ?>

                                <tr>

                                    <td>
                                        <?php echo $index + 1; ?>
                                    </td>

                                    <td>

                                        <strong>
                                            <?php
                                            echo htmlspecialchars(
                                                $unit["kode_unit"]
                                            );
                                            ?>
                                        </strong>

                                    </td>

                                    <td>

                                        <?php
                                        echo htmlspecialchars(
                                            $unit["judul_unit"]
                                        );
                                        ?>

                                    </td>

                                    <td>

                                        <div class="d-flex gap-2">

                                            <a
                                                href="unit-edit.php?id=<?php echo $unit["id"]; ?>"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Edit Unit"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <a
                                                href="unit-hapus.php?id=<?php echo $unit["id"]; ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Hapus Unit"
                                                onclick="return confirm('Yakin ingin menghapus unit kompetensi ini?');"
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
                                    colspan="4"
                                    class="text-center py-5"
                                >

                                    <i
                                        class="bi bi-journal-text"
                                        style="font-size: 40px;"
                                    ></i>

                                    <p class="mt-3 mb-0">
                                        Belum ada unit kompetensi
                                        untuk skema ini.
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