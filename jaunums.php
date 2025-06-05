<?php
require "files/database.php";
require "files/header_klients.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT * FROM it_speks_Jaunumi WHERE Jaunumi_ID = $id AND Statuss = 'Aktīvs'";
$result = mysqli_query($savienojums, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<div class='container'><h2>Ziņa nav atrasta vai nav pieejama.</h2></div>";
    require "files/footer.php";
    exit;
}

$news = mysqli_fetch_assoc($result);
?>

<body class="client-bg">
    <div class="container single-news">
        <div class="news-full">
            <?php if ($news['Bilde']): ?>
                <div class="news-full-img">
                    <?php
                    if (strlen($news['Bilde']) > 100) {
                        echo '<img src="data:image/jpeg;base64,' . base64_encode($news['Bilde']) . '" alt="Ziņas attēls">';
                    } else {
                        echo '<img src="uploads/' . htmlspecialchars($news['Bilde']) . '" alt="Ziņas attēls">';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <h1 class="page-title"><?= htmlspecialchars($news['Nosaukums']) ?></h1>
            <p class="news-full-date"><?= date("d.m.Y", strtotime($news['Publicesanas_datums'])) ?></p>

            <div class="news-full-text">
                <?= nl2br($news['Text']) ?>
            </div>

            <a href="jaunumi.php" class="btn-apply">⬅ Atpakaļ uz sarakstu</a>
        </div>
    </div>

    <?php require "files/footer.php"; ?>