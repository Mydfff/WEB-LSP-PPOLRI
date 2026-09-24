<?php
// ==========================================================
// DETAIL SKEMA SERTIFIKASI
// LSP PPPOLRI
// ==========================================================

// ==========================================================
// KONEKSI DATABASE
// ==========================================================
require_once "../../config/database.php";


// ==========================================================
// AMBIL ID SKEMA
// ==========================================================
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: skema.php");
    exit;
}


// ==========================================================
// AMBIL DATA SKEMA
// ==========================================================
$stmt = $pdo->prepare("
    SELECT *
    FROM skema
    WHERE id = :id
      AND status = 'aktif'
    LIMIT 1
");

$stmt->execute([
    ":id" => $id
]);

$skema = $stmt->fetch(PDO::FETCH_ASSOC);


// ==========================================================
// JIKA SKEMA TIDAK DITEMUKAN
// ==========================================================
if (!$skema) {
    header("Location: skema.php");
    exit;
}


// ==========================================================
// AMBIL UNIT KOMPETENSI
// ==========================================================
$stmtUnit = $pdo->prepare("
    SELECT *
    FROM unit_skema
    WHERE skema_id = :skema_id
    ORDER BY id ASC
");

$stmtUnit->execute([
    ":skema_id" => $id
]);

$unitList = $stmtUnit->fetchAll(PDO::FETCH_ASSOC);

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
        <?php echo htmlspecialchars($skema["nama_skema"]); ?>
        - LSP PPPOLRI
    </title>


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    >


    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <!-- CSS Global -->
    <link
        rel="stylesheet"
        href="../../assets/css/header.css"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/style.css"
    >


    <!-- CSS Skema -->
    <link
        rel="stylesheet"
        href="../../assets/css/skema.css"
    >


    <!-- Responsive -->
    <link
        rel="stylesheet"
        href="../../assets/css/responsive.css"
    >

</head>


<body>


    <!-- ==================================================
         TOP HEADER
         ================================================== -->

    <div id="top-header"></div>


    <!-- ==================================================
         NAVBAR
         ================================================== -->

    <div id="navbar"></div>


    <!-- ==================================================
         CONTENT
         ================================================== -->

    <main>

        <section class="detail-skema-section">

            <div class="container">


                <!-- ==================================================
                     HEADER DETAIL SKEMA
                     ================================================== -->

                <div class="detail-skema-header">

                    <h1>

                        <?php
                        echo htmlspecialchars($skema["nama_skema"]);
                        ?>

                    </h1>


                    <div class="detail-skema-info">


                        <!-- KODE SKEMA -->

                        <?php if (!empty($skema["kode_skema"])): ?>

                            <p>

                                <strong>
                                    Kode Skema :
                                </strong>

                                <?php
                                echo htmlspecialchars($skema["kode_skema"]);
                                ?>

                            </p>

                        <?php endif; ?>


                        <!-- ACUAN -->

                        <p>

                            <strong>
                                Acuan :
                            </strong>

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $skema["acuan"] ?? "-"
                                )
                            );
                            ?>

                        </p>


                    </div>

                </div>


                <!-- ==================================================
                     DESKRIPSI
                     ================================================== -->

                <?php if (!empty($skema["deskripsi"])): ?>

                    <div class="detail-skema-block">

                        <div class="detail-section-title">

                            <i class="bi bi-info-circle"></i>

                            <h2>
                                Deskripsi Skema
                            </h2>

                        </div>


                        <div class="persyaratan-content">

                            <p>
                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $skema["deskripsi"]
                                    )
                                );
                                ?>
                            </p>

                        </div>

                    </div>

                <?php endif; ?>


                <!-- ==================================================
                     UNIT KOMPETENSI
                     ================================================== -->

                <div class="detail-skema-block">


                    <div class="detail-section-title">

                        <i class="bi bi-list-check"></i>

                        <h2>
                            Unit Kompetensi
                        </h2>

                    </div>


                    <?php if (count($unitList) > 0): ?>

                        <div class="table-responsive">

                            <table
                                class="table detail-kompetensi-table"
                            >

                                <thead>

                                    <tr>

                                        <th width="8%">
                                            No.
                                        </th>

                                        <th width="25%">
                                            Kode Unit
                                        </th>

                                        <th>
                                            Judul Unit Kompetensi
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php foreach ($unitList as $index => $unit): ?>

                                        <tr>

                                            <td>
                                                <?php
                                                echo $index + 1;
                                                ?>
                                            </td>


                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $unit["kode_unit"]
                                                );
                                                ?>
                                            </td>


                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    $unit["judul_unit"]
                                                );
                                                ?>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php else: ?>


                        <!-- Jika belum ada unit -->

                        <div class="text-center py-4">

                            <i
                                class="bi bi-info-circle fs-2 text-muted"
                            ></i>

                            <p class="text-muted mt-2 mb-0">

                                Belum ada unit kompetensi
                                untuk skema ini.

                            </p>

                        </div>


                    <?php endif; ?>


                </div>


                <!-- ==================================================
                     PERSYARATAN SERTIFIKASI
                     ================================================== -->

                <div class="detail-skema-block">


                    <div class="detail-section-title">

                        <i class="bi bi-clipboard-check"></i>

                        <h2>
                            Persyaratan Sertifikasi
                        </h2>

                    </div>


                    <div class="persyaratan-content">

                        <ol>

                            <li>
                                Persyaratan peserta sertifikasi.
                            </li>

                            <li>
                                Memenuhi kompetensi dan pengalaman
                                sesuai dengan skema yang dipilih.
                            </li>

                            <li>
                                Menyerahkan dokumen persyaratan
                                yang telah ditentukan.
                            </li>

                        </ol>

                    </div>


                </div>


                <!-- ==================================================
                     DOKUMEN & INFORMASI
                     ================================================== -->

                <div class="detail-skema-block">


                    <div class="detail-section-title">

                        <i class="bi bi-file-earmark-text"></i>

                        <h2>
                            Dokumen & Informasi
                        </h2>

                    </div>


                    <div class="dokumen-list">


                        <!-- Dokumen 1 -->

                        <div class="dokumen-item">

                            <div class="dokumen-icon">

                                <i
                                    class="bi bi-file-earmark-pdf"
                                ></i>

                            </div>


                            <div class="dokumen-info">

                                <h3>
                                    Dokumen Skema Sertifikasi
                                </h3>

                                <p>
                                    Dokumen resmi skema sertifikasi.
                                </p>

                            </div>


                            <a
                                href="#"
                                class="dokumen-btn"
                            >

                                Lihat

                                <i
                                    class="bi bi-arrow-right"
                                ></i>

                            </a>

                        </div>


                        <!-- Dokumen 2 -->

                        <div class="dokumen-item">

                            <div class="dokumen-icon">

                                <i
                                    class="bi bi-file-earmark-text"
                                ></i>

                            </div>


                            <div class="dokumen-info">

                                <h3>
                                    Persyaratan Sertifikasi
                                </h3>

                                <p>
                                    Informasi mengenai persyaratan
                                    sertifikasi.
                                </p>

                            </div>


                            <a
                                href="#"
                                class="dokumen-btn"
                            >

                                Lihat

                                <i
                                    class="bi bi-arrow-right"
                                ></i>

                            </a>

                        </div>


                        <!-- Dokumen 3 -->

                        <div class="dokumen-item">

                            <div class="dokumen-icon">

                                <i
                                    class="bi bi-file-earmark-check"
                                ></i>

                            </div>


                            <div class="dokumen-info">

                                <h3>
                                    Standar Kompetensi
                                </h3>

                                <p>
                                    Dokumen acuan standar kompetensi.
                                </p>

                            </div>


                            <a
                                href="#"
                                class="dokumen-btn"
                            >

                                Lihat

                                <i
                                    class="bi bi-arrow-right"
                                ></i>

                            </a>

                        </div>


                    </div>

                </div>


                <!-- ==================================================
                     KEMBALI
                     ================================================== -->

                <div class="detail-back">

                    <a href="skema.php">

                        <i class="bi bi-arrow-left"></i>

                        Kembali ke Daftar Skema

                    </a>

                </div>


            </div>

        </section>

    </main>


    <!-- ==================================================
         FOOTER
         ================================================== -->

    <div id="footer"></div>


    <!-- ==================================================
         JAVASCRIPT
         ================================================== -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
    ></script>

    <script src="../../assets/js/include.js"></script>

    <script src="../../assets/js/navbar.js"></script>

    <script src="../../assets/js/main.js"></script>


</body>

</html>