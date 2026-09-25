
<?php

// ==========================================================
// DATA PESERTA - ADMIN LSP PPPOLRI
// File: admin/peserta/daftar.php
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

$pageTitle = "Data Peserta";
$pageSubtitle = "Kelola dan lihat data peserta LSP PPPOLRI";

// ==========================================================
// FILTER SEARCH
// ==========================================================

$search = trim($_GET["search"] ?? "");

// ==========================================================
// STATISTIK PESERTA
// ==========================================================

// Total seluruh akun peserta
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM akun_peserta
");

$totalPeserta = (int) $stmt->fetchColumn();

// Total peserta aktif
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM akun_peserta
    WHERE status = 'aktif'
");

$totalAktif = (int) $stmt->fetchColumn();

// Total peserta yang sudah melakukan pendaftaran
$stmt = $pdo->query("
    SELECT COUNT(DISTINCT peserta_id)
    FROM pendaftaran
    WHERE peserta_id IS NOT NULL
");

$totalSudahMendaftar = (int) $stmt->fetchColumn();

// ==========================================================
// QUERY DATA PESERTA
// ==========================================================
//
// Satu peserta hanya ditampilkan satu kali.
// Data pendaftaran yang digunakan adalah pendaftaran terakhir.
//
// ==========================================================

$sql = "
    SELECT
        ap.id,
        ap.email,
        ap.status,
        ap.created_at AS akun_created_at,
        p.id AS pendaftaran_id,
        p.nama_lengkap,
        p.nik,
        p.no_hp,
        p.nomor_pendaftaran,
        p.status AS status_pendaftaran,
        p.created_at AS pendaftaran_created_at,
        s.nama_skema
    FROM akun_peserta ap
    LEFT JOIN pendaftaran p
        ON p.id = (
            SELECT p2.id
            FROM pendaftaran p2
            WHERE p2.peserta_id = ap.id
            ORDER BY p2.created_at DESC, p2.id DESC
            LIMIT 1
        )
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
            ap.email LIKE ?
            OR p.nama_lengkap LIKE ?
            OR p.nik LIKE ?
            OR p.no_hp LIKE ?
            OR p.nomor_pendaftaran LIKE ?
        )
    ";

    $keyword = "%" . $search . "%";

    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
}

// ==========================================================
// ORDER
// ==========================================================

$sql .= "
    ORDER BY
        COALESCE(p.created_at, ap.created_at) DESC,
        ap.id DESC
";

// ==========================================================
// EKSEKUSI QUERY
// ==========================================================

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$peserta = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================================================
// HELPER STATUS AKUN
// ==========================================================

function getPesertaStatusLabel($status)
{
    switch ($status) {
        case "aktif":
            return "Aktif";

        case "nonaktif":
            return "Nonaktif";

        default:
            return ucfirst($status);
    }
}

function getPesertaStatusClass($status)
{
    switch ($status) {
        case "aktif":
            return "peserta-status-active";

        case "nonaktif":
            return "peserta-status-inactive";

        default:
            return "peserta-status-inactive";
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

    <title>Data Peserta | LSP PPPOLRI</title>

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

    <!-- =====================================================
         CSS DATA PESERTA
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../../assets/css/admin-peserta.css"
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

        <main class="peserta-content">

            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <section class="page-heading">

                <span class="page-label">
                    DATA PESERTA
                </span>

                <h1>
                    Data Peserta
                </h1>

                <p>
                    Kelola dan lihat data peserta yang terdaftar
                    pada LSP PPPOLRI.
                </p>

            </section>

            <!-- =================================================
                 STATISTICS
            ================================================== -->

            <section class="peserta-statistics">

                <!-- Total Peserta -->

                <div class="peserta-stat-card">

                    <div class="peserta-stat-info">

                        <span>
                            Total Peserta
                        </span>

                        <strong>
                            <?= $totalPeserta; ?>
                        </strong>

                    </div>

                    <div class="peserta-stat-icon">
                        <i class="bi bi-people"></i>
                    </div>

                </div>

                <!-- Peserta Aktif -->

                <div class="peserta-stat-card">

                    <div class="peserta-stat-info">

                        <span>
                            Peserta Aktif
                        </span>

                        <strong>
                            <?= $totalAktif; ?>
                        </strong>

                    </div>

                    <div class="peserta-stat-icon">
                        <i class="bi bi-person-check"></i>
                    </div>

                </div>

                <!-- Sudah Mendaftar -->

                <div class="peserta-stat-card">

                    <div class="peserta-stat-info">

                        <span>
                            Sudah Mendaftar
                        </span>

                        <strong>
                            <?= $totalSudahMendaftar; ?>
                        </strong>

                    </div>

                    <div class="peserta-stat-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>

                </div>

            </section>

            <!-- =================================================
                 FILTER
            ================================================== -->

            <form
                method="GET"
                action=""
                class="peserta-filter"
            >

                <!-- Search -->

                <div class="peserta-search">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        name="search"
                        placeholder="Cari nama / email / NIK / nomor HP..."
                        value="<?= htmlspecialchars($search); ?>"
                    >

                </div>

                <!-- Filter Button -->

                <button
                    type="submit"
                    class="btn-peserta-filter"
                >

                    <i class="bi bi-search me-1"></i>
                    Cari

                </button>

                <?php if ($search !== ""): ?>

                    <a
                        href="daftar.php"
                        class="btn-peserta-reset"
                    >

                        <i class="bi bi-arrow-counterclockwise"></i>
                        Reset

                    </a>

                <?php endif; ?>

            </form>

            <!-- =================================================
                 TABLE
            ================================================== -->

            <section class="peserta-table-card">

                <!-- Table Header -->

                <div class="peserta-table-header">

                    <div>

                        <span class="card-label">
                            PESERTA
                        </span>

                        <h3>
                            Daftar Peserta
                        </h3>

                    </div>

                    <span>
                        <?= count($peserta); ?> data ditemukan
                    </span>

                </div>

                <!-- Table -->

                <div class="peserta-table-wrapper">

                    <?php if (!empty($peserta)): ?>

                        <table class="peserta-table">

                            <thead>

                                <tr>

                                    <th>No</th>

                                    <th>Nama Peserta</th>

                                    <th>Email</th>

                                    <th>No. HP</th>

                                    <th>Skema Terakhir</th>

                                    <th>Status</th>

                                    <th>Aksi</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($peserta as $index => $data): ?>

                                    <tr>

                                        <!-- No -->

                                        <td>
                                            <?= $index + 1; ?>
                                        </td>

                                        <!-- Nama -->

                                        <td>

                                            <span class="peserta-name">

                                                <?= htmlspecialchars(
                                                    $data["nama_lengkap"] ?? "Belum ada data"
                                                ); ?>

                                            </span>

                                        </td>

                                        <!-- Email -->

                                        <td>

                                            <span class="peserta-email">

                                                <?= htmlspecialchars(
                                                    $data["email"]
                                                ); ?>

                                            </span>

                                        </td>

                                        <!-- No HP -->

                                        <td>

                                            <?= htmlspecialchars(
                                                $data["no_hp"] ?? "-"
                                            ); ?>

                                        </td>

                                        <!-- Skema -->

                                        <td>

                                            <span class="peserta-scheme">

                                                <?= htmlspecialchars(
                                                    $data["nama_skema"] ?? "-"
                                                ); ?>

                                            </span>

                                        </td>

                                        <!-- Status -->

                                        <td>

                                            <span
                                                class="peserta-status <?= getPesertaStatusClass(
                                                    $data["status"]
                                                ); ?>"
                                            >

                                                <?= htmlspecialchars(
                                                    getPesertaStatusLabel(
                                                        $data["status"]
                                                    )
                                                ); ?>

                                            </span>

                                        </td>

                                        <!-- Aksi -->

                                        <td>

                                            <div class="peserta-actions">

                                                <!-- Detail -->

                                                <a
                                                    href="detail.php?id=<?= (int) $data["id"]; ?>"
                                                    class="btn-peserta-detail"
                                                    title="Detail Peserta"
                                                >

                                                    <i class="bi bi-eye"></i>
                                                    Detail

                                                </a>

                                                <!-- Hapus -->

                                                <button
                                                    type="button"
                                                    class="btn-admin-delete"
                                                    title="Hapus Peserta"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalHapusPeserta"
                                                    data-peserta-id="<?= (int) $data["id"]; ?>"
                                                    data-peserta-nama="<?= htmlspecialchars(
                                                        $data["nama_lengkap"] ?? $data["email"],
                                                        ENT_QUOTES,
                                                        "UTF-8"
                                                    ); ?>"
                                                >

                                                    <i class="bi bi-trash-fill"></i>

                                                </button>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    <?php else: ?>

                        <!-- =================================================
                             DATA KOSONG
                        ================================================== -->

                        <div class="peserta-empty">

                            <i class="bi bi-people"></i>

                            <h4>
                                Belum Ada Data Peserta
                            </h4>

                            <p>

                                <?php if ($search !== ""): ?>

                                    Tidak ditemukan peserta yang sesuai
                                    dengan pencarian.

                                <?php else: ?>

                                    Belum terdapat data peserta
                                    yang terdaftar.

                                <?php endif; ?>

                            </p>

                        </div>

                    <?php endif; ?>

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
     MODAL HAPUS PESERTA
========================================================== -->

<div
    class="modal fade"
    id="modalHapusPeserta"
    tabindex="-1"
    aria-labelledby="modalHapusPesertaLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="modalHapusPesertaLabel"
                >
                    Konfirmasi Hapus
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>

            </div>

            <div class="modal-body text-center">

                <div class="mb-3">

                    <i
                        class="bi bi-trash-fill"
                        style="font-size: 42px;"
                    ></i>

                </div>

                <h5>
                    Hapus Peserta?
                </h5>

                <p class="text-muted mb-0">

                    Apakah Anda yakin ingin menghapus peserta
                    <strong id="namaPesertaHapus"></strong>?

                </p>

                <p class="text-muted small mt-2 mb-0">

                    Data akun peserta akan dihapus dan
                    tidak dapat dikembalikan.

                </p>

            </div>

            <div class="modal-footer justify-content-center">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Tidak
                </button>

                <a
                    href="#"
                    id="btnKonfirmasiHapusPeserta"
                    class="btn btn-danger"
                >

                    <i class="bi bi-trash-fill me-1"></i>
                    Hapus

                </a>

            </div>

        </div>

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

<!-- =========================================================
     MODAL HAPUS PESERTA JS
========================================================== -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const modalHapus =
        document.getElementById("modalHapusPeserta");

    const namaPesertaHapus =
        document.getElementById("namaPesertaHapus");

    const btnKonfirmasiHapus =
        document.getElementById("btnKonfirmasiHapusPeserta");

    if (!modalHapus) {
        return;
    }

    modalHapus.addEventListener(
        "show.bs.modal",
        function (event) {

            const button = event.relatedTarget;

            const pesertaId =
                button.getAttribute("data-peserta-id");

            const pesertaNama =
                button.getAttribute("data-peserta-nama");

            namaPesertaHapus.textContent =
                pesertaNama;

            btnKonfirmasiHapus.href =
                "hapus.php?id=" + pesertaId;

        }
    );

});

</script>

</body>
</html>