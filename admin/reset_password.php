<?php

require_once "../config/database.php";

$username = "admin";
$passwordBaru = "adminLSP256";

$hashPassword = password_hash(
    $passwordBaru,
    PASSWORD_DEFAULT
);

$stmt = $pdo->prepare("
    UPDATE admin
    SET password = ?
    WHERE username = ?
");

$stmt->execute([
    $hashPassword,
    $username
]);

if ($stmt->rowCount() > 0) {

    echo "Password berhasil direset.<br>";
    echo "Username: " . htmlspecialchars($username) . "<br>";
    echo "Password baru: " . htmlspecialchars($passwordBaru);

} else {

    echo "Data admin tidak ditemukan atau password tidak berubah.";

}