<?php
// ==========================================================
// TAMBAH UNIT KOMPETENSI - ADMIN LSP PPPOLRI
// File: admin/skema/unit-tambah.php
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
// AMBIL ID SKEMA
// ==========================================================
$skemaId = filter_input(INPUT_GET, 'skema_id', FILTER_VALIDATE_INT);

if (!$skemaId) {
    header("Location: daftar.php");
    exit;
}

// ==========================================================
// AMBIL DATA SKEMA
// ==========================================================
$stmt = $pdo->prepare("
    SELECT *
    FROM skema
    WHERE id = :id
");

$stmt->execute([
    ":id" => $skemaId
]);

$skema = $stmt->fetch(PDO::FETCH_ASSOC);

// ==========================================================
// CEK SKEMA
// ==========================================================
if (!$skema) {
    header("Location: daftar.php");
    exit;
}

// ==========================================================
// PAGE INFORMATION
// ==========================================================
$pageTitle = "Tambah Unit Kompetensi";
$pageSubtitle = "Menambahkan unit kompetensi";

// ==========================================================
// PROSES SIMPAN
// ==========================================================
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $kodeUnit = trim($_POST["kode_unit"] ?? "");
    $judulUnit = trim($_POST["judul_unit"] ?? "");

    // ------------------------------------------------------
    // VALIDASI
    // ------------------------------------------------------
    if ($kodeUnit === "") {

        $error = "Kode unit wajib diisi.";

    } elseif ($judulUnit === "") {

        $error = "Judul unit kompetensi wajib diisi.";

    } else {

        try {

            // --------------------------------------------------
            // SIMPAN UNIT
            // --------------------------------------------------
            $stmt = $pdo->prepare("
                INSERT INTO unit_skema
                (
                    skema_id,
                    kode_unit,
                    judul_unit
                )
                VALUES
                (
                    :skema_id,
                    :kode_unit,
                    :judul_unit
                )
            ");

            $stmt->execute([
                ":skema_id" => $skemaId,
                ":kode_unit" => $kodeUnit,
                ":judul_unit" => $judulUnit
            ]);

            // --------------------------------------------------
            // KEMBALI KE DETAIL SKEMA
            // --------------------------------------------------
            header("Location: detail.php?id=" . $skemaId . "&success=unit_added");
            exit;

        } catch (PDOException $e) {

            $error = "Unit kompetensi gagal disimpan. Silakan coba lagi.";
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
        Tambah Unit Kompetensi | LSP PPPOLRI
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
                    KOMPETENSI
                </span>

                <h1>
                    Tambah Unit Kompetensi
                </h1>

                <p>
                    Tambahkan unit kompetensi untuk skema:
                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $skema["nama_skema"]
                        );
                        ?>
                    </strong>
                </p>

            </section>


            <!-- =================================================
                 FORM
            ================================================== -->
            <section class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <span class="card-label">
                            UNIT KOMPETENSI
                        </span>

                        <h3>
                            Data Unit Kompetensi
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

                        <?php
                        echo htmlspecialchars($error);
                        ?>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                     FORM INPUT
                ================================================== -->
                <form
                    method="POST"
                    action=""
                >

                    <!-- KODE UNIT -->
                    <div class="mb-4">

                        <label
                            for="kode_unit"
                            class="form-label"
                        >
                            Kode Unit Kompetensi
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="kode_unit"
                            id="kode_unit"
                            class="form-control"
                            placeholder="Contoh: N.80PAM00.001.2"
                            value="<?php echo htmlspecialchars($_POST["kode_unit"] ?? ""); ?>"
                            required
                        >

                    </div>


                    <!-- JUDUL UNIT -->
                    <div class="mb-4">

                        <label
                            for="judul_unit"
                            class="form-label"
                        >
                            Judul Unit Kompetensi
                            <span class="text-danger">*</span>
                        </label>

                        <textarea
                            name="judul_unit"
                            id="judul_unit"
                            class="form-control"
                            rows="4"
                            placeholder="Contoh: Melaksanakan Persiapan Pelaksanaan Tugas"
                            required
                        ><?php echo htmlspecialchars($_POST["judul_unit"] ?? ""); ?></textarea>

                    </div>


                    <!-- =================================================
                         BUTTON
                    ================================================== -->
                    <div class="d-flex gap-2">

                        <a
                            href="detail.php?id=<?php echo $skemaId; ?>"
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
                            Simpan Unit
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