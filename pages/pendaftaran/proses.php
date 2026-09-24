<?php

session_start();

require_once "../../config/database.php";
require_once "../../config/mailer.php";


/* =====================================================
   VALIDASI REQUEST
===================================================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: pendaftaran.php");
    exit;
}


/* =====================================================
   VALIDASI SESSION
===================================================== */

$pesertaId = $_SESSION['pendaftaran_peserta_id'] ?? null;
$email     = $_SESSION['pendaftaran_email'] ?? null;
$skemaId   = $_SESSION['pendaftaran_skema_id'] ?? null;

if (!$pesertaId || !$email || !$skemaId) {
    die("Session pendaftaran tidak lengkap. Silakan ulangi proses pendaftaran.");
}


/* =====================================================
   CEK PESERTA
===================================================== */

$stmtPeserta = $pdo->prepare("
    SELECT id, email, status
    FROM akun_peserta
    WHERE id = ?
    LIMIT 1
");

$stmtPeserta->execute([$pesertaId]);

$peserta = $stmtPeserta->fetch(PDO::FETCH_ASSOC);

if (!$peserta) {
    die("Data peserta tidak ditemukan.");
}

if ($peserta['status'] !== 'aktif') {
    die("Akun peserta belum aktif.");
}


/* =====================================================
   CEK SKEMA
===================================================== */

$stmtSkema = $pdo->prepare("
    SELECT id, nama_skema
    FROM skema
    WHERE id = ?
      AND status = 'aktif'
    LIMIT 1
");

$stmtSkema->execute([$skemaId]);

$skema = $stmtSkema->fetch(PDO::FETCH_ASSOC);

if (!$skema) {
    die("Skema sertifikasi tidak ditemukan atau tidak aktif.");
}


/* =====================================================
   AMBIL DATA FORM
===================================================== */

$nik                  = trim($_POST['nik'] ?? '');
$namaLengkap          = trim($_POST['nama_lengkap'] ?? '');
$tempatLahir          = trim($_POST['tempat_lahir'] ?? '');
$tanggalLahir         = $_POST['tanggal_lahir'] ?? '';
$jenisKelamin         = $_POST['jenis_kelamin'] ?? '';
$kebangsaan           = trim($_POST['kebangsaan'] ?? '');
$alamat               = trim($_POST['alamat'] ?? '');
$kodePos              = trim($_POST['kode_pos'] ?? '');
$noHp                 = trim($_POST['no_hp'] ?? '');
$namaSertifikat       = trim($_POST['nama_sertifikat'] ?? '');
$namaInstitusi        = trim($_POST['nama_institusi'] ?? '');
$jurusan              = trim($_POST['jurusan'] ?? '');
$strata               = trim($_POST['strata'] ?? '');
$tahunLulus           = trim($_POST['tahun_lulus'] ?? '');
$namaPerusahaan       = trim($_POST['nama_perusahaan'] ?? '');
$jabatan              = trim($_POST['jabatan'] ?? '');
$alamatPerusahaan     = trim($_POST['alamat_perusahaan'] ?? '');
$telpPerusahaan       = trim($_POST['telp_perusahaan'] ?? '');
$tujuanAsesmen        = trim($_POST['tujuan_asesmen'] ?? '');
$declaration          = $_POST['declaration'] ?? '';


/* =====================================================
   VALIDASI FIELD WAJIB
===================================================== */

$requiredFields = [

    'NIK'                  => $nik,
    'Nama Lengkap'         => $namaLengkap,
    'Tempat Lahir'         => $tempatLahir,
    'Tanggal Lahir'        => $tanggalLahir,
    'Jenis Kelamin'        => $jenisKelamin,
    'Kebangsaan'           => $kebangsaan,
    'Alamat'               => $alamat,
    'Kode Pos'              => $kodePos,
    'No. HP'                => $noHp,
    'Nama Sertifikat'       => $namaSertifikat,
    'Nama Institusi'        => $namaInstitusi,
    'Jurusan'               => $jurusan,
    'Strata'                => $strata,
    'Tahun Lulus'           => $tahunLulus,
    'Nama Perusahaan'       => $namaPerusahaan,
    'Jabatan'               => $jabatan,
    'Alamat Perusahaan'     => $alamatPerusahaan,
    'Telepon Perusahaan'    => $telpPerusahaan,
    'Tujuan Asesmen'        => $tujuanAsesmen

];

foreach ($requiredFields as $label => $value) {

    if ($value === '') {
        die("Field {$label} wajib diisi.");
    }

}


/* =====================================================
   VALIDASI PERNYATAAN
===================================================== */

if ($declaration !== '1') {
    die("Anda harus menyetujui pernyataan pendaftaran.");
}


/* =====================================================
   DAFTAR DOKUMEN
===================================================== */

$documents = [

    'ijazah_pendidikan' => [
        'label'    => 'Ijazah Pendidikan',
        'required' => true
    ],

    'ijazah_pelatihan' => [
        'label'    => 'Ijazah Pelatihan',
        'required' => true
    ],

    'kta_sekuriti' => [
        'label'    => 'KTA Sekuriti',
        'required' => true
    ],

    'ktp' => [
        'label'    => 'KTP',
        'required' => true
    ],

    'sertifikat_lama' => [
        'label'    => 'Sertifikat Lama',
        'required' => false
    ],

    'cv' => [
        'label'    => 'CV',
        'required' => true
    ],

    'surat_rekomendasi' => [
        'label'    => 'Surat Rekomendasi',
        'required' => true
    ],

    'surat_keterangan_kerja' => [
        'label'    => 'Surat Keterangan Kerja',
        'required' => false
    ],

    'pasfoto' => [
        'label'    => 'Pas Foto',
        'required' => true
    ]

];


/* =====================================================
   VALIDASI FILE
===================================================== */

$allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];

$maxFileSize = 5 * 1024 * 1024;


/* =====================================================
   CEK FILE WAJIB
===================================================== */

foreach ($documents as $field => $document) {

    if (!$document['required']) {
        continue;
    }

    if (
        !isset($_FILES[$field]) ||
        $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE
    ) {
        die("File {$document['label']} wajib diupload.");
    }

}


/* =====================================================
   FOLDER UPLOAD
===================================================== */

$uploadDirectory = __DIR__ . "/../../uploads/pendaftaran/";

if (!is_dir($uploadDirectory)) {

    if (!mkdir($uploadDirectory, 0777, true)) {
        die("Folder upload tidak dapat dibuat.");
    }

}


/* =====================================================
   TRANSACTION
===================================================== */

$uploadedFiles = [];

try {

    $pdo->beginTransaction();


    /* =================================================
       NOMOR PENDAFTARAN
    ================================================= */

    $nomorPendaftaran =
        'REG-' .
        date('Ymd') .
        '-' .
        strtoupper(bin2hex(random_bytes(3)));


    /* =================================================
       INSERT PENDAFTARAN
    ================================================= */

    $stmtPendaftaran = $pdo->prepare("
        INSERT INTO pendaftaran
        (
            peserta_id,
            skema_id,
            nomor_pendaftaran,
            nik,
            nama_lengkap,
            tempat_lahir,
            tanggal_lahir,
            jenis_kelamin,
            kebangsaan,
            alamat_rumah,
            kode_pos,
            no_hp,
            nama_sertifikat,
            nama_institusi,
            jurusan,
            strata,
            tahun_lulus,
            nama_perusahaan,
            jabatan,
            alamat_perusahaan,
            telp_fax_perusahaan,
            tujuan_asesmen,
            status,
            created_at,
            updated_at
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            'diajukan',
            NOW(),
            NOW()
        )
    ");

    $stmtPendaftaran->execute([

        $pesertaId,
        $skemaId,
        $nomorPendaftaran,
        $nik,
        $namaLengkap,
        $tempatLahir,
        $tanggalLahir,
        $jenisKelamin,
        $kebangsaan,
        $alamat,
        $kodePos,
        $noHp,
        $namaSertifikat,
        $namaInstitusi,
        $jurusan,
        $strata,
        $tahunLulus,
        $namaPerusahaan,
        $jabatan,
        $alamatPerusahaan,
        $telpPerusahaan,
        $tujuanAsesmen

    ]);


    /* =================================================
       ID PENDAFTARAN
    ================================================= */

    $pendaftaranId = $pdo->lastInsertId();


    /* =================================================
       CEK MIME FILE
    ================================================= */

    $finfo = new finfo(FILEINFO_MIME_TYPE);


    /* =================================================
       INSERT DOKUMEN
    ================================================= */

    $stmtDokumen = $pdo->prepare("
        INSERT INTO dokumen_pendaftaran
        (
            pendaftaran_id,
            jenis_dokumen,
            nama_file_asli,
            nama_file_simpan,
            path_file,
            tipe_file,
            ukuran_file,
            status_verifikasi,
            created_at,
            updated_at
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            'menunggu',
            NOW(),
            NOW()
        )
    ");


    foreach ($documents as $field => $document) {


        /* =============================================
           FILE OPTIONAL YANG TIDAK DIUPLOAD
        ============================================= */

        if (
            !isset($_FILES[$field]) ||
            $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE
        ) {
            continue;
        }


        $file = $_FILES[$field];


        /* =============================================
           CEK ERROR UPLOAD
        ============================================= */

        if ($file['error'] !== UPLOAD_ERR_OK) {

            throw new Exception(
                "Terjadi kesalahan saat upload file {$document['label']}."
            );

        }


        /* =============================================
           CEK UKURAN
        ============================================= */

        if ($file['size'] > $maxFileSize) {

            throw new Exception(
                "File {$document['label']} terlalu besar. Maksimal 5 MB."
            );

        }


        /* =============================================
           AMBIL EXTENSION
        ============================================= */

        $originalName = $file['name'];

        $extension = strtolower(
            pathinfo($originalName, PATHINFO_EXTENSION)
        );


        if (!in_array($extension, $allowedExtensions, true)) {

            throw new Exception(
                "Format file {$document['label']} tidak diperbolehkan."
            );

        }


        /* =============================================
           VALIDASI MIME
        ============================================= */

        $mimeType = $finfo->file($file['tmp_name']);

        $allowedMimeTypes = [

            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png'

        ];


        if (
            !isset($allowedMimeTypes[$extension]) ||
            $mimeType !== $allowedMimeTypes[$extension]
        ) {

            throw new Exception(
                "File {$document['label']} tidak valid."
            );

        }


        /* =============================================
           NAMA FILE BARU
        ============================================= */

        $uniqueName =
            date('YmdHis') .
            '_' .
            bin2hex(random_bytes(8)) .
            '.' .
            $extension;


        /* =============================================
           PATH FILE
        ============================================= */

        $destination = $uploadDirectory . $uniqueName;

        $databasePath =
            "uploads/pendaftaran/" .
            $uniqueName;


        /* =============================================
           PINDAHKAN FILE
        ============================================= */

        if (!move_uploaded_file(
            $file['tmp_name'],
            $destination
        )) {

            throw new Exception(
                "Gagal menyimpan file {$document['label']}."
            );

        }


        /* Simpan untuk rollback jika terjadi error */

        $uploadedFiles[] = $destination;


        /* =============================================
           INSERT DATA DOKUMEN
        ============================================= */

        $stmtDokumen->execute([

            $pendaftaranId,
            $field,
            $originalName,
            $uniqueName,
            $databasePath,
            $mimeType,
            $file['size']

        ]);

    }


    /* =================================================
       COMMIT DATABASE
    ================================================= */

    $pdo->commit();


    /* =================================================
       KIRIM EMAIL NOTIFIKASI KE LSP
       DATABASE SUDAH BERHASIL TERSIMPAN
    ================================================= */

    $emailLsp = 'deffsuha@gmail.com';

    $subjekEmail =
        "Pendaftaran Sertifikasi Baru - {$nomorPendaftaran}";


    $isiEmail = "

    <!DOCTYPE html>

    <html lang='id'>

    <head>

        <meta charset='UTF-8'>

        <title>Pendaftaran Sertifikasi Baru</title>

    </head>

    <body
        style='
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        '
    >

        <h2 style='color: #72000e;'>
            Pendaftaran Sertifikasi Baru
        </h2>

        <p>
            Telah masuk pendaftaran sertifikasi baru
            melalui website LSP PPPOLRI.
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
                    " . htmlspecialchars($nomorPendaftaran) . "
                </td>

            </tr>


            <tr>

                <td>
                    <strong>Nama Peserta</strong>
                </td>

                <td>
                    :
                    " . htmlspecialchars($namaLengkap) . "
                </td>

            </tr>


            <tr>

                <td>
                    <strong>Email Peserta</strong>
                </td>

                <td>
                    :
                    " . htmlspecialchars($email) . "
                </td>

            </tr>


            <tr>

                <td>
                    <strong>Skema Sertifikasi</strong>
                </td>

                <td>
                    :
                    " . htmlspecialchars($skema['nama_skema']) . "
                </td>

            </tr>


            <tr>

                <td>
                    <strong>Waktu Pendaftaran</strong>
                </td>

                <td>
                    :
                    " . date('d-m-Y H:i:s') . "
                </td>

            </tr>


            <tr>

                <td>
                    <strong>Status</strong>
                </td>

                <td>
                    :
                    Diajukan
                </td>

            </tr>

        </table>


        <br>


        <p>
            Silakan masuk ke dashboard admin untuk
            memeriksa data dan dokumen peserta.
        </p>


        <p>
            <strong>LSP PPPOLRI</strong>
        </p>

    </body>

    </html>

    ";


    kirimEmail(
        $emailLsp,
        $subjekEmail,
        $isiEmail
    );


    /* =================================================
       SIMPAN SESSION
    ================================================= */

    $_SESSION['pendaftaran_id'] = $pendaftaranId;

    $_SESSION['pendaftaran_email'] = $email;

    $_SESSION['pendaftaran_skema_id'] = $skemaId;


    /* =================================================
       HALAMAN BERHASIL
    ================================================= */

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
            Pendaftaran Berhasil - LSP PPPOLRI
        </title>

        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
            rel="stylesheet"
        >

        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        >

    </head>


    <body class="bg-light">

        <div class="container py-5">

            <div class="row justify-content-center">

                <div class="col-md-7">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body text-center p-5">

                            <div class="mb-4">

                                <i
                                    class="bi bi-check-circle-fill text-success"
                                    style="font-size: 70px;"
                                ></i>

                            </div>


                            <h2 class="fw-bold mb-3">
                                Pendaftaran Berhasil
                            </h2>


                            <p class="text-muted mb-4">

                                Data pendaftaran sertifikasi Anda
                                telah berhasil dikirim dan sedang
                                menunggu proses verifikasi admin.

                            </p>


                            <div class="alert alert-light border">

                                <div class="small text-muted mb-1">

                                    Nomor Pendaftaran

                                </div>


                                <div class="fs-4 fw-bold">

                                    <?= htmlspecialchars($nomorPendaftaran) ?>

                                </div>

                            </div>


                            <p class="small text-muted mb-4">

                                Simpan nomor pendaftaran ini untuk
                                keperluan pengecekan status pendaftaran.

                            </p>


                            <div
                                class="d-flex justify-content-center gap-2 flex-wrap"
                            >

                                <a
                                    href="status.php"
                                    class="btn btn-primary"
                                >

                                    <i class="bi bi-search me-1"></i>

                                    Lihat Status Pendaftaran

                                </a>


                                <a
                                    href="../../index.php"
                                    class="btn btn-outline-secondary"
                                >

                                    <i class="bi bi-house-door me-1"></i>

                                    Kembali ke Beranda

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </body>

    </html>

    <?php

    exit;


/* =====================================================
   ERROR HANDLER
===================================================== */

} catch (Throwable $e) {


    /* Rollback database */

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }


    /* Hapus file yang sudah sempat diupload */

    foreach ($uploadedFiles as $uploadedFile) {

        if (file_exists($uploadedFile)) {

            @unlink($uploadedFile);

        }

    }


    /* Tampilkan error */

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
            Gagal Memproses Pendaftaran
        </title>

        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
            rel="stylesheet"
        >

    </head>


    <body class="bg-light">

        <div class="container py-5">

            <div class="row justify-content-center">

                <div class="col-md-7">

                    <div class="alert alert-danger shadow-sm">

                        <h5 class="fw-bold">

                            Terjadi kesalahan saat memproses pendaftaran.

                        </h5>


                        <p class="mb-0">

                            <?= htmlspecialchars($e->getMessage()) ?>

                        </p>

                    </div>


                    <a
                        href="javascript:history.back()"
                        class="btn btn-secondary"
                    >

                        Kembali

                    </a>

                </div>

            </div>

        </div>

    </body>

    </html>

    <?php

}

?>