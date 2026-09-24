<?php

session_start();

require_once "../../config/database.php";


/* =====================================================
   AMBIL SESSION PENDAFTARAN
===================================================== */

$pesertaId = (int) ($_SESSION['pendaftaran_peserta_id'] ?? 0);
$skemaId   = (int) ($_SESSION['pendaftaran_skema_id'] ?? 0);
$email     = trim($_SESSION['pendaftaran_email'] ?? '');


/* =====================================================
   VALIDASI SESSION PESERTA
===================================================== */

if ($pesertaId <= 0 || $email === '' || $skemaId <= 0) {
    header("Location: akun.php?skema_id=" . $skemaId);
    exit;
}


/* =====================================================
   CEK DATA PESERTA
===================================================== */

$stmtPeserta = $pdo->prepare("
    SELECT
        id,
        email,
        status
    FROM akun_peserta
    WHERE id = ?
    LIMIT 1
");

$stmtPeserta->execute([$pesertaId]);

$peserta = $stmtPeserta->fetch(PDO::FETCH_ASSOC);


if (!$peserta) {
    unset(
        $_SESSION['pendaftaran_peserta_id'],
        $_SESSION['pendaftaran_email']
    );

    header("Location: akun.php?skema_id=" . $skemaId);
    exit;
}


/* =====================================================
   VALIDASI SKEMA
===================================================== */

$stmtSkema = $pdo->prepare("
    SELECT
        id,
        nama_skema,
        deskripsi
    FROM skema
    WHERE id = ?
      AND status = 'aktif'
    LIMIT 1
");

$stmtSkema->execute([$skemaId]);

$skema = $stmtSkema->fetch(PDO::FETCH_ASSOC);


if (!$skema) {
    header("Location: pendaftaran.php");
    exit;
}


/* =====================================================
   PASTIKAN SESSION TETAP TERSIMPAN
===================================================== */

$_SESSION['pendaftaran_peserta_id'] = $pesertaId;
$_SESSION['pendaftaran_skema_id']   = $skemaId;
$_SESSION['pendaftaran_email']      = $email;

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
        Formulir Pendaftaran - LSP PPPOLRI
    </title>


    <!-- =====================================================
         GOOGLE FONT
    ====================================================== -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

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


                <div class="progress-line active"></div>


                <!-- STEP 3 -->

                <div class="progress-item active">

                    <div class="progress-number">
                        3
                    </div>

                    <span>
                        Isi & Unggah
                    </span>

                </div>


                <div class="progress-line"></div>


                <!-- STEP 4 -->

                <div class="progress-item">

                    <div class="progress-number">
                        4
                    </div>

                    <span>
                        Verifikasi
                    </span>

                </div>


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
                 PAGE HEADER
            ================================================== -->

            <div class="formulir-header">

                <span>
                    FORMULIR PERMOHONAN SERTIFIKASI
                </span>

                <h1>
                    Data Peserta
                </h1>

                <p>
                    Lengkapi seluruh data sesuai dengan dokumen
                    identitas dan dokumen pendukung yang Anda miliki.
                </p>

            </div>


            <!-- =================================================
                 SKEMA TERPILIH
            ================================================== -->

            <div class="selected-skema formulir-skema">

                <div class="selected-skema-icon">

                    <i class="bi bi-award-fill"></i>

                </div>

                <div class="selected-skema-content">

                    <span>
                        Skema Sertifikasi
                    </span>

                    <strong>
                        <?= htmlspecialchars(
                            $skema['nama_skema']
                        ); ?>
                    </strong>

                </div>

            </div>


            <!-- =================================================
                 FORM
            ================================================== -->

            <form
                action="proses.php"
                method="POST"
                enctype="multipart/form-data"
                id="formPendaftaran"
            >

                <input
                    type="hidden"
                    name="skema_id"
                    value="<?= (int) $skemaId; ?>"
                >


                <input
                    type="hidden"
                    name="email"
                    value="<?= htmlspecialchars($email); ?>"
                >


                <!-- =================================================
                     BAGIAN 1
                ================================================== -->

                <div class="form-section-card">

                    <div class="form-section-title">

                        <div class="form-section-number">
                            1
                        </div>

                        <div>

                            <h2>
                                Data Pribadi
                            </h2>

                            <p>
                                Lengkapi data pribadi sesuai identitas resmi.
                            </p>

                        </div>

                    </div>


                    <div class="row g-4">


                        <!-- NIK -->

                        <div class="col-md-6">

                            <label
                                for="nik"
                                class="form-label-registration"
                            >
                                Nomor KTP / SIM
                                <span>*</span>
                            </label>

                            <input
                                type="text"
                                name="nik"
                                id="nik"
                                class="form-control"
                                placeholder="Masukkan nomor KTP / SIM"
                                required
                            >

                        </div>


                        <!-- Nama -->

                        <div class="col-md-6">

                            <label
                                for="nama_lengkap"
                                class="form-label-registration"
                            >
                                Nama Lengkap
                                <span>*</span>
                            </label>

                            <input
                                type="text"
                                name="nama_lengkap"
                                id="nama_lengkap"
                                class="form-control"
                                placeholder="Masukkan nama lengkap"
                                required
                            >

                        </div>


                        <!-- Tempat Lahir -->

                        <div class="col-md-6">

                            <label
                                for="tempat_lahir"
                                class="form-label-registration"
                            >
                                Tempat Lahir
                                <span>*</span>
                            </label>

                            <input
                                type="text"
                                name="tempat_lahir"
                                id="tempat_lahir"
                                class="form-control"
                                placeholder="Masukkan tempat lahir"
                                required
                            >

                        </div>


                        <!-- Tanggal Lahir -->

                        <div class="col-md-6">

                            <label
                                for="tanggal_lahir"
                                class="form-label-registration"
                            >
                                Tanggal Lahir
                                <span>*</span>
                            </label>

                            <input
                                type="date"
                                name="tanggal_lahir"
                                id="tanggal_lahir"
                                class="form-control"
                                required
                            >

                        </div>


                        <!-- Jenis Kelamin -->

                        <div class="col-md-6">

                            <label
                                class="form-label-registration"
                            >
                                Jenis Kelamin
                                <span>*</span>
                            </label>

                            <div class="gender-options">

                                <label class="gender-option">

                                    <input
                                        type="radio"
                                        name="jenis_kelamin"
                                        value="Laki-laki"
                                        required
                                    >

                                    <span>
                                        Laki-laki
                                    </span>

                                </label>


                                <label class="gender-option">

                                    <input
                                        type="radio"
                                        name="jenis_kelamin"
                                        value="Perempuan"
                                    >

                                    <span>
                                        Perempuan
                                    </span>

                                </label>

                            </div>

                        </div>


                        <!-- Kebangsaan -->

                        <div class="col-md-6">

                            <label
                                for="kebangsaan"
                                class="form-label-registration"
                            >
                                Kebangsaan
                                <span>*</span>
                            </label>

                            <input
                                type="text"
                                name="kebangsaan"
                                id="kebangsaan"
                                class="form-control"
                                value="Indonesia"
                                required
                            >

                        </div>


                        <!-- Alamat -->

                        <div class="col-md-8">

                            <label
                                for="alamat"
                                class="form-label-registration"
                            >
                                Alamat Rumah
                                <span>*</span>
                            </label>

                            <textarea
                                name="alamat"
                                id="alamat"
                                class="form-control"
                                rows="3"
                                placeholder="Masukkan alamat lengkap"
                                required
                            ></textarea>

                        </div>


                        <!-- Kode Pos -->

                        <div class="col-md-4">

                            <label
                                for="kode_pos"
                                class="form-label-registration"
                            >
                                Kode Pos
                                <span>*</span>
                            </label>

                            <input
                                type="text"
                                name="kode_pos"
                                id="kode_pos"
                                class="form-control"
                                placeholder="Kode pos"
                                required
                            >

                        </div>


                        <!-- Telepon -->

                        <div class="col-md-6">

                            <label
                                for="no_hp"
                                class="form-label-registration"
                            >
                                Nomor Telepon / HP
                                <span>*</span>
                            </label>

                            <input
                                type="tel"
                                name="no_hp"
                                id="no_hp"
                                class="form-control"
                                placeholder="Contoh: 08123456789"
                                required
                            >

                        </div>


                        <!-- Email -->

                        <div class="col-md-6">

                            <label
                                for="email_display"
                                class="form-label-registration"
                            >
                                Alamat Email
                            </label>

                            <input
                                type="email"
                                id="email_display"
                                class="form-control"
                                value="<?= htmlspecialchars($email); ?>"
                                readonly
                            >

                        </div>


                        <!-- Nama Sertifikat -->

                        <div class="col-12">

                            <label
                                for="nama_sertifikat"
                                class="form-label-registration"
                            >
                                Nama yang Akan Disematkan pada Sertifikat
                                <span>*</span>
                            </label>

                            <input
                                type="text"
                                name="nama_sertifikat"
                                id="nama_sertifikat"
                                class="form-control text-uppercase"
                                placeholder="MASUKKAN NAMA SESUAI IDENTITAS"
                                style="text-transform: uppercase;"
                                required
                            >

                            <small class="form-help">
                                Nama akan ditulis dengan huruf kapital
                                pada sertifikat.
                            </small>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     BAGIAN 2
                ================================================== -->

                <div class="form-section-card">

                    <div class="form-section-title">

                        <div class="form-section-number">
                            2
                        </div>

                        <div>

                            <h2>
                                Data Pendidikan Formal
                            </h2>

                            <p>
                                Isi dengan pendidikan formal terakhir.
                            </p>

                        </div>

                    </div>


                    <div class="row g-4">


                        <!-- Institusi -->

                        <div class="col-md-6">

                            <label
                                for="nama_institusi"
                                class="form-label-registration"
                            >
                                Nama Institusi
                                <span>*</span>
                            </label>

                            <input
                                type="text"
                                name="nama_institusi"
                                id="nama_institusi"
                                class="form-control"
                                placeholder="Nama sekolah / perguruan tinggi"
                                required
                            >

                        </div>


                        <!-- Jurusan -->

                        <div class="col-md-6">

                            <label
                                for="jurusan"
                                class="form-label-registration"
                            >
                                Jurusan / Program
                                <span>*</span>
                            </label>

                            <input
                                type="text"
                                name="jurusan"
                                id="jurusan"
                                class="form-control"
                                placeholder="Jurusan / program studi"
                                required
                            >

                        </div>


                        <!-- Strata -->

                        <div class="col-md-6">

                            <label
                                for="strata"
                                class="form-label-registration"
                            >
                                Strata
                                <span>*</span>
                            </label>

                            <select
                                name="strata"
                                id="strata"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Pilih strata
                                </option>

                                <option value="SMA/SMK">
                                    SMA / SMK
                                </option>

                                <option value="D3">
                                    D3
                                </option>

                                <option value="D4">
                                    D4
                                </option>

                                <option value="S1">
                                    S1
                                </option>

                                <option value="S2">
                                    S2
                                </option>

                                <option value="S3">
                                    S3
                                </option>

                                <option value="Lainnya">
                                    Lainnya
                                </option>

                            </select>

                        </div>


                        <!-- Tahun Lulus -->

                        <div class="col-md-6">

                            <label
                                for="tahun_lulus"
                                class="form-label-registration"
                            >
                                Tahun Lulus
                                <span>*</span>
                            </label>

                            <input
                                type="number"
                                name="tahun_lulus"
                                id="tahun_lulus"
                                class="form-control"
                                min="1950"
                                max="<?= date('Y'); ?>"
                                placeholder="Contoh: 2020"
                                required
                            >

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     BAGIAN 3
                ================================================== -->

                <div class="form-section-card">

                    <div class="form-section-title">

                        <div class="form-section-number">
                            3
                        </div>

                        <div>

                            <h2>
                                Data Pekerjaan
                            </h2>

                            <p>
                                Isi dengan data tempat bekerja saat ini.
                            </p>

                        </div>

                    </div>


                    <div class="row g-4">


                        <!-- Perusahaan -->

                        <div class="col-md-6">

                            <label
                                for="nama_perusahaan"
                                class="form-label-registration"
                            >
                                Nama Perusahaan
                            </label>

                            <input
                                type="text"
                                name="nama_perusahaan"
                                id="nama_perusahaan"
                                class="form-control"
                                placeholder="Nama perusahaan / instansi"
                            >

                        </div>


                        <!-- Jabatan -->

                        <div class="col-md-6">

                            <label
                                for="jabatan"
                                class="form-label-registration"
                            >
                                Jabatan
                            </label>

                            <input
                                type="text"
                                name="jabatan"
                                id="jabatan"
                                class="form-control"
                                placeholder="Jabatan saat ini"
                            >

                        </div>


                        <!-- Alamat -->

                        <div class="col-md-8">

                            <label
                                for="alamat_perusahaan"
                                class="form-label-registration"
                            >
                                Alamat Perusahaan
                            </label>

                            <textarea
                                name="alamat_perusahaan"
                                id="alamat_perusahaan"
                                class="form-control"
                                rows="3"
                                placeholder="Alamat tempat bekerja"
                            ></textarea>

                        </div>


                        <!-- Telp/Fax -->

                        <div class="col-md-4">

                            <label
                                for="telp_perusahaan"
                                class="form-label-registration"
                            >
                                No. Telp / Fax
                            </label>

                            <input
                                type="text"
                                name="telp_perusahaan"
                                id="telp_perusahaan"
                                class="form-control"
                                placeholder="Nomor telepon / fax"
                            >

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     BAGIAN 4
                ================================================== -->

                <div class="form-section-card">

                    <div class="form-section-title">

                        <div class="form-section-number">
                            4
                        </div>

                        <div>

                            <h2>
                                Data Sertifikasi
                            </h2>

                            <p>
                                Data skema mengikuti skema yang dipilih
                                pada tahap sebelumnya.
                            </p>

                        </div>

                    </div>


                    <div class="selected-skema">

                        <div class="selected-skema-icon">

                            <i class="bi bi-patch-check-fill"></i>

                        </div>

                        <div class="selected-skema-content">

                            <span>
                                Skema Sertifikasi
                            </span>

                            <strong>
                                <?= htmlspecialchars(
                                    $skema['nama_skema']
                                ); ?>
                            </strong>

                        </div>

                    </div>


                    <div class="row g-4">


                        <!-- Tujuan Asesmen -->

                        <div class="col-12">

                            <label
                                class="form-label-registration"
                            >
                                Tujuan Asesmen
                                <span>*</span>
                            </label>

                            <div class="assessment-options">

                                <label class="assessment-option">

                                    <input
                                        type="radio"
                                        name="tujuan_asesmen"
                                        value="Sertifikasi"
                                        required
                                    >

                                    <div>

                                        <strong>
                                            Sertifikasi
                                        </strong>

                                        <small>
                                            Pengajuan sertifikasi kompetensi.
                                        </small>

                                    </div>

                                </label>


                                <label class="assessment-option">

                                    <input
                                        type="radio"
                                        name="tujuan_asesmen"
                                        value="Sertifikasi Ulang"
                                    >

                                    <div>

                                        <strong>
                                            Sertifikasi Ulang
                                        </strong>

                                        <small>
                                            Pengajuan sertifikasi ulang.
                                        </small>

                                    </div>

                                </label>


                                <label class="assessment-option">

                                    <input
                                        type="radio"
                                        name="tujuan_asesmen"
                                        value="Rekognisi Pembelajaran Lampau"
                                    >

                                    <div>

                                        <strong>
                                            Rekognisi Pembelajaran Lampau
                                        </strong>

                                        <small>
                                            RPL.
                                        </small>

                                    </div>

                                </label>


                                <label class="assessment-option">

                                    <input
                                        type="radio"
                                        name="tujuan_asesmen"
                                        value="Pengakuan Kompetensi Terkini (PKT)"
                                    >

                                    <div>

                                        <strong>
                                            Pengakuan Kompetensi Terkini
                                        </strong>

                                        <small>
                                            PKT.
                                        </small>

                                    </div>

                                </label>

                            </div>

                        </div>


                        <!-- Unit Kompetensi -->

                        <div class="col-12">

                            <div class="unit-kompetensi-info">

                                <i class="bi bi-info-circle"></i>

                                <div>

                                    <strong>
                                        Unit Kompetensi
                                    </strong>

                                    <p>
                                        Daftar unit kompetensi akan
                                        mengikuti kemasan pada skema
                                        sertifikasi yang dipilih.
                                    </p>

                                </div>

                            </div>

                            <div class="unit-kompetensi-placeholder">

                                <i class="bi bi-list-check"></i>

                                <p>
                                    Unit kompetensi untuk skema
                                    <strong>
                                        <?= htmlspecialchars(
                                            $skema['nama_skema']
                                        ); ?>
                                    </strong>
                                    akan ditampilkan dari data skema.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     BAGIAN 5
                ================================================== -->

                <div class="form-section-card">

                    <div class="form-section-title">

                        <div class="form-section-number">
                            5
                        </div>

                        <div>

                            <h2>
                                Bukti Kelengkapan Pemohon
                            </h2>

                            <p>
                                Upload dokumen pendukung sesuai
                                persyaratan skema yang diajukan.
                            </p>

                        </div>

                    </div>


                    <!-- =============================================
                         PERSYARATAN DASAR
                    ============================================== -->

                    <div class="document-group">

                        <h3>
                            5.1 Bukti Persyaratan Dasar
                        </h3>

                        <p class="document-description">
                            Dokumen persyaratan dasar sesuai ketentuan
                            skema yang dipilih.
                        </p>


                        <!-- Ijazah Pendidikan -->

                        <div class="document-upload">

                            <label for="ijazah_pendidikan">

                                <span class="document-number">
                                    01
                                </span>

                                <span class="document-title">

                                    Copy Ijazah Pendidikan

                                    <small>
                                        PDF / JPG / PNG
                                    </small>

                                </span>

                                <input
                                    type="file"
                                    name="ijazah_pendidikan"
                                    id="ijazah_pendidikan"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    required
                                >

                            </label>

                        </div>


                        <!-- Ijazah Pelatihan -->

                        <div class="document-upload">

                            <label for="ijazah_pelatihan">

                                <span class="document-number">
                                    02
                                </span>

                                <span class="document-title">

                                    Copy Ijazah Pelatihan Gada Utama

                                    <small>
                                        PDF / JPG / PNG
                                    </small>

                                </span>

                                <input
                                    type="file"
                                    name="ijazah_pelatihan"
                                    id="ijazah_pelatihan"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    required
                                >

                            </label>

                        </div>


                        <!-- KTA -->

                        <div class="document-upload">

                            <label for="kta_sekuriti">

                                <span class="document-number">
                                    03
                                </span>

                                <span class="document-title">

                                    Copy KTA Sekuriti

                                    <small>
                                        PDF / JPG / PNG
                                    </small>

                                </span>

                                <input
                                    type="file"
                                    name="kta_sekuriti"
                                    id="kta_sekuriti"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    required
                                >

                            </label>

                        </div>

                    </div>


                    <!-- =============================================
                         BUKTI ADMINISTRASI
                    ============================================== -->

                    <div class="document-group">

                        <h3>
                            5.2 Bukti Administrasi
                        </h3>

                        <p class="document-description">
                            Dokumen administrasi yang diperlukan untuk
                            proses verifikasi.
                        </p>


                        <!-- KTP -->

                        <div class="document-upload">

                            <label for="ktp">

                                <span class="document-number">
                                    01
                                </span>

                                <span class="document-title">

                                    Copy Kartu Tanda Penduduk (KTP)

                                    <small>
                                        PDF / JPG / PNG
                                    </small>

                                </span>

                                <input
                                    type="file"
                                    name="ktp"
                                    id="ktp"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    required
                                >

                            </label>

                        </div>


                        <!-- Sertifikat lama -->

                        <div class="document-upload">

                            <label for="sertifikat_lama">

                                <span class="document-number">
                                    02
                                </span>

                                <span class="document-title">

                                    Copy Sertifikat Pelatihan yang Lama

                                    <small>
                                        PDF / JPG / PNG
                                    </small>

                                </span>

                                <input
                                    type="file"
                                    name="sertifikat_lama"
                                    id="sertifikat_lama"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                >

                            </label>

                        </div>


                        <!-- CV -->

                        <div class="document-upload">

                            <label for="cv">

                                <span class="document-number">
                                    03
                                </span>

                                <span class="document-title">

                                    Daftar Riwayat Hidup Terakhir

                                    <small>
                                        PDF / JPG / PNG
                                    </small>

                                </span>

                                <input
                                    type="file"
                                    name="cv"
                                    id="cv"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    required
                                >

                            </label>

                        </div>


                        <!-- Rekomendasi -->

                        <div class="document-upload">

                            <label for="surat_rekomendasi">

                                <span class="document-number">
                                    04
                                </span>

                                <span class="document-title">

                                    Surat Rekomendasi Mengikuti Uji Kompetensi

                                    <small>
                                        PDF / JPG / PNG
                                    </small>

                                </span>

                                <input
                                    type="file"
                                    name="surat_rekomendasi"
                                    id="surat_rekomendasi"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    required
                                >

                            </label>

                        </div>


                        <!-- Surat Kerja -->

                        <div class="document-upload">

                            <label for="surat_keterangan_kerja">

                                <span class="document-number">
                                    05
                                </span>

                                <span class="document-title">

                                    Surat Keterangan Kerja

                                    <small>
                                        PDF / JPG / PNG
                                    </small>

                                </span>

                                <input
                                    type="file"
                                    name="surat_keterangan_kerja"
                                    id="surat_keterangan_kerja"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                >

                            </label>

                        </div>


                        <!-- Pas Foto -->

                        <div class="document-upload">

                            <label for="pasfoto">

                                <span class="document-number">
                                    06
                                </span>

                                <span class="document-title">

                                    Pas Foto Berwarna 3 × 4
                                    Latar Belakang Merah

                                    <small>
                                        JPG / PNG
                                    </small>

                                </span>

                                <input
                                    type="file"
                                    name="pasfoto"
                                    id="pasfoto"
                                    accept=".jpg,.jpeg,.png"
                                    required
                                >

                            </label>

                        </div>

                    </div>


                    <div class="document-warning">

                        <i class="bi bi-exclamation-circle"></i>

                        <p>
                            Pastikan seluruh dokumen dapat dibaca dengan
                            jelas. Dokumen akan diperiksa oleh admin pada
                            tahap verifikasi administrasi.
                        </p>

                    </div>

                </div>


                <!-- =================================================
                     PERNYATAAN
                ================================================== -->

                <div class="form-section-card declaration-card">

                    <div class="declaration-check">

                        <input
                            type="checkbox"
                            id="declaration"
                            name="declaration"
                            value="1"
                            required
                        >

                        <label for="declaration">

                            Saya menyatakan bahwa data dan dokumen yang
                            saya berikan dalam formulir ini adalah benar
                            dan dapat dipertanggungjawabkan.

                        </label>

                    </div>

                </div>


                <!-- =================================================
                     ACTION
                ================================================== -->

                <div class="form-final-actions">

                    <button
                        type="button"
                        class="btn-back-registration"
                        onclick="history.back()"
                    >

                        <i class="bi bi-arrow-left"></i>

                        Kembali

                    </button>


                    <button
                        type="submit"
                        class="btn-next-registration"
                    >

                        Ajukan Pendaftaran

                        <i class="bi bi-send"></i>

                    </button>

                </div>

            </form>

        </div>

    </main>


    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <div id="footer"></div>


    <!-- =====================================================
         JAVASCRIPT
    ====================================================== -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
    </script>

    <script src="../../assets/js/include.js"></script>

    <script src="../../assets/js/navbar.js"></script>

    <script src="../../assets/js/main.js"></script>


    <!-- =====================================================
         FILE VALIDATION
    ====================================================== -->

    <script>

        document
            .getElementById("formPendaftaran")
            .addEventListener("submit", function(event) {

                const maxSize = 5 * 1024 * 1024;

                const files = this.querySelectorAll(
                    'input[type="file"]'
                );

                for (const fileInput of files) {

                    if (
                        fileInput.files.length > 0 &&
                        fileInput.files[0].size > maxSize
                    ) {

                        event.preventDefault();

                        alert(
                            "Ukuran file maksimal 5 MB: " +
                            fileInput.files[0].name
                        );

                        fileInput.focus();

                        return;
                    }

                }

            });

    </script>

</body>

</html>