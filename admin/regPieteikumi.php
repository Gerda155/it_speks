<?php
session_start();
ob_start();

if (!isset($_SESSION['lietotajvards'])) {
    header("Location: login.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../files/header.php";
require "../files/database.php";

// Определение: редактируем или создаём
$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$id = $isEdit ? intval($_GET['id']) : null;

$pieteikums = [
    'Vards' => '',
    'Uzvards' => '',
    'Epasts' => '',
    'Talrunis' => '',
    'Izglitiba' => '',
    'Darba_pieredze' => '',
    'CV' => '',
    'Komentars' => '',
    'Vakances_ID' => '',
    'Statuss' => 'Jauns'
];

// Получаем данные из БД, если редактируем
if ($isEdit) {
    $query = "SELECT * FROM it_speks_Pieteiksanas WHERE Pieteiksanas_ID = $id LIMIT 1";
    $result = mysqli_query($savienojums, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $pieteikums = mysqli_fetch_assoc($result);
    } else {
        echo "<p style='color: red; text-align: center;'>Pieteikums nav atrasts</p>";
        $isEdit = false;
    }
}

// Получаем вакансии
$vakancesResult = mysqli_query($savienojums, "SELECT Vakances_ID, Amata_nosaukums FROM it_speks_Vakances");
$vakances = [];
while ($row = mysqli_fetch_assoc($vakancesResult)) {
    $vakances[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vards = mysqli_real_escape_string($savienojums, $_POST['vards']);
    $uzvards = mysqli_real_escape_string($savienojums, $_POST['uzvards']);
    $epasts = mysqli_real_escape_string($savienojums, $_POST['epasts']);
    $vakances_id = (int)$_POST['vakances_id'];
    $statuss = mysqli_real_escape_string($savienojums, $_POST['statuss']);
    $datums = date("Y-m-d");
    $cv_type = $_POST['cv_type'];
    $talrunis = mysqli_real_escape_string($savienojums, $_POST['talrunis']);
    $komentars = mysqli_real_escape_string($savienojums, $_POST['komentars']);

    $izglitiba = null;
    $darba_pieredze = null;
    $cvBlob = null;

    if ($cv_type === 'file' && isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $cvBlob = addslashes(file_get_contents($_FILES['cv']['tmp_name']));
    } elseif ($cv_type === 'manual') {
        $izglitiba = mysqli_real_escape_string($savienojums, $_POST['izglitiba']);
        $darba_pieredze = mysqli_real_escape_string($savienojums, $_POST['darba_pieredze']);
    }

    if ($isEdit) {
        $updateQuery = "
        UPDATE it_speks_Pieteiksanas 
        SET Vards='$vards', Uzvards='$uzvards', Epasts='$epasts',
            Talrunis='$talrunis',
            Komentars='$komentars',
            Vakances_ID=$vakances_id,
            Statuss='$statuss',
            Izglitiba=" . ($izglitiba !== null ? "'$izglitiba'" : "NULL") . ",
            Darba_pieredze=" . ($darba_pieredze !== null ? "'$darba_pieredze'" : "NULL") . ",
            CV=" . ($cvBlob !== null ? "'$cvBlob'" : "NULL") . "
        WHERE Pieteiksanas_ID=$id
    ";
        mysqli_query($savienojums, $updateQuery);

        // Получаем ФИО пользователя
        $lietotajvards = $_SESSION['lietotajvards'];
        $stmt = $savienojums->prepare("SELECT Vards, Uzvards FROM it_speks_Lietotaji WHERE Lietotajvards = ?");
        $stmt->bind_param("s", $lietotajvards);
        $stmt->execute();
        $stmt->bind_result($vardsLietotaja, $uzvardsLietotaja);
        $stmt->fetch();
        $stmt->close();

        $lietotajsPilns = "$vardsLietotaja $uzvardsLietotaja";
        $darbiba = "Rediģēts";
        $objekts = "Pieteikums ar ID $id";

        $stmt2 = $savienojums->prepare("INSERT INTO it_speks_DarbibuVesture (Objekts, Notikums, Datums, Lietotajs) VALUES (?, ?, NOW(), ?)");
        $stmt2->bind_param("sss", $objekts, $darbiba, $lietotajsPilns);
        $stmt2->execute();
        $stmt2->close();
    } else {
        $insertQuery = "
        INSERT INTO it_speks_Pieteiksanas 
        (Vards, Uzvards, Epasts, Talrunis, Komentars, Vakances_ID, Pieteiksanas_datums, Statuss, Izglitiba, Darba_pieredze, CV)
        VALUES (
            '$vards', '$uzvards', '$epasts', '$talrunis', '$komentars', $vakances_id, '$datums', '$statuss',
            " . ($izglitiba !== null ? "'$izglitiba'" : "NULL") . ",
            " . ($darba_pieredze !== null ? "'$darba_pieredze'" : "NULL") . ",
            " . ($cvBlob !== null ? "'$cvBlob'" : "NULL") . "
        )
    ";
        mysqli_query($savienojums, $insertQuery);

        // Получаем ФИО пользователя
        $lietotajvards = $_SESSION['lietotajvards'];
        $stmt = $savienojums->prepare("SELECT Vards, Uzvards FROM it_speks_Lietotaji WHERE Lietotajvards = ?");
        $stmt->bind_param("s", $lietotajvards);
        $stmt->execute();
        $stmt->bind_result($vardsLietotaja, $uzvardsLietotaja);
        $stmt->fetch();
        $stmt->close();

        $lietotajsPilns = "$vardsLietotaja $uzvardsLietotaja";
        $darbiba = "Izveidots";
        $objekts = "Jauns pieteikums";

        $stmt2 = $savienojums->prepare("INSERT INTO it_speks_DarbibuVesture (Objekts, Notikums, Datums, Lietotajs) VALUES (?, ?, NOW(), ?)");
        $stmt2->bind_param("sss", $objekts, $darbiba, $lietotajsPilns);
        $stmt2->execute();
        $stmt2->close();
    }
    header("Location: crudPieteikumi.php");
    exit();
}
?>

<main>
    <div class="form-grid-card center">
        <div class="login-box">
            <h1><?= $isEdit ? "Rediģēt pieteikumu" : "Izveidot jaunu pieteikumu" ?></h1>
            <p class="login-subtitle">Aizpildi visus laukus</p>

            <form action="<?= $isEdit ? '?id=' . $id : '' ?>" method="POST" enctype="multipart/form-data">
                <input type="text" name="vards" placeholder="Vārds" value="<?= htmlspecialchars($pieteikums['Vards'] ?? '') ?>" required />
                <input type="text" name="uzvards" placeholder="Uzvārds" value="<?= htmlspecialchars($pieteikums['Uzvards'] ?? '') ?>" required />
                <input type="email" name="epasts" placeholder="E-pasts" value="<?= htmlspecialchars($pieteikums['Epasts'] ?? '') ?>" required />
                <input type="text" name="talrunis" placeholder="Tālrunis" value="<?= htmlspecialchars($pieteikums['Talrunis'] ?? '') ?>" />

                <label>CV informācija:</label>
                <div>
                    <input type="radio" name="cv_type" value="file" id="cv_file_radio" checked>
                    <label for="cv_file_radio">Augšupielādēt CV failu</label>

                    <input type="radio" name="cv_type" value="manual" id="cv_manual_radio">
                    <label for="cv_manual_radio">Ievadīt manuāli</label>
                </div>

                <div id="cv_file_section">
                    <label for="cv">CV</label>
                    <input type="file" name="cv" id="cv">
                </div>

                <div id="cv_manual_section" style="display: none;">
                    <textarea name="izglitiba" placeholder="Izglītība"><?= htmlspecialchars($pieteikums['Izglitiba'] ?? '') ?></textarea>
                    <textarea name="darba_pieredze" placeholder="Darba pieredze"><?= htmlspecialchars($pieteikums['Darba_pieredze'] ?? '') ?></textarea>
                </div>

                <label for="vakances_id">Vakance</label>
                <select name="vakances_id" id="vakances_id" required>
                    <option value="">Izvēlies vakanci</option>
                    <?php foreach ($vakances as $vakance): ?>
                        <option value="<?= htmlspecialchars($vakance['Vakances_ID'] ?? '') ?>"
                            <?= (isset($pieteikums['Vakances_ID']) && $vakance['Vakances_ID'] == $pieteikums['Vakances_ID']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($vakance['Amata_nosaukums'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="statuss">Statuss</label>
                <select name="statuss" id="statuss" required>
                    <?php
                    $statusi = ['Jauns', 'Gaida atbildi', 'Apstiprināts', 'Noraidīts'];
                    foreach ($statusi as $s) {
                        $selected = ($s === $pieteikums['Statuss']) ? 'selected' : '';
                        echo "<option value=\"$s\" $selected>$s</option>";
                    }
                    ?>
                </select>

                <textarea name="komentars" placeholder="Komentārs"><?= htmlspecialchars($pieteikums['Komentars'] ?? '') ?></textarea>

                <button class="ielogot" type="submit">
                    <i class="fas <?= $isEdit ? 'fa-save' : 'fa-plus-circle' ?>"></i>
                    <?= $isEdit ? 'Saglabāt izmaiņas' : 'Izveidot pieteikumu' ?>
                </button>
            </form>
        </div>
    </div>
</main>
<script>
    document.querySelectorAll('input[name="cv_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const fileSection = document.getElementById('cv_file_section');
            const manualSection = document.getElementById('cv_manual_section');
            if (this.value === 'file') {
                fileSection.style.display = 'block';
                manualSection.style.display = 'none';
            } else {
                fileSection.style.display = 'none';
                manualSection.style.display = 'block';
            }
        });
    });
</script>

<?php
require "../files/footer.php";
ob_end_flush();
?>