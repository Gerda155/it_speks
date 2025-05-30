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

// Проверяем, редактируем или создаём
$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$vakance = [
    'Lietotaj_ID' => '',
    'Amata_nosaukums' => '',
    'Uznemuma_nosaukums' => '',
    'Atrasanas_vieta' => '',
    'Alga' => '',
    'Prasibas' => '',
    'Darba_apraksts' => '',
    'Statuss' => 'Aktīvs',
    'Publicesanas_datums' => '',
    'Beigu_datums' => '',
    'Tips' => 'Pilna laika',
    'Bilde' => null
];
$imagePreview = "";

if ($isEdit) {
    $id = intval($_GET['id']);
    $query = "SELECT Lietotaj_ID, Amata_nosaukums, Uznemuma_nosaukums, Atrasanas_vieta, Alga, Prasibas, Darba_apraksts, Statuss, Publicesanas_datums, Beigu_datums, Tips, Bilde FROM it_speks_Vakances WHERE Vakances_ID = $id LIMIT 1";
    $result = mysqli_query($savienojums, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $vakance = mysqli_fetch_assoc($result);

        if (!empty($vakance['Bilde'])) {
            $base64 = base64_encode($vakance['Bilde']);
            $imagePreview = '<img src="data:image/jpeg;base64,' . $base64 . '" alt="Pašreizējais attēls" style="max-width:100%; margin-bottom: 10px; border-radius: 8px;">';
        }
    } else {
        echo "<p style='color: red; text-align: center;'>Vakance nav atrasta</p>";
        $isEdit = false;
    }
}

$moderatori = [];
$modQuery = "SELECT Lietotaj_ID, Vards, Uzvards FROM it_speks_Lietotaji ORDER BY Uzvards ASC";
$modResult = mysqli_query($savienojums, $modQuery);
if ($modResult) {
    while ($row = mysqli_fetch_assoc($modResult)) {
        $moderatori[] = $row;
    }
}

$izveletaisModeratorID = $isEdit ? intval($vakance['Lietotaj_ID']) : intval($_SESSION['lietotajvards']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $izveletaisModeratorID = intval($_POST['moderators']);
    $amata_nosaukums = mysqli_real_escape_string($savienojums, $_POST['amata_nosaukums']);
    $uznemuma_nosaukums = mysqli_real_escape_string($savienojums, $_POST['uznemuma_nosaukums']);
    $atrasanas_vieta = mysqli_real_escape_string($savienojums, $_POST['atrasanas_vieta']);
    $alga = floatval($_POST['alga']);
    $prasibas = mysqli_real_escape_string($savienojums, $_POST['prasibas']);
    $darba_apraksts = mysqli_real_escape_string($savienojums, $_POST['darba_apraksts']);
    $statuss = $_POST['statuss'];
    $publicesanas_datums = $_POST['publicesanas_datums'];
    $beigu_datums = $_POST['beigu_datums'];
    $tips = $_POST['tips'];

    // Обработка файла изображения (если загружен)
    $bilde_data = null;
    if (!empty($_FILES['bilde']['tmp_name'])) {
        $bilde_data = file_get_contents($_FILES['bilde']['tmp_name']);
        $bilde_data = mysqli_real_escape_string($savienojums, $bilde_data);
    }

    if ($isEdit) {
        // UPDATE запрос
        $sql = "UPDATE it_speks_Vakances SET 
            Lietotaj_ID = $izveletaisModeratorID,
            Amata_nosaukums = '$amata_nosaukums',
            Uznemuma_nosaukums = '$uznemuma_nosaukums',
            Atrasanas_vieta = '$atrasanas_vieta',
            Alga = $alga,
            Prasibas = '$prasibas',
            Darba_apraksts = '$darba_apraksts',
            Statuss = '$statuss',
            Publicesanas_datums = '$publicesanas_datums',
            Beigu_datums = '$beigu_datums',
            Tips = '$tips'";

        if ($bilde_data !== null) {
            $sql .= ", Bilde = '$bilde_data'";
        }
        $sql .= " WHERE Vakances_ID = $id";

        if (mysqli_query($savienojums, $sql)) {
            header("Location: crudVakances.php?success=updated");
            exit();
        } else {
            echo "<p style='color:red;'>Kļūda atjauninot vakanci: " . mysqli_error($savienojums) . "</p>";
        }

    } else {
        // INSERT запрос
        $sql = "INSERT INTO it_speks_Vakances 
            (Lietotaj_ID, Amata_nosaukums, Uznemuma_nosaukums, Atrasanas_vieta, Alga, Prasibas, Darba_apraksts, Statuss, Publicesanas_datums, Beigu_datums, Tips, Bilde)
            VALUES
            ($izveletaisModeratorID, '$amata_nosaukums', '$uznemuma_nosaukums', '$atrasanas_vieta', $alga, '$prasibas', '$darba_apraksts', '$statuss', '$publicesanas_datums', '$beigu_datums', '$tips', " . ($bilde_data !== null ? "'$bilde_data'" : "NULL") . ")";

        if (mysqli_query($savienojums, $sql)) {
            header("Location: crudVakances.php?success=created");
            exit();
        } else {
            echo "<p style='color:red;'>Kļūda pievienojot vakanci: " . mysqli_error($savienojums) . "</p>";
        }
    }
}

?>

<main>
    <div class="form-grid-card">
        <div class="login-box">
            <h1><?= $isEdit ? "Rediģēt vakanci" : "Izveidot jaunu vakanci" ?></h1>
            <p class="login-subtitle">Aizpildi visus laukus</p>
            <div class="kluda"></div>

            <form action="<?= $isEdit ? '?id=' . $id : '' ?>" method="POST" enctype="multipart/form-data" class="form-layout">

                <label for="moderators">Lietotājs</label>
                <select name="moderators" id="moderators" required>
                    <option value="">-- Izvēlies moderatoru --</option>
                    <?php foreach ($moderatori as $mod): ?>
                        <option value="<?= $mod['Lietotaj_ID'] ?>" <?= ($mod['Lietotaj_ID'] == $izveletaisModeratorID) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mod['Uzvards']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input
                    type="text"
                    name="amata_nosaukums"
                    id="amata_nosaukums"
                    placeholder="Amata nosaukums"
                    value="<?= htmlspecialchars($vakance['Amata_nosaukums']) ?>"
                    required
                />

                <input
                    type="text"
                    name="uznemuma_nosaukums"
                    id="uznemuma_nosaukums"
                    placeholder="Uzņēmuma nosaukums"
                    value="<?= htmlspecialchars($vakance['Uznemuma_nosaukums']) ?>"
                    required
                />

                <input
                    type="text"
                    name="atrasanas_vieta"
                    id="atrasanas_vieta"
                    placeholder="Atrašanās vieta"
                    value="<?= htmlspecialchars($vakance['Atrasanas_vieta']) ?>"
                    required
                />

                <input
                    type="text"
                    name="alga"
                    id="alga"
                    placeholder="Alga (EUR)"
                    value="<?= htmlspecialchars($vakance['Alga']) ?>"
                    min="0"
                    step="0.01"
                    required
                />

                <textarea
                    name="prasibas"
                    id="prasibas"
                    placeholder="Prasības"
                    rows="4"
                    required
                ><?= htmlspecialchars($vakance['Prasibas']) ?></textarea>

                <textarea
                    name="darba_apraksts"
                    id="darba_apraksts"
                    placeholder="Darba apraksts"
                    rows="4"
                    required
                ><?= htmlspecialchars($vakance['Darba_apraksts']) ?></textarea>

                <select name="statuss" id="statuss" required>
                    <option value="Aktīvs" <?= $vakance['Statuss'] === 'Aktīvs' ? 'selected' : '' ?>>Aktīvs</option>
                    <option value="Melnraksts" <?= $vakance['Statuss'] === 'Melnraksts' ? 'selected' : '' ?>>Melnraksts</option>
                    <option value="Neaktīvs" <?= $vakance['Statuss'] === 'Neaktīvs' ? 'selected' : '' ?>>Neaktīvs</option>
                </select>

                <label for="publicesanas_datums">Publicēšanas datums</label>
                <input
                    type="date"
                    name="publicesanas_datums"
                    id="publicesanas_datums"
                    value="<?= htmlspecialchars($vakance['Publicesanas_datums']) ?>"
                    required
                />

                <label for="beigu_datums">Beigu datums</label>
                <input
                    type="date"
                    name="beigu_datums"
                    id="beigu_datums"
                    value="<?= htmlspecialchars($vakance['Beigu_datums']) ?>"
                    required
                />

                <select name="tips" id="tips" required>
                    <option value="Pilna laika" <?= $vakance['Tips'] === 'Pilna laika' ? 'selected' : '' ?>>Pilna laika</option>
                    <option value="Nepilna laika" <?= $vakance['Tips'] === 'Nepilna laika' ? 'selected' : '' ?>>Nepilna laika</option>
                    <option value="Prakse" <?= $vakance['Tips'] === 'Prakse' ? 'selected' : '' ?>>Prakse</option>
                </select>

                <button type="submit" class="ielogot" id="<?= $isEdit ? 'saglabat' : 'izveidot' ?>">
                    <i class="fas <?= $isEdit ? 'fa-save' : 'fa-plus-circle' ?>"></i>
                    <?= $isEdit ? 'Saglabāt izmaiņas' : 'Izveidot vakanci' ?>
                </button>
        </div>

        <!-- Колонка 2: Изображение -->
        <div class="login-box">
            <h2>Attēls</h2>
            <?php if ($isEdit && $imagePreview): ?>
                <label>Esošais attēls:</label>
                <div><?= $imagePreview ?></div>
            <?php endif; ?>

            <label for="bilde" class="custom-file-label">
                <i class="fas fa-image"></i> <?= $isEdit ? "Mainīt attēlu" : "Pievienot bildi" ?>
            </label>
            <input type="file" name="bilde" id="bilde" accept="image/*" />
        </div>
            </form>
    </div>
</main>

<?php
require "../files/footer.php";
ob_end_flush();
?>
