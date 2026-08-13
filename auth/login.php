<?php

session_start();


// =====================================================
// JIKA SUDAH LOGIN
// =====================================================

if (isset($_SESSION["admin_id"])) {

    header("Location: ../admin/dashboard.php");
    exit;

}


// =====================================================
// AMBIL PESAN ERROR DARI PROSES LOGIN
// =====================================================

$error = $_SESSION["login_error"] ?? "";

unset($_SESSION["login_error"]);

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
        Login Admin | LSP PPPOLRI
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
         BOOTSTRAP ICONS
    ====================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >


    <!-- =====================================================
         LOGIN CSS
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../assets/css/login.css"
    >

</head>


<body>


    <!-- =====================================================
         LOGIN PAGE
    ====================================================== -->

    <main class="login-page">


        <!-- =================================================
             BACKGROUND DECORATION
        ================================================== -->

        <div class="login-decoration login-decoration-one"></div>

        <div class="login-decoration login-decoration-two"></div>


        <!-- =================================================
             LOGIN CONTAINER
        ================================================== -->

        <div class="login-container">


            <!-- =================================================
                 LEFT / BRAND
            ================================================== -->

            <section class="login-brand">


                <div class="brand-content">


                    <!-- Logo -->

                    <div class="brand-logo">

                        <i class="bi bi-shield-check"></i>

                    </div>


                    <!-- Brand -->

                    <span class="brand-label">
                        SISTEM INFORMASI
                    </span>

                    <h1>
                        LSP PPPOLRI
                    </h1>

                    <p>
                        Panel administrasi untuk mengelola
                        informasi dan layanan website
                        LSP PPPOLRI.
                    </p>


                    <!-- Information -->

                    <div class="brand-info">


                        <div class="brand-info-item">

                            <div class="brand-info-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>

                            <div>

                                <strong>
                                    Sistem Terintegrasi
                                </strong>

                                <span>
                                    Kelola website dalam satu panel.
                                </span>

                            </div>

                        </div>


                        <div class="brand-info-item">

                            <div class="brand-info-icon">
                                <i class="bi bi-lock"></i>
                            </div>

                            <div>

                                <strong>
                                    Akses Terlindungi
                                </strong>

                                <span>
                                    Halaman khusus administrator.
                                </span>

                            </div>

                        </div>


                    </div>

                </div>


                <!-- Brand Footer -->

                <div class="brand-footer">

                    <span>
                        &copy; <?= date("Y"); ?> LSP PPPOLRI
                    </span>

                </div>

            </section>



            <!-- =================================================
                 RIGHT / FORM
            ================================================== -->

            <section class="login-form-section">


                <div class="login-form-wrapper">


                    <!-- Mobile Logo -->

                    <div class="mobile-logo">

                        <div class="mobile-logo-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>

                        <div>

                            <strong>
                                LSP PPPOLRI
                            </strong>

                            <span>
                                Admin Panel
                            </span>

                        </div>

                    </div>


                    <!-- Heading -->

                    <div class="login-heading">

                        <span class="login-label">
                            ADMIN PANEL
                        </span>

                        <h2>
                            Selamat Datang
                        </h2>

                        <p>
                            Silakan masuk menggunakan akun
                            administrator Anda.
                        </p>

                    </div>


                    <!-- Error -->

                    <?php if ($error !== ""): ?>

                        <div class="login-alert">

                            <i class="bi bi-exclamation-circle-fill"></i>

                            <div>

                                <strong>
                                    Login gagal
                                </strong>

                                <span>
                                    <?= htmlspecialchars($error); ?>
                                </span>

                            </div>

                        </div>

                    <?php endif; ?>


                    <!-- =================================================
                         FORM LOGIN
                    ================================================== -->

                    <form
                         action="proses-login.php"
                            method="POST"
                            class="login-form"
                    >


                        <!-- Username -->

                        <div class="form-group">

                            <label for="username">
                                Username
                            </label>

                            <div class="input-wrapper">

                                <i class="bi bi-person"></i>

                                <input
                                    type="text"
                                    id="username"
                                    name="username"
                                    placeholder="Masukkan username"
                                    autocomplete="username"
                                    value="<?= htmlspecialchars($_POST["username"] ?? ""); ?>"
                                    required
                                    autofocus
                                >

                            </div>

                        </div>


                        <!-- Password -->

                        <div class="form-group">

                            <div class="form-label-row">

                                <label for="password">
                                    Password
                                </label>

                            </div>


                            <div class="input-wrapper">

                                <i class="bi bi-lock"></i>

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="Masukkan password"
                                    autocomplete="current-password"
                                    required
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

                        </div>


                        <!-- Remember / Info -->

                        <div class="login-info">

                            <span>
                                <i class="bi bi-shield-lock"></i>

                                Akses hanya untuk administrator
                            </span>

                        </div>


                        <!-- Submit -->

                        <button
                            type="submit"
                            class="login-button"
                        >

                            <span>
                                Masuk ke Dashboard
                            </span>

                            <i class="bi bi-arrow-right"></i>

                        </button>


                    </form>


                    <!-- Back -->

                    <a
                        href="../index.php"
                        class="back-home"
                    >

                        <i class="bi bi-arrow-left"></i>

                        Kembali ke website

                    </a>


                </div>

            </section>


        </div>


    </main>


    <!-- =====================================================
         PASSWORD TOGGLE
    ====================================================== -->

    <script>

        const passwordInput =
            document.getElementById("password");

        const passwordToggle =
            document.getElementById("passwordToggle");


        if (passwordToggle && passwordInput) {

            passwordToggle.addEventListener(
                "click",
                function () {

                    const icon =
                        this.querySelector("i");


                    if (passwordInput.type === "password") {

                        passwordInput.type = "text";

                        icon.classList.remove("bi-eye");

                        icon.classList.add("bi-eye-slash");

                        this.setAttribute(
                            "aria-label",
                            "Sembunyikan password"
                        );

                    } else {

                        passwordInput.type = "password";

                        icon.classList.remove("bi-eye-slash");

                        icon.classList.add("bi-eye");

                        this.setAttribute(
                            "aria-label",
                            "Tampilkan password"
                        );

                    }

                }
            );

        }

    </script>


</body>

</html>