<?php

// ==========================================================
// PENDAFTARAN SERTIFIKASI - ADMIN LSP PPPOLRI
// File: admin/sertifikasi/pendaftaran/daftar.php
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
// KONEKSI DATABASE
// ==========================================================

require_once "../../../config/database.php";


// ==========================================================
// DATA ADMIN
// ==========================================================

$adminNama = $_SESSION["admin_nama"] ?? "Administrator";
$adminRole = $_SESSION["admin_role"] ?? "Administrator";


// ==========================================================
// PAGE INFORMATION
// ==========================================================

$pageTitle = "Pendaftaran Sertifikasi";
$pageSubtitle = "Kelola dan periksa data pendaftaran sertifikasi peserta";


// ==========================================================
// FILTER
// ==========================================================

$search = trim($_GET["search"] ?? "");
$status = trim($_GET["status"] ?? "");


// ==========================================================
// STATISTIK
// ==========================================================

// Total pendaftaran
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM pendaftaran
");

$totalPendaftaran = (int) $stmt->fetchColumn();


// Total menunggu verifikasi
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM pendaftaran
    WHERE status = 'diajukan'
");

$totalMenunggu = (int) $stmt->fetchColumn();


// Total disetujui
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM pendaftaran
    WHERE status = 'disetujui'
");

$totalDisetujui = (int) $stmt->fetchColumn();


// ==========================================================
// QUERY PENDAFTARAN
// ==========================================================

$sql = "
    SELECT
        p.id,
        p.nomor_pendaftaran,
        p.nama_lengkap,
        p.skema_id,
        p.status,
        p.created_at,
        s.nama_skema
    FROM pendaftaran p

    LEFT JOIN skema s
        ON s.id = p.skema_id

    WHERE 1 = 1
";

$params = [];


// ==========================================================
// SEARCH
// ==========================================================

if ($search !== "") {

    $sql .= "
        AND (
            p.nomor_pendaftaran LIKE ?
            OR p.nama_lengkap LIKE ?
        )
    ";

    $keyword = "%" . $search . "%";

    $params[] = $keyword;
    $params[] = $keyword;

}


// ==========================================================
// FILTER STATUS
// ==========================================================

$allowedStatus = [
    "diajukan",
    "verifikasi",
    "disetujui",
    "ditolak"
];

if (
    $status !== ""
    && in_array($status, $allowedStatus, true)
) {

    $sql .= "
        AND p.status = ?
    ";

    $params[] = $status;

}


// ==========================================================
// ORDER
// ==========================================================

$sql .= "
    ORDER BY p.created_at DESC
";


// ==========================================================
// EKSEKUSI
// ==========================================================

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$pendaftaran = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ==========================================================
// HELPER STATUS
// ==========================================================

function getStatusLabel($status)
{
    switch ($status) {

        case "diajukan":
            return "Diajukan";

        case "verifikasi":
            return "Sedang Diverifikasi";

        case "disetujui":
            return "Disetujui";

        case "ditolak":
            return "Ditolak";

        default:
            return ucfirst($status);

    }
}


function getStatusClass($status)
{
    switch ($status) {

        case "diajukan":
            return "status-pending";

        case "verifikasi":
            return "status-verifikasi";

        case "disetujui":
            return "status-approved";

        case "ditolak":
            return "status-rejected";

        default:
            return "status-pending";

    }
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
        Pendaftaran Sertifikasi | LSP PPPOLRI
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


    <!-- =====================================================
         CSS HALAMAN
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../../../assets/css/admin-pendaftaran-daftar.css"
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

        <main class="pendaftaran-content">


            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <section class="page-heading">

                <span class="page-label">
                    SERTIFIKASI
                </span>

                <h1>
                    Pendaftaran Sertifikasi
                </h1>

                <p>
                    Kelola dan periksa data pendaftaran sertifikasi peserta.
                </p>

            </section>


            <!-- =================================================
                 STATISTICS
            ================================================== -->

            <section class="registration-statistics">


                <!-- Total Pendaftaran -->

                <div class="registration-stat-card">

                    <div class="registration-stat-info">

                        <span>
                            Total Pendaftaran
                        </span>

                        <strong>
                            <?= $totalPendaftaran; ?>
                        </strong>

                    </div>

                    <div class="registration-stat-icon">

                        <i class="bi bi-file-earmark-text"></i>

                    </div>

                </div>


                <!-- Menunggu Verifikasi -->

                <div class="registration-stat-card">

                    <div class="registration-stat-info">

                        <span>
                            Menunggu Verifikasi
                        </span>

                        <strong>
                            <?= $totalMenunggu; ?>
                        </strong>

                    </div>

                    <div class="registration-stat-icon">

                        <i class="bi bi-hourglass-split"></i>

                    </div>

                </div>


                <!-- Disetujui -->

                <div class="registration-stat-card">

                    <div class="registration-stat-info">

                        <span>
                            Disetujui
                        </span>

                        <strong>
                            <?= $totalDisetujui; ?>
                        </strong>

                    </div>

                    <div class="registration-stat-icon">

                        <i class="bi bi-check-circle"></i>

                    </div>

                </div>


            </section>


            <!-- =================================================
                 FILTER
            ================================================== -->

            <form
                method="GET"
                action=""
                class="registration-filter"
            >


                <!-- Search -->

                <div class="registration-search">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        name="search"
                        placeholder="Cari peserta / nomor pendaftaran..."
                        value="<?= htmlspecialchars($search); ?>"
                    >

                </div>


                <!-- Status -->

                <select name="status">

                    <option value="">
                        Semua Status
                    </option>

                    <option
                        value="diajukan"
                        <?= $status === "diajukan" ? "selected" : ""; ?>
                    >
                        Diajukan
                    </option>

                    <option
                        value="verifikasi"
                        <?= $status === "verifikasi" ? "selected" : ""; ?>
                    >
                        Sedang Diverifikasi
                    </option>

                    <option
                        value="disetujui"
                        <?= $status === "disetujui" ? "selected" : ""; ?>
                    >
                        Disetujui
                    </option>

                    <option
                        value="ditolak"
                        <?= $status === "ditolak" ? "selected" : ""; ?>
                    >
                        Ditolak
                    </option>

                </select>


                <!-- Filter Button -->

                <button
                    type="submit"
                    class="btn-filter"
                >

                    <i class="bi bi-funnel me-1"></i>

                    Filter

                </button>


            </form>


            <!-- =================================================
                 TABLE
            ================================================== -->

            <section class="registration-table-card">


                <!-- Table Header -->

                <div class="registration-table-header">

                    <h3>
                        Daftar Pendaftar
                    </h3>

                    <span>
                        <?= count($pendaftaran); ?> data ditemukan
                    </span>

                </div>


                <div class="registration-table-wrapper">


                    <?php if (!empty($pendaftaran)): ?>


                        <table class="registration-table">


                            <thead>

                                <tr>

                                    <th>
                                        No
                                    </th>

                                    <th>
                                        Nomor Pendaftaran
                                    </th>

                                    <th>
                                        Nama Peserta
                                    </th>

                                    <th>
                                        Skema Sertifikasi
                                    </th>

                                    <th>
                                        Tanggal
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th>
                                        Aksi
                                    </th>

                                </tr>

                            </thead>


                            <tbody>


                                <?php foreach ($pendaftaran as $index => $data): ?>


                                    <tr>


                                        <!-- No -->

                                        <td>
                                            <?= $index + 1; ?>
                                        </td>


                                        <!-- Nomor Pendaftaran -->

                                        <td>

                                            <span class="registration-number">

                                                <?= htmlspecialchars(
                                                    $data["nomor_pendaftaran"]
                                                ); ?>

                                            </span>

                                        </td>


                                        <!-- Nama -->

                                        <td>

                                            <span class="participant-name">

                                                <?= htmlspecialchars(
                                                    $data["nama_lengkap"]
                                                ); ?>

                                            </span>

                                        </td>


                                        <!-- Skema -->

                                        <td>

                                            <span class="scheme-name">

                                                <?= htmlspecialchars(
                                                    $data["nama_skema"] ?? "-"
                                                ); ?>

                                            </span>

                                        </td>


                                        <!-- Tanggal -->

                                        <td>

                                            <?= date(
                                                "d M Y",
                                                strtotime($data["created_at"])
                                            ); ?>

                                        </td>


                                        <!-- Status -->

                                        <td>

                                            <span
                                                class="registration-status
                                                <?= getStatusClass(
                                                    $data["status"]
                                                ); ?>"
                                            >

                                                <?= htmlspecialchars(
                                                    getStatusLabel(
                                                        $data["status"]
                                                    )
                                                ); ?>

                                            </span>

                                        </td>


                                        <!-- Aksi -->

                                        <td>

                                            <a
                                                href="detail.php?id=<?= (int) $data["id"]; ?>"
                                                class="btn-detail"
                                            >

                                                <i class="bi bi-eye"></i>

                                                Detail

                                            </a>

                                        </td>


                                    </tr>


                                <?php endforeach; ?>


                            </tbody>


                        </table>


                    <?php else: ?>


                        <!-- =================================================
                             DATA KOSONG
                        ================================================== -->

                        <div class="registration-empty">

                            <i class="bi bi-inbox"></i>

                            <h4>
                                Belum Ada Pendaftaran
                            </h4>

                            <p>
                                Belum terdapat data pendaftaran
                                sertifikasi yang sesuai.
                            </p>

                        </div>


                    <?php endif; ?>


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