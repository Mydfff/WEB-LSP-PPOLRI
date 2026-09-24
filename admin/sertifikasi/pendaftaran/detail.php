<?php

// ==========================================================
// DETAIL PENDAFTARAN SERTIFIKASI - ADMIN LSP PPPOLRI
// File: admin/sertifikasi/pendaftaran/detail.php
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
require_once "../../../config/mailer.php";


// ==========================================================
// DATA ADMIN
// ==========================================================

$adminNama = $_SESSION["admin_nama"] ?? "Administrator";
$adminRole = $_SESSION["admin_role"] ?? "Administrator";


// ==========================================================
// AMBIL ID PENDAFTARAN
// ==========================================================

$pendaftaranId = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;

if ($pendaftaranId <= 0) {

    header("Location: daftar.php");
    exit;

}


/* ==========================================================
   PROSES SETUJUI / TOLAK PENDAFTARAN
========================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";


    /* ======================================================
       AMBIL DATA PENDAFTARAN + EMAIL PESERTA
    ====================================================== */

    $stmtAction = $pdo->prepare("
        SELECT
            p.id,
            p.nomor_pendaftaran,
            p.nama_lengkap,
            p.status,
            s.nama_skema,
            ap.email
        FROM pendaftaran p

        LEFT JOIN skema s
            ON s.id = p.skema_id

        LEFT JOIN akun_peserta ap
            ON ap.id = p.peserta_id

        WHERE p.id = ?

        LIMIT 1
    ");

    $stmtAction->execute([
        $pendaftaranId
    ]);

    $dataAction = $stmtAction->fetch(PDO::FETCH_ASSOC);


    if (!$dataAction) {

        die("Data pendaftaran tidak ditemukan.");

    }


    /* ======================================================
       SETUJUI PENDAFTARAN
    ====================================================== */

    if ($action === "approve") {

                // ======================================================
            // CEK SEMUA DOKUMEN HARUS VALID
            // ======================================================

            $stmtCekDokumen = $pdo->prepare("
                SELECT
                    COUNT(*) AS total,
                    SUM(
                        CASE
                            WHEN status_verifikasi = 'valid'
                            THEN 1
                            ELSE 0
                        END
                    ) AS valid
                FROM dokumen_pendaftaran
                WHERE pendaftaran_id = ?
            ");

            $stmtCekDokumen->execute([
                $pendaftaranId
            ]);

            $cekDokumen = $stmtCekDokumen->fetch(
                PDO::FETCH_ASSOC
            );

            $totalDokumenApprove =
                (int) ($cekDokumen["total"] ?? 0);

            $jumlahValidApprove =
                (int) ($cekDokumen["valid"] ?? 0);


            // ======================================================
            // JIKA BELUM SEMUA VALID
            // ======================================================

            if (
                $totalDokumenApprove === 0 ||
                $jumlahValidApprove !== $totalDokumenApprove
            ) {

                header(
                    "Location: detail.php?id=" .
                    $pendaftaranId .
                    "&error=document"
                );

                exit;
            }


            // ======================================================
            // SEMUA DOKUMEN VALID
            // LANJUT SETUJUI PENDAFTARAN
            // ======================================================

            $stmtApprove = $pdo->prepare("
                UPDATE pendaftaran
                SET
                    status = 'disetujui',
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmtApprove->execute([
                $pendaftaranId
            ]);

        $stmtApprove = $pdo->prepare("

            UPDATE pendaftaran

            SET
                status = 'disetujui',
                updated_at = NOW()

            WHERE id = ?

        ");

        $stmtApprove->execute([
            $pendaftaranId
        ]);


        /* ==================================================
           KIRIM EMAIL KE PESERTA
        ================================================== */

        if (!empty($dataAction["email"])) {

            $subjekEmail =
                "Pendaftaran Sertifikasi Disetujui - " .
                $dataAction["nomor_pendaftaran"];


            $isiEmail = "

            <!DOCTYPE html>

            <html lang='id'>

            <head>

                <meta charset='UTF-8'>

                <title>
                    Pendaftaran Sertifikasi Disetujui
                </title>

            </head>


            <body
                style='
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                '
            >

                <h2 style='color: #72000e;'>
                    Pendaftaran Sertifikasi Disetujui
                </h2>


                <p>
                    Yth.
                    <strong>
                        " . htmlspecialchars(
                            $dataAction["nama_lengkap"]
                        ) . "
                    </strong>
                </p>


                <p>
                    Kami informasikan bahwa pendaftaran sertifikasi
                    Anda telah <strong>disetujui</strong> oleh
                    LSP PPPOLRI.
                </p>


                <table
                    cellpadding='8'
                    cellspacing='0'
                    style='border-collapse: collapse;'
                >

                    <tr>

                        <td>
                            <strong>Nomor Pendaftaran</strong>
                        </td>

                        <td>
                            :
                            " . htmlspecialchars(
                                $dataAction["nomor_pendaftaran"]
                            ) . "
                        </td>

                    </tr>


                    <tr>

                        <td>
                            <strong>Nama Peserta</strong>
                        </td>

                        <td>
                            :
                            " . htmlspecialchars(
                                $dataAction["nama_lengkap"]
                            ) . "
                        </td>

                    </tr>


                    <tr>

                        <td>
                            <strong>Skema Sertifikasi</strong>
                        </td>

                        <td>
                            :
                            " . htmlspecialchars(
                                $dataAction["nama_skema"] ?? "-"
                            ) . "
                        </td>

                    </tr>


                    <tr>

                        <td>
                            <strong>Status</strong>
                        </td>

                        <td>
                            :
                            Disetujui
                        </td>

                    </tr>

                </table>


                <br>


                <p>
                    Silakan memantau informasi selanjutnya
                    melalui website LSP PPPOLRI.
                </p>


                <p>
                    Terima kasih atas partisipasi Anda.
                </p>


                <p>
                    <strong>LSP PPPOLRI</strong>
                </p>

            </body>

            </html>

            ";


            kirimEmail(
                $dataAction["email"],
                $subjekEmail,
                $isiEmail
            );

        }


        header(
            "Location: detail.php?id=" .
            $pendaftaranId .
            "&success=approved"
        );

        exit;

    }


    /* ======================================================
       TOLAK PENDAFTARAN
    ====================================================== */

    if ($action === "reject") {

        $stmtReject = $pdo->prepare("

            UPDATE pendaftaran

            SET
                status = 'ditolak',
                updated_at = NOW()

            WHERE id = ?

        ");

        $stmtReject->execute([
            $pendaftaranId
        ]);


        /* ==================================================
           KIRIM EMAIL KE PESERTA
        ================================================== */

        if (!empty($dataAction["email"])) {

            $subjekEmail =
                "Pendaftaran Sertifikasi Ditolak - " .
                $dataAction["nomor_pendaftaran"];


            $isiEmail = "

            <!DOCTYPE html>

            <html lang='id'>

            <head>

                <meta charset='UTF-8'>

                <title>
                    Pendaftaran Sertifikasi Ditolak
                </title>

            </head>


            <body
                style='
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                '
            >

                <h2 style='color: #72000e;'>
                    Pendaftaran Sertifikasi
                </h2>


                <p>
                    Yth.
                    <strong>
                        " . htmlspecialchars(
                            $dataAction["nama_lengkap"]
                        ) . "
                    </strong>
                </p>


                <p>
                    Kami informasikan bahwa pendaftaran sertifikasi
                    Anda dengan nomor pendaftaran:
                </p>


                <p>

                    <strong>
                        " . htmlspecialchars(
                            $dataAction["nomor_pendaftaran"]
                        ) . "
                    </strong>

                </p>


                <p>
                    setelah melalui proses pemeriksaan oleh
                    admin LSP PPPOLRI, saat ini berstatus:
                </p>


                <p>

                    <strong>
                        DITOLAK
                    </strong>

                </p>


                <p>
                    Untuk informasi mengenai alasan penolakan
                    dan proses selanjutnya, silakan menghubungi
                    LSP PPPOLRI.
                </p>


                <p>
                    <strong>Skema Sertifikasi:</strong><br>

                    " . htmlspecialchars(
                        $dataAction["nama_skema"] ?? "-"
                    ) . "
                </p>


                <br>


                <p>
                    Terima kasih.
                </p>


                <p>
                    <strong>LSP PPPOLRI</strong>
                </p>

            </body>

            </html>

            ";


            kirimEmail(
                $dataAction["email"],
                $subjekEmail,
                $isiEmail
            );

        }


        header(
            "Location: detail.php?id=" .
            $pendaftaranId .
            "&success=rejected"
        );

        exit;

    }

}


// ==========================================================
// QUERY DATA PENDAFTARAN
// ==========================================================

$stmt = $pdo->prepare("

    SELECT

        p.id,
        p.peserta_id,
        p.skema_id,
        p.nomor_pendaftaran,
        p.nik,
        p.nama_lengkap,
        p.tempat_lahir,
        p.tanggal_lahir,
        p.jenis_kelamin,
        p.kebangsaan,
        p.alamat_rumah,
        p.kode_pos,
        p.no_hp,
        p.nama_sertifikat,
        p.nama_institusi,
        p.jurusan,
        p.strata,
        p.tahun_lulus,
        p.nama_perusahaan,
        p.jabatan,
        p.alamat_perusahaan,
        p.telp_fax_perusahaan,
        p.tujuan_asesmen,
        p.status,
        p.created_at,
        p.updated_at,

        s.nama_skema,

        ap.email

    FROM pendaftaran p

    LEFT JOIN skema s
        ON s.id = p.skema_id

    LEFT JOIN akun_peserta ap
        ON ap.id = p.peserta_id

    WHERE p.id = ?

    LIMIT 1

");


$stmt->execute([
    $pendaftaranId
]);


$pendaftaran = $stmt->fetch(
    PDO::FETCH_ASSOC
);


// ==========================================================
// VALIDASI DATA
// ==========================================================

if (!$pendaftaran) {

    header("Location: daftar.php");
    exit;

}


// ==========================================================
// QUERY DOKUMEN
// ==========================================================

$stmtDokumen = $pdo->prepare("

    SELECT

        id,
        jenis_dokumen,
        nama_file_asli,
        nama_file_simpan,
        path_file,
        tipe_file,
        ukuran_file,
        status_verifikasi,
        catatan,
        created_at,
        updated_at

    FROM dokumen_pendaftaran

    WHERE pendaftaran_id = ?

    ORDER BY id ASC

");


$stmtDokumen->execute([
    $pendaftaranId
]);


$dokumenList = $stmtDokumen->fetchAll(
    PDO::FETCH_ASSOC
);

// ==========================================================
// RINGKASAN STATUS DOKUMEN
// ==========================================================

$totalDokumen = count($dokumenList);

$jumlahValid = 0;
$jumlahTidakValid = 0;
$jumlahMenunggu = 0;

foreach ($dokumenList as $doc) {

    $statusDokumen = $doc["status_verifikasi"] ?? "menunggu";

    if ($statusDokumen === "valid") {

        $jumlahValid++;

    } elseif ($statusDokumen === "tidak_valid") {

        $jumlahTidakValid++;

    } else {

        $jumlahMenunggu++;

    }
}

$semuaDokumenValid =
    $totalDokumen > 0 &&
    $jumlahValid === $totalDokumen;

// ==========================================================
// HELPER STATUS PENDAFTARAN
// ==========================================================

function getDetailStatusLabel($status)
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


function getDetailStatusClass($status)
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


// ==========================================================
// HELPER NAMA DOKUMEN
// ==========================================================

function getDokumenLabel($jenis)
{

    switch ($jenis) {

        case "ijazah_pendidikan":
            return "Ijazah Pendidikan";

        case "ijazah_pelatihan":
            return "Ijazah Pelatihan";

        case "kta_sekuriti":
            return "KTA Sekuriti";

        case "ktp":
            return "KTP";

        case "cv":
            return "CV";

        case "surat_rekomendasi":
            return "Surat Rekomendasi";

        case "pasfoto":
            return "Pasfoto";

        case "sertifikat_lama":
            return "Sertifikat Lama";

        case "surat_keterangan_kerja":
            return "Surat Keterangan Kerja";

        default:

            return ucwords(
                str_replace(
                    "_",
                    " ",
                    $jenis
                )
            );

    }

}


// ==========================================================
// PAGE INFORMATION
// ==========================================================

$pageTitle = "Detail Pendaftaran";

$pageSubtitle = $pendaftaran["nomor_pendaftaran"];

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
        Detail Pendaftaran | LSP PPPOLRI
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
         CSS HALAMAN DETAIL
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../../../assets/css/admin-pendaftaran-detail.css"
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

        <main class="detail-content">


            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <section class="detail-page-heading">

                <div>

                    <span class="detail-page-label">
                        SERTIFIKASI
                    </span>

                    <h1>
                        Detail Pendaftaran
                    </h1>

                    <p>
                        Periksa informasi dan dokumen peserta.
                    </p>

                </div>


                <div>

                    <span
                        class="detail-status-badge <?= htmlspecialchars(
                            getDetailStatusClass(
                                $pendaftaran["status"]
                            )
                        ); ?>"
                    >

                        <i class="bi bi-circle-fill"></i>

                        <?= htmlspecialchars(
                            getDetailStatusLabel(
                                $pendaftaran["status"]
                            )
                        ); ?>

                    </span>

                </div>

            </section>


            <!-- =================================================
                 NOMOR PENDAFTARAN
            ================================================== -->

            <section class="detail-number-card">

                <div>

                    <span>
                        Nomor Pendaftaran
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $pendaftaran["nomor_pendaftaran"]
                        ); ?>

                    </strong>

                </div>


                <div>

                    <span>
                        Tanggal Pendaftaran
                    </span>

                    <strong>

                        <?= date(
                            "d M Y",
                            strtotime(
                                $pendaftaran["created_at"]
                            )
                        ); ?>

                    </strong>

                </div>

            </section>


            <!-- =================================================
                 DATA PESERTA
            ================================================== -->

            <section class="detail-card">

                <div class="detail-card-header">

                    <h3>
                        DATA PESERTA
                    </h3>

                </div>


                <div class="detail-card-body">

                    <div class="detail-grid">


                        <div class="detail-item">

                            <span>
                                Nama Lengkap
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["nama_lengkap"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                NIK
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["nik"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                Tempat Lahir
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["tempat_lahir"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                Tanggal Lahir
                            </span>

                            <strong>

                                <?php if (!empty($pendaftaran["tanggal_lahir"])): ?>

                                    <?= date(
                                        "d-m-Y",
                                        strtotime(
                                            $pendaftaran["tanggal_lahir"]
                                        )
                                    ); ?>

                                <?php else: ?>

                                    -

                                <?php endif; ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                Jenis Kelamin
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["jenis_kelamin"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                Kebangsaan
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["kebangsaan"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                Email
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["email"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                No. HP
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["no_hp"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                        <div class="detail-item detail-item-full">

                            <span>
                                Alamat
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["alamat_rumah"] ?: "-"
                                ); ?>


                                <?php if (!empty($pendaftaran["kode_pos"])): ?>

                                    <br>

                                    Kode Pos:

                                    <?= htmlspecialchars(
                                        $pendaftaran["kode_pos"]
                                    ); ?>

                                <?php endif; ?>

                            </strong>

                        </div>


                    </div>

                </div>

            </section>


            <!-- =================================================
                 DATA PENDIDIKAN
            ================================================== -->

            <section class="detail-card">

                <div class="detail-card-header">

                    <h3>
                        DATA PENDIDIKAN
                    </h3>

                </div>


                <div class="detail-card-body">

                    <div class="detail-grid">


                        <div class="detail-item">

                            <span>
                                Nama Sertifikat
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["nama_sertifikat"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                Institusi
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["nama_institusi"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                Jurusan
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["jurusan"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                Strata
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["strata"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                Tahun Lulus
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["tahun_lulus"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                    </div>

                </div>

            </section>


            <!-- =================================================
                 DATA PEKERJAAN
            ================================================== -->

            <section class="detail-card">

                <div class="detail-card-header">

                    <h3>
                        DATA PEKERJAAN
                    </h3>

                </div>


                <div class="detail-card-body">

                    <div class="detail-grid">


                        <div class="detail-item">

                            <span>
                                Perusahaan
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["nama_perusahaan"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                Jabatan
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["jabatan"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                        <div class="detail-item detail-item-full">

                            <span>
                                Alamat Perusahaan
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["alamat_perusahaan"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                Telp / Fax
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["telp_fax_perusahaan"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                    </div>

                </div>

            </section>


            <!-- =================================================
                 DATA SERTIFIKASI
            ================================================== -->

            <section class="detail-card">

                <div class="detail-card-header">

                    <h3>
                        DATA SERTIFIKASI
                    </h3>

                </div>


                <div class="detail-card-body">

                    <div class="detail-grid">


                        <div class="detail-item detail-item-full">

                            <span>
                                Skema Sertifikasi
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["nama_skema"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                        <div class="detail-item detail-item-full">

                            <span>
                                Tujuan Asesmen
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $pendaftaran["tujuan_asesmen"] ?: "-"
                                ); ?>

                            </strong>

                        </div>


                    </div>

                </div>

            </section>


            <!-- =================================================
                 DOKUMEN
            ================================================== -->

            <section class="detail-card">

                <div class="detail-card-header">

                    <h3>
                        DOKUMEN PENDAFTARAN
                    </h3>

                    <span>
                        <?= count($dokumenList); ?> dokumen
                    </span>

                </div>


                <div class="detail-card-body detail-document-body">


                    <?php if (!empty($dokumenList)): ?>


                        <div class="document-table-wrapper">

                            <table class="document-table">

                                <thead>

                                    <tr>

                                        <th>
                                            No
                                        </th>

                                        <th>
                                            Dokumen
                                        </th>

                                        <th>
                                            Nama File
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


                                    <?php foreach (
                                        $dokumenList
                                        as $index => $dokumen
                                    ): ?>


                                        <tr>


                                            <td>

                                                <?= $index + 1; ?>

                                            </td>


                                            <td>

                                                <span class="document-name">

                                                    <?= htmlspecialchars(
                                                        getDokumenLabel(
                                                            $dokumen["jenis_dokumen"]
                                                        )
                                                    ); ?>

                                                </span>

                                            </td>


                                            <td>

                                                <span class="document-file">

                                                    <?= htmlspecialchars(
                                                        $dokumen["nama_file_asli"]
                                                    ); ?>

                                                </span>

                                            </td>


                                            <td>


                                                <?php

                                                $statusDokumen =
                                                    $dokumen[
                                                        "status_verifikasi"
                                                    ];

                                                ?>


                                                <span
                                                    class="document-status <?= htmlspecialchars(
                                                        $statusDokumen
                                                    ); ?>"
                                                >

                                                    <?php

                                                    if (
                                                        $statusDokumen
                                                        === "valid"
                                                    ) {

                                                        echo "Valid";

                                                    } elseif (
                                                        $statusDokumen
                                                        === "tidak_valid"
                                                    ) {

                                                        echo "Tidak Valid";

                                                    } else {

                                                        echo "Menunggu";

                                                    }

                                                    ?>

                                                </span>


                                            </td>


                                            <td>


                                                <?php if (
                                                    !empty(
                                                        $dokumen["path_file"]
                                                    )
                                                ): ?>


                                                    <a
                                                        href="../../../<?= htmlspecialchars(
                                                            $dokumen["path_file"]
                                                        ); ?>"
                                                        target="_blank"
                                                        class="btn-document"
                                                    >
                                                        <i class="bi bi-eye"></i>
                                                        Lihat
                                                    </a>

                                                    <a
                                                        href="verifikasi.php?id=<?= (int) $dokumen["id"]; ?>"
                                                        class="btn-document"
                                                    >
                                                        <i class="bi bi-shield-check"></i>
                                                        Verifikasi
                                                    </a>
                                                

                                                <?php else: ?>


                                                    <span class="text-muted">

                                                        File tidak tersedia

                                                    </span>


                                                <?php endif; ?>


                                            </td>


                                        </tr>


                                    <?php endforeach; ?>


                                </tbody>

                            </table>

                        </div>
                        <?php if (!empty($dokumenList)): ?>

                        <div class="mt-4">

                            <div class="detail-card-header">
                                <h3>
                                    RINGKASAN VERIFIKASI
                                </h3>
                            </div>

                            <div class="row g-3 mt-1">

                                <div class="col-md-3">

                                    <div class="border rounded p-3">

                                        <span class="text-muted d-block">
                                            Total Dokumen
                                        </span>

                                        <strong class="fs-4">
                                            <?= $totalDokumen; ?>
                                        </strong>

                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <div class="border rounded p-3">

                                        <span class="text-muted d-block">
                                            Valid
                                        </span>

                                        <strong class="fs-4 text-success">
                                            <?= $jumlahValid; ?>
                                        </strong>

                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <div class="border rounded p-3">

                                        <span class="text-muted d-block">
                                            Menunggu
                                        </span>

                                        <strong class="fs-4 text-warning">
                                            <?= $jumlahMenunggu; ?>
                                        </strong>

                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <div class="border rounded p-3">

                                        <span class="text-muted d-block">
                                            Tidak Valid
                                        </span>

                                        <strong class="fs-4 text-danger">
                                            <?= $jumlahTidakValid; ?>
                                        </strong>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endif; ?>


                    <?php else: ?>


                        <div class="document-empty">

                            <i class="bi bi-inbox"></i>

                            <h4>
                                Belum Ada Dokumen
                            </h4>

                            <p>
                                Belum terdapat dokumen yang diunggah peserta.
                            </p>

                        </div>


                    <?php endif; ?>


                </div>

            </section>


            <!-- =================================================
                 CATATAN ADMIN
            ================================================== -->

            <section class="detail-card">

                <div class="detail-card-header">

                    <h3>
                        CATATAN ADMIN
                    </h3>

                </div>


                <div class="detail-card-body">

                    <textarea
                        class="admin-note-textarea"
                        name="catatan_admin"
                        placeholder="Tambahkan catatan admin..."
                    ></textarea>

                </div>

            </section>


            <!-- =================================================
                 ACTION
            ================================================== -->

            <section class="detail-actions">


                <a
                    href="daftar.php"
                    class="detail-btn detail-btn-back"
                >

                    <i class="bi bi-arrow-left"></i>

                    Kembali

                </a>


                <form method="POST" action="">

                    <input
                        type="hidden"
                        name="action"
                        value="reject"
                    >

                    <button
                        type="button"
                        class="detail-btn detail-btn-reject"
                        onclick="openConfirmModal('reject')"
                    >

                        <i class="bi bi-x-circle"></i>

                        Tolak

                    </button>

                </form>


                <form method="POST" action="">

                <input
                    type="hidden"
                    name="action"
                    value="approve"
                >

                <?php if ($semuaDokumenValid): ?>

                    <button
                        type="button"
                        class="detail-btn detail-btn-approve"
                        onclick="openConfirmModal('approve')"
                    >
                        <i class="bi bi-check-circle"></i>
                        Setujui Pendaftaran
                    </button>

                <?php else: ?>

                    <button
                        type="button"
                        class="detail-btn detail-btn-approve"
                        disabled
                        title="Semua dokumen harus berstatus Valid"
                    >
                        <i class="bi bi-lock"></i>
                        Setujui Pendaftaran
                    </button>

                <?php endif; ?>

            </form>


            </section>


        </main>


        <!-- =====================================================
             FOOTER
        ====================================================== -->

        <?php require_once "../../components/footer.php"; ?>


    </div>

</div>


<!-- ==========================================================
     MODAL KONFIRMASI
========================================================== -->

<div
    id="confirmationModal"
    class="confirmation-modal"
>

    <div class="confirmation-modal-box">


        <div
            id="confirmationIcon"
            class="confirmation-icon"
        >

            <i class="bi bi-question-circle"></i>

        </div>


        <h3 id="confirmationTitle">
            Konfirmasi
        </h3>


        <p id="confirmationMessage">
            Apakah Anda yakin dengan tindakan ini?
        </p>


        <form
            method="POST"
            action=""
            id="confirmationForm"
        >

            <input
                type="hidden"
                name="action"
                id="confirmationAction"
                value=""
            >


            <div class="confirmation-actions">


                <button
                    type="button"
                    class="confirmation-btn confirmation-btn-cancel"
                    onclick="closeConfirmModal()"
                >

                    Batal

                </button>


                <button
                    type="submit"
                    id="confirmationSubmit"
                    class="confirmation-btn"
                >

                    Konfirmasi

                </button>


            </div>

        </form>

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


<script>

function openConfirmModal(action) {

    const modal =
        document.getElementById("confirmationModal");

    const title =
        document.getElementById("confirmationTitle");

    const message =
        document.getElementById("confirmationMessage");

    const icon =
        document.getElementById("confirmationIcon");

    const submitButton =
        document.getElementById("confirmationSubmit");

    const actionInput =
        document.getElementById("confirmationAction");


    /* =========================================
       SETUJUI
    ========================================= */

    if (action === "approve") {

        title.textContent =
            "Setujui Pendaftaran?";

        message.textContent =
            "Apakah Anda yakin ingin menyetujui pendaftaran peserta ini?";

        icon.className =
            "confirmation-icon approve";

        icon.innerHTML =
            '<i class="bi bi-check-circle"></i>';

        submitButton.textContent =
            "Ya, Setujui";

        submitButton.className =
            "confirmation-btn approve";

    }


    /* =========================================
       TOLAK
    ========================================= */

    if (action === "reject") {

        title.textContent =
            "Tolak Pendaftaran?";

        message.textContent =
            "Apakah Anda yakin ingin menolak pendaftaran peserta ini?";

        icon.className =
            "confirmation-icon reject";

        icon.innerHTML =
            '<i class="bi bi-x-circle"></i>';

        submitButton.textContent =
            "Ya, Tolak";

        submitButton.className =
            "confirmation-btn reject";

    }


    /* =========================================
       SET ACTION
    ========================================= */

    actionInput.value = action;


    /* =========================================
       TAMPILKAN MODAL
    ========================================= */

    modal.classList.add("show");

    document.body.style.overflow = "hidden";

}


/* =========================================
   TUTUP MODAL
========================================= */

function closeConfirmModal() {

    const modal =
        document.getElementById("confirmationModal");

    modal.classList.remove("show");

    document.body.style.overflow = "";

}


/* =========================================
   KLIK BACKGROUND MODAL
========================================= */

document
    .getElementById("confirmationModal")
    .addEventListener("click", function(event) {

        if (event.target === this) {

            closeConfirmModal();

        }

    });


/* =========================================
   ESC UNTUK MENUTUP
========================================= */

document.addEventListener("keydown", function(event) {

    if (event.key === "Escape") {

        closeConfirmModal();

    }

});

</script>


</body>

</html>