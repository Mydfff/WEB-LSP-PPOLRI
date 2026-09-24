<?php

session_start();

require_once "../../config/database.php";


/* =====================================================
   VALIDASI SKEMA
===================================================== */

$skemaId = isset($_GET['skema_id'])
    ? (int) $_GET['skema_id']
    : (
        isset($_SESSION['pendaftaran_skema_id'])
            ? (int) $_SESSION['pendaftaran_skema_id']
            : 0
    );


if ($skemaId <= 0) {

    header("Location: pendaftaran.php");
    exit;

}


/* =====================================================
   AMBIL DATA SKEMA
===================================================== */

$stmt = $pdo->prepare("
    SELECT
        id,
        nama_skema,
        deskripsi
    FROM skema
    WHERE id = ?
      AND status = 'aktif'
    LIMIT 1
");

$stmt->execute([$skemaId]);

$skema = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$skema) {

    header("Location: pendaftaran.php");
    exit;

}


/* =====================================================
   SIMPAN SKEMA KE SESSION
===================================================== */

$_SESSION['pendaftaran_skema_id'] = (int) $skema['id'];


/* =====================================================
   VARIABEL FORM
===================================================== */

$error = "";

$email = "";


/* =====================================================
   PROSES BUAT AKUN
===================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');

    $password = $_POST['password'] ?? '';

    $passwordConfirmation =
        $_POST['password_confirmation'] ?? '';

    $agreement =
        isset($_POST['agreement']);


    /* =================================================
       VALIDASI
    ================================================= */

    if ($email === '') {

        $error = "Email wajib diisi.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Format email tidak valid.";

    } elseif ($password === '') {

        $error = "Password wajib diisi.";

    } elseif (strlen($password) < 8) {

        $error = "Password minimal 8 karakter.";

    } elseif ($password !== $passwordConfirmation) {

        $error = "Konfirmasi password tidak sesuai.";

    } elseif (!$agreement) {

        $error = "Silakan menyetujui ketentuan pendaftaran.";

    } else {


        /* =================================================
           CEK EMAIL
        ================================================= */

        $stmt = $pdo->prepare("
            SELECT
                id
            FROM akun_peserta
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        $akun = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($akun) {

            $error =
                "Email tersebut sudah terdaftar. Silakan gunakan email lain.";

        } else {


            /* =================================================
               SIMPAN AKUN
            ================================================= */

            try {

                $passwordHash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


                $stmt = $pdo->prepare("
                    INSERT INTO akun_peserta
                    (
                        email,
                        password_hash,
                        status
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        'aktif'
                    )
                ");


                $stmt->execute([
                    $email,
                    $passwordHash
                ]);


                /* =================================================
                   AMBIL ID AKUN PESERTA
                ================================================= */

                $pesertaId = (int) $pdo->lastInsertId();
                
                if ($pesertaId <= 0) {

                    throw new PDOException(
                        "ID akun peserta tidak berhasil dibuat."
                    );

                }


                /* =================================================
                   SIMPAN DATA KE SESSION
                ================================================= */

                $_SESSION['pendaftaran_peserta_id'] =
                    $pesertaId;

                $_SESSION['pendaftaran_email'] =
                    $email;

                $_SESSION['pendaftaran_skema_id'] =
                    (int) $skemaId;


                /* =================================================
                   LANJUT KE FORMULIR
                ================================================= */

                header("Location: formulir.php");
                exit;


            } catch (PDOException $e) {

                error_log(
                    "Gagal membuat akun peserta: "
                    . $e->getMessage()
                );

                $error =
                    "Terjadi kesalahan saat membuat akun. Silakan coba lagi.";

            }

        }

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

    <title>Buat Akun - Pendaftaran Sertifikasi | LSP PPPOLRI</title>


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

                <div class="progress-item completed">

                    <div class="progress-number">
                        <i class="bi bi-check"></i>
                    </div>

                    <span>
                        Pilih Skema
                    </span>

                </div>


                <div class="progress-line active"></div>


                <div class="progress-item active">

                    <div class="progress-number">
                        2
                    </div>

                    <span>
                        Buat Akun
                    </span>

                </div>


                <div class="progress-line"></div>


                <div class="progress-item">

                    <div class="progress-number">
                        3
                    </div>

                    <span>
                        Isi & Unggah
                    </span>

                </div>


                <div class="progress-line"></div>


                <div class="progress-item">

                    <div class="progress-number">
                        4
                    </div>

                    <span>
                        Verifikasi
                    </span>

                </div>


                <div class="progress-line"></div>


                <div class="progress-item">

                    <div class="progress-number">
                        5
                    </div>

                    <span>
                        Jadwal Asesmen
                    </span>

                </div>


                <div class="progress-line"></div>


                <div class="progress-item">

                    <div class="progress-number">
                        6
                    </div>

                    <span>
                        Keputusan
                    </span>

                </div>


                <div class="progress-line"></div>


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
                 ACCOUNT CARD
            ================================================== -->

            <div class="account-registration-card">


                <!-- =============================================
                     HEADER
                ============================================== -->

                <div class="account-header">

                    <span class="account-subtitle">
                        PENDAFTARAN SERTIFIKASI
                    </span>

                    <h1>
                        Buat Akun
                    </h1>

                    <p>
                        Buat akun peserta untuk melanjutkan
                        proses pendaftaran sertifikasi.
                    </p>

                </div>


                <!-- =============================================
                     SKEMA YANG DIPILIH
                ============================================== -->

                <div class="selected-skema">

                    <div class="selected-skema-icon">

                        <i class="bi bi-award-fill"></i>

                    </div>


                    <div class="selected-skema-content">

                        <span>
                            Skema yang dipilih
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $skema['nama_skema']
                            ); ?>
                        </strong>

                    </div>

                </div>


                <!-- =============================================
                     FORM
                ============================================== -->

                <form
                    action="akun.php?skema_id=<?= (int) $skema['id']; ?>"
                    method="POST"
                    id="accountForm"
                >

                    <input
                        type="hidden"
                        name="skema_id"
                        value="<?= (int) $skema['id']; ?>"
                    >


                    <!-- =========================================
                         EMAIL
                    ========================================== -->

                    <div class="form-group-registration">

                        <label for="email">

                            Email

                            <span>*</span>

                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="Masukkan email aktif"
                            value="<?= htmlspecialchars($email); ?>"
                            required
                            autocomplete="email"
                        >

                        <small>
                            Email digunakan untuk menerima
                            informasi pendaftaran dan notifikasi
                            dari LSP PPPOLRI.
                        </small>

                    </div>


                    <!-- =========================================
                         PASSWORD
                    ========================================== -->

                    <div class="form-group-registration">

                        <label for="password">

                            Password

                            <span>*</span>

                        </label>

                        <div class="password-input">

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                placeholder="Buat password"
                                minlength="8"
                                required
                                autocomplete="new-password"
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                onclick="togglePassword('password', this)"
                            >

                                <i class="bi bi-eye"></i>

                            </button>

                        </div>

                        <small>
                            Password minimal 8 karakter.
                        </small>

                    </div>


                    <!-- =========================================
                         KONFIRMASI PASSWORD
                    ========================================== -->

                    <div class="form-group-registration">

                        <label for="password_confirmation">

                            Konfirmasi Password

                            <span>*</span>

                        </label>

                        <div class="password-input">

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Ulangi password"
                                minlength="8"
                                required
                                autocomplete="new-password"
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                onclick="togglePassword(
                                    'password_confirmation',
                                    this
                                )"
                            >

                                <i class="bi bi-eye"></i>

                            </button>

                        </div>

                    </div>


                    <!-- =========================================
                         PERSETUJUAN
                    ========================================== -->

                    <div class="account-agreement">

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="agreement"
                                name="agreement"
                                required
                            >

                            <label
                                class="form-check-label"
                                for="agreement"
                            >

                                Saya menyatakan bahwa email yang
                                digunakan aktif dan dapat menerima
                                informasi terkait proses pendaftaran
                                sertifikasi.

                            </label>

                        </div>

                    </div>


                    <!-- =========================================
                         ACTION
                    ========================================== -->

                    <div class="account-actions">

                        <a
                            href="pendaftaran.php"
                            class="btn-back-registration"
                        >

                            <i class="bi bi-arrow-left"></i>

                            Kembali

                        </a>


                        <button
                            type="submit"
                            class="btn-next-registration"
                        >

                            Selanjutnya

                            <i class="bi bi-arrow-right"></i>

                        </button>

                    </div>

                </form>

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
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
    </script>


    <!-- =====================================================
         COMPONENT LOADER
    ====================================================== -->

    <script src="../../assets/js/include.js"></script>


    <!-- =====================================================
         NAVBAR
    ====================================================== -->

    <script src="../../assets/js/navbar.js"></script>


    <!-- =====================================================
         MAIN JS
    ====================================================== -->

    <script src="../../assets/js/main.js"></script>


    <!-- =====================================================
         PASSWORD TOGGLE
    ====================================================== -->

    <script>

        function togglePassword(inputId, button) {

            const input = document.getElementById(inputId);
            const icon = button.querySelector("i");

            if (input.type === "password") {

                input.type = "text";

                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");

            } else {

                input.type = "password";

                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Validasi Password
        |--------------------------------------------------------------------------
        */

        document
            .getElementById("accountForm")
            .addEventListener("submit", function(event) {

                const password =
                    document.getElementById("password").value;

                const confirmation =
                    document.getElementById(
                        "password_confirmation"
                    ).value;


                if (password !== confirmation) {

                    event.preventDefault();

                    alert("Konfirmasi password tidak sesuai.");

                }

            });

    </script>

</body>

</html>