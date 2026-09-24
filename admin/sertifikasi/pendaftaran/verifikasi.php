<?php
// ==========================================================
// VERIFIKASI DOKUMEN PENDAFTARAN
// File: admin/sertifikasi/pendaftaran/verifikasi.php
// ==========================================================

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../../../auth/login.php");
    exit;
}

require_once "../../../config/database.php";

$adminNama = $_SESSION["admin_nama"] ?? "Administrator";
$adminRole = $_SESSION["admin_role"] ?? "Administrator";


// ==========================================================
// AMBIL ID DOKUMEN
// ==========================================================

$dokumenId = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;

if ($dokumenId <= 0) {
    header("Location: daftar.php");
    exit;
}


// ==========================================================
// PROSES VERIFIKASI
// ==========================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $status = $_POST["status_verifikasi"] ?? "";
    $catatan = trim($_POST["catatan"] ?? "");

    $statusValid = [
        "valid",
        "tidak_valid"
    ];

    if (!in_array($status, $statusValid, true)) {

        header(
            "Location: verifikasi.php?id=" .
            $dokumenId .
            "&error=status"
        );

        exit;
    }


    // ======================================================
    // UPDATE DOKUMEN
    // ======================================================

    $stmtUpdate = $pdo->prepare("
        UPDATE dokumen_pendaftaran
        SET
            status_verifikasi = ?,
            catatan = ?,
            updated_at = NOW()
        WHERE id = ?
    ");

    $stmtUpdate->execute([
        $status,
        $catatan !== "" ? $catatan : null,
        $dokumenId
    ]);


    // ======================================================
    // KEMBALI KE DETAIL
    // ======================================================

    $stmtParent = $pdo->prepare("
        SELECT pendaftaran_id
        FROM dokumen_pendaftaran
        WHERE id = ?
        LIMIT 1
    ");

    $stmtParent->execute([
        $dokumenId
    ]);

    $parent = $stmtParent->fetch(PDO::FETCH_ASSOC);

    if ($parent) {

        header(
            "Location: detail.php?id=" .
            (int) $parent["pendaftaran_id"] .
            "&success=document"
        );

        exit;
    }

    header("Location: daftar.php");
    exit;
}


// ==========================================================
// AMBIL DATA DOKUMEN
// ==========================================================

$stmt = $pdo->prepare("
    SELECT
        d.id,
        d.pendaftaran_id,
        d.jenis_dokumen,
        d.nama_file_asli,
        d.nama_file_simpan,
        d.path_file,
        d.tipe_file,
        d.ukuran_file,
        d.status_verifikasi,
        d.catatan,
        d.created_at,
        d.updated_at,

        p.nomor_pendaftaran,
        p.nama_lengkap

    FROM dokumen_pendaftaran d

    LEFT JOIN pendaftaran p
        ON p.id = d.pendaftaran_id

    WHERE d.id = ?

    LIMIT 1
");

$stmt->execute([
    $dokumenId
]);

$dokumen = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$dokumen) {
    header("Location: daftar.php");
    exit;
}


// ==========================================================
// LABEL DOKUMEN
// ==========================================================

function getDokumenLabel($jenis)
{
    $labels = [

        "ijazah_pendidikan" =>
            "Ijazah Pendidikan",

        "ijazah_pelatihan" =>
            "Ijazah / Sertifikat Pelatihan",

        "kta_sekuriti" =>
            "KTA Sekuriti",

        "ktp" =>
            "KTP",

        "sertifikat_lama" =>
            "Sertifikat Lama",

        "cv" =>
            "Curriculum Vitae (CV)",

        "surat_rekomendasi" =>
            "Surat Rekomendasi",

        "surat_keterangan_kerja" =>
            "Surat Keterangan Kerja",

        "pasfoto" =>
            "Pas Foto"
    ];

    return $labels[$jenis]
        ?? ucfirst(
            str_replace(
                "_",
                " ",
                $jenis
            )
        );
}


// ==========================================================
// FORMAT UKURAN FILE
// ==========================================================

function formatUkuranFile($bytes)
{
    if (!$bytes) {
        return "-";
    }

    if ($bytes >= 1048576) {
        return number_format(
            $bytes / 1048576,
            2
        ) . " MB";
    }

    if ($bytes >= 1024) {
        return number_format(
            $bytes / 1024,
            2
        ) . " KB";
    }

    return $bytes . " B";
}


// ==========================================================
// PAGE
// ==========================================================

$pageTitle = "Verifikasi Dokumen";

$pageSubtitle =
    $dokumen["nomor_pendaftaran"];

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
        Verifikasi Dokumen - LSP PPPOLRI
    </title>

    <link
        rel="stylesheet"
        href="../../../assets/css/admin.css"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    >

    <style>

        .verification-content {
            padding: 30px;
        }

        .verification-card {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,.05);
            margin-bottom: 20px;
        }

        .verification-title {
            font-size: 22px;
            font-weight: 700;
            color: #72000e;
            margin-bottom: 5px;
        }

        .verification-subtitle {
            color: #777;
            margin-bottom: 25px;
        }

        .document-info {
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #777;
        }

        .info-value {
            font-weight: 600;
            text-align: right;
        }

        .document-preview {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            margin-bottom: 25px;
        }

        .document-preview i {
            font-size: 50px;
            color: #72000e;
            margin-bottom: 15px;
        }

        .document-preview a {
            display: inline-block;
            margin-top: 10px;
        }

        .verification-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-valid {
            background: #198754;
            color: #fff;
            border: none;
        }

        .btn-valid:hover {
            background: #157347;
            color: #fff;
        }

        .btn-invalid {
            background: #dc3545;
            color: #fff;
            border: none;
        }

        .btn-invalid:hover {
            background: #bb2d3b;
            color: #fff;
        }

        .btn-back {
            background: #6c757d;
            color: #fff;
            border: none;
        }

        .btn-back:hover {
            background: #5c636a;
            color: #fff;
        }

        @media (max-width: 768px) {

            .verification-content {
                padding: 20px;
            }

            .info-row {
                flex-direction: column;
                gap: 5px;
            }

            .info-value {
                text-align: left;
            }

        }

    </style>

</head>

<body>

<div class="admin-wrapper">

    <?php
    require_once "../../components/sidebar.php";
    ?>

    <div class="admin-main">

        <?php
        require_once "../../components/header.php";
        ?>


        <main class="verification-content">

            <div class="verification-card">

                <div class="verification-title">
                    Verifikasi Dokumen
                </div>

                <div class="verification-subtitle">
                    Periksa dokumen peserta sebelum menentukan
                    status verifikasi.
                </div>


                <!-- ==========================================
                     INFORMASI DOKUMEN
                =========================================== -->

                <div class="document-info">

                    <div class="info-row">

                        <div class="info-label">
                            Nama Peserta
                        </div>

                        <div class="info-value">

                            <?= htmlspecialchars(
                                $dokumen["nama_lengkap"]
                            ) ?>

                        </div>

                    </div>


                    <div class="info-row">

                        <div class="info-label">
                            Nomor Pendaftaran
                        </div>

                        <div class="info-value">

                            <?= htmlspecialchars(
                                $dokumen["nomor_pendaftaran"]
                            ) ?>

                        </div>

                    </div>


                    <div class="info-row">

                        <div class="info-label">
                            Jenis Dokumen
                        </div>

                        <div class="info-value">

                            <?= htmlspecialchars(
                                getDokumenLabel(
                                    $dokumen["jenis_dokumen"]
                                )
                            ) ?>

                        </div>

                    </div>


                    <div class="info-row">

                        <div class="info-label">
                            Nama File
                        </div>

                        <div class="info-value">

                            <?= htmlspecialchars(
                                $dokumen["nama_file_asli"]
                            ) ?>

                        </div>

                    </div>


                    <div class="info-row">

                        <div class="info-label">
                            Tipe File
                        </div>

                        <div class="info-value">

                            <?= htmlspecialchars(
                                $dokumen["tipe_file"] ?? "-"
                            ) ?>

                        </div>

                    </div>


                    <div class="info-row">

                        <div class="info-label">
                            Ukuran File
                        </div>

                        <div class="info-value">

                            <?= formatUkuranFile(
                                (int) $dokumen["ukuran_file"]
                            ) ?>

                        </div>

                    </div>

                </div>


                <!-- ==========================================
                     FILE
                =========================================== -->

                <div class="document-preview">

                    <i class="bi bi-file-earmark-text"></i>

                    <h5>
                        <?= htmlspecialchars(
                            $dokumen["nama_file_asli"]
                        ) ?>
                    </h5>

                    <p class="text-muted mb-2">
                        Silakan buka dokumen untuk diperiksa.
                    </p>

                    <a
                        href="../../../<?= htmlspecialchars(
                            $dokumen["path_file"]
                        ) ?>"
                        target="_blank"
                        class="btn btn-outline-danger"
                    >

                        <i class="bi bi-eye me-1"></i>

                        Lihat Dokumen

                    </a>

                </div>


                <!-- ==========================================
                     FORM VERIFIKASI
                =========================================== -->

                <form
                    method="POST"
                    action=""
                >

                    <div class="mb-4">

                        <label
                            for="catatan"
                            class="form-label fw-semibold"
                        >

                            Catatan Admin

                        </label>

                        <textarea
                            name="catatan"
                            id="catatan"
                            rows="4"
                            class="form-control"
                            placeholder="Masukkan catatan jika diperlukan..."
                        ><?= htmlspecialchars(
                            $dokumen["catatan"] ?? ""
                        ) ?></textarea>

                    </div>


                    <div class="verification-actions">

                        <button
                            type="submit"
                            name="status_verifikasi"
                            value="valid"
                            class="btn btn-valid"
                        >

                            <i class="bi bi-check-circle me-1"></i>

                            Tandai Valid

                        </button>


                        <button
                            type="submit"
                            name="status_verifikasi"
                            value="tidak_valid"
                            class="btn btn-invalid"
                        >

                            <i class="bi bi-x-circle me-1"></i>

                            Tandai Tidak Valid

                        </button>


                        <a
                            href="detail.php?id=<?= (int) $dokumen["pendaftaran_id"] ?>"
                            class="btn btn-back"
                        >

                            <i class="bi bi-arrow-left me-1"></i>

                            Kembali

                        </a>

                    </div>

                </form>

            </div>

        </main>


        <?php
        require_once "../../components/footer.php";
        ?>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>

<script
    src="../../../assets/js/admin.js"
></script>

</body>

</html>