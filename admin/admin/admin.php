
<?php

// ==========================================================
// KELOLA ADMIN - ADMIN LSP PPPOLRI
// File: admin/admin/admin.php
// ==========================================================

session_start();


// ==========================================================
// CEK LOGIN
// ==========================================================

if (!isset($_SESSION["admin_id"])) {

    header("Location: ../auth/login.php");

    exit;
}


// ==========================================================
// KONEKSI DATABASE
// ==========================================================

require_once "../../config/database.php";


// ==========================================================
// DATA ADMIN
// ==========================================================

$adminNama = $_SESSION["admin_nama"] ?? "Administrator";
$adminRole = $_SESSION["admin_role"] ?? "Administrator";


// ==========================================================
// PAGE INFORMATION
// ==========================================================

$pageTitle = "Kelola Administrator";
$pageSubtitle = "Kelola akun administrator LSP PPPOLRI";


// ==========================================================
// DATA ADMIN YANG SEDANG LOGIN
// ==========================================================

$admin_id = $_SESSION["admin_id"];

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

$currentAdmin = $stmtLogin->fetch(PDO::FETCH_ASSOC);


// ==========================================================
// JIKA DATA ADMIN LOGIN TIDAK DITEMUKAN
// ==========================================================

if (!$currentAdmin) {

    session_unset();
    session_destroy();

    header("Location: ../auth/login.php");

    exit;
}


// ==========================================================
// DATA SEMUA ADMIN
// ==========================================================

$stmtAdmin = $pdo->query("
    SELECT
        id,
        nama_lengkap,
        username,
        email,
        role,
        status,
        created_at
    FROM admin
    ORDER BY id ASC
");

$admins = $stmtAdmin->fetchAll(PDO::FETCH_ASSOC);


// ==========================================================
// STATISTIK ADMIN
// ==========================================================

$totalAdmin = count($admins);

$adminAktif = 0;
$adminNonaktif = 0;

foreach ($admins as $admin) {

    if ($admin["status"] === "aktif") {

        $adminAktif++;

    } else {

        $adminNonaktif++;
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
        Kelola Admin | LSP PPPOLRI
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
        href="../../assets/css/admin.css"
    >


    <!-- =====================================================
         KELOLA ADMIN CSS
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../../assets/css/kelola-admin.css"
    >

</head>


<body>


<div class="admin-wrapper">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <?php require_once "../components/sidebar.php"; ?>


    <!-- =====================================================
         MAIN AREA
    ====================================================== -->

    <div class="admin-main">


        <!-- =================================================
             HEADER
        ================================================== -->

        <?php require_once "../components/header.php"; ?>


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
                        Kelola Administrator
                    </h1>

                    <p>
                        Kelola akun administrator yang terdaftar
                        dalam sistem LSP PPPOLRI.
                    </p>

                </div>


                <a
                    href="admin-tambah.php"
                    class="btn-add-admin"
                >

                    <i class="bi bi-person-plus-fill"></i>

                    Tambah Admin

                </a>

            </section>


            <!-- =================================================
                 STATISTICS
            ================================================== -->

            <section class="admin-statistics">


                <!-- TOTAL ADMIN -->

                <div class="admin-stat-card">

                    <div class="admin-stat-info">

                        <span>
                            Total Admin
                        </span>

                        <strong>
                            <?= $totalAdmin; ?>
                        </strong>

                    </div>

                    <div class="admin-stat-icon">

                        <i class="bi bi-people"></i>

                    </div>

                </div>


                <!-- ADMIN AKTIF -->

                <div class="admin-stat-card">

                    <div class="admin-stat-info">

                        <span>
                            Admin Aktif
                        </span>

                        <strong>
                            <?= $adminAktif; ?>
                        </strong>

                    </div>

                    <div class="admin-stat-icon">

                        <i class="bi bi-person-check"></i>

                    </div>

                </div>


                <!-- ADMIN NONAKTIF -->

                <div class="admin-stat-card">

                    <div class="admin-stat-info">

                        <span>
                            Admin Nonaktif
                        </span>

                        <strong>
                            <?= $adminNonaktif; ?>
                        </strong>

                    </div>

                    <div class="admin-stat-icon">

                        <i class="bi bi-person-x"></i>

                    </div>

                </div>


            </section>


            <!-- =================================================
                 TABLE
            ================================================== -->

            <section class="admin-table-card">


                <!-- TABLE HEADER -->

                <div class="admin-table-header">

                    <div>

                        <span class="card-label">
                            ADMINISTRATOR
                        </span>

                        <h3>
                            Daftar Administrator
                        </h3>

                    </div>

                    <span>
                        <?= count($admins); ?> data ditemukan
                    </span>

                </div>


                <!-- TABLE -->

                <div class="admin-table-wrapper">


                    <?php if (!empty($admins)): ?>


                        <table class="admin-table">


                            <thead>

                                <tr>

                                    <th>
                                        No
                                    </th>

                                    <th>
                                        Administrator
                                    </th>

                                    <th>
                                        Username
                                    </th>

                                    <th>
                                        Email
                                    </th>

                                    <th>
                                        Role
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th>
                                        Tanggal Dibuat
                                    </th>

                                    <th>
                                        Aksi
                                    </th>

                                </tr>

                            </thead>


                            <tbody>


                                <?php foreach ($admins as $index => $admin): ?>


                                    <tr>


                                        <!-- NO -->

                                        <td>
                                            <?= $index + 1; ?>
                                        </td>


                                        <!-- ADMINISTRATOR -->

                                        <td>

                                            <span class="admin-name">

                                                <?= htmlspecialchars(
                                                    $admin["nama_lengkap"]
                                                ); ?>

                                            </span>


                                            <?php if (
                                                (int) $admin["id"] ===
                                                (int) $currentAdmin["id"]
                                            ): ?>

                                                <span class="admin-current">
                                                    Anda
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- USERNAME -->

                                        <td>

                                            <?= htmlspecialchars(
                                                $admin["username"]
                                            ); ?>

                                        </td>


                                        <!-- EMAIL -->

                                        <td>

                                            <?= htmlspecialchars(
                                                $admin["email"] ?? "-"
                                            ); ?>

                                        </td>


                                        <!-- ROLE -->

                                        <td>

                                            <?php if (
                                                $admin["role"] === "superadmin"
                                            ): ?>

                                                <span class="admin-role superadmin">
                                                    Superadmin
                                                </span>

                                            <?php else: ?>

                                                <span class="admin-role">
                                                    Admin
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- STATUS -->

                                        <td>

                                            <?php if (
                                                $admin["status"] === "aktif"
                                            ): ?>

                                                <span class="admin-status active">

                                                    <i class="bi bi-check-circle-fill"></i>

                                                    Aktif

                                                </span>

                                            <?php else: ?>

                                                <span class="admin-status inactive">

                                                    <i class="bi bi-x-circle-fill"></i>

                                                    Nonaktif

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- TANGGAL -->

                                        <td>

                                            <?php

                                            if (!empty($admin["created_at"])) {

                                                echo date(
                                                    "d-m-Y",
                                                    strtotime(
                                                        $admin["created_at"]
                                                    )
                                                );

                                            } else {

                                                echo "-";
                                            }

                                            ?>

                                        </td>


                                       
                                        <!-- =====================================================
                                            AKSI
                                        ====================================================== -->

                                        <td>

                                            <div class="admin-actions">

                                                <!-- =================================================
                                                    EDIT ADMIN
                                                ================================================== -->

                                                <a
                                                    href="./admin-edit.php?id=<?= (int) $admin["id"]; ?>"
                                                    class="btn-admin-edit"
                                                    title="Edit Admin"
                                                >
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>


                                                <!-- =================================================
                                                    HAPUS ADMIN
                                                ================================================== -->

                                                <?php if (
                                                    (int) $admin["id"] !==
                                                    (int) $currentAdmin["id"]
                                                ): ?>

                                                    
                                                <button
                                                    type="button"
                                                    class="btn-admin-delete"
                                                    title="Hapus Admin"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalHapusAdmin"
                                                    data-admin-id="<?= (int) $admin["id"]; ?>"
                                                    data-admin-nama="<?= htmlspecialchars($admin["nama_lengkap"], ENT_QUOTES, "UTF-8"); ?>"
                                                >
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>


                                                <?php else: ?>

                                                    <!--
                                                        Admin yang sedang login
                                                        tidak dapat menghapus dirinya sendiri
                                                    -->

                                                    <button
                                                        type="button"
                                                        class="btn-admin-delete disabled"
                                                        disabled
                                                        title="Akun yang sedang digunakan tidak dapat dihapus"
                                                    >
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>

                                                <?php endif; ?>

                                            </div>

                                        </td>


                                    </tr>


                                <?php endforeach; ?>


                            </tbody>


                        </table>


                    <?php else: ?>


                        <!-- =================================================
                             DATA KOSONG
                        ================================================== -->

                        <div class="admin-empty">

                            <i class="bi bi-people"></i>

                            <h4>
                                Belum Ada Administrator
                            </h4>

                            <p>
                                Belum terdapat akun administrator
                                yang terdaftar.
                            </p>

                            <a
                                href="admin-tambah.php"
                                class="btn-add-admin"
                            >

                                <i class="bi bi-person-plus-fill"></i>

                                Tambah Admin

                            </a>

                        </div>


                    <?php endif; ?>


                </div>


            </section>
            
            <!-- ==========================================================
                MODAL KONFIRMASI HAPUS ADMIN
            ========================================================== -->

            <div
                class="modal fade"
                id="modalHapusAdmin"
                tabindex="-1"
                aria-labelledby="modalHapusAdminLabel"
                aria-hidden="true"
            >
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="modalHapusAdminLabel">
                                Konfirmasi Hapus
                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Tutup"
                            ></button>
                        </div>

                        <div class="modal-body text-center">

                            <div class="mb-3">
                                <i
                                    class="bi bi-trash-fill"
                                    style="font-size: 42px;"
                                ></i>
                            </div>

                            <h5>Hapus Administrator?</h5>

                            <p class="text-muted mb-0">
                                Apakah Anda yakin ingin menghapus admin
                                <strong id="namaAdminHapus"></strong>?
                            </p>

                            <p class="text-muted small mt-2 mb-0">
                                Data administrator yang dihapus tidak dapat dikembalikan.
                            </p>

                        </div>

                        <div class="modal-footer justify-content-center">

                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                            >
                                Tidak
                            </button>

                            <a
                                href="#"
                                id="btnKonfirmasiHapus"
                                class="btn btn-danger"
                            >
                                <i class="bi bi-trash-fill me-1"></i>
                                Hapus
                            </a>

                        </div>

                    </div>
                </div>
            </div>
            ```


        </main>


        <!-- =====================================================
             FOOTER
        ====================================================== -->

        <?php require_once "../components/footer.php"; ?>


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
            src="../../assets/js/admin.js"
        ></script>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../../assets/js/admin.js"></script>

        <script>
        document.addEventListener("DOMContentLoaded", function () {

            const modalHapus = document.getElementById("modalHapusAdmin");
            const namaAdminHapus = document.getElementById("namaAdminHapus");
            const btnKonfirmasiHapus = document.getElementById("btnKonfirmasiHapus");

            if (!modalHapus) {
                return;
            }

            modalHapus.addEventListener("show.bs.modal", function (event) {

                const button = event.relatedTarget;

                const adminId = button.getAttribute("data-admin-id");
                const adminNama = button.getAttribute("data-admin-nama");

                namaAdminHapus.textContent = adminNama;

                btnKonfirmasiHapus.href =
                    "./admin-hapus.php?id=" + adminId;
            });

        });
        </script>

</body>

</html>
