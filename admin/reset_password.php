<?php
require "../files/database.php";

$token = $_GET['token'] ?? '';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['parole'];
    $confirmPassword = $_POST['apstiprini'];

    if ($newPassword !== $confirmPassword) {
        $error = "Paroles nesakrīt.";
    } else {
        $sql = "SELECT Lietotajvards FROM it_speks_Lietotaji WHERE reset_token = ? AND reset_token_expire > NOW()";
        $stmt = mysqli_prepare($savienojums, $sql);
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $lietotajvards = $row['Lietotajvards'];
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);

            $sqlUpdate = "UPDATE it_speks_Lietotaji SET Parole = ?, reset_token = NULL, reset_token_expire = NULL WHERE Lietotajvards = ?";
            $stmtUpdate = mysqli_prepare($savienojums, $sqlUpdate);
            mysqli_stmt_bind_param($stmtUpdate, "ss", $hash, $lietotajvards);
            mysqli_stmt_execute($stmtUpdate);

            $success = "Parole veiksmīgi nomainīta. Vari ielogoties.";
        } else {
            $error = "Nederīga vai beigusies saite.";
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
            <img style="width: 15rem;" src="../files/ITsLogo.png" alt="Logo" />
            <h1>Atiestatīt paroli</h1>
            <form method="POST">
                <input type="password" name="parole" placeholder="Jaunā parole" required>
                <input type="password" name="apstiprini" placeholder="Apstiprini paroli" required>
                <button type="submit" class="ielogot">Atjaunot paroli</button>
            </form>
            <?= $success ?: $error ?><br>
            <a href="login.php" class="back-to-main"><i class="fas fa-arrow-left"></i> Atpakaļ</a>
        </div>
    </div>
</body>