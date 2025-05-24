<!DOCTYPE html>
<html lang="lv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panelis</title>
    <link rel="stylesheet" href="../files/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="../files/script.js" defer></script>
</head>

<body>
    <header class="topbar">
        <div class="left-box">
            <div class="logo">
                <img src="../files/ITsLogo.png" alt="Logo">
            </div>
            <div class="admin-name">
                Vārds Uzvārds
            </div>
        </div>
        <button class="logout-btn">Iziet</button>
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
                            <i class="fas fa-briefcase"></i><span>Pieteikumi</span>
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
            </ul>
            <div class="sidebar-buttons">
                <button class="edit-button" onclick="window.location.href='index.php'">
                    <i class="fas fa-edit"></i> Rediģēt
                </button>
                <button class="edit-button" onclick="window.location.href='konts.php'">
                    <i class="fas fa-user"></i> Mans konts
                </button>
            </div>
        </aside>