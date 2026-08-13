<?php

session_start();

require_once "../config/database.php";


/*
|--------------------------------------------------------------------------
| CEK LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['admin_id'])) {

    header("Location: ../auth/login.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| DATA ADMIN YANG SEDANG LOGIN
|--------------------------------------------------------------------------
*/

$admin_id = $_SESSION['admin_id'];

$stmtLogin = $pdo->prepare("
    SELECT
        id,
        nama_lengkap,
        username,
        email,
        role,
        status
    FROM admin
    WHERE id = ?
    LIMIT 1
");

$stmtLogin->execute([
    $admin_id
]);

$currentAdmin = $stmtLogin->fetch();


/*
|--------------------------------------------------------------------------
| JIKA DATA ADMIN LOGIN TIDAK DITEMUKAN
|--------------------------------------------------------------------------
*/

if (!$currentAdmin) {

    session_unset();

    session_destroy();

    header("Location: ../auth/login.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL ID ADMIN YANG AKAN DIEDIT
|--------------------------------------------------------------------------
*/

$edit_id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


/*
|--------------------------------------------------------------------------
| VALIDASI ID
|--------------------------------------------------------------------------
*/

if ($edit_id <= 0) {

    header("Location: admin.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL DATA ADMIN
|--------------------------------------------------------------------------
*/

$stmtAdmin = $pdo->prepare("
    SELECT
        id,
        nama_lengkap,
        username,
        email,
        role,
        status
    FROM admin
    WHERE id = ?
    LIMIT 1
");

$stmtAdmin->execute([
    $edit_id
]);

$admin = $stmtAdmin->fetch();


/*
|--------------------------------------------------------------------------
| JIKA ADMIN TIDAK DITEMUKAN
|--------------------------------------------------------------------------
*/

if (!$admin) {

    header("Location: admin.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| VARIABLE FORM
|--------------------------------------------------------------------------
*/

$error = "";

$nama_lengkap = $admin['nama_lengkap'];
$username = $admin['username'];
$email = $admin['email'] ?? "";
$role = $admin['role'];
$status = $admin['status'];


/*
|--------------------------------------------------------------------------
| PROSES UPDATE ADMIN
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama_lengkap = trim(
        $_POST["nama_lengkap"] ?? ""
    );

    $username = trim(
        $_POST["username"] ?? ""
    );

    $email = trim(
        $_POST["email"] ?? ""
    );

    $password = $_POST["password"] ?? "";

    $role = $_POST["role"] ?? "admin";

    $status = $_POST["status"] ?? "aktif";


    /*
    |--------------------------------------------------------------------------
    | VALIDASI DATA
    |--------------------------------------------------------------------------
    */

    if ($nama_lengkap === "") {

        $error = "Nama lengkap wajib diisi.";

    } elseif ($username === "") {

        $error = "Username wajib diisi.";

    } elseif (
        $password !== "" &&
        strlen($password) < 6
    ) {

        $error = "Password minimal 6 karakter.";

    } elseif (
        !in_array(
            $role,
            ["admin", "superadmin"],
            true
        )
    ) {

        $error = "Role tidak valid.";

    } elseif (
        !in_array(
            $status,
            ["aktif", "nonaktif"],
            true
        )
    ) {

        $error = "Status tidak valid.";

    }


    /*
    |--------------------------------------------------------------------------
    | CEK USERNAME
    |--------------------------------------------------------------------------
    */

    if ($error === "") {

        $stmtUsername = $pdo->prepare("
            SELECT
                id
            FROM admin
            WHERE username = ?
            AND id != ?
            LIMIT 1
        ");

        $stmtUsername->execute([
            $username,
            $edit_id
        ]);

        if ($stmtUsername->fetch()) {

            $error = "Username sudah digunakan oleh admin lain.";

        }
    }


    /*
    |--------------------------------------------------------------------------
    | CEK EMAIL
    |--------------------------------------------------------------------------
    */

    if (
        $error === "" &&
        $email !== ""
    ) {

        $stmtEmail = $pdo->prepare("
            SELECT
                id
            FROM admin
            WHERE email = ?
            AND id != ?
            LIMIT 1
        ");

        $stmtEmail->execute([
            $email,
            $edit_id
        ]);

        if ($stmtEmail->fetch()) {

            $error = "Email sudah digunakan oleh admin lain.";

        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE DATA
    |--------------------------------------------------------------------------
    */

    if ($error === "") {


        /*
        |--------------------------------------------------------------------------
        | JIKA PASSWORD DIISI
        |--------------------------------------------------------------------------
        */

        if ($password !== "") {

            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            $stmtUpdate = $pdo->prepare("
                UPDATE admin
                SET
                    nama_lengkap = ?,
                    username = ?,
                    email = ?,
                    password = ?,
                    role = ?,
                    status = ?
                WHERE id = ?
            ");


            $stmtUpdate->execute([

                $nama_lengkap,

                $username,

                $email !== ""
                    ? $email
                    : null,

                $passwordHash,

                $role,

                $status,

                $edit_id

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | JIKA PASSWORD KOSONG
        |--------------------------------------------------------------------------
        */

        else {

            $stmtUpdate = $pdo->prepare("
                UPDATE admin
                SET
                    nama_lengkap = ?,
                    username = ?,
                    email = ?,
                    role = ?,
                    status = ?
                WHERE id = ?
            ");


            $stmtUpdate->execute([

                $nama_lengkap,

                $username,

                $email !== ""
                    ? $email
                    : null,

                $role,

                $status,

                $edit_id

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | JIKA ADMIN YANG DIEDIT ADALAH AKUN SENDIRI
        |--------------------------------------------------------------------------
        */

        if (
            (int)$edit_id ===
            (int)$currentAdmin['id']
        ) {

            /*
            |--------------------------------------------------------------------------
            | UPDATE SESSION USERNAME / DATA YANG RELEVAN
            |--------------------------------------------------------------------------
            */

            $_SESSION['admin_username'] = $username;

        }


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        header(
            "Location: admin.php?success=admin_updated"
        );

        exit;
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
        Edit Admin - LSP PPPOLRI
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
        href="https://fonts.googleapis.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <!-- =====================================================
         CSS UTAMA ADMIN

         SAMA DENGAN ADMIN TAMBAH
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >


    <!-- =====================================================
         CSS FORM ADMIN

         MENGGUNAKAN STYLE FORM YANG SAMA
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../assets/css/admin-tambah.css"
    >

</head>


<body>

<div class="admin-wrapper">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="admin-sidebar">


        <!-- BRAND -->

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
             NAVIGATION
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
                    class="sidebar-link active"
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
             FOOTER
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


        <!-- =================================================
             TOPBAR
        ================================================== -->

        <header class="admin-topbar">


            <!-- HAMBURGER -->

            <button
                type="button"
                class="sidebar-toggle"
                id="sidebarToggle"
                aria-label="Buka menu"
                aria-expanded="false"
            >

                <i class="bi bi-list"></i>

            </button>



            <!-- TITLE -->

            <div class="topbar-title">

                <h2>
                    Edit Admin
                </h2>

                <span>
                    Panel Administrasi LSP PPPOLRI
                </span>

            </div>



            <!-- ACTIONS -->

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



                <!-- PROFILE -->

                <a
                    href="profile.php"
                    class="admin-profile"
                    title="Profil Admin"
                >

                    <div class="admin-avatar">

                        <i class="bi bi-person-fill"></i>

                    </div>


                    <div class="admin-profile-info">

                        <strong>
                            <?= htmlspecialchars(
                                $currentAdmin['nama_lengkap']
                            ); ?>
                        </strong>

                        <span>
                            <?= htmlspecialchars(
                                $currentAdmin['role']
                            ); ?>
                        </span>

                    </div>


                    <i class="bi bi-chevron-down profile-arrow"></i>

                </a>

            </div>

        </header>



        <!-- =================================================
             CONTENT
        ================================================== -->

        <main class="admin-content">


            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <section class="admin-page-header">


                <div>

                    <span class="admin-page-label">
                        MANAJEMEN SISTEM
                    </span>


                    <h1>
                        Edit Administrator
                    </h1>


                    <p>
                        Perbarui informasi akun administrator
                        yang terdaftar dalam sistem LSP PPPOLRI.
                    </p>

                </div>



                <!-- KEMBALI -->

                <a
                    href="admin.php"
                    class="btn-back-admin"
                >

                    <i class="bi bi-arrow-left"></i>

                    Kembali

                </a>

            </section>



            <!-- =================================================
                 FORM CARD
            ================================================== -->

            <section class="admin-form-card">


                <!-- FORM HEADER -->

                <div class="admin-form-card-header">

                    <div class="admin-form-card-icon">

                        <i class="bi bi-person-gear"></i>

                    </div>


                    <div>

                        <span>
                            ADMINISTRATOR
                        </span>

                        <h2>
                            Informasi Administrator
                        </h2>

                        <p>
                            Perbarui data administrator
                            yang akan disimpan ke sistem.
                        </p>

                    </div>

                </div>



                <!-- =================================================
                     ERROR
                ================================================== -->

                <?php if ($error !== ""): ?>

                    <div class="admin-form-alert error">

                        <i
                            class="bi bi-exclamation-circle-fill"
                        ></i>

                        <span>
                            <?= htmlspecialchars($error); ?>
                        </span>

                    </div>

                <?php endif; ?>



                <!-- =================================================
                     FORM
                ================================================== -->

                <form
                    method="POST"
                    action=""
                    class="admin-form"
                    autocomplete="off"
                >


                    <div class="form-grid">


                        <!-- =================================================
                             NAMA LENGKAP
                        ================================================== -->

                        <div class="form-group">

                            <label for="nama_lengkap">

                                Nama Lengkap

                                <span>
                                    *
                                </span>

                            </label>


                            <input
                                type="text"
                                id="nama_lengkap"
                                name="nama_lengkap"
                                value="<?= htmlspecialchars(
                                    $nama_lengkap
                                ); ?>"
                                placeholder="Contoh: Budi Santoso"
                                required
                            >

                        </div>



                        <!-- =================================================
                             USERNAME
                        ================================================== -->

                        <div class="form-group">

                            <label for="username">

                                Username

                                <span>
                                    *
                                </span>

                            </label>


                            <input
                                type="text"
                                id="username"
                                name="username"
                                value="<?= htmlspecialchars(
                                    $username
                                ); ?>"
                                placeholder="Masukkan username"
                                required
                            >

                        </div>



                        <!-- =================================================
                             EMAIL
                        ================================================== -->

                        <div class="form-group">

                            <label for="email">

                                Email

                            </label>


                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?= htmlspecialchars(
                                    $email
                                ); ?>"
                                placeholder="admin@lsppppolri.id"
                            >

                        </div>



                        <!-- =================================================
                             PASSWORD
                        ================================================== -->

                        <div class="form-group">

                            <label for="password">

                                Password Baru

                            </label>


                            <div class="password-input">

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="Kosongkan jika tidak diubah"
                                    minlength="6"
                                >


                                <button
                                    type="button"
                                    class="password-toggle"
                                    id="passwordToggle"
                                    aria-label="Tampilkan password"
                                >

                                    <i class="bi bi-eye"></i>

                                </button>

                            </div>


                            <small>
                                Kosongkan jika password lama tetap digunakan.
                            </small>

                        </div>



                        <!-- =================================================
                             ROLE
                        ================================================== -->

                        <div class="form-group">

                            <label for="role">

                                Role

                                <span>
                                    *
                                </span>

                            </label>


                            <select
                                id="role"
                                name="role"
                                required
                            >

                                <option
                                    value="admin"
                                    <?= $role === "admin"
                                        ? "selected"
                                        : ""; ?>
                                >
                                    Admin
                                </option>


                                <option
                                    value="superadmin"
                                    <?= $role === "superadmin"
                                        ? "selected"
                                        : ""; ?>
                                >
                                    Superadmin
                                </option>

                            </select>

                        </div>



                        <!-- =================================================
                             STATUS
                        ================================================== -->

                        <div class="form-group">

                            <label for="status">

                                Status

                                <span>
                                    *
                                </span>

                            </label>


                            <select
                                id="status"
                                name="status"
                                required
                            >

                                <option
                                    value="aktif"
                                    <?= $status === "aktif"
                                        ? "selected"
                                        : ""; ?>
                                >
                                    Aktif
                                </option>


                                <option
                                    value="nonaktif"
                                    <?= $status === "nonaktif"
                                        ? "selected"
                                        : ""; ?>
                                >
                                    Nonaktif
                                </option>

                            </select>

                        </div>

                    </div>



                    <!-- =================================================
                         FORM ACTION
                    ================================================== -->

                    <div class="form-actions">


                        <a
                            href="admin.php"
                            class="btn-form-cancel"
                        >

                            Batal

                        </a>


                        <button
                            type="submit"
                            class="btn-form-submit"
                        >

                            <i class="bi bi-save-fill"></i>

                            Simpan Perubahan

                        </button>

                    </div>

                </form>

            </section>

        </main>



        <!-- =================================================
             FOOTER
        ================================================== -->

        <footer class="admin-footer">

            <p>

                &copy;

                <?= date('Y'); ?>

                LSP PPPOLRI.

                Admin Dashboard.

            </p>

        </footer>

    </div>

</div>



<!-- =====================================================
     ADMIN JS
====================================================== -->

<script src="../assets/js/admin.js"></script>



<!-- =====================================================
     PASSWORD TOGGLE
====================================================== -->

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const passwordInput =
            document.getElementById("password");

        const passwordToggle =
            document.getElementById("passwordToggle");


        if (
            passwordInput &&
            passwordToggle
        ) {

            passwordToggle.addEventListener(
                "click",
                function () {

                    const icon =
                        passwordToggle.querySelector("i");


                    if (
                        passwordInput.type ===
                        "password"
                    ) {

                        passwordInput.type =
                            "text";

                        icon.classList.remove(
                            "bi-eye"
                        );

                        icon.classList.add(
                            "bi-eye-slash"
                        );

                    } else {

                        passwordInput.type =
                            "password";

                        icon.classList.remove(
                            "bi-eye-slash"
                        );

                        icon.classList.add(
                            "bi-eye"
                        );

                    }

                }
            );

        }

    }
);

</script>


</body>

</html>