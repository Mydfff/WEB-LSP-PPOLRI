<?php
session_start();

// ==========================================================
// CEK LOGIN ADMIN
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
// AMBIL ID UNIT
// ==========================================================
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: daftar.php");
    exit;
}

// ==========================================================
// AMBIL DATA UNIT + DATA SKEMA
// ==========================================================
$stmt = $pdo->prepare("
    SELECT 
        unit_skema.*,
        skema.nama_skema
    FROM unit_skema
    INNER JOIN skema ON unit_skema.skema_id = skema.id
    WHERE unit_skema.id = :id
    LIMIT 1
");

$stmt->execute([
    ":id" => $id
]);

$unit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$unit) {
    header("Location: daftar.php");
    exit;
}

$skemaId = $unit["skema_id"];

$error = "";

// ==========================================================
// PROSES UPDATE
// ==========================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $kodeUnit  = trim($_POST["kode_unit"] ?? "");
    $judulUnit = trim($_POST["judul_unit"] ?? "");

    // Validasi
    if ($kodeUnit === "") {
        $error = "Kode unit kompetensi wajib diisi.";
    } elseif ($judulUnit === "") {
        $error = "Judul unit kompetensi wajib diisi.";
    } else {

        try {

            $stmtUpdate = $pdo->prepare("
                UPDATE unit_skema
                SET 
                    kode_unit = :kode_unit,
                    judul_unit = :judul_unit
                WHERE id = :id
            ");

            $stmtUpdate->execute([
                ":kode_unit"  => $kodeUnit,
                ":judul_unit" => $judulUnit,
                ":id"         => $id
            ]);

            header(
                "Location: detail.php?id=" .
                $skemaId .
                "&success=unit_updated"
            );
            exit;

        } catch (PDOException $e) {

            $error = "Data unit kompetensi gagal diperbarui.";
        }
    }

    // Supaya form tetap menampilkan input terakhir
    $unit["kode_unit"]  = $kodeUnit;
    $unit["judul_unit"] = $judulUnit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Unit Kompetensi - Admin LSP PPPOLRI</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Admin CSS -->
    <link rel="stylesheet" href="../../assets/css/admin.css">

</head>

<body>

<div class="admin-wrapper">

    <!-- SIDEBAR -->
    <?php require_once "../components/sidebar.php"; ?>

    <div class="admin-main">

        <!-- HEADER -->
        <?php require_once "../components/header.php"; ?>

        <!-- CONTENT -->
        <main class="dashboard-content">

            <div class="dashboard-welcome mb-4">

                <div>
                    <h2>Edit Unit Kompetensi</h2>

                    <p class="text-muted mb-0">
                        Perbarui data unit kompetensi pada skema sertifikasi.
                    </p>
                </div>

            </div>


            <!-- INFORMASI SKEMA -->
            <div class="card dashboard-card mb-4">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div
                            class="bg-primary bg-opacity-10 text-primary rounded p-3 me-3"
                        >
                            <i class="bi bi-diagram-3 fs-4"></i>
                        </div>

                        <div>

                            <small class="text-muted">
                                Skema Sertifikasi
                            </small>

                            <h5 class="mb-0">
                                <?php echo htmlspecialchars($unit["nama_skema"]); ?>
                            </h5>

                        </div>

                    </div>

                </div>

            </div>


            <!-- FORM EDIT -->
            <div class="card dashboard-card">

                <div class="card-body">

                    <?php if ($error !== ""): ?>

                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>

                    <?php endif; ?>


                    <form method="POST">

                        <!-- KODE UNIT -->
                        <div class="mb-3">

                            <label
                                for="kode_unit"
                                class="form-label"
                            >
                                Kode Unit Kompetensi
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="kode_unit"
                                name="kode_unit"
                                value="<?php echo htmlspecialchars($unit["kode_unit"]); ?>"
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
                            </label>

                            <textarea
                                class="form-control"
                                id="judul_unit"
                                name="judul_unit"
                                rows="4"
                                required
                            ><?php echo htmlspecialchars($unit["judul_unit"]); ?></textarea>

                        </div>


                        <!-- BUTTON -->
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
                                Simpan Perubahan
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </main>


        <!-- FOOTER -->
        <?php require_once "../components/footer.php"; ?>

    </div>

</div>


<!-- Bootstrap JS -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>

<!-- Admin JS -->
<script src="../../assets/js/admin.js"></script>

</body>
</html>