<?php require "files/header_klients.php"; ?>

<body>
    <section class="hero">
        <div class="hero-content">
            <h1 class="fade-in">Atrodi savu ideālo darbu IT jomā</h1>
            <p class="fade-in delay">Mēs savienojam talantus ar iespējām visā Latvijā</p>
            <a href="#vakances" class="btn fade-in delay2">Sākt meklēšanu</a>
        </div>
    </section>

    <section id="par" class="section">
        <h2>Par mums</h2>
        <p>IT Spēks ir platforma, kas palīdz studentiem un profesionāļiem atrast piemērotas vakances IT nozarē. Mūsu misija – savienot cilvēkus ar iespējām, kas viņiem patiesi nozīmīgas.</p>
    </section>

    <section id="vakances" class="section">
        <h2>Populārās vakances</h2>
        <ul class="job-list">
            <li>🔍 Junior Web izstrādātājs - Riga</li>
            <li>💻 Front-End interns - Liepāja</li>
            <li>🧠 Data Analyst Assistant - Daugavpils</li>
        </ul>
        <a href="vakances.php" class="btn">Skatīt visas</a>
    </section>

    <section id="kontakti" class="section">
        <h2>Sazinies ar mums</h2>
        <p>📧 info@it-speks.lv | 📞 +371 12345678</p>
    </section>

    <script>
        document.querySelector('.menu-icon').addEventListener('click', () => {
            document.querySelector('.nav-links').classList.toggle('show');
        });
    </script>
    <?php require "files/footer.php"; ?>