<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../files/database.php";

$kluda = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lietotajvards = trim($_POST['lietotajvards'] ?? '');
    $parole = $_POST['parole'] ?? '';

    if ($lietotajvards === '' || $parole === '') {
        $kluda = "Lūdzu, aizpildi visus laukus.";
    } else {
        $sql = "SELECT Lietotajvards, Parole FROM it_speks_Lietotaji WHERE Lietotajvards = ?";
        $stmt = mysqli_prepare($savienojums, $sql);
        mysqli_stmt_bind_param($stmt, "s", $lietotajvards);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $hashParole = $row['Parole'];

            if (password_verify($parole, $hashParole)) {
                $_SESSION['lietotajvards'] = $lietotajvards;
                header("Location: index.php");
                exit();
            } else {
                $kluda = "Nepareizs lietotājvārds vai parole.";
            }
        } else {
            $kluda = "Nepareizs lietotājvārds vai parole.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Panelis | Ielogoties</title>
    <link rel="stylesheet" href="../files/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script src="../files/script.js" defer></script>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img style="width: 15rem;" src="../files/ITsLogo.png" alt="Logo" />
            <h1>Piekļuve admin panelim</h1>
            <p class="login-subtitle">Tikai autorizētiem lietotājiem</p>
            <?php if ($kluda): ?>
                <div class="kluda" style="color: red; margin-bottom: 1rem; text-align: center;"><?= htmlspecialchars($kluda) ?></div>
            <?php endif; ?>
            <form action="" method="POST">
                <input type="text" name="lietotajvards" id="lietotajvards" placeholder="Lietotājvārds" required value="<?= htmlspecialchars($_POST['lietotajvards'] ?? '') ?>" />
                <input type="password" name="parole" id="parole" placeholder="Parole" required />
                <button class="ielogot" type="submit"><i class="fas fa-sign-in-alt"></i> Ielogoties</button>
            </form>
            <a href="/" class="back-to-main"><i class="fas fa-arrow-left"></i> Atpakaļ uz galveno lapu</a>
        </div>
    </div>
</body>
</html>
