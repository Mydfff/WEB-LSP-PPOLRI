<?php
// ==========================================================
// DAFTAR BERITA - LSP PPPOLRI
// File: admin/berita/daftar.php
// ==========================================================

session_start();

// ==========================================================
// CEK LOGIN
// ==========================================================

if (!isset($_SESSION["admin_id"])) {

    header("Location: ../../auth/login.php");
    exit;

}

// ==========================================================
// KONEKSI DATABASE
// ==========================================================

require_once "../../config/database.php";

// ==========================================================
// DATA ADMIN DARI SESSION
// ==========================================================

$adminNama = $_SESSION["admin_nama"] ?? "Administrator";
$adminRole = $_SESSION["admin_role"] ?? "Administrator";

// ==========================================================
// HELPER HTML
// ==========================================================

function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    );
}

// ==========================================================
// AMBIL DATA BERITA DARI DATABASE
// ==========================================================

$beritaList = [];

try {

    $stmt = $pdo->query("
        SELECT
            id,
            judul,
            kategori,
            gambar,
            isi,
            status,
            created_at
        FROM berita
        ORDER BY created_at DESC
    ");

    $beritaList = $stmt->fetchAll();

} catch (PDOException $e) {

    $beritaList = [];

    $databaseError =
        "Data berita gagal diambil dari database.";

}

// ==========================================================
// PESAN SUCCESS
// ==========================================================

$successMessage = $_SESSION["success"] ?? "";

unset($_SESSION["success"]);

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
        Daftar Berita | LSP PPPOLRI
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

</head>


<body>


<!-- =========================================================
     ADMIN WRAPPER
========================================================== -->

<div class="admin-wrapper">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside
        class="admin-sidebar"
        id="adminSidebar"
    >


        <!-- SIDEBAR BRAND -->

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


        <!-- SIDEBAR NAVIGATION -->

        <nav class="sidebar-nav">


            <!-- MENU UTAMA -->

            <div class="nav-section">

                <span class="nav-section-title">
                    MENU UTAMA
                </span>


                <!-- DASHBOARD -->

                <a
                    href="../dashboard.php"
                    class="sidebar-link"
                >

                    <i class="bi bi-grid-fill"></i>

                    <span>
                        Dashboard
                    </span>

                </a>


                <!-- BERITA -->

                <a
                    href="daftar.php"
                    class="sidebar-link active"
                >

                    <i class="bi bi-newspaper"></i>

                    <span>
                        Berita
                    </span>

                </a>


                <!-- GALERI -->

                <a
                    href="../galeri.php"
                    class="sidebar-link"
                >

                    <i class="bi bi-images"></i>

                    <span>
                        Galeri
                    </span>

                </a>


                <!-- SKEMA -->

                <a
                    href="../skema.php"
                    class="sidebar-link"
                >

                    <i class="bi bi-award"></i>

                    <span>
                        Skema Sertifikasi
                    </span>

                </a>


                <!-- FAQ -->

                <a
                    href="../faq.php"
                    class="sidebar-link"
                >

                    <i class="bi bi-question-circle"></i>

                    <span>
                        FAQ
                    </span>

                </a>


                <!-- PESERTA -->

                <a
                    href="../peserta.php"
                    class="sidebar-link"
                >

                    <i class="bi bi-people"></i>

                    <span>
                        Data Peserta
                    </span>

                </a>

            </div>


            <!-- MANAGEMENT -->

            <div class="nav-section">

                <span class="nav-section-title">
                    MANAGEMENT
                </span>


                <!-- KELOLA ADMIN -->

                <a
                    href="../admin.php"
                    class="sidebar-link"
                >

                    <i class="bi bi-person-gear"></i>

                    <span>
                        Kelola Admin
                    </span>

                </a>


                <!-- PENGATURAN -->

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


        <!-- SIDEBAR FOOTER -->

        <div class="sidebar-footer">

            <a
                href="../../auth/logout.php"
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
         OVERLAY MOBILE
    ====================================================== -->

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>


    <!-- =====================================================
         MAIN AREA
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
                    Berita
                </h2>

                <span>
                    Kelola berita dan informasi LSP PPPOLRI
                </span>

            </div>


            <!-- TOPBAR RIGHT -->

            <div class="topbar-actions">


                <!-- NOTIFICATION -->

                <button
                    type="button"
                    class="notification-btn"
                    aria-label="Notifikasi"
                >

                    <i class="bi bi-bell"></i>

                    <span class="notification-badge">
                        3
                    </span>

                </button>


                <!-- PROFILE -->

                <a
                    href="../profile.php"
                    class="admin-profile"
                    title="Profil Admin"
                >

                    <div class="admin-avatar">

                        <i class="bi bi-person-fill"></i>

                    </div>


                    <div class="admin-profile-info">

                        <strong>
                            <?php echo e($adminNama); ?>
                        </strong>

                        <span>
                            <?php echo e($adminRole); ?>
                        </span>

                    </div>


                    <i class="bi bi-chevron-down profile-arrow"></i>

                </a>

            </div>

        </header>


        <!-- =================================================
             CONTENT
        ================================================== -->

        <main class="dashboard-content">

            <?php if (isset($_SESSION["success"])): ?>

    <div
        class="alert alert-success alert-dismissible fade show"
        role="alert"
    >

        <i class="bi bi-check-circle me-2"></i>

        <?php
        echo e($_SESSION["success"]);
        ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>

    </div>

    <?php unset($_SESSION["success"]); ?>

<?php endif; ?>


<?php if (isset($_SESSION["error"])): ?>

    <div
        class="alert alert-danger alert-dismissible fade show"
        role="alert"
    >

        <i class="bi bi-exclamation-circle me-2"></i>

        <?php
        echo e($_SESSION["error"]);
        ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>

    </div>

    <?php unset($_SESSION["error"]); ?>

<?php endif; ?>
            <!-- =================================================
                 HEADER HALAMAN
            ================================================== -->

            <section class="dashboard-welcome">

                <span class="dashboard-label">
                    CONTENT MANAGEMENT
                </span>

                <h1>
                    Daftar Berita
                </h1>

                <p>
                    Kelola berita dan pengumuman yang akan
                    ditampilkan pada website LSP PPPOLRI.
                </p>

            </section>


            <!-- =================================================
                 PESAN BERHASIL
            ================================================== -->

            <?php if ($successMessage !== ""): ?>

                <div
                    class="alert alert-success alert-dismissible fade show"
                    role="alert"
                >

                    <i class="bi bi-check-circle me-2"></i>

                    <?php echo e($successMessage); ?>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"
                    ></button>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 ERROR DATABASE
            ================================================== -->

            <?php if (isset($databaseError)): ?>

                <div
                    class="alert alert-danger"
                    role="alert"
                >

                    <i class="bi bi-exclamation-triangle me-2"></i>

                    <?php echo e($databaseError); ?>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 TOOLBAR
            ================================================== -->

            <section class="dashboard-card">


                <div
                    class="d-flex justify-content-between align-items-center flex-wrap gap-3"
                >

                    <div>

                        <span class="card-label">
                            BERITA
                        </span>

                        <h3 class="mb-0">
                            Semua Berita
                        </h3>

                    </div>


                    <!-- TOMBOL TAMBAH -->

                    <a
                        href="tambah.php"
                        class="btn btn-danger"
                    >

                        <i class="bi bi-plus-lg"></i>

                        Tambah Berita

                    </a>

                </div>


                <!-- =================================================
                     SEARCH
                ================================================== -->

                <div class="row mt-4">

                    <div class="col-lg-6">

                        <div class="input-group">

                            <span class="input-group-text">

                                <i class="bi bi-search"></i>

                            </span>


                            <input
                                type="text"
                                class="form-control"
                                id="searchBerita"
                                placeholder="Cari judul berita..."
                            >

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     TABLE
                ================================================== -->

                <div class="table-responsive mt-4">

                    <table
                        class="table align-middle"
                        id="tableBerita"
                    >

                        <thead>

                            <tr>

                                <th style="width: 70px;">
                                    No
                                </th>

                                <th style="width: 110px;">
                                    Foto
                                </th>

                                <th>
                                    Judul Berita
                                </th>

                                <th>
                                    Kategori
                                </th>

                                <th>
                                    Tanggal
                                </th>

                                <th>
                                    Status
                                </th>

                                <th
                                    class="text-center"
                                    style="width: 160px;"
                                >
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            <?php if (!empty($beritaList)): ?>


                                <?php foreach (
                                    $beritaList
                                    as $index => $berita
                                ): ?>


                                    <tr>


                                        <!-- NO -->

                                        <td>

                                            <?php
                                            echo $index + 1;
                                            ?>

                                        </td>


                                        <!-- FOTO -->

                                        <td>

                                            <?php if (
                                                !empty(
                                                    $berita["gambar"]
                                                )
                                            ): ?>

                                                <img
                                                    src="<?php
                                                    echo e(
                                                        "../../uploads/berita/" .
                                                        $berita["gambar"]
                                                    );
                                                    ?>"
                                                    alt="<?php
                                                    echo e(
                                                        $berita["judul"]
                                                    );
                                                    ?>"
                                                    style="
                                                        width:70px;
                                                        height:55px;
                                                        object-fit:cover;
                                                        border-radius:8px;
                                                    "
                                                >

                                            <?php else: ?>

                                                <div
                                                    style="
                                                        width:70px;
                                                        height:55px;
                                                        border-radius:8px;
                                                        background:#f1f3f5;
                                                        display:flex;
                                                        align-items:center;
                                                        justify-content:center;
                                                    "
                                                >

                                                    <i
                                                        class="bi bi-image text-muted"
                                                    ></i>

                                                </div>

                                            <?php endif; ?>

                                        </td>


                                        <!-- JUDUL -->

                                        <td>

                                            <strong>

                                                <?php
                                                echo e(
                                                    $berita["judul"]
                                                );
                                                ?>

                                            </strong>

                                        </td>


                                        <!-- KATEGORI -->

                                        <td>

                                            <span
                                                class="badge text-bg-light"
                                            >

                                                <?php
                                                echo e(
                                                    $berita["kategori"]
                                                );
                                                ?>

                                            </span>

                                        </td>


                                        <!-- TANGGAL -->

                                        <td>

                                            <span>

                                                <i
                                                    class="bi bi-calendar3 me-1"
                                                ></i>

                                                <?php

                                                echo date(
                                                    "d M Y",
                                                    strtotime(
                                                        $berita["created_at"]
                                                    )
                                                );

                                                ?>

                                            </span>

                                        </td>


                                        <!-- STATUS -->

                                        <td>

                                            <?php if (
                                                $berita["status"]
                                                === "publish"
                                            ): ?>

                                                <span
                                                    class="badge text-bg-success"
                                                >

                                                    Published

                                                </span>

                                            <?php else: ?>

                                                <span
                                                    class="badge text-bg-secondary"
                                                >

                                                    Draft

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- AKSI -->

                                        <td>

                                            <div
                                                class="d-flex justify-content-center gap-2"
                                            >


                                                <!-- EDIT -->

                                                <a
                                                    href="edit.php?id=<?php
                                                    echo (int)
                                                        $berita["id"];
                                                    ?>"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Edit Berita"
                                                >

                                                    <i
                                                        class="bi bi-pencil-square"
                                                    ></i>

                                                </a>


                                                <!-- HAPUS -->

                                                <a
                                                    href="hapus.php?id=<?php
                                                    echo (int)
                                                        $berita["id"];
                                                    ?>"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Hapus Berita"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini?');"
                                                >

                                                    <i
                                                        class="bi bi-trash"
                                                    ></i>

                                                </a>

                                            </div>

                                        </td>

                                    </tr>


                                <?php endforeach; ?>


                            <?php else: ?>


                                <!-- BELUM ADA DATA -->

                                <tr>

                                    <td
                                        colspan="7"
                                        class="text-center py-5"
                                    >

                                        <i
                                            class="bi bi-newspaper fs-1 d-block mb-3 text-muted"
                                        ></i>


                                        <strong>
                                            Belum ada berita
                                        </strong>


                                        <p class="mb-3 text-muted">

                                            Silakan tambahkan
                                            berita baru.

                                        </p>


                                        <a
                                            href="tambah.php"
                                            class="btn btn-danger"
                                        >

                                            <i
                                                class="bi bi-plus-lg me-1"
                                            ></i>

                                            Tambah Berita

                                        </a>

                                    </td>

                                </tr>


                            <?php endif; ?>


                        </tbody>

                    </table>

                </div>


            </section>


        </main>


        <!-- =================================================
             FOOTER
        ================================================== -->

        <footer class="admin-footer">

            <p>

                &copy;
                <?php echo date("Y"); ?>

                LSP PPPOLRI.
                Admin Dashboard.

            </p>

        </footer>


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


<!-- =========================================================
     SEARCH BERITA
========================================================== -->

<script>

const searchBerita =
    document.getElementById("searchBerita");


if (searchBerita) {

    searchBerita.addEventListener(
        "keyup",
        function () {

            const keyword =
                this.value.toLowerCase().trim();


            const rows =
                document.querySelectorAll(
                    "#tableBerita tbody tr"
                );


            rows.forEach(function (row) {

                const text =
                    row.innerText.toLowerCase();


                if (text.includes(keyword)) {

                    row.style.display = "";

                } else {

                    row.style.display = "none";

                }

            });

        }
    );

}

</script>


</body>

</html>