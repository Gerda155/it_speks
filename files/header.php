<?php
session_start();
if (!isset($_SESSION['lietotajvards'])) {
    header("Location: login.php");
    exit();
}

require "../files/database.php"; 

$lietotajvards = $_SESSION['lietotajvards'];

$sql = "SELECT Vards, Uzvards FROM it_speks_Lietotaji WHERE Lietotajvards = ?";
$stmt = mysqli_prepare($savienojums, $sql);
mysqli_stmt_bind_param($stmt, "s", $lietotajvards);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);
    $vards = htmlspecialchars($user['Vards']);
    $uzvards = htmlspecialchars($user['Uzvards']);
} else {
    $vards = "Nezināms";
    $uzvards = "Lietotājs";
}
?>

<!DOCTYPE html>
<html lang="lv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panelis</title>
    <link rel="stylesheet" href="../files/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <header class="topbar">
        <div class="left-box">
            <div class="logo">
                <img src="../files/ITsLogo.png" alt="Logo">
            </div>
            <div class="admin-name">
                <?= $vards . ' ' . $uzvards ?>
            </div>
        </div>
        <button class="logout-btn" id="logoutBtn">Iziet</button>
    </header>

    <div class="main-container">
        <aside class="sidebar">
            <ul class="sidebar-list">
                <li class="sidebar-item">
                    <div class="sidebar-header">
                        <div class="sidebar-label">
                            <i class="fas fa-newspaper"></i><span>Jaunumi</span>
                        </div>
                        <div class="chevron-icon">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="dropdown-content">
                        <a class="dropdown-item" href="crudJaunumi.php?status=Aktīvs">Aktīvie</a><br>
                        <a class="dropdown-item" href="crudJaunumi.php?status=Neaktivs">Arhīvs</a><br>
                        <a class="dropdown-item" href="crudJaunumi.php?status=Melnraksts">Melnraksti</a>
                    </div>
                </li>
                <li class="sidebar-item">
                    <div class="sidebar-header">
                        <div class="sidebar-label">
                            <i class="fas fa-briefcase"></i><span>Vakances</span>
                        </div>
                        <div class="chevron-icon">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="dropdown-content">
                        <a class="dropdown-item" href="crudVakances.php?status=Aktīvs">Aktīvie</a><br>
                        <a class="dropdown-item" href="crudVakances.php?status=Neaktivs">Arhīvs</a><br>
                        <a class="dropdown-item" href="crudVakances.php?status=Melnraksts">Melnraksti</a>
                    </div>
                </li>
                <li class="sidebar-item">
                    <div class="sidebar-header">
                        <div class="sidebar-label">
                            <i class="fa-solid fa-file-pen"></i><span>Pieteikumi</span>
                        </div>
                        <div class="chevron-icon">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="dropdown-content">
                        <a class="dropdown-item" href="crudPieteikumi.php?status=Jauns">Jaunie</a><br>
                        <a class="dropdown-item" href="crudPieteikumi.php?status=Apstiprināts">Apstiprinātie</a><br>
                        <a class="dropdown-item" href="crudPieteikumi.php?status=Noraidīts">Noraidītie</a><br>
                        <a class="dropdown-item" href="crudPieteikumi.php?status=Gaida atbildi">Gaida atbildi</a>
                    </div>
                </li>
                <?php if (isset($_SESSION['loma']) && $_SESSION['loma'] === 'Administrators'): ?>
                    <li class="sidebar-item">
                        <div class="sidebar-header">
                            <div class="sidebar-label">
                                <i class="fas fa-user-shield"></i><span>Moderatori</span>
                            </div>
                            <div class="chevron-icon">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                        <div class="dropdown-content">
                            <a class="dropdown-item" href="crudModeratori.php?status=Aktivs">Aktīvie</a><br>
                            <a class="dropdown-item" href="crudModeratori.php?status=Neaktivs">Neaktīvie</a>
                        </div>
                    </li>
                <?php endif; ?>

            </ul>
            <div class="sidebar-buttons">
                <button class="edit-button" onclick="window.location.href='index.php'">Sākumlapa</button>
                <button class="edit-button" onclick="window.location.href='konts.php'">
                    <i class="fas fa-user"></i> Mans konts
                </button>
            </div>
        </aside>

        <!-- Модалка выхода -->
        <div id="logoutModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
            <div style="background:#fff; padding:20px; border-radius:8px; max-width:320px; width:90%; text-align:center;">
                <h2>Vai tiešām vēlies iziet no konta?</h2>
                <div style="margin-top:20px;">
                    <button id="confirmLogout" style="margin-right:10px; padding:8px 16px; background:#ef4444; color:#fff; border:none; border-radius:4px; cursor:pointer;">Jā, iziet</button>
                    <button id="cancelLogout" style="padding:8px 16px; background:#999; color:#fff; border:none; border-radius:4px; cursor:pointer;">Atcelt</button>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('logoutBtn').addEventListener('click', () => {
                document.getElementById('logoutModal').style.display = 'flex';
            });

            document.getElementById('cancelLogout').addEventListener('click', () => {
                document.getElementById('logoutModal').style.display = 'none';
            });

            document.getElementById('confirmLogout').addEventListener('click', () => {
                window.location.href = '../admin/logout.php';
            });
        </script>