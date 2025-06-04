<?php
require "../files/database.php";

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $error = 'Ievadi savu lietotājvārdu (e-pastu).';
    } else {
        $token = bin2hex(random_bytes(16));
        $expire = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $sql = "UPDATE it_speks_Lietotaji SET reset_token = ?, reset_token_expire = ? WHERE Epasts = ?";
        $stmt = mysqli_prepare($savienojums, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $token, $expire, $email);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) === 1) {
            $link = "https://kristovskis.lv/3pt/fedotova/it_speks/admin/reset_password.php?token=$token";
            $subject = "Paroles atjaunošana";
            $message = "Noklikšķini uz saites, lai atiestatītu paroli: $link";
            $headers = "From: no-reply@tavsdomens.lv";

            mail($email, $subject, $message, $headers);
            $success = 'E-pasts ar paroles atiestatīšanu nosūtīts.';
        } else {
            $error = 'Lietotājs nav atrasts.';
        }
    }
}
?>

<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Panelis | Atjaunināt Parole</title>
<link rel="stylesheet" href="../files/style.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
<script src="../files/script.js" defer></script>
</head>

<body>
    <div class="login-container">
        <div class="login-box">
            <img style="width: 15rem;" src="../files/ITsLogo.png" alt="Logo"/>
            <h1>Atiestatīt paroli</h1>
            <form method="POST">
                <input type="email" name="email" placeholder="Tavs e-pasts" required>
                <button type="submit" class="ielogot">Sūtīt saiti</button>
            </form>
            <?= $success ?: $error ?><br>
            <a href="login.php" class="back-to-main"><i class="fas fa-arrow-left"></i> Atpakaļ</a>
        </div>
    </div>
</body>