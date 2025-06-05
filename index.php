<?php
require "files/database.php";
require "files/header_klients.php";

// Получаем 3 последние активные вакансии
$vakancesQuery = "SELECT * FROM it_speks_Vakances WHERE Statuss = 'Aktīvs' ORDER BY Publicesanas_datums DESC LIMIT 3";
$vakancesResult = mysqli_query($savienojums, $vakancesQuery);

// Получаем 3 последние активные новости
$jaunumiQuery = "SELECT * FROM it_speks_Jaunumi WHERE Statuss = 'Aktīvs' ORDER BY Publicesanas_datums DESC LIMIT 3";
$jaunumiResult = mysqli_query($savienojums, $jaunumiQuery);
?>

<body>
    <section class="hero">
        <div class="hero-content">
            <h1 class="fade-in">Atrodi savu ideālo darbu IT jomā</h1>
            <p class="fade-in delay">Mēs savienojam talantus ar iespējām visā Latvijā</p>
            <a href="#vakances" class="btn fade-in delay2">Sākt meklēšanu</a>
        </div>
    </section>

    <section id="par" class="section" style="background-color: #e3f2fd;">
    <h2>Par mums</h2>
    <p><strong>IT Spēks</strong> ir uzticama platforma, kas palīdz studentiem un profesionāļiem atrast piemērotas vakances IT nozarē. Mūsu misija – savienot cilvēkus ar iespējām, kas viņiem patiesi nozīmīgas.</p>
    <p>Mēs sadarbojamies ar vadošajiem uzņēmumiem Latvijā un nodrošinām vienkāršu veidu, kā soli pa solim uzsākt vai turpināt savu karjeru tehnoloģiju jomā.</p>
</section>

    <section id="vakances" class="section">
        <h2>Jaunākas vakances</h2>
        <ul class="job-list">
            <?php while ($v = mysqli_fetch_assoc($vakancesResult)): ?>
                <li class="job-card">
                    <a href="vakance.php?id=<?= $v['Vakances_ID'] ?>">
                        <div class="job-card-img">
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
                        <div class="job-card-info">
                            <h3><?= htmlspecialchars($v['Amata_nosaukums']) ?></h3>
                            <p><strong><?= htmlspecialchars($v['Uznemuma_nosaukums']) ?></strong></p>
                            <p><?= htmlspecialchars($v['Atrasanas_vieta']) ?> | <?= htmlspecialchars($v['Tips']) ?></p>
                        </div>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
        <a href="vakances.php" class="btn">Skatīt visas</a>
    </section>

    <section id="jaunumi" class="section">
        <h2>Jaunumi</h2>
        <ul class="job-list">
            <?php while ($n = mysqli_fetch_assoc($jaunumiResult)): ?>
                <li class="job-card">
                    <a href="jaunums.php?id=<?= $n['Jaunumi_ID'] ?>">
                        <div class="job-card-img">
                            <?php
                            if ($n['Bilde'] && strlen($n['Bilde']) > 100) {
                                echo '<img src="data:image/jpeg;base64,' . base64_encode($n['Bilde']) . '" alt="Ziņas bilde">';
                            } elseif ($n['Bilde']) {
                                echo '<img src="uploads/' . htmlspecialchars($n['Bilde']) . '" alt="Ziņas bilde">';
                            } else {
                                echo '<img src="files/no-image.jpg" alt="Nav attēla">';
                            }
                            ?>
                        </div>
                        <div class="job-card-info">
                            <h3><?= htmlspecialchars($n['Nosaukums']) ?></h3>
                            <p><?= mb_strimwidth(strip_tags($n['Text']), 0, 100, "...") ?></p>
                            <p class="date"><?= date("d.m.Y", strtotime($n['Publicesanas_datums'])) ?></p>
                        </div>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
        <a href="jaunumi.php" class="btn">Skatīt visas</a>
    </section>

   <section id="kontakti" class="section" style="background-color: #263238; color: #fff;">
    <h2>Sazinies ar mums</h2>
    <p>Vai tev ir jautājumi vai ieteikumi? Mēs vienmēr esam gatavi palīdzēt!</p>
    <p>📧 <a href="mailto:info@it-speks.lv" style="color: #90caf9;">info@it-speks.lv</a> | 📞 +371 12345678</p>
</section>

    <script>
        document.querySelector('.menu-icon')?.addEventListener('click', () => {
            document.querySelector('.nav-links')?.classList.toggle('show');
        });
    </script>

    <?php require "files/footer.php"; ?>