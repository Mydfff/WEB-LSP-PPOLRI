<?php

session_start();

require_once "../../config/database.php";


/* =====================================================
   VARIABEL
===================================================== */

$pendaftaran = null;

$error = "";

$nomorPendaftaran = "";

$email = "";


/* =====================================================
   CEK STATUS
===================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nomorPendaftaran = trim(
        $_POST['nomor_pendaftaran'] ?? ''
    );

    $email = trim(
        $_POST['email'] ?? ''
    );


    /* =================================================
       VALIDASI INPUT
    ================================================= */

    if ($nomorPendaftaran === '') {

        $error = "Nomor pendaftaran wajib diisi.";

    } elseif ($email === '') {

        $error = "Email pendaftaran wajib diisi.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Format email tidak valid.";

    } else {

        try {

            /* =============================================
               AMBIL DATA PENDAFTARAN
            ============================================= */

            $stmt = $pdo->prepare("
                SELECT
                    p.id,
                    p.nomor_pendaftaran,
                    p.nama_lengkap,
                    p.skema_id,
                    p.status,
                    p.created_at,
                    s.nama_skema,
                    ap.email
                FROM pendaftaran p
                LEFT JOIN skema s
                    ON s.id = p.skema_id
                LEFT JOIN akun_peserta ap
                    ON ap.id = p.peserta_id
                WHERE p.nomor_pendaftaran = ?
                  AND LOWER(ap.email) = LOWER(?)
                LIMIT 1
            ");

            $stmt->execute([
                $nomorPendaftaran,
                $email
            ]);

            $pendaftaran = $stmt->fetch(PDO::FETCH_ASSOC);


            /* =============================================
               DATA TIDAK DITEMUKAN
            ============================================= */

            if (!$pendaftaran) {

                $error =
                    "Data pendaftaran tidak ditemukan. " .
                    "Pastikan nomor pendaftaran dan email " .
                    "yang digunakan saat mendaftar sudah benar.";

            }

        } catch (PDOException $e) {

            error_log(
                "Gagal mengambil status pendaftaran: "
                . $e->getMessage()
            );

            $error =
                "Terjadi kesalahan saat mengambil " .
                "data status pendaftaran.";
        }
    }
}


/* =====================================================
   STATUS LABEL
===================================================== */

$statusLabel = "";

if ($pendaftaran) {

    switch ($pendaftaran['status']) {

        case 'diajukan':

            $statusLabel = "Menunggu Verifikasi";

            break;


        case 'verifikasi':

            $statusLabel = "Sedang Diverifikasi";

            break;


        case 'disetujui':

            $statusLabel = "Disetujui";

            break;


        case 'ditolak':

            $statusLabel = "Ditolak";

            break;


        default:

            $statusLabel = ucfirst(
                $pendaftaran['status']
            );

            break;
    }
}


/* =====================================================
   LOGIKA PROGRESS PENDAFTARAN
===================================================== */

/*
   Progress utama:

   1. Pilih Skema
   2. Buat Akun
   3. Isi & Unggah
   4. Verifikasi
   5. Jadwal Asesmen
   6. Keputusan
   7. Sertifikat
*/


$statusPendaftaran = $pendaftaran['status'] ?? '';

$progressVerifikasi = in_array(
    $statusPendaftaran,
    [
        'verifikasi',
        'disetujui',
        'ditolak'
    ],
    true
);

$progressJadwal = in_array(
    $statusPendaftaran,
    [
        'disetujui'
    ],
    true
);

$progressKeputusan = false;

$progressSertifikat = false;

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
        Cek Status Pendaftaran - LSP PPPOLRI
    </title>


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
         CSS PENDAFTARAN
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
     MAIN
====================================================== -->

<main class="pendaftaran-page">

    <div class="container">


        <!-- =================================================
             PROGRESS
        ================================================== -->

        <div class="registration-progress">


            <!-- STEP 1 -->

            <div class="progress-item completed">

                <div class="progress-number">

                    <i class="bi bi-check"></i>

                </div>

                <span>
                    Pilih Skema
                </span>

            </div>


            <!-- LINE -->

            <div class="progress-line active"></div>


            <!-- STEP 2 -->

            <div class="progress-item completed">

                <div class="progress-number">

                    <i class="bi bi-check"></i>

                </div>

                <span>
                    Buat Akun
                </span>

            </div>


            <!-- LINE -->

            <div class="progress-line active"></div>


            <!-- STEP 3 -->

            <div class="progress-item completed">

                <div class="progress-number">

                    <i class="bi bi-check"></i>

                </div>

                <span>
                    Isi &amp; Unggah
                </span>

            </div>


            <!-- LINE -->

            <div class="progress-line active"></div>


            <!-- STEP 4 -->

            <div class="progress-item active">

                <div class="progress-number">

                    <i class="bi bi-check"></i>

                </div>

                <span>
                    Verifikasi
                </span>

            </div>


            <!-- LINE -->

            <div class="progress-line"></div>


            <!-- STEP 5 -->

            <div class="progress-item">

                <div class="progress-number">
                    5
                </div>

                <span>
                    Jadwal Asesmen
                </span>

            </div>


            <!-- LINE -->

            <div class="progress-line"></div>


            <!-- STEP 6 -->

            <div class="progress-item">

                <div class="progress-number">
                    6
                </div>

                <span>
                    Keputusan
                </span>

            </div>


            <!-- LINE -->

            <div class="progress-line"></div>


            <!-- STEP 7 -->

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
             STATUS WRAPPER
        ================================================== -->

        <div class="status-wrapper">

            <div class="status-card">


                <!-- =============================================
                     HEADER
                ============================================== -->

                <div class="text-center mb-4">

                    <span class="status-subtitle">
                        PENDAFTARAN SERTIFIKASI
                    </span>

                    <h1 class="status-title mt-2 mb-2">
                        Cek Status Pendaftaran
                    </h1>

                    <p class="status-subtitle mb-0">

                        Masukkan nomor pendaftaran dan email
                        yang digunakan saat mendaftar untuk
                        melihat status proses sertifikasi.

                    </p>

                </div>


                <!-- =============================================
                     FORM CEK STATUS
                ============================================== -->

                <form
                    action="status.php"
                    method="POST"
                >


                    <!-- NOMOR PENDAFTARAN -->

                    <div class="mb-3">

                        <label
                            for="nomor_pendaftaran"
                            class="form-label"
                        >

                            Nomor Pendaftaran

                        </label>

                        <input
                            type="text"
                            id="nomor_pendaftaran"
                            name="nomor_pendaftaran"
                            class="form-control"
                            placeholder="Contoh: REG-20260923-BEBA19"
                            value="<?= htmlspecialchars($nomorPendaftaran); ?>"
                            required
                        >

                    </div>


                    <!-- EMAIL PENDAFTARAN -->

                    <div class="mb-3">

                        <label
                            for="email"
                            class="form-label"
                        >

                            Email Pendaftaran

                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="Email yang digunakan saat mendaftar"
                            value="<?= htmlspecialchars($email); ?>"
                            required
                        >

                    </div>


                    <!-- BUTTON -->

                    <button
                        type="submit"
                        class="btn btn-primary-custom w-100"
                    >

                        <i class="bi bi-search me-1"></i>

                        Cek Status

                    </button>

                </form>


                <!-- =============================================
                     ERROR
                ============================================== -->

                <?php if ($error !== ''): ?>

                    <div
                        class="alert alert-danger mt-4 mb-0"
                    >

                        <i
                            class="bi bi-exclamation-circle me-2"
                        ></i>

                        <?= htmlspecialchars($error); ?>

                    </div>

                <?php endif; ?>


                <!-- =============================================
                     HASIL STATUS
                ============================================== -->

                <?php if ($pendaftaran): ?>

                    <div class="result-card">


                        <!-- NOMOR -->

                        <div class="mb-4">

                            <div class="data-label">
                                Nomor Pendaftaran
                            </div>

                            <div class="registration-number">

                                <?= htmlspecialchars(
                                    $pendaftaran['nomor_pendaftaran']
                                ); ?>

                            </div>

                        </div>


                        <!-- DATA -->

                        <div class="row">


                            <!-- NAMA -->

                            <div class="col-md-6">

                                <div class="data-label">
                                    Nama Peserta
                                </div>

                                <div class="data-value">

                                    <?= htmlspecialchars(
                                        $pendaftaran['nama_lengkap']
                                    ); ?>

                                </div>

                            </div>


                            <!-- SKEMA -->

                            <div class="col-md-6">

                                <div class="data-label">
                                    Skema Sertifikasi
                                </div>

                                <div class="data-value">

                                    <?= htmlspecialchars(
                                        $pendaftaran['nama_skema'] ?? '-'
                                    ); ?>

                                </div>

                            </div>


                            <!-- EMAIL -->

                            <div class="col-md-6">

                                <div class="data-label">
                                    Email Pendaftaran
                                </div>

                                <div class="data-value">

                                    <?= htmlspecialchars(
                                        $pendaftaran['email'] ?? '-'
                                    ); ?>

                                </div>

                            </div>


                            <!-- TANGGAL -->

                            <div class="col-md-6">

                                <div class="data-label">
                                    Tanggal Pendaftaran
                                </div>

                                <div class="data-value">

                                    <?php

                                    if (
                                        !empty(
                                            $pendaftaran['created_at']
                                        )
                                    ) {

                                        echo date(
                                            'd-m-Y H:i',
                                            strtotime(
                                                $pendaftaran['created_at']
                                            )
                                        );

                                    } else {

                                        echo '-';

                                    }

                                    ?>

                                </div>

                            </div>


                            <!-- STATUS -->

                            <div class="col-md-6">

                                <div class="data-label">
                                    Status Pendaftaran
                                </div>

                                <div class="data-value">

                                    <span class="status-badge">

                                        <i
                                            class="bi bi-clock-history me-1"
                                        ></i>

                                        <?= htmlspecialchars(
                                            $statusLabel
                                        ); ?>

                                    </span>

                                </div>

                            </div>

                        </div>


                        <!-- =====================================
                             PROSES STATUS
                        ====================================== -->

                        <div class="progress-wrapper">


                            <!-- STEP 1 -->

                            <div class="step active">

                                <div class="step-icon">

                                    <i class="bi bi-check"></i>

                                </div>

                                <div>

                                    <div class="step-title">
                                        Pendaftaran Dikirim
                                    </div>

                                    <div class="step-description">

                                        Data dan dokumen pendaftaran
                                        telah berhasil dikirim.

                                    </div>

                                </div>

                            </div>


                            <!-- STEP 2 -->

                            <div
                                class="step
                                <?= $progressVerifikasi
                                    ? 'active'
                                    : ''; ?>"
                            >

                                <div class="step-icon">

                                    <i class="bi bi-shield-check"></i>

                                </div>

                                <div>

                                    <div class="step-title">
                                        Verifikasi
                                    </div>

                                    <div class="step-description">

                                        Admin memeriksa data dan
                                        dokumen pendaftaran.

                                    </div>

                                </div>

                            </div>


                            <!-- STEP 3 -->

                            <div
                                class="step
                                <?= $progressJadwal
                                    ? 'active'
                                    : ''; ?>"
                            >

                                <div class="step-icon">

                                    <i class="bi bi-calendar-check"></i>

                                </div>

                                <div>

                                    <div class="step-title">
                                        Jadwal Asesmen
                                    </div>

                                    <div class="step-description">

                                        Peserta mendapatkan informasi
                                        jadwal asesmen.

                                    </div>

                                </div>

                            </div>


                            <!-- STEP 4 -->

                            <div
                                class="step
                                <?= $progressKeputusan
                                    ? 'active'
                                    : ''; ?>"
                            >

                                <div class="step-icon">

                                    <i class="bi bi-award"></i>

                                </div>

                                <div>

                                    <div class="step-title">
                                        Keputusan
                                    </div>

                                    <div class="step-description">

                                        Hasil proses asesmen
                                        ditentukan oleh LSP.

                                    </div>

                                </div>

                            </div>


                            <!-- STEP 5 -->

                            <div
                                class="step
                                <?= $progressSertifikat
                                    ? 'active'
                                    : ''; ?>"
                            >

                                <div class="step-icon">

                                    <i class="bi bi-file-earmark-check"></i>

                                </div>

                                <div>

                                    <div class="step-title">
                                        Sertifikat
                                    </div>

                                    <div class="step-description">

                                        Sertifikat diterbitkan setelah
                                        proses sertifikasi selesai.

                                    </div>

                                </div>

                            </div>


                        </div>

                    </div>

                <?php endif; ?>


                <!-- =============================================
                     KEMBALI
                ============================================== -->

                <div class="text-center mt-4">

                    <a
                        href="../../index.php"
                        class="back-link"
                    >

                        <i class="bi bi-arrow-left me-1"></i>

                        Kembali ke Beranda

                    </a>

                </div>

            </div>

        </div>

    </div>

</main>


<!-- =====================================================
     FOOTER
====================================================== -->

<div id="footer"></div>


<!-- =====================================================
     BOOTSTRAP JS
====================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>


<!-- =====================================================
     COMPONENT LOADER
====================================================== -->

<script
    src="../../assets/js/include.js"
></script>


<!-- =====================================================
     NAVBAR
====================================================== -->

<script
    src="../../assets/js/navbar.js"
></script>


<!-- =====================================================
     MAIN JS
====================================================== -->

<script
    src="../../assets/js/main.js"
></script>

</body>

</html>