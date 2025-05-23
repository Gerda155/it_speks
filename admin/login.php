<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panelis | Ielogoties</title>
    <link rel="stylesheet" href="../files/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="../files/script.js" defer></script>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Piekļuve admin panelim</h1>
            <p class="login-subtitle">Tikai autorizētiem lietotājiem</p>
            <div class="kluda"></div>
            <form action="" method="POST">
                <input type="text" name="lietotajvards" id="lietotajvards" placeholder="Lietotājvārds" required>
                <input type="password" name="parole" id="parole" placeholder="Parole" required>
                <button type="submit" id="ielogot"><i class="fas fa-sign-in-alt"></i> Ielogoties</button>
            </form>
            <a href="/" class="back-to-main"><i class="fas fa-arrow-left"></i> Atpakaļ uz galveno lapu</a>
        </div>
    </div>
</body>
</html>
