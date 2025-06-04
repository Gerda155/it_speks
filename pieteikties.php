<?php
require "files/database.php";

$vakance_id = $_GET['vakance_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vards = $_POST['vards'];
    $uzvards = $_POST['uzvards'];
    $epasts = $_POST['epasts'];
    $talrunis = $_POST['talrunis'];
    $izglitiba = $_POST['izglitiba'];
    $pieredze = $_POST['pieredze'];
    $cv = null;

    // Загрузка файла
    if (!empty($_FILES['cv']['tmp_name'])) {
        $cv = file_get_contents($_FILES['cv']['tmp_name']);
        $cv = mysqli_real_escape_string($savienojums, $cv);
    }

    // Сохраняем в it_speks_Pieteiksanas
    $query = "
        INSERT INTO it_speks_Pieteiksanas 
        (Vards, Uzvards, Vakances_ID, Epasts, Talrunis, Izglitiba, Darba_pieredze, CV, Pieteiksanas_datums, Statuss, Komentars)
        VALUES ('$vards', '$uzvards', '$vakance_id', '$epasts', '$talrunis', '$izglitiba', '$pieredze', '$cv', NOW(), 'Jauns', '')
    ";
    if (mysqli_query($savienojums, $query)) {
        // Журнал действий
        $objekts = "Vakance ID $vakance_id";
        $notikums = "Pieteikums";
        $datums = date('Y-m-d H:i:s');
        mysqli_query($savienojums, "
            INSERT INTO it_speks_DarbibuVesture (Objekts, Notikums, Datums, Lietotajs)
            VALUES ('$objekts', '$notikums', '$datums', 'posetītājs')
        ");
        echo "<p>Pieteikums veiksmīgi nosūtīts!</p>";
    } else {
        echo "<p>Kļūda saglabājot datus: " . mysqli_error($savienojums) . "</p>";
    }
}

require "files/header_klients.php";
?>

<h1>Pieteikties vakancei</h1>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="vards" placeholder="Vārds" required>
    <input type="text" name="uzvards" placeholder="Uzvārds" required>
    <input type="email" name="epasts" placeholder="E-pasts" required>
    <input type="text" name="talrunis" placeholder="Tālrunis" required>
    <textarea name="izglitiba" placeholder="Izglītība" required></textarea>
    <textarea name="pieredze" placeholder="Darba pieredze" required></textarea>
    <label>CV (PDF/DOC):</label>
    <input type="file" name="cv" accept=".pdf,.doc,.docx">
    <button type="submit">Pieteikties</button>
</form>
