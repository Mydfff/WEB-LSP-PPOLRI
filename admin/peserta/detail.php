
<?php

// ==========================================================
// DETAIL PESERTA - ADMIN LSP PPPOLRI
// File: admin/peserta/detail.php
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
// DATABASE
// ==========================================================

require_once "../../config/database.php";

// ==========================================================
// DATA ADMIN & PAGE
// ==========================================================

$adminNama = $_SESSION["admin_nama"] ?? "Administrator";
$adminRole = $_SESSION["admin_role"] ?? "Administrator";

$pageTitle = "Detail Peserta";
$pageSubtitle = "Informasi lengkap dan riwayat pendaftaran peserta";

// ==========================================================
// VALIDASI ID PESERTA
// ==========================================================

$pesertaId = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$pesertaId || $pesertaId <= 0) {
    header("Location: daftar.php");
    exit;
}

// ==========================================================
// HELPER
// ==========================================================

function e($value)
{
    return htmlspecialchars(
        (string) ($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

function tampil($value)
{
    return $value !== null && trim((string) $value) !== ""
        ? e($value)
        : "-";
}

function tanggalIndonesia($tanggal)
{
    if (empty($tanggal)) {
        return "-";
    }

    $timestamp = strtotime($tanggal);

    if ($timestamp === false) {
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

    return date("j", $timestamp) . " " .
        $bulan[(int) date("n", $timestamp)] . " " .
        date("Y", $timestamp);
}

function statusPendaftaranLabel($status)
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
            return ucfirst((string) $status);
    }
}

function statusPendaftaranClass($status)
{
    switch ($status) {
        case "diajukan":
            return "peserta-status-pending";

        case "verifikasi":
            return "peserta-status-verification";

        case "disetujui":
            return "peserta-status-approved";

        case "ditolak":
            return "peserta-status-rejected";

        default:
            return "peserta-status-pending";
    }
}

// ==========================================================
// AMBIL AKUN PESERTA
// ==========================================================

$stmt = $pdo->prepare("
    SELECT
        id,
        email,
        status,
        created_at,
        updated_at
    FROM akun_peserta
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$pesertaId]);

$akun = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$akun) {
    header("Location: daftar.php");
    exit;
}

// ==========================================================
// AMBIL DATA PENDAFTARAN TERAKHIR
// ==========================================================

$stmt = $pdo->prepare("
    SELECT
        p.*,
        s.nama_skema
    FROM pendaftaran p

    LEFT JOIN skema s
        ON s.id = p.skema_id

    WHERE p.peserta_id = ?

    ORDER BY
        p.created_at DESC,
        p.id DESC

    LIMIT 1
");

$stmt->execute([$pesertaId]);

$data = $stmt->fetch(PDO::FETCH_ASSOC);

// ==========================================================
// AMBIL SELURUH RIWAYAT PENDAFTARAN
// ==========================================================

$stmt = $pdo->prepare("
    SELECT
        p.id,
        p.nomor_pendaftaran,
        p.status,
        p.created_at,
        s.nama_skema

    FROM pendaftaran p

    LEFT JOIN skema s
        ON s.id = p.skema_id

    WHERE p.peserta_id = ?

    ORDER BY
        p.created_at DESC,
        p.id DESC
");

$stmt->execute([$pesertaId]);

$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPendaftaran = count($riwayat);

// ==========================================================
// HITUNG PENDAFTARAN DISETUJUI
// ==========================================================

$totalDisetujui = 0;

foreach ($riwayat as $item) {
    if ($item["status"] === "disetujui") {
        $totalDisetujui++;
    }
}

// ==========================================================
// DATA TAMPILAN
// ==========================================================

$namaPeserta = $data["nama_lengkap"] ?? "Belum mengisi formulir";

$statusAkun = strtolower($akun["status"] ?? "");

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Detail Peserta | LSP PPPOLRI</title>

    <!-- BOOTSTRAP -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- BOOTSTRAP ICONS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <!-- GOOGLE FONT -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- ADMIN CSS -->
    <link
        rel="stylesheet"
        href="../../assets/css/admin.css"
    >

    <!-- CSS KHUSUS DATA PESERTA -->
    <link
        rel="stylesheet"
        href="../../assets/css/admin-peserta.css"
    >

</head>

<body>

<div class="admin-wrapper">

    <!-- SIDEBAR -->
    <?php require_once "../components/sidebar.php"; ?>

    <div class="admin-main">

        <!-- HEADER -->
        <?php require_once "../components/header.php"; ?>

        <main class="peserta-content">

            <!-- ==========================================
                 PAGE HEADING
            =========================================== -->

            <section class="page-heading">

                <span class="page-label">
                    DATA PESERTA
                </span>

                <h1>Detail Peserta</h1>

                <p>
                    Informasi lengkap dan riwayat
                    pendaftaran peserta LSP PPPOLRI.
                </p>

            </section>

            <!-- ==========================================
                 BACK BUTTON
            =========================================== -->

            <div class="peserta-detail-navigation">

                <a
                    href="daftar.php"
                    class="btn-peserta-back"
                >
                    <i class="bi bi-arrow-left"></i>
                    Kembali ke Data Peserta
                </a>

            </div>

            <!-- ==========================================
                 PROFILE CARD
            =========================================== -->

            <section class="peserta-profile-card">

                <div class="peserta-profile-avatar">
                    <i class="bi bi-person"></i>
                </div>

                <div class="peserta-profile-info">

                    <span class="card-label">
                        PROFIL PESERTA
                    </span>

                    <h2>
                        <?= e($namaPeserta); ?>
                    </h2>

                    <p>
                        <i class="bi bi-envelope"></i>
                        <?= e($akun["email"]); ?>
                    </p>

                    <span class="peserta-status <?= $statusAkun === "aktif"
                        ? "peserta-status-active"
                        : "peserta-status-inactive"; ?>">

                        <?= $statusAkun === "aktif"
                            ? "Aktif"
                            : "Nonaktif"; ?>

                    </span>

                </div>

            </section>

            <!-- ==========================================
                 STATISTIK PESERTA
            =========================================== -->

            <section class="peserta-detail-statistics">

                <div class="peserta-stat-card">

                    <div class="peserta-stat-info">

                        <span>Total Pendaftaran</span>

                        <strong>
                            <?= $totalPendaftaran; ?>
                        </strong>

                    </div>

                    <div class="peserta-stat-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>

                </div>

                <div class="peserta-stat-card">

                    <div class="peserta-stat-info">

                        <span>Pendaftaran Disetujui</span>

                        <strong>
                            <?= $totalDisetujui; ?>
                        </strong>

                    </div>

                    <div class="peserta-stat-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>

                </div>

            </section>

            <!-- ==========================================
                 DATA AKUN
            =========================================== -->

            <section class="peserta-detail-card">

                <div class="peserta-detail-card-header">

                    <div>
                        <span class="card-label">
                            INFORMASI AKUN
                        </span>

                        <h3>Data Akun</h3>
                    </div>

                    <i class="bi bi-person-circle"></i>

                </div>

                <div class="peserta-detail-grid">

                    <div class="peserta-detail-field">

                        <span>Email</span>

                        <strong>
                            <?= e($akun["email"]); ?>
                        </strong>

                    </div>

                    <div class="peserta-detail-field">

                        <span>Status Akun</span>

                        <strong>
                            <?= ucfirst(e($akun["status"])); ?>
                        </strong>

                    </div>

                    <div class="peserta-detail-field">

                        <span>Tanggal Akun Dibuat</span>

                        <strong>
                            <?= tanggalIndonesia($akun["created_at"]); ?>
                        </strong>

                    </div>

                </div>

            </section>

            <!-- ==========================================
                 DATA PRIBADI
            =========================================== -->

            <section class="peserta-detail-card">

                <div class="peserta-detail-card-header">

                    <div>
                        <span class="card-label">
                            INFORMASI PESERTA
                        </span>

                        <h3>Data Pribadi</h3>
                    </div>

                    <i class="bi bi-person-vcard"></i>

                </div>

                <?php if ($data): ?>

                    <div class="peserta-detail-grid">

                        <div class="peserta-detail-field">

                            <span>Nama Lengkap</span>

                            <strong>
                                <?= tampil($data["nama_lengkap"]); ?>
                            </strong>

                        </div>

                        <div class="peserta-detail-field">

                            <span>NIK</span>

                            <strong>
                                <?= tampil($data["nik"]); ?>
                            </strong>

                        </div>

                        <div class="peserta-detail-field">

                            <span>Tempat Lahir</span>

                            <strong>
                                <?= tampil($data["tempat_lahir"]); ?>
                            </strong>

                        </div>

                        <div class="peserta-detail-field">

                            <span>Tanggal Lahir</span>

                            <strong>
                                <?= tanggalIndonesia($data["tanggal_lahir"]); ?>
                            </strong>

                        </div>

                        <div class="peserta-detail-field">

                            <span>Jenis Kelamin</span>

                            <strong>
                                <?= tampil($data["jenis_kelamin"]); ?>
                            </strong>

                        </div>

                        <div class="peserta-detail-field">

                            <span>Kebangsaan</span>

                            <strong>
                                <?= tampil($data["kebangsaan"]); ?>
                            </strong>

                        </div>

                        <div class="peserta-detail-field">

                            <span>Nomor HP</span>

                            <strong>
                                <?= tampil($data["no_hp"]); ?>
                            </strong>

                        </div>

                        <div class="peserta-detail-field">

                            <span>Kode Pos</span>

                            <strong>
                                <?= tampil($data["kode_pos"]); ?>
                            </strong>

                        </div>

                        <div class="peserta-detail-field peserta-field-full">

                            <span>Alamat Rumah</span>

                            <strong>
                                <?= tampil($data["alamat_rumah"]); ?>
                            </strong>

                        </div>

                    </div>

                <?php else: ?>

                    <div class="peserta-detail-empty">

                        <i class="bi bi-info-circle"></i>

                        <p>
                            Peserta sudah membuat akun,
                            tetapi belum mengisi formulir pendaftaran.
                        </p>

                    </div>

                <?php endif; ?>

            </section>

            <!-- ==========================================
                 DATA PENDIDIKAN
            =========================================== -->

            <?php if ($data): ?>

                <section class="peserta-detail-card">

                    <div class="peserta-detail-card-header">

                        <div>
                            <span class="card-label">
                                INFORMASI PENDIDIKAN
                            </span>

                            <h3>Data Pendidikan</h3>
                        </div>

                        <i class="bi bi-mortarboard"></i>

                    </div>

                    <div class="peserta-detail-grid">

                        <div class="peserta-detail-field">

                            <span>Nama Institusi</span>

                            <strong>
                                <?= tampil($data["nama_institusi"]); ?>
                            </strong>

                        </div>

                        <div class="peserta-detail-field">

                            <span>Jurusan</span>

                            <strong>
                                <?= tampil($data["jurusan"]); ?>
                            </strong>

                        </div>

                        <div class="peserta-detail-field">

                            <span>Strata Pendidikan</span>

                            <strong>
                                <?= tampil($data["strata"]); ?>
                            </strong>

                        </div>

                        <div class="peserta-detail-field">

                            <span>Tahun Lulus</span>

                            <strong>
                                <?= tampil($data["tahun_lulus"]); ?>
                            </strong>

                        </div>

                        <div class="peserta-detail-field">

                            <span>Nama Sertifikat</span>

                            <strong>
                                <?= tampil($data["nama_sertifikat"]); ?>
                            </strong>

                        </div>

                    </div>

                </section>

                <!-- ======================================
                     DATA PEKERJAAN
                ======================================= -->

                <section class="peserta-detail-card">

                    <div class="peserta-detail-card-header">

                        <div>
                            <span class="card-label">
                                INFORMASI PEKERJAAN
                            </span>

                            <h3>Data Pekerjaan</h3>
                        </div>

                        <i class="bi bi-briefcase"></i>

                    </div>

                    <div class="peserta-detail-grid">

                        <div class="peserta-detail-field">

                            <span>Nama Perusahaan</span>

                            <strong>
                                <?= tampil($data["nama_perusahaan"]); ?>
                            </strong>

                        </div>

                        <div class="peserta-detail-field">

                            <span>Jabatan</span>

                            <strong>
                                <?= tampil($data["jabatan"]); ?>
                            </strong>

                        </div>

                        <div class="peserta-detail-field">

                            <span>Telepon / Fax Perusahaan</span>

                            <strong>
                                <?= tampil($data["telp_fax_perusahaan"]); ?>
                            </strong>

                        </div>

                        <div class="peserta-detail-field peserta-field-full">

                            <span>Alamat Perusahaan</span>

                            <strong>
                                <?= tampil($data["alamat_perusahaan"]); ?>
                            </strong>

                        </div>

                    </div>

                </section>

            <?php endif; ?>

            <!-- ==========================================
                 RIWAYAT PENDAFTARAN
            =========================================== -->

            <section class="peserta-table-card">

                <div class="peserta-table-header">

                    <div>

                        <span class="card-label">
                            SERTIFIKASI
                        </span>

                        <h3>
                            Riwayat Pendaftaran
                        </h3>

                    </div>

                    <span>
                        <?= $totalPendaftaran; ?> pendaftaran
                    </span>

                </div>

                <div class="peserta-table-wrapper">

                    <?php if (!empty($riwayat)): ?>

                        <table class="peserta-table">

                            <thead>

                                <tr>
                                    <th>No</th>
                                    <th>Nomor Pendaftaran</th>
                                    <th>Skema Sertifikasi</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($riwayat as $index => $item): ?>

                                    <tr>

                                        <td>
                                            <?= $index + 1; ?>
                                        </td>

                                        <td>

                                            <span class="peserta-registration-number">

                                                <?= e($item["nomor_pendaftaran"]); ?>

                                            </span>

                                        </td>

                                        <td>

                                            <span class="peserta-scheme">

                                                <?= tampil($item["nama_skema"]); ?>

                                            </span>

                                        </td>

                                        <td>

                                            <?= tanggalIndonesia(
                                                $item["created_at"]
                                            ); ?>

                                        </td>

                                        <td>

                                            <span class="peserta-status <?= statusPendaftaranClass(
                                                $item["status"]
                                            ); ?>">

                                                <?= e(statusPendaftaranLabel(
                                                    $item["status"]
                                                )); ?>

                                            </span>

                                        </td>

                                        <td>

                                            <a
                                                href="../sertifikasi/pendaftaran/detail.php?id=<?= (int) $item["id"]; ?>"
                                                class="btn-peserta-detail"
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

                        <div class="peserta-detail-empty">

                            <i class="bi bi-inbox"></i>

                            <h4>Belum Ada Pendaftaran</h4>

                            <p>
                                Peserta belum melakukan
                                pendaftaran sertifikasi.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </section>

        </main>

        <!-- FOOTER -->
        <?php require_once "../components/footer.php"; ?>

    </div>

</div>

<!-- BOOTSTRAP JS -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>

<!-- ADMIN JS -->
<script src="../../assets/js/admin.js"></script>

</body>
</html>