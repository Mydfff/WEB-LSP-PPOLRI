<?php

require_once __DIR__ . "/config/mailer.php";

$tujuan = "umaydef@gmail.com";

$subjek = "Test Email - LSP PPPOLRI";

$isiHTML = "
<h2>Test Email LSP PPPOLRI</h2>

<p>Email SMTP Gmail berhasil terhubung.</p>

<p>Sistem notifikasi email LSP PPPOLRI siap digunakan.</p>
";

if (kirimEmail($tujuan, $subjek, $isiHTML)) {

    echo "Email berhasil dikirim.";

} else {

    echo "Email gagal dikirim.";

}