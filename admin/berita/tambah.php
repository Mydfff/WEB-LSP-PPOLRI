<?php
// ==========================================================
// TAMBAH BERITA - LSP PPPOLRI
// File: admin/berita/tambah.php
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
// DATA ADMIN
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
// VARIABEL
// ==========================================================

$errors = [];

$judul = "";
$kategori = "";
$isi = "";
$status = "draft";

// ==========================================================
// PROSES FORM
// ==========================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ======================================================
    // AMBIL DATA FORM
    // ======================================================

    $judul = trim($_POST["judul"] ?? "");
    $kategori = trim($_POST["kategori"] ?? "");
    $isi = trim($_POST["isi"] ?? "");
    $status = $_POST["status"] ?? "draft";


    // ======================================================
    // VALIDASI JUDUL
    // ======================================================

    if ($judul === "") {

        $errors[] = "Judul berita wajib diisi.";

    }


    // ======================================================
    // VALIDASI KATEGORI
    // ======================================================

    $kategoriValid = [
        "Kegiatan",
        "Informasi",
        "Sertifikasi",
        "Pengumuman"
    ];

    if (!in_array($kategori, $kategoriValid, true)) {

        $errors[] = "Kategori berita tidak valid.";

    }


    // ======================================================
    // VALIDASI ISI
    // ======================================================

    if ($isi === "") {

        $errors[] = "Isi berita wajib diisi.";

    }


    // ======================================================
    // VALIDASI STATUS
    // ======================================================

    if (!in_array($status, ["draft", "publish"], true)) {

        $status = "draft";

    }


    // ======================================================
    // VARIABEL GAMBAR
    // ======================================================

    $namaGambar = null;


    // ======================================================
    // CEK UPLOAD GAMBAR
    // ======================================================

    if (
        isset($_FILES["gambar"]) &&
        $_FILES["gambar"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {

        $gambar = $_FILES["gambar"];


        // ==================================================
        // CEK ERROR UPLOAD
        // ==================================================

        if ($gambar["error"] !== UPLOAD_ERR_OK) {

            $errors[] = "Gambar gagal diupload.";

        } else {

            // ==================================================
            // INFORMASI FILE
            // ==================================================

            $namaAsli = $gambar["name"];
            $tmpName = $gambar["tmp_name"];
            $ukuran = $gambar["size"];


            // ==================================================
            // EXTENSION
            // ==================================================

            $extension = strtolower(
                pathinfo(
                    $namaAsli,
                    PATHINFO_EXTENSION
                )
            );


            // ==================================================
            // EXTENSION YANG DIIZINKAN
            // ==================================================

            $allowedExtensions = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];


            // ==================================================
            // MIME TYPE YANG DIIZINKAN
            // ==================================================

            $allowedMimeTypes = [
                "image/jpeg",
                "image/png",
                "image/webp"
            ];


            // ==================================================
            // DETEKSI MIME
            // ==================================================

            $mimeType = mime_content_type($tmpName);


            // ==================================================
            // VALIDASI UKURAN
            // ==================================================

            if ($ukuran > 2 * 1024 * 1024) {

                $errors[] =
                    "Ukuran gambar maksimal 2 MB.";

            }


            // ==================================================
            // VALIDASI EXTENSION
            // ==================================================

            if (!in_array(
                $extension,
                $allowedExtensions,
                true
            )) {

                $errors[] =
                    "Format gambar harus JPG, JPEG, PNG, atau WEBP.";

            }


            // ==================================================
            // VALIDASI MIME
            // ==================================================

            if (!in_array(
                $mimeType,
                $allowedMimeTypes,
                true
            )) {

                $errors[] =
                    "File yang diupload bukan gambar yang valid.";

            }


            // ==================================================
            // SIMPAN GAMBAR
            // ==================================================

            if (empty($errors)) {

                // ==============================================
                // FOLDER UPLOAD
                // ==============================================

                $folderUpload = "../../uploads/berita/";


                // ==============================================
                // BUAT FOLDER JIKA BELUM ADA
                // ==============================================

                if (!is_dir($folderUpload)) {

                    mkdir(
                        $folderUpload,
                        0777,
                        true
                    );

                }


                // ==============================================
                // NAMA FILE UNIK
                // ==============================================

                $namaGambar =
                    "berita-" .
                    date("YmdHis") .
                    "-" .
                    uniqid() .
                    "." .
                    $extension;


                // ==============================================
                // TARGET FILE
                // ==============================================

                $targetFile =
                    $folderUpload .
                    $namaGambar;


                // ==============================================
                // PINDAHKAN FILE
                // ==============================================

                if (!move_uploaded_file(
                    $tmpName,
                    $targetFile
                )) {

                    $errors[] =
                        "Gambar gagal disimpan.";

                    $namaGambar = null;

                }

            }

        }

    }


    // ======================================================
    // SIMPAN KE DATABASE
    // ======================================================

    if (empty($errors)) {

        try {

            $query = "
                INSERT INTO berita
                (
                    judul,
                    kategori,
                    gambar,
                    isi,
                    status,
                    created_at
                )
                VALUES
                (
                    :judul,
                    :kategori,
                    :gambar,
                    :isi,
                    :status,
                    NOW()
                )
            ";


            $stmt = $pdo->prepare($query);


            $stmt->execute([

                ":judul" => $judul,

                ":kategori" => $kategori,

                ":gambar" => $namaGambar,

                ":isi" => $isi,

                ":status" => $status

            ]);


            // ==================================================
            // PESAN BERHASIL
            // ==================================================

            $_SESSION["success"] =
                "Berita berhasil ditambahkan.";


            // ==================================================
            // KEMBALI KE DAFTAR
            // ==================================================

            header("Location: daftar.php");

            exit;


        } catch (PDOException $e) {

            // ==================================================
            // HAPUS GAMBAR JIKA DATABASE GAGAL
            // ==================================================

            if ($namaGambar !== null) {

                $fileGambar =
                    "../../uploads/berita/" .
                    $namaGambar;


                if (file_exists($fileGambar)) {

                    unlink($fileGambar);

                }

            }


            $errors[] =
                "Data berita gagal disimpan ke database.";

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

    <title>
        Tambah Berita | LSP PPPOLRI
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
         MOBILE OVERLAY
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
                    Tambah Berita
                </h2>

                <span>
                    Tambahkan berita baru LSP PPPOLRI
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


            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <section class="dashboard-welcome">

                <span class="dashboard-label">
                    CONTENT MANAGEMENT
                </span>

                <h1>
                    Tambah Berita
                </h1>

                <p>
                    Tambahkan berita baru untuk ditampilkan
                    pada website LSP PPPOLRI.
                </p>

            </section>


            <!-- =================================================
                 ERROR
            ================================================== -->

            <?php if (!empty($errors)): ?>

                <div
                    class="alert alert-danger"
                    role="alert"
                >

                    <strong>
                        Terjadi kesalahan:
                    </strong>


                    <ul class="mb-0 mt-2">

                        <?php foreach ($errors as $error): ?>

                            <li>
                                <?php echo e($error); ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 FORM CARD
            ================================================== -->

            <section class="dashboard-card">


                <div class="mb-4">

                    <span class="card-label">
                        BERITA
                    </span>

                    <h3 class="mb-1">
                        Form Tambah Berita
                    </h3>

                    <p class="text-muted mb-0">
                        Isi data berita yang akan ditampilkan
                        pada website.
                    </p>

                </div>


                <!-- =================================================
                     FORM
                ================================================== -->

                <form
                    action=""
                    method="POST"
                    enctype="multipart/form-data"
                >


                    <div class="row g-4">


                        <!-- JUDUL -->

                        <div class="col-12">

                            <label
                                for="judul"
                                class="form-label fw-semibold"
                            >

                                Judul Berita

                            </label>


                            <input
                                type="text"
                                class="form-control"
                                id="judul"
                                name="judul"
                                placeholder="Masukkan judul berita"
                                value="<?php echo e($judul); ?>"
                                required
                            >

                        </div>


                        <!-- KATEGORI -->

                        <div class="col-md-6">

                            <label
                                for="kategori"
                                class="form-label fw-semibold"
                            >

                                Kategori

                            </label>


                            <select
                                class="form-select"
                                id="kategori"
                                name="kategori"
                                required
                            >

                                <option value="">
                                    -- Pilih Kategori --
                                </option>


                                <option
                                    value="Kegiatan"
                                    <?php
                                    echo $kategori === "Kegiatan"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Kegiatan
                                </option>


                                <option
                                    value="Informasi"
                                    <?php
                                    echo $kategori === "Informasi"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Informasi
                                </option>


                                <option
                                    value="Sertifikasi"
                                    <?php
                                    echo $kategori === "Sertifikasi"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Sertifikasi
                                </option>


                                <option
                                    value="Pengumuman"
                                    <?php
                                    echo $kategori === "Pengumuman"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Pengumuman
                                </option>

                            </select>

                        </div>


                        <!-- STATUS -->

                        <div class="col-md-6">

                            <label
                                for="status"
                                class="form-label fw-semibold"
                            >

                                Status

                            </label>


                            <select
                                class="form-select"
                                id="status"
                                name="status"
                            >

                                <option
                                    value="draft"
                                    <?php
                                    echo $status === "draft"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Draft
                                </option>


                                <option
                                    value="publish"
                                    <?php
                                    echo $status === "publish"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Publish
                                </option>

                            </select>

                        </div>


                        <!-- GAMBAR -->

                        <div class="col-12">

                            <label
                                for="gambar"
                                class="form-label fw-semibold"
                            >

                                Gambar Berita

                            </label>


                            <input
                                type="file"
                                class="form-control"
                                id="gambar"
                                name="gambar"
                                accept=".jpg,.jpeg,.png,.webp"
                            >


                            <div class="form-text">

                                Format JPG, JPEG, PNG, atau WEBP.
                                Maksimal 2 MB.

                            </div>


                            <!-- PREVIEW -->

                            <div
                                class="mt-3"
                                style="
                                    width: 100%;
                                    height: 250px;
                                    border: 2px dashed #ddd;
                                    border-radius: 10px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    overflow: hidden;
                                    background: #fafafa;
                                "
                            >

                                <div
                                    id="imagePlaceholder"
                                    class="text-center text-muted"
                                >

                                    <i
                                        class="bi bi-image fs-1 d-block mb-2"
                                    ></i>

                                    <span>
                                        Preview gambar
                                    </span>

                                </div>


                                <img
                                    id="previewImage"
                                    src=""
                                    alt="Preview gambar"
                                    style="
                                        width: 100%;
                                        height: 100%;
                                        object-fit: cover;
                                        display: none;
                                    "
                                >

                            </div>

                        </div>


                        <!-- ISI -->

                        <div class="col-12">

                            <label
                                for="isi"
                                class="form-label fw-semibold"
                            >

                                Isi Berita

                            </label>


                            <textarea
                                class="form-control"
                                id="isi"
                                name="isi"
                                rows="10"
                                placeholder="Tuliskan isi berita..."
                                required
                            ><?php echo e($isi); ?></textarea>

                        </div>


                        <!-- BUTTON -->

                        <div class="col-12">

                            <hr>


                            <div
                                class="d-flex justify-content-end gap-2"
                            >


                                <a
                                    href="daftar.php"
                                    class="btn btn-outline-secondary"
                                >

                                    <i class="bi bi-arrow-left me-1"></i>

                                    Batal

                                </a>


                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                >

                                    <i class="bi bi-save me-1"></i>

                                    Simpan Berita

                                </button>

                            </div>

                        </div>

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
     PREVIEW GAMBAR
========================================================== -->

<script>

const inputGambar =
    document.getElementById("gambar");

const previewImage =
    document.getElementById("previewImage");

const imagePlaceholder =
    document.getElementById("imagePlaceholder");


inputGambar.addEventListener(
    "change",
    function () {

        const file = this.files[0];


        if (!file) {

            previewImage.src = "";

            previewImage.style.display = "none";

            imagePlaceholder.style.display = "block";

            return;

        }


        // Cek ukuran di browser

        if (file.size > 2 * 1024 * 1024) {

            alert(
                "Ukuran gambar maksimal 2 MB."
            );

            this.value = "";

            previewImage.src = "";

            previewImage.style.display = "none";

            imagePlaceholder.style.display = "block";

            return;

        }


        const reader =
            new FileReader();


        reader.onload =
            function (event) {

                previewImage.src =
                    event.target.result;

                previewImage.style.display =
                    "block";

                imagePlaceholder.style.display =
                    "none";

            };


        reader.readAsDataURL(file);

    }
);

</script>


</body>

</html>