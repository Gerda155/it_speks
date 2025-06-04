<?php
require "files/database.php";
require "files/header_klients.php";

// Проверка ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Vakance nav atrasta.</p>";
    exit;
}

$id = intval($_GET['id']);

// Получаем только активную вакансию
$query = "SELECT * FROM it_speks_Vakances WHERE Vakances_ID = $id AND Statuss = 'Aktīvs'";
$result = mysqli_query($savienojums, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<p>Vakance nav atrasta vai ir neaktīva.</p>";
    exit;
}

$v = mysqli_fetch_assoc($result);
?>

<body class="client-bg">
    <div class="container single-vacancy">
        <h1 class="page-title"><?= htmlspecialchars($v['Amata_nosaukums']) ?></h1>

        <div class="vacancy-wrapper">
            <div class="vacancy-image">
                <?php
                if ($v['Bilde'] && strlen($v['Bilde']) > 100) {
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($v['Bilde']) . '" alt="Vakances bilde">';
                } elseif ($v['Bilde']) {
                    echo '<img src="uploads/' . htmlspecialchars($v['Bilde']) . '" alt="Vakances bilde">';
                } else {
                    echo '<img src="files/no-image.jpg" alt="Nav attēla">';
                }
                ?>
            </div>

            <div class="vacancy-full-info">
                <p><strong>Uzņēmuma nosaukums:</strong> <?= htmlspecialchars($v['Uznemuma_nosaukums']) ?></p>
                <p><strong>Atrašanās vieta:</strong> <?= htmlspecialchars($v['Atrasanas_vieta']) ?></p>
                <p><strong>Alga:</strong> <?= htmlspecialchars($v['Alga']) ?> €</p>
                <p><strong>Darba tips:</strong> <?= htmlspecialchars($v['Tips']) ?></p>
                <p><strong>Publicēšanas datums:</strong> <?= htmlspecialchars($v['Publicesanas_datums']) ?></p>
                <p><strong>Beigu datums:</strong> <?= htmlspecialchars($v['Beigu_datums']) ?></p>
                <p><strong>Prasības:</strong><br> <?= nl2br(htmlspecialchars($v['Prasibas'])) ?></p>
                <p><strong>Darba apraksts:</strong><br> <?= nl2br(htmlspecialchars($v['Darba_apraksts'])) ?></p>
            </div>

            <div class="back-button">
                <a href="pieteikties.php?vakance_id=<?= $vakance['Vakances_ID'] ?>" class="btn">Pieteikties</a>
                <a href="vakances.php" class="btn-apply">⬅ Atpakaļ uz sarakstu</a>
            </div>
        </div>
    </div>

<?php require "files/footer.php"; ?>
