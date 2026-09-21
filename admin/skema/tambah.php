<?php
// ==========================================================
// TAMBAH SKEMA SERTIFIKASI - ADMIN LSP PPPOLRI
// File: admin/skema/tambah.php
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
// PAGE INFORMATION
// ==========================================================
$pageTitle = "Tambah Skema";
$pageSubtitle = "Menambahkan skema sertifikasi baru";

// ==========================================================
// PROSES TAMBAH DATA
// ==========================================================
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $namaSkema = trim($_POST["nama_skema"] ?? "");
    $kodeSkema = trim($_POST["kode_skema"] ?? "");
    $acuan = trim($_POST["acuan"] ?? "");
    $deskripsi = trim($_POST["deskripsi"] ?? "");
    $status = $_POST["status"] ?? "aktif";

    // ------------------------------------------------------
    // VALIDASI
    // ------------------------------------------------------
    if ($namaSkema === "") {

        $error = "Nama skema wajib diisi.";

    } else {

        try {

            // --------------------------------------------------
            // INSERT DATA
            // --------------------------------------------------
            $stmt = $pdo->prepare("
                INSERT INTO skema
                (
                    nama_skema,
                    kode_skema,
                    acuan,
                    deskripsi,
                    status
                )
                VALUES
                (
                    :nama_skema,
                    :kode_skema,
                    :acuan,
                    :deskripsi,
                    :status
                )
            ");

            $stmt->execute([
                ":nama_skema" => $namaSkema,
                ":kode_skema" => $kodeSkema !== "" ? $kodeSkema : null,
                ":acuan" => $acuan !== "" ? $acuan : null,
                ":deskripsi" => $deskripsi !== "" ? $deskripsi : null,
                ":status" => $status
            ]);

            // --------------------------------------------------
            // REDIRECT KE DAFTAR
            // --------------------------------------------------
            header("Location: daftar.php?success=added");
            exit;

        } catch (PDOException $e) {

            $error = "Data gagal disimpan. Silakan coba lagi.";

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
        Tambah Skema | LSP PPPOLRI
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
        <main class="dashboard-content">

            <!-- =================================================
                 PAGE HEADER
            ================================================== -->
            <section class="dashboard-welcome">

                <span class="dashboard-label">
                    SERTIFIKASI
                </span>

                <h1>
                    Tambah Skema Sertifikasi
                </h1>

                <p>
                    Tambahkan data skema sertifikasi baru
                    ke dalam sistem LSP PPPOLRI.
                </p>

            </section>


            <!-- =================================================
                 FORM
            ================================================== -->
            <section class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <span class="card-label">
                            FORM DATA
                        </span>

                        <h3>
                            Informasi Skema
                        </h3>

                    </div>

                </div>


                <!-- =================================================
                     ERROR
                ================================================== -->
                <?php if ($error !== ""): ?>

                    <div
                        class="alert alert-danger"
                        role="alert"
                    >
                        <i class="bi bi-exclamation-circle me-2"></i>

                        <?php echo htmlspecialchars($error); ?>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                     FORM INPUT
                ================================================== -->
                <form
                    method="POST"
                    action=""
                >

                    <!-- NAMA SKEMA -->
                    <div class="mb-4">

                        <label
                            for="nama_skema"
                            class="form-label"
                        >
                            Nama Skema
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="nama_skema"
                            id="nama_skema"
                            class="form-control"
                            placeholder="Contoh: Skema Sertifikasi Okupasi Gada Pratama"
                            value="<?php echo htmlspecialchars($_POST["nama_skema"] ?? ""); ?>"
                            required
                        >

                    </div>


                    <!-- KODE SKEMA -->
                    <div class="mb-4">

                        <label
                            for="kode_skema"
                            class="form-label"
                        >
                            Kode Skema
                        </label>

                        <input
                            type="text"
                            name="kode_skema"
                            id="kode_skema"
                            class="form-control"
                            placeholder="Masukkan kode skema jika tersedia"
                            value="<?php echo htmlspecialchars($_POST["kode_skema"] ?? ""); ?>"
                        >

                        <div class="form-text">
                            Kosongkan jika kode skema belum tersedia.
                        </div>

                    </div>


                    <!-- ACUAN -->
                    <div class="mb-4">

                        <label
                            for="acuan"
                            class="form-label"
                        >
                            Acuan / Standar
                        </label>

                        <textarea
                            name="acuan"
                            id="acuan"
                            class="form-control"
                            rows="5"
                            placeholder="Masukkan acuan atau standar kompetensi skema..."
                        ><?php echo htmlspecialchars($_POST["acuan"] ?? ""); ?></textarea>

                    </div>


                    <!-- DESKRIPSI -->
                    <div class="mb-4">

                        <label
                            for="deskripsi"
                            class="form-label"
                        >
                            Deskripsi Skema
                        </label>

                        <textarea
                            name="deskripsi"
                            id="deskripsi"
                            class="form-control"
                            rows="5"
                            placeholder="Masukkan deskripsi singkat mengenai skema..."
                        ><?php echo htmlspecialchars($_POST["deskripsi"] ?? ""); ?></textarea>

                    </div>


                    <!-- STATUS -->
                    <div class="mb-4">

                        <label
                            for="status"
                            class="form-label"
                        >
                            Status
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="form-select"
                        >

                            <option
                                value="aktif"
                                <?php
                                echo (
                                    ($_POST["status"] ?? "aktif")
                                    === "aktif"
                                ) ? "selected" : "";
                                ?>
                            >
                                Aktif
                            </option>

                            <option
                                value="nonaktif"
                                <?php
                                echo (
                                    ($_POST["status"] ?? "")
                                    === "nonaktif"
                                ) ? "selected" : "";
                                ?>
                            >
                                Nonaktif
                            </option>

                        </select>

                    </div>


                    <!-- =================================================
                         BUTTON
                    ================================================== -->
                    <div class="d-flex gap-2">

                        <a
                            href="daftar.php"
                            class="btn btn-secondary"
                        >
                            <i class="bi bi-arrow-left me-1"></i>
                            Kembali
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-save me-1"></i>
                            Simpan Skema
                        </button>

                    </div>

                </form>

            </section>

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

</body>

</html>