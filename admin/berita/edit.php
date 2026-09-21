<?php
// ==========================================================
// EDIT BERITA - LSP PPPOLRI
// File: admin/berita/edit.php
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
// AMBIL ID DARI URL
// ==========================================================

$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

// ==========================================================
// CEK ID
// ==========================================================

if (!$id) {

    header("Location: daftar.php");
    exit;

}

// ==========================================================
// AMBIL DATA BERITA
// ==========================================================

try {

    $stmt = $pdo->prepare("
        SELECT
            id,
            judul,
            kategori,
            gambar,
            isi,
            status,
            created_at
        FROM berita
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);

    $berita = $stmt->fetch();

} catch (PDOException $e) {

    die("Data berita gagal diambil dari database.");

}

// ==========================================================
// CEK DATA
// ==========================================================

if (!$berita) {

    header("Location: daftar.php");
    exit;

}

// ==========================================================
// VARIABEL FORM
// ==========================================================

$judul = $berita["judul"];
$kategori = $berita["kategori"];
$isi = $berita["isi"];
$status = $berita["status"];
$gambarLama = $berita["gambar"];

$errors = [];

// ==========================================================
// PROSES UPDATE
// ==========================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ======================================================
    // AMBIL DATA FORM
    // ======================================================

    $judul = trim($_POST["judul"] ?? "");

    $kategori = trim(
        $_POST["kategori"] ?? ""
    );

    $isi = trim(
        $_POST["isi"] ?? ""
    );

    $status = $_POST["status"] ?? "draft";


    // ======================================================
    // VALIDASI JUDUL
    // ======================================================

    if ($judul === "") {

        $errors[] =
            "Judul berita wajib diisi.";

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

    if (!in_array(
        $kategori,
        $kategoriValid,
        true
    )) {

        $errors[] =
            "Kategori berita tidak valid.";

    }


    // ======================================================
    // VALIDASI ISI
    // ======================================================

    if ($isi === "") {

        $errors[] =
            "Isi berita wajib diisi.";

    }


    // ======================================================
    // VALIDASI STATUS
    // ======================================================

    if (!in_array(
        $status,
        ["draft", "publish"],
        true
    )) {

        $status = "draft";

    }


    // ======================================================
    // GAMBAR BARU
    // ======================================================

    $namaGambarBaru = null;


    // ======================================================
    // CEK UPLOAD GAMBAR
    // ======================================================

    if (
        isset($_FILES["gambar"]) &&
        $_FILES["gambar"]["error"]
        !== UPLOAD_ERR_NO_FILE
    ) {

        $gambar = $_FILES["gambar"];


        // ==================================================
        // ERROR UPLOAD
        // ==================================================

        if (
            $gambar["error"]
            !== UPLOAD_ERR_OK
        ) {

            $errors[] =
                "Gambar gagal diupload.";

        } else {

            // ==================================================
            // DATA FILE
            // ==================================================

            $namaAsli =
                $gambar["name"];

            $tmpName =
                $gambar["tmp_name"];

            $ukuran =
                $gambar["size"];


            // ==================================================
            // EXTENSION
            // ==================================================

            $extension =
                strtolower(
                    pathinfo(
                        $namaAsli,
                        PATHINFO_EXTENSION
                    )
                );


            // ==================================================
            // EXTENSION DIIZINKAN
            // ==================================================

            $allowedExtensions = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];


            // ==================================================
            // MIME DIIZINKAN
            // ==================================================

            $allowedMimeTypes = [
                "image/jpeg",
                "image/png",
                "image/webp"
            ];


            // ==================================================
            // MIME FILE
            // ==================================================

            $mimeType =
                mime_content_type(
                    $tmpName
                );


            // ==================================================
            // VALIDASI UKURAN
            // ==================================================

            if (
                $ukuran >
                2 * 1024 * 1024
            ) {

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
            // SIMPAN GAMBAR BARU
            // ==================================================

            if (empty($errors)) {

                $folderUpload =
                    "../../uploads/berita/";


                // ==============================================
                // BUAT FOLDER
                // ==============================================

                if (!is_dir(
                    $folderUpload
                )) {

                    mkdir(
                        $folderUpload,
                        0777,
                        true
                    );

                }


                // ==============================================
                // NAMA FILE BARU
                // ==============================================

                $namaGambarBaru =
                    "berita-" .
                    date("YmdHis") .
                    "-" .
                    uniqid() .
                    "." .
                    $extension;


                // ==============================================
                // TARGET
                // ==============================================

                $targetFile =
                    $folderUpload .
                    $namaGambarBaru;


                // ==============================================
                // PINDAHKAN FILE
                // ==============================================

                if (!move_uploaded_file(
                    $tmpName,
                    $targetFile
                )) {

                    $errors[] =
                        "Gambar gagal disimpan.";

                    $namaGambarBaru = null;

                }

            }

        }

    }


    // ======================================================
    // UPDATE DATABASE
    // ======================================================

    if (empty($errors)) {

        try {

            // ==================================================
            // JIKA ADA GAMBAR BARU
            // ==================================================

            if ($namaGambarBaru !== null) {

                $stmt = $pdo->prepare("
                    UPDATE berita
                    SET
                        judul = ?,
                        kategori = ?,
                        gambar = ?,
                        isi = ?,
                        status = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $judul,
                    $kategori,
                    $namaGambarBaru,
                    $isi,
                    $status,
                    $id
                ]);

            } else {

                // ==============================================
                // JIKA TIDAK GANTI GAMBAR
                // ==============================================

                $stmt = $pdo->prepare("
                    UPDATE berita
                    SET
                        judul = ?,
                        kategori = ?,
                        isi = ?,
                        status = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $judul,
                    $kategori,
                    $isi,
                    $status,
                    $id
                ]);

            }


            // ==================================================
            // HAPUS GAMBAR LAMA
            // ==================================================

            if (
                $namaGambarBaru !== null &&
                !empty($gambarLama)
            ) {

                $fileLama =
                    "../../uploads/berita/" .
                    $gambarLama;


                if (
                    file_exists(
                        $fileLama
                    )
                ) {

                    unlink(
                        $fileLama
                    );

                }

            }


            // ==================================================
            // PESAN
            // ==================================================

            $_SESSION["success"] =
                "Berita berhasil diperbarui.";


            // ==================================================
            // KEMBALI
            // ==================================================

            header(
                "Location: daftar.php"
            );

            exit;


        } catch (PDOException $e) {

            // ==================================================
            // HAPUS GAMBAR BARU JIKA UPDATE GAGAL
            // ==================================================

            if (
                $namaGambarBaru !== null
            ) {

                $fileBaru =
                    "../../uploads/berita/" .
                    $namaGambarBaru;


                if (
                    file_exists(
                        $fileBaru
                    )
                ) {

                    unlink(
                        $fileBaru
                    );

                }

            }


            $errors[] =
                "Data berita gagal diperbarui.";

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
        Edit Berita | LSP PPPOLRI
    </title>


    <!-- BOOTSTRAP -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- BOOTSTRAP ICONS -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >


    <!-- GOOGLE FONT -->

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


    <!-- ADMIN CSS -->

    <link
        rel="stylesheet"
        href="../../assets/css/admin.css"
    >

</head>


<body>


<div class="admin-wrapper">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside
        class="admin-sidebar"
        id="adminSidebar"
    >

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


        <nav class="sidebar-nav">


            <div class="nav-section">

                <span class="nav-section-title">
                    MENU UTAMA
                </span>


                <a
                    href="../dashboard.php"
                    class="sidebar-link"
                >

                    <i class="bi bi-grid-fill"></i>

                    <span>
                        Dashboard
                    </span>

                </a>


                <a
                    href="daftar.php"
                    class="sidebar-link active"
                >

                    <i class="bi bi-newspaper"></i>

                    <span>
                        Berita
                    </span>

                </a>


                <a
                    href="../galeri.php"
                    class="sidebar-link"
                >

                    <i class="bi bi-images"></i>

                    <span>
                        Galeri
                    </span>

                </a>


                <a
                    href="../skema.php"
                    class="sidebar-link"
                >

                    <i class="bi bi-award"></i>

                    <span>
                        Skema Sertifikasi
                    </span>

                </a>


                <a
                    href="../faq.php"
                    class="sidebar-link"
                >

                    <i class="bi bi-question-circle"></i>

                    <span>
                        FAQ
                    </span>

                </a>


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


            <div class="nav-section">

                <span class="nav-section-title">
                    MANAGEMENT
                </span>


                <a
                    href="../admin.php"
                    class="sidebar-link"
                >

                    <i class="bi bi-person-gear"></i>

                    <span>
                        Kelola Admin
                    </span>

                </a>


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


    <!-- OVERLAY -->

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>


    <!-- =====================================================
         MAIN
    ====================================================== -->

    <div class="admin-main">


        <!-- TOPBAR -->

        <header class="admin-topbar">


            <button
                type="button"
                class="sidebar-toggle"
                id="sidebarToggle"
                aria-label="Buka menu"
                aria-expanded="false"
            >

                <i class="bi bi-list"></i>

            </button>


            <div class="topbar-title">

                <h2>
                    Edit Berita
                </h2>

                <span>
                    Ubah informasi berita LSP PPPOLRI
                </span>

            </div>


            <div class="topbar-actions">


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


        <!-- =====================================================
             CONTENT
        ====================================================== -->

        <main class="dashboard-content">


            <section class="dashboard-welcome">

                <span class="dashboard-label">
                    CONTENT MANAGEMENT
                </span>

                <h1>
                    Edit Berita
                </h1>

                <p>
                    Perbarui informasi berita yang sudah
                    tersimpan pada website LSP PPPOLRI.
                </p>

            </section>


            <!-- ERROR -->

            <?php if (!empty($errors)): ?>

                <div
                    class="alert alert-danger"
                    role="alert"
                >

                    <strong>
                        Terjadi kesalahan:
                    </strong>


                    <ul class="mb-0 mt-2">

                        <?php foreach (
                            $errors
                            as $error
                        ): ?>

                            <li>
                                <?php echo e($error); ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>


            <!-- FORM -->

            <section class="dashboard-card">


                <div class="mb-4">

                    <span class="card-label">
                        BERITA
                    </span>

                    <h3 class="mb-1">
                        Edit Informasi Berita
                    </h3>

                    <p class="text-muted mb-0">
                        Perubahan akan langsung disimpan
                        ke database.
                    </p>

                </div>


                <form
                    action="edit.php?id=<?php echo $id; ?>"
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


                        <!-- GAMBAR LAMA -->

                        <div class="col-12">

                            <label
                                class="form-label fw-semibold"
                            >

                                Gambar Saat Ini

                            </label>


                            <?php if (
                                !empty($gambarLama)
                            ): ?>

                                <div class="mb-3">

                                    <img
                                        src="<?php
                                        echo e(
                                            "../../uploads/berita/" .
                                            $gambarLama
                                        );
                                        ?>"
                                        alt="Gambar berita"
                                        style="
                                            width:300px;
                                            height:180px;
                                            object-fit:cover;
                                            border-radius:10px;
                                        "
                                    >

                                </div>

                            <?php else: ?>

                                <div
                                    class="text-muted mb-3"
                                >

                                    <i class="bi bi-image me-1"></i>

                                    Belum ada gambar.

                                </div>

                            <?php endif; ?>


                            <!-- UPLOAD BARU -->

                            <label
                                for="gambar"
                                class="form-label"
                            >

                                Ganti Gambar

                            </label>


                            <input
                                type="file"
                                class="form-control"
                                id="gambar"
                                name="gambar"
                                accept=".jpg,.jpeg,.png,.webp"
                            >


                            <div class="form-text">

                                Kosongkan jika tidak ingin
                                mengganti gambar.

                                JPG, JPEG, PNG, WEBP.
                                Maksimal 2 MB.

                            </div>


                            <!-- PREVIEW BARU -->

                            <div
                                class="mt-3"
                                style="
                                    width:100%;
                                    height:250px;
                                    border:2px dashed #ddd;
                                    border-radius:10px;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    overflow:hidden;
                                    background:#fafafa;
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
                                        Preview gambar baru
                                    </span>

                                </div>


                                <img
                                    id="previewImage"
                                    src=""
                                    alt="Preview gambar"
                                    style="
                                        width:100%;
                                        height:100%;
                                        object-fit:cover;
                                        display:none;
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

                                    <i
                                        class="bi bi-arrow-left me-1"
                                    ></i>

                                    Batal

                                </a>


                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                >

                                    <i
                                        class="bi bi-save me-1"
                                    ></i>

                                    Simpan Perubahan

                                </button>

                            </div>

                        </div>

                    </div>

                </form>

            </section>

        </main>


        <!-- FOOTER -->

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


<!-- BOOTSTRAP JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>


<!-- ADMIN JS -->

<script
    src="../../assets/js/admin.js"
></script>


<!-- =========================================================
     PREVIEW GAMBAR BARU
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

        const file =
            this.files[0];


        if (!file) {

            previewImage.src = "";

            previewImage.style.display =
                "none";

            imagePlaceholder.style.display =
                "block";

            return;

        }


        // ==================================================
        // CEK UKURAN
        // ==================================================

        if (
            file.size >
            2 * 1024 * 1024
        ) {

            alert(
                "Ukuran gambar maksimal 2 MB."
            );

            this.value = "";

            previewImage.src = "";

            previewImage.style.display =
                "none";

            imagePlaceholder.style.display =
                "block";

            return;

        }


        // ==================================================
        // PREVIEW
        // ==================================================

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