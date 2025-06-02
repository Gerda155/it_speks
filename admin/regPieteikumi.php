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

$errorMessage = '';
$successMessage = '';

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

// Если редактирование — загружаем заявку
if ($isEdit) {
    $query = "SELECT * FROM it_speks_Pieteiksanas WHERE Pieteiksanas_ID = $id LIMIT 1";
    $result = mysqli_query($savienojums, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $pieteikums = mysqli_fetch_assoc($result);
    } else {
        $errorMessage = "Pieteikums nav atrasts.";
        $isEdit = false;
    }
}

// Получаем список вакансий
$vakances = [];
$vakancesResult = mysqli_query($savienojums, "SELECT Vakances_ID, Amata_nosaukums FROM it_speks_Vakances");
while ($row = mysqli_fetch_assoc($vakancesResult)) {
    $vakances[] = $row;
}

// === ОБРАБОТКА POST ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vards = mysqli_real_escape_string($savienojums, $_POST['vards']);
    $uzvards = mysqli_real_escape_string($savienojums, $_POST['uzvards']);
    $epasts = mysqli_real_escape_string($savienojums, $_POST['epasts']);
    $talrunis = mysqli_real_escape_string($savienojums, $_POST['talrunis']);
    $komentars = mysqli_real_escape_string($savienojums, $_POST['komentars']);
    $vakances_id = (int)$_POST['vakances_id'];
    $statuss = mysqli_real_escape_string($savienojums, $_POST['statuss']);
    $cv_type = $_POST['cv_type'];

    $izglitiba = null;
    $darba_pieredze = null;
    $cvBlob = null;

    if (empty($vards) || empty($uzvards) || empty($epasts)) {
        $errorMessage = "Lūdzu, aizpildi visus obligātos laukus: Vārds, Uzvārds, E-pasts.";
    } else {
        if ($cv_type === 'file' && isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $cvBlob = addslashes(file_get_contents($_FILES['cv']['tmp_name']));
        } elseif ($cv_type === 'manual') {
            $izglitiba = mysqli_real_escape_string($savienojums, $_POST['izglitiba']);
            $darba_pieredze = mysqli_real_escape_string($savienojums, $_POST['darba_pieredze']);
        }

        if ($isEdit) {
            // === ОБНОВЛЕНИЕ ===
            $updateParts = [
                "Vards='$vards'",
                "Uzvards='$uzvards'",
                "Epasts='$epasts'",
                "Talrunis='$talrunis'",
                "Komentars='$komentars'",
                "Vakances_ID=$vakances_id",
                "Statuss='$statuss'"
            ];

            if ($cv_type === 'file' && $cvBlob !== null) {
                $updateParts[] = "CV='$cvBlob'";
                $updateParts[] = "Izglitiba=NULL";
                $updateParts[] = "Darba_pieredze=NULL";
            } elseif ($cv_type === 'manual') {
                $updateParts[] = "Izglitiba=" . ($izglitiba ? "'$izglitiba'" : "NULL");
                $updateParts[] = "Darba_pieredze=" . ($darba_pieredze ? "'$darba_pieredze'" : "NULL");
                $updateParts[] = "CV=NULL";
            }

            $updateQuery = "UPDATE it_speks_Pieteiksanas SET " . implode(", ", $updateParts) . " WHERE Pieteiksanas_ID=$id";
            mysqli_query($savienojums, $updateQuery);

            $objekts = "Pieteikums ar ID $id";
            $darbiba = "Rediģēts";
        } else {
            // === СОЗДАНИЕ ===
            $insertQuery = "INSERT INTO it_speks_Pieteiksanas 
        (Vards, Uzvards, Epasts, Talrunis, Izglitiba, Darba_pieredze, CV, Komentars, Vakances_ID, Statuss, Pieteiksanas_datums)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $savienojums->prepare($insertQuery);
            $stmt->bind_param(
                "ssssssssis",
                $vards,
                $uzvards,
                $epasts,
                $talrunis,
                $izglitiba,
                $darba_pieredze,
                $cvBlob,
                $komentars,
                $vakances_id,
                $statuss
            );

            if ($stmt->execute()) {
                $id = $stmt->insert_id;
                $objekts = "Pieteikums ar ID $id";
                $darbiba = "Izveidots";
            } else {
                $errorMessage = "Kļūda saglabājot pieteikumu: " . $stmt->error;
            }
            $stmt->close();
        }

        // === ЛОГИРОВАНИЕ ===
        if (empty($errorMessage)) {
            $lietotajvards = $_SESSION['lietotajvards'];
            $stmtUser = $savienojums->prepare("SELECT Vards, Uzvards FROM it_speks_Lietotaji WHERE Lietotajvards = ?");
            $stmtUser->bind_param("s", $lietotajvards);
            $stmtUser->execute();
            $stmtUser->bind_result($vardsLietotaja, $uzvardsLietotaja);
            $stmtUser->fetch();
            $stmtUser->close();

            $lietotajsPilns = "$vardsLietotaja $uzvardsLietotaja";

            $stmtLog = $savienojums->prepare("INSERT INTO it_speks_DarbibuVesture (Objekts, Notikums, Datums, Lietotajs) VALUES (?, ?, NOW(), ?)");
            $stmtLog->bind_param("sss", $objekts, $darbiba, $lietotajsPilns);
            $stmtLog->execute();
            $stmtLog->close();

            $successMessage = $isEdit ? "Pieteikums veiksmīgi atjaunināts." : "Pieteikums veiksmīgi izveidots.";
        }
    }
}
?>

<main>
    <div class="form-grid-card center">
        <div class="login-box">
            <h1><?= $isEdit ? "Rediģēt pieteikumu" : "Izveidot jaunu pieteikumu" ?></h1>
            <?php if ($successMessage): ?>
                <p style="color: green; font-weight: bold;"><?= htmlspecialchars($successMessage) ?></p>
            <?php elseif ($errorMessage): ?>
                <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorMessage) ?></p>
            <?php endif; ?>

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
                <a href="crudPieteikumi.php" class="back-to-main"><i class="fas fa-arrow-left"></i> Atpakaļ</a>
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