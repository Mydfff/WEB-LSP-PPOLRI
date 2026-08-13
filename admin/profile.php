<?php

/* =========================================================
   PROFILE ADMIN - LSP PPPOLRI

   File:
   admin/profile.php

   Database:
   MySQL / PDO

   Fitur:
   - Tampilkan profil admin
   - Edit profil
   - Upload foto
   - Ganti password
   - Update session
========================================================= */

session_start();

require_once "../config/database.php";


/* =========================================================
   1. CEK LOGIN
========================================================= */

if (!isset($_SESSION["admin_id"])) {

    header("Location: ../auth/login.php");
    exit;

}


$admin_id = (int) $_SESSION["admin_id"];


/* =========================================================
   2. VARIABLE DEFAULT
========================================================= */

$success_message = "";
$error_message = "";


/* =========================================================
   3. CEK KOLOM no_telepon
========================================================= */

$hasNoTelepon = false;

try {

    $columnCheck = $pdo->query("
        SHOW COLUMNS FROM admin LIKE 'no_telepon'
    ");

    $hasNoTelepon = (bool) $columnCheck->fetch();

} catch (PDOException $e) {

    $hasNoTelepon = false;

}


/* =========================================================
   4. AMBIL DATA ADMIN
========================================================= */

try {

    $stmt = $pdo->prepare("
        SELECT
            id,
            nama_lengkap,
            username,
            email,
            password,
            role,
            status,
            foto
        FROM admin
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([
        $admin_id
    ]);

    $admin = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$admin) {

        session_unset();
        session_destroy();

        header("Location: ../auth/login.php");
        exit;

    }


    /*
     * Jika kolom no_telepon memang tersedia,
     * ambil datanya secara terpisah.
     */

    if ($hasNoTelepon) {

        $stmtPhone = $pdo->prepare("
            SELECT no_telepon
            FROM admin
            WHERE id = ?
            LIMIT 1
        ");

        $stmtPhone->execute([
            $admin_id
        ]);

        $admin["no_telepon"] =
            $stmtPhone->fetchColumn() ?: "";

    } else {

        $admin["no_telepon"] = "";

    }


} catch (PDOException $e) {

    $error_message =
        "Data profil tidak dapat dimuat.";

    $admin = [

        "id" => $admin_id,

        "nama_lengkap" =>
            $_SESSION["admin_nama"] ?? "Administrator",

        "username" =>
            $_SESSION["admin_username"] ?? "",

        "email" =>
            $_SESSION["admin_email"] ?? "",

        "password" => "",

        "role" =>
            $_SESSION["admin_role"] ?? "admin",

        "status" => "aktif",

        "foto" =>
            $_SESSION["admin_foto"] ?? "",

        "no_telepon" => ""

    ];

}


/* =========================================================
   5. PROSES POST
========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";


    /* =====================================================
       A. UPDATE INFORMASI PROFIL
    ===================================================== */

    if ($action === "update_profile") {

        $nama_lengkap =
            trim($_POST["nama_lengkap"] ?? "");

        $username =
            trim($_POST["username"] ?? "");

        $email =
            trim($_POST["email"] ?? "");

        $no_telepon =
            trim($_POST["no_telepon"] ?? "");


        /* ---------------------------------------------
           VALIDASI
        --------------------------------------------- */

        if ($nama_lengkap === "") {

            $error_message =
                "Nama lengkap wajib diisi.";

        } elseif ($username === "") {

            $error_message =
                "Username wajib diisi.";

        } elseif ($email === "") {

            $error_message =
                "Email wajib diisi.";

        } elseif (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            $error_message =
                "Format email tidak valid.";

        }


        /* ---------------------------------------------
           CEK USERNAME
        --------------------------------------------- */

        if ($error_message === "") {

            try {

                $stmtUsername = $pdo->prepare("
                    SELECT id
                    FROM admin
                    WHERE username = ?
                    AND id != ?
                    LIMIT 1
                ");

                $stmtUsername->execute([
                    $username,
                    $admin_id
                ]);


                if ($stmtUsername->fetch()) {

                    $error_message =
                        "Username sudah digunakan admin lain.";

                }

            } catch (PDOException $e) {

                $error_message =
                    "Gagal memeriksa username.";

            }

        }


        /* ---------------------------------------------
           CEK EMAIL
        --------------------------------------------- */

        if ($error_message === "") {

            try {

                $stmtEmail = $pdo->prepare("
                    SELECT id
                    FROM admin
                    WHERE email = ?
                    AND id != ?
                    LIMIT 1
                ");

                $stmtEmail->execute([
                    $email,
                    $admin_id
                ]);


                if ($stmtEmail->fetch()) {

                    $error_message =
                        "Email sudah digunakan admin lain.";

                }

            } catch (PDOException $e) {

                $error_message =
                    "Gagal memeriksa email.";

            }

        }


        /* ---------------------------------------------
           UPDATE DATABASE
        --------------------------------------------- */

        if ($error_message === "") {

            try {

                if ($hasNoTelepon) {

                    $update = $pdo->prepare("
                        UPDATE admin
                        SET
                            nama_lengkap = ?,
                            username = ?,
                            email = ?,
                            no_telepon = ?
                        WHERE id = ?
                    ");

                    $update->execute([
                        $nama_lengkap,
                        $username,
                        $email,
                        $no_telepon,
                        $admin_id
                    ]);

                } else {

                    $update = $pdo->prepare("
                        UPDATE admin
                        SET
                            nama_lengkap = ?,
                            username = ?,
                            email = ?
                        WHERE id = ?
                    ");

                    $update->execute([
                        $nama_lengkap,
                        $username,
                        $email,
                        $admin_id
                    ]);

                }


                /* -------------------------------------
                   UPDATE SESSION
                ------------------------------------- */

                $_SESSION["admin_nama"] =
                    $nama_lengkap;

                $_SESSION["admin_username"] =
                    $username;

                $_SESSION["admin_email"] =
                    $email;


                $success_message =
                    "Informasi profil berhasil diperbarui.";


                /* -------------------------------------
                   AMBIL DATA TERBARU
                ------------------------------------- */

                $stmtRefresh = $pdo->prepare("
                    SELECT
                        id,
                        nama_lengkap,
                        username,
                        email,
                        password,
                        role,
                        status,
                        foto
                    FROM admin
                    WHERE id = ?
                    LIMIT 1
                ");

                $stmtRefresh->execute([
                    $admin_id
                ]);

                $admin =
                    $stmtRefresh->fetch(PDO::FETCH_ASSOC);


                if ($hasNoTelepon) {

                    $stmtPhone = $pdo->prepare("
                        SELECT no_telepon
                        FROM admin
                        WHERE id = ?
                        LIMIT 1
                    ");

                    $stmtPhone->execute([
                        $admin_id
                    ]);

                    $admin["no_telepon"] =
                        $stmtPhone->fetchColumn() ?: "";

                } else {

                    $admin["no_telepon"] = "";

                }


            } catch (PDOException $e) {

                $error_message =
                    "Terjadi kesalahan saat memperbarui profil.";

            }

        }

    }


    /* =====================================================
       B. UPDATE PASSWORD
    ===================================================== */

    if ($action === "update_password") {

        $password_lama =
            $_POST["password_lama"] ?? "";

        $password_baru =
            $_POST["password_baru"] ?? "";

        $password_konfirmasi =
            $_POST["password_konfirmasi"] ?? "";


        /* ---------------------------------------------
           VALIDASI
        --------------------------------------------- */

        if (
            $password_lama === "" ||
            $password_baru === "" ||
            $password_konfirmasi === ""
        ) {

            $error_message =
                "Semua kolom password wajib diisi.";

        } elseif (
            strlen($password_baru) < 8
        ) {

            $error_message =
                "Password baru minimal 8 karakter.";

        } elseif (
            $password_baru !==
            $password_konfirmasi
        ) {

            $error_message =
                "Konfirmasi password tidak sesuai.";

        }


        /* ---------------------------------------------
           CEK PASSWORD LAMA
        --------------------------------------------- */

        if ($error_message === "") {

            try {

                $stmtPassword = $pdo->prepare("
                    SELECT password
                    FROM admin
                    WHERE id = ?
                    LIMIT 1
                ");

                $stmtPassword->execute([
                    $admin_id
                ]);

                $passwordHash =
                    $stmtPassword->fetchColumn();


                if (
                    !$passwordHash ||
                    !password_verify(
                        $password_lama,
                        $passwordHash
                    )
                ) {

                    $error_message =
                        "Password lama tidak sesuai.";

                }

            } catch (PDOException $e) {

                $error_message =
                    "Gagal memverifikasi password.";

            }

        }


        /* ---------------------------------------------
           SIMPAN PASSWORD BARU
        --------------------------------------------- */

        if ($error_message === "") {

            try {

                $passwordHashBaru =
                    password_hash(
                        $password_baru,
                        PASSWORD_DEFAULT
                    );


                $updatePassword = $pdo->prepare("
                    UPDATE admin
                    SET password = ?
                    WHERE id = ?
                ");

                $updatePassword->execute([
                    $passwordHashBaru,
                    $admin_id
                ]);


                $success_message =
                    "Password berhasil diperbarui.";


            } catch (PDOException $e) {

                $error_message =
                    "Terjadi kesalahan saat mengubah password.";

            }

        }

    }


    /* =====================================================
       C. UPLOAD FOTO ADMIN
    ===================================================== */

    if ($action === "upload_photo") {

        if (
            !isset($_FILES["foto"]) ||
            $_FILES["foto"]["error"] !== UPLOAD_ERR_OK
        ) {

            $error_message =
                "Silakan pilih foto terlebih dahulu.";

        } else {

            $file = $_FILES["foto"];


            /* -----------------------------------------
               BATAS UKURAN
            ----------------------------------------- */

            $maxSize =
                2 * 1024 * 1024;


            if ($file["size"] > $maxSize) {

                $error_message =
                    "Ukuran foto maksimal 2 MB.";

            }


            /* -----------------------------------------
               CEK MIME TYPE
            ----------------------------------------- */

            if ($error_message === "") {

                $finfo =
                    finfo_open(FILEINFO_MIME_TYPE);

                $mime =
                    finfo_file(
                        $finfo,
                        $file["tmp_name"]
                    );

                finfo_close($finfo);


                $allowedMime = [

                    "image/jpeg" => "jpg",
                    "image/png"  => "png",
                    "image/webp" => "webp"

                ];


                if (
                    !isset(
                        $allowedMime[$mime]
                    )
                ) {

                    $error_message =
                        "Format foto harus JPG, PNG, atau WEBP.";

                }

            }


            /* -----------------------------------------
               CEK GAMBAR VALID
            ----------------------------------------- */

            if ($error_message === "") {

                $imageInfo =
                    @getimagesize(
                        $file["tmp_name"]
                    );


                if ($imageInfo === false) {

                    $error_message =
                        "File yang dipilih bukan gambar yang valid.";

                }

            }


            /* -----------------------------------------
               SIMPAN FOTO
            ----------------------------------------- */

            if ($error_message === "") {

                try {

                    $uploadDir =
                        dirname(__DIR__) .
                        DIRECTORY_SEPARATOR .
                        "uploads" .
                        DIRECTORY_SEPARATOR .
                        "admin";


                    /* Buat folder jika belum ada */

                    if (
                        !is_dir($uploadDir)
                    ) {

                        mkdir(
                            $uploadDir,
                            0755,
                            true
                        );

                    }


                    $extension =
                        $allowedMime[$mime];


                    $newFileName =
                        "admin_" .
                        $admin_id .
                        "_" .
                        bin2hex(
                            random_bytes(8)
                        ) .
                        "." .
                        $extension;


                    $targetFile =
                        $uploadDir .
                        DIRECTORY_SEPARATOR .
                        $newFileName;


                    /* Pindahkan file */

                    if (
                        !move_uploaded_file(
                            $file["tmp_name"],
                            $targetFile
                        )
                    ) {

                        throw new Exception(
                            "File gagal dipindahkan."
                        );

                    }


                    /* ---------------------------------
                       FOTO LAMA
                    --------------------------------- */

                    $oldPhoto =
                        $admin["foto"] ?? "";


                    /* ---------------------------------
                       UPDATE DATABASE
                    --------------------------------- */

                    $stmtPhoto = $pdo->prepare("
                        UPDATE admin
                        SET foto = ?
                        WHERE id = ?
                    ");

                    $stmtPhoto->execute([
                        $newFileName,
                        $admin_id
                    ]);


                    /* ---------------------------------
                       HAPUS FOTO LAMA
                    --------------------------------- */

                    if (
                        !empty($oldPhoto)
                    ) {

                        $oldFile =
                            $uploadDir .
                            DIRECTORY_SEPARATOR .
                            basename(
                                $oldPhoto
                            );


                        if (
                            is_file($oldFile)
                        ) {

                            @unlink(
                                $oldFile
                            );

                        }

                    }


                    /* ---------------------------------
                       UPDATE SESSION
                    --------------------------------- */

                    $_SESSION["admin_foto"] =
                        $newFileName;


                    /* ---------------------------------
                       UPDATE DATA ADMIN
                    --------------------------------- */

                    $admin["foto"] =
                        $newFileName;


                    $success_message =
                        "Foto profil berhasil diperbarui.";


                } catch (Throwable $e) {

                    $error_message =
                        "Foto gagal disimpan.";

                }

            }

        }

    }

}


/* =========================================================
   6. SIAPKAN FOTO ADMIN
========================================================= */

$fotoDatabase =
    trim($admin["foto"] ?? "");


$defaultPhoto =
    "../assets/img/admin/default-admin.png";


if (
    $fotoDatabase !== ""
) {

    $fotoPath =
        "../uploads/admin/" .
        basename($fotoDatabase);


    /*
     * Jika file benar-benar ada,
     * gunakan foto tersebut.
     */

    $physicalPhotoPath =
        dirname(__DIR__) .
        DIRECTORY_SEPARATOR .
        "uploads" .
        DIRECTORY_SEPARATOR .
        "admin" .
        DIRECTORY_SEPARATOR .
        basename($fotoDatabase);


    if (
        is_file($physicalPhotoPath)
    ) {

        $admin_photo =
            $fotoPath;

    } else {

        $admin_photo =
            $defaultPhoto;

    }

} else {

    $admin_photo =
        $defaultPhoto;

}


/* =========================================================
   7. HELPER HTML
========================================================= */

function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}


$adminNama =
    $admin["nama_lengkap"] ?? "Administrator";

$adminUsername =
    $admin["username"] ?? "";

$adminEmail =
    $admin["email"] ?? "";

$adminRole =
    $admin["role"] ?? "admin";

$adminStatus =
    $admin["status"] ?? "aktif";

$adminPhone =
    $admin["no_telepon"] ?? "";


/* =========================================================
   8. UPDATE SESSION FOTO JIKA ADA
========================================================= */

$_SESSION["admin_nama"] =
    $adminNama;

$_SESSION["admin_username"] =
    $adminUsername;

$_SESSION["admin_email"] =
    $adminEmail;

$_SESSION["admin_role"] =
    $adminRole;

$_SESSION["admin_foto"] =
    $admin["foto"] ?? "";

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
        Profil Admin | LSP PPPOLRI
    </title>


    <!-- =================================================
         BOOTSTRAP
    ================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- =================================================
         BOOTSTRAP ICONS
    ================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >


    <!-- =================================================
         GOOGLE FONT
    ================================================== -->

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


    <!-- =================================================
         ADMIN CSS
    ================================================== -->

    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >


    <!-- =================================================
         PROFILE CSS
    ================================================== -->

    <link
        rel="stylesheet"
        href="../assets/css/profile.css"
    >

</head>


<body>


<!-- =====================================================
     SIDEBAR
====================================================== -->

<aside class="admin-sidebar">


    <!-- =================================================
         SIDEBAR BRAND
    ================================================== -->

    <div class="sidebar-brand">

        <div class="brand-logo">

            <i class="bi bi-shield-check"></i>

        </div>


        <div class="brand-text">

            <strong>
                LSP PPPOLRI
            </strong>

            <span>
                Admin Panel
            </span>

        </div>

    </div>



    <!-- =================================================
         SIDEBAR NAVIGATION
    ================================================== -->

    <nav class="sidebar-nav">


        <!-- =================================================
             MENU UTAMA
        ================================================== -->

        <div class="nav-section">

            <span class="nav-section-title">
                MENU UTAMA
            </span>


            <!-- Dashboard -->

            <a
                href="dashboard.php"
                class="sidebar-link"
            >

                <i class="bi bi-grid-fill"></i>

                <span>
                    Dashboard
                </span>

            </a>


            <!-- Berita -->

            <a
                href="berita.php"
                class="sidebar-link"
            >

                <i class="bi bi-newspaper"></i>

                <span>
                    Berita
                </span>

            </a>


            <!-- Galeri -->

            <a
                href="galeri.php"
                class="sidebar-link"
            >

                <i class="bi bi-images"></i>

                <span>
                    Galeri
                </span>

            </a>


            <!-- Skema -->

            <a
                href="skema.php"
                class="sidebar-link"
            >

                <i class="bi bi-award"></i>

                <span>
                    Skema Sertifikasi
                </span>

            </a>


            <!-- FAQ -->

            <a
                href="faq.php"
                class="sidebar-link"
            >

                <i class="bi bi-question-circle"></i>

                <span>
                    FAQ
                </span>

            </a>


            <!-- Peserta -->

            <a
                href="peserta.php"
                class="sidebar-link"
            >

                <i class="bi bi-people"></i>

                <span>
                    Data Peserta
                </span>

            </a>

        </div>



        <!-- =================================================
             MANAGEMENT
        ================================================== -->

        <div class="nav-section">

            <span class="nav-section-title">
                MANAGEMENT
            </span>


            <!-- Kelola Admin -->

            <a
                href="admin.php"
                class="sidebar-link"
            >

                <i class="bi bi-person-gear"></i>

                <span>
                    Kelola Admin
                </span>

            </a>


            <!-- Pengaturan -->

            <a
                href="#"
                class="sidebar-link"
            >

                <i class="bi bi-gear"></i>

                <span>
                    Pengaturan
                </span>

            </a>

        </div>

    </nav>



    <!-- =================================================
         SIDEBAR FOOTER
    ================================================== -->

    <div class="sidebar-footer">

        <a
            href="../auth/logout.php"
            class="sidebar-link"
        >

            <i class="bi bi-box-arrow-right"></i>

            <span>
                Logout
            </span>

        </a>

    </div>

</aside>



<!-- =====================================================
     SIDEBAR OVERLAY
====================================================== -->

<div
    class="sidebar-overlay"
    id="sidebarOverlay"
></div>


<!-- =====================================================
     MAIN
====================================================== -->

<div class="admin-main">


   <!-- =====================================================
     TOPBAR
====================================================== -->

<header class="admin-topbar">


    <!-- =================================================
         HAMBURGER
    ================================================== -->

    <button
        type="button"
        class="sidebar-toggle"
        id="sidebarToggle"
        aria-label="Buka menu"
        aria-expanded="false"
    >

        <i class="bi bi-list"></i>

    </button>



    <!-- =================================================
         TOPBAR TITLE
    ================================================== -->

    <div class="topbar-title">

        <h2>
            Profil Admin
        </h2>

        <span>
            Panel Administrasi LSP PPPOLRI
        </span>

    </div>



    <!-- =================================================
         TOPBAR ACTIONS
    ================================================== -->

    <div class="topbar-actions">


        <!-- NOTIFICATION -->

        <button
            type="button"
            class="notification-btn"
            aria-label="Notifikasi"
        >

            <i class="bi bi-bell"></i>

            <span class="notification-badge">
                0
            </span>

        </button>


            <!-- Admin Profile -->

            <a
                href="profile.php"
                class="admin-profile"
                title="Profil Admin"
            >

                <img
                    src="<?= e($admin_photo); ?>"
                    alt="Foto <?= e($adminNama); ?>"
                >


                <div class="admin-profile-info">

                    <strong>
                        <?= e($adminNama); ?>
                    </strong>

                    <span>
                        <?= e($adminRole); ?>
                    </span>

                </div>


                <i class="bi bi-chevron-down"></i>

            </a>

        </div>

    </header>


    <!-- =================================================
         CONTENT
    ================================================== -->

    <main class="admin-content">


        <!-- PAGE HEADER -->

        <div class="profile-page-header">

            <div>

                <span class="profile-eyebrow">
                    ACCOUNT MANAGEMENT
                </span>

                <h2>
                    Profil Admin
                </h2>

                <p>
                    Kelola informasi akun administrator
                    LSP PPPOLRI.
                </p>

            </div>


            <div class="profile-breadcrumb">

                <a href="dashboard.php">
                    Dashboard
                </a>

                <i class="bi bi-chevron-right"></i>

                <span>
                    Profil Admin
                </span>

            </div>

        </div>


        <!-- =================================================
             ALERT SUCCESS
        ================================================== -->

        <?php if ($success_message !== ""): ?>

            <div
                class="profile-alert profile-alert-success"
                role="alert"
            >

                <i class="bi bi-check-circle-fill"></i>

                <span>
                    <?= e($success_message); ?>
                </span>

            </div>

        <?php endif; ?>


        <!-- =================================================
             ALERT ERROR
        ================================================== -->

        <?php if ($error_message !== ""): ?>

            <div
                class="profile-alert profile-alert-error"
                role="alert"
            >

                <i class="bi bi-exclamation-circle-fill"></i>

                <span>
                    <?= e($error_message); ?>
                </span>

            </div>

        <?php endif; ?>


        <!-- =================================================
             PROFILE HERO
        ================================================== -->

        <section class="profile-hero">


            <!-- FOTO -->

            <div class="profile-avatar-wrapper">


                <img
                    src="<?= e($admin_photo); ?>"
                    alt="Foto <?= e($adminNama); ?>"
                    class="profile-avatar"
                    id="profileAvatar"
                >


                <!-- FORM UPLOAD FOTO -->

                <form
                    action="profile.php"
                    method="POST"
                    enctype="multipart/form-data"
                    id="photoForm"
                >

                    <input
                        type="hidden"
                        name="action"
                        value="upload_photo"
                    >


                    <input
                        type="file"
                        name="foto"
                        id="photoInput"
                        accept="image/jpeg,image/png,image/webp"
                        hidden
                    >


                    <button
                        type="button"
                        class="profile-avatar-edit"
                        id="changePhotoButton"
                        title="Ubah foto profil"
                    >

                        <i class="bi bi-camera-fill"></i>

                    </button>

                </form>

            </div>


            <!-- INFO PROFIL -->

            <div class="profile-hero-info">


                <span class="profile-role-badge">

                    <i class="bi bi-shield-check"></i>

                    <?= e($adminRole); ?>

                </span>


                <h2>
                    <?= e($adminNama); ?>
                </h2>


                <p>

                    <i class="bi bi-person"></i>

                    @<?= e($adminUsername); ?>

                </p>


                <p>

                    <i class="bi bi-envelope"></i>

                    <?= e($adminEmail); ?>

                </p>

            </div>


            <!-- STATUS -->

            <div class="profile-status">

                <span class="status-dot"></span>

                <span>
                    <?= strtolower($adminStatus) === "aktif"
                        ? "Akun Aktif"
                        : "Akun Tidak Aktif"; ?>
                </span>

            </div>

        </section>


        <!-- =================================================
             PROFILE GRID
        ================================================== -->

        <div class="profile-grid">


            <!-- =================================================
                 INFORMASI PERSONAL
            ================================================== -->

            <section
                class="profile-card profile-information-card"
            >


                <div class="profile-card-header">

                    <div>

                        <span class="profile-card-icon">

                            <i class="bi bi-person-vcard-fill"></i>

                        </span>

                    </div>


                    <div>

                        <h3>
                            Informasi Personal
                        </h3>

                        <p>
                            Perbarui informasi akun administrator.
                        </p>

                    </div>

                </div>


                <form
                    action="profile.php"
                    method="POST"
                    class="profile-form"
                >

                    <input
                        type="hidden"
                        name="action"
                        value="update_profile"
                    >


                    <!-- NAMA -->

                    <div class="profile-form-group">

                        <label for="nama_lengkap">
                            Nama Lengkap
                        </label>

                        <div class="profile-input-wrapper">

                            <i class="bi bi-person"></i>

                            <input
                                type="text"
                                id="nama_lengkap"
                                name="nama_lengkap"
                                value="<?= e($adminNama); ?>"
                                placeholder="Masukkan nama lengkap"
                                autocomplete="name"
                                required
                            >

                        </div>

                    </div>


                    <!-- USERNAME -->

                    <div class="profile-form-group">

                        <label for="username">
                            Username
                        </label>

                        <div class="profile-input-wrapper">

                            <i class="bi bi-at"></i>

                            <input
                                type="text"
                                id="username"
                                name="username"
                                value="<?= e($adminUsername); ?>"
                                placeholder="Masukkan username"
                                autocomplete="username"
                                required
                            >

                        </div>

                    </div>


                    <!-- EMAIL -->

                    <div class="profile-form-group">

                        <label for="email">
                            Email
                        </label>

                        <div class="profile-input-wrapper">

                            <i class="bi bi-envelope"></i>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?= e($adminEmail); ?>"
                                placeholder="Masukkan email"
                                autocomplete="email"
                                required
                            >

                        </div>

                    </div>


                    <!-- TELEPON -->

                    <div class="profile-form-group">

                        <label for="no_telepon">
                            Nomor Telepon
                        </label>

                        <div class="profile-input-wrapper">

                            <i class="bi bi-telephone"></i>

                            <input
                                type="tel"
                                id="no_telepon"
                                name="no_telepon"
                                value="<?= e($adminPhone); ?>"
                                placeholder="Masukkan nomor telepon"
                                autocomplete="tel"
                                <?= !$hasNoTelepon
                                    ? 'disabled'
                                    : ''; ?>
                            >

                        </div>

                        <?php if (!$hasNoTelepon): ?>

                            <small>
                                Kolom nomor telepon belum tersedia
                                pada database.
                            </small>

                        <?php endif; ?>

                    </div>


                    <!-- ROLE -->

                    <div class="profile-form-group">

                        <label for="role">
                            Role
                        </label>

                        <div class="profile-input-wrapper">

                            <i class="bi bi-shield-check"></i>

                            <input
                                type="text"
                                id="role"
                                value="<?= e($adminRole); ?>"
                                readonly
                                disabled
                            >

                        </div>

                        <small>
                            Role hanya dapat diubah melalui
                            pengaturan administrator.
                        </small>

                    </div>


                    <!-- BUTTON -->

                    <div class="profile-form-actions">

                        <button
                            type="submit"
                            class="profile-btn profile-btn-primary"
                        >

                            <i class="bi bi-check-lg"></i>

                            Simpan Perubahan

                        </button>

                    </div>

                </form>

            </section>


            <!-- =================================================
                 KEAMANAN AKUN
            ================================================== -->

            <section
                class="profile-card profile-security-card"
            >


                <div class="profile-card-header">

                    <div>

                        <span class="profile-card-icon">

                            <i class="bi bi-shield-lock-fill"></i>

                        </span>

                    </div>


                    <div>

                        <h3>
                            Keamanan Akun
                        </h3>

                        <p>
                            Perbarui password administrator.
                        </p>

                    </div>

                </div>


                <form
                    action="profile.php"
                    method="POST"
                    class="profile-form"
                    id="passwordForm"
                >

                    <input
                        type="hidden"
                        name="action"
                        value="update_password"
                    >


                    <!-- PASSWORD LAMA -->

                    <div class="profile-form-group">

                        <label for="password_lama">
                            Password Lama
                        </label>

                        <div class="profile-input-wrapper">

                            <i class="bi bi-lock"></i>

                            <input
                                type="password"
                                id="password_lama"
                                name="password_lama"
                                placeholder="Masukkan password lama"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                data-target="password_lama"
                                aria-label="Tampilkan password"
                            >

                                <i class="bi bi-eye"></i>

                            </button>

                        </div>

                    </div>


                    <!-- PASSWORD BARU -->

                    <div class="profile-form-group">

                        <label for="password_baru">
                            Password Baru
                        </label>

                        <div class="profile-input-wrapper">

                            <i class="bi bi-key"></i>

                            <input
                                type="password"
                                id="password_baru"
                                name="password_baru"
                                placeholder="Minimal 8 karakter"
                                autocomplete="new-password"
                                minlength="8"
                                required
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                data-target="password_baru"
                                aria-label="Tampilkan password"
                            >

                                <i class="bi bi-eye"></i>

                            </button>

                        </div>

                    </div>


                    <!-- KONFIRMASI -->

                    <div class="profile-form-group">

                        <label for="password_konfirmasi">
                            Konfirmasi Password Baru
                        </label>

                        <div class="profile-input-wrapper">

                            <i class="bi bi-shield-lock"></i>

                            <input
                                type="password"
                                id="password_konfirmasi"
                                name="password_konfirmasi"
                                placeholder="Ulangi password baru"
                                autocomplete="new-password"
                                minlength="8"
                                required
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                data-target="password_konfirmasi"
                                aria-label="Tampilkan password"
                            >

                                <i class="bi bi-eye"></i>

                            </button>

                        </div>

                    </div>


                    <!-- PASSWORD INFO -->

                    <div class="password-info">

                        <i class="bi bi-info-circle-fill"></i>

                        <div>

                            <strong>
                                Tips keamanan
                            </strong>

                            <p>
                                Gunakan password minimal 8 karakter
                                dengan kombinasi huruf dan angka.
                            </p>

                        </div>

                    </div>


                    <!-- BUTTON -->

                    <div class="profile-form-actions">

                        <button
                            type="submit"
                            class="profile-btn profile-btn-dark"
                        >

                            <i class="bi bi-shield-lock"></i>

                            Ubah Password

                        </button>

                    </div>

                </form>

            </section>

        </div>


        <!-- =================================================
             ACCOUNT INFORMATION
        ================================================== -->

        <section class="profile-account-info">


            <div class="account-info-item">

                <div class="account-info-icon">

                    <i class="bi bi-person-check-fill"></i>

                </div>

                <div>

                    <span>
                        Status Akun
                    </span>

                    <strong>
                        <?= strtolower($adminStatus) === "aktif"
                            ? "Aktif"
                            : "Tidak Aktif"; ?>
                    </strong>

                </div>

            </div>


            <div class="account-info-item">

                <div class="account-info-icon">

                    <i class="bi bi-shield-fill-check"></i>

                </div>

                <div>

                    <span>
                        Role
                    </span>

                    <strong>
                        <?= e($adminRole); ?>
                    </strong>

                </div>

            </div>


            <div class="account-info-item">

                <div class="account-info-icon">

                    <i class="bi bi-database-check"></i>

                </div>

                <div>

                    <span>
                        Database
                    </span>

                    <strong>
                        Connected
                    </strong>

                </div>

            </div>

        </section>


    </main>


    <!-- =================================================
         FOOTER
    ================================================== -->

    <footer class="admin-footer">

        <p>
            &copy;
            <?= date("Y"); ?>
            LSP PPPOLRI.
            Admin Dashboard.
        </p>

    </footer>


</div>


<!-- =====================================================
     JAVASCRIPT
====================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>


<script
    src="../assets/js/admin.js"
></script>


<script
    src="../assets/js/profile.js"
></script>


<script>

/* =========================================================
   UPLOAD FOTO
========================================================= */

const changePhotoButton =
    document.getElementById(
        "changePhotoButton"
    );

const photoInput =
    document.getElementById(
        "photoInput"
    );

const photoForm =
    document.getElementById(
        "photoForm"
    );


if (
    changePhotoButton &&
    photoInput &&
    photoForm
) {

    changePhotoButton.addEventListener(
        "click",
        function () {

            photoInput.click();

        }
    );


    photoInput.addEventListener(
        "change",
        function () {

            if (
                photoInput.files &&
                photoInput.files.length > 0
            ) {

                const file =
                    photoInput.files[0];


                /*
                 * Validasi ukuran di browser
                 */

                if (
                    file.size >
                    2 * 1024 * 1024
                ) {

                    alert(
                        "Ukuran foto maksimal 2 MB."
                    );

                    photoInput.value = "";

                    return;

                }


                /*
                 * Validasi tipe file
                 */

                const allowedTypes = [

                    "image/jpeg",
                    "image/png",
                    "image/webp"

                ];


                if (
                    !allowedTypes.includes(
                        file.type
                    )
                ) {

                    alert(
                        "Format foto harus JPG, PNG, atau WEBP."
                    );

                    photoInput.value = "";

                    return;

                }


                /*
                 * Submit otomatis
                 */

                photoForm.submit();

            }

        }
    );

}


/* =========================================================
   TOGGLE PASSWORD
========================================================= */

document
    .querySelectorAll(
        ".password-toggle"
    )
    .forEach(
        function (button) {

            button.addEventListener(
                "click",
                function () {

                    const targetId =
                        button.dataset.target;

                    const input =
                        document.getElementById(
                            targetId
                        );


                    if (!input) {

                        return;

                    }


                    if (
                        input.type === "password"
                    ) {

                        input.type =
                            "text";

                        button.innerHTML =
                            '<i class="bi bi-eye-slash"></i>';

                    } else {

                        input.type =
                            "password";

                        button.innerHTML =
                            '<i class="bi bi-eye"></i>';

                    }

                }
            );

        }
    );

</script>


</body>
</html>