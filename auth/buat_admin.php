<?php

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| DATA ADMIN PERTAMA
|--------------------------------------------------------------------------
*/

$nama_lengkap = "Administrator";
$username = "admin";
$email = "admin@lsp-ppolri.id";
$password = "admin123";
$role = "super_admin";
$status = "aktif";


try {

    /*
    |--------------------------------------------------------------------------
    | Cek apakah username sudah ada
    |--------------------------------------------------------------------------
    */

    $check = $pdo->prepare("
        SELECT id
        FROM admin
        WHERE username = ?
        LIMIT 1
    ");

    $check->execute([$username]);

    if ($check->fetch()) {

        die("Username admin sudah ada di database.");

    }


    /*
    |--------------------------------------------------------------------------
    | Hash Password
    |--------------------------------------------------------------------------
    */

    $password_hash = password_hash(
        $password,
        PASSWORD_DEFAULT
    );


    /*
    |--------------------------------------------------------------------------
    | Insert Admin
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        INSERT INTO admin
        (
            nama_lengkap,
            username,
            email,
            password,
            role,
            status
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ");

    $stmt->execute([
        $nama_lengkap,
        $username,
        $email,
        $password_hash,
        $role,
        $status
    ]);


    echo "
    <h2>Admin berhasil dibuat.</h2>

    <p>
        Username: <strong>admin</strong>
    </p>

    <p>
        Password: <strong>admin123</strong>
    </p>

    <p>
        Silakan lanjut ke halaman login.
    </p>
    ";


} catch (PDOException $e) {

    die(
        "Gagal membuat admin: " .
        $e->getMessage()
    );

}