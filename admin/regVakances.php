<?php
session_start();
ob_start();

if (!isset($_SESSION['lietotajvards'])) {
    header("Location: login.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$errorMessage = '';
$successMessage = '';

require "../files/header.php";
require "../files/database.php";

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
    $stmt = $savienojums->prepare("SELECT * FROM it_speks_Vakances WHERE Vakances_ID = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $vakance = $result->fetch_assoc();
        if (!empty($vakance['Bilde'])) {
            $base64 = base64_encode($vakance['Bilde']);
            $imagePreview = '<img src="data:image/jpeg;base64,' . $base64 . '" alt="Pašreizējais attēls" style="max-width:100%; margin-bottom: 10px; border-radius: 8px;">';
        }
    } else {
        $errorMessage = "Vakance nav atrasta.";
        $isEdit = false;
    }
}

// Получение списка модераторов
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
    $datums = date("Y-m-d H:i:s");
    $currentUsername = $_SESSION['lietotajvards'];

    $stmtUser = $savienojums->prepare("SELECT Lietotaj_ID, Vards, Uzvards FROM it_speks_Lietotaji WHERE Lietotajvards = ?");
    $stmtUser->bind_param("s", $currentUsername);
    $stmtUser->execute();
    $resUser = $stmtUser->get_result();
    if ($resUser->num_rows === 0) {
        $errorMessage = "Lietotājs nav atrasts.";
    } else {
        $userData = $resUser->fetch_assoc();
        $currentUserId = $userData['Lietotaj_ID'];
        $currentUserFullName = $userData['Vards'] . ' ' . $userData['Uzvards'];
        $izveletaisModeratorID = $isEdit ? intval($_POST['moderators']) : $currentUserId;

        // Проверка полей
        $amata_nosaukums = trim($_POST['amata_nosaukums']);
        $uznemuma_nosaukums = trim($_POST['uznemuma_nosaukums']);
        $atrasanas_vieta = trim($_POST['atrasanas_vieta']);
        $alga = floatval($_POST['alga']);
        $prasibas = trim($_POST['prasibas']);
        $darba_apraksts = trim($_POST['darba_apraksts']);
        $statuss = $_POST['statuss'];
        $publicesanas_datums = $_POST['publicesanas_datums'];
        $beigu_datums = $_POST['beigu_datums'];
        $tips = $_POST['tips'];

        // Проверка обязательных полей
        if (empty($amata_nosaukums) || empty($uznemuma_nosaukums) || empty($atrasanas_vieta)) {
            $errorMessage = "Lūdzu, aizpildiet visus obligātos laukus.";
        } else {
            $bilde_data = null;
            if (!empty($_FILES['bilde']['tmp_name'])) {
                $bilde_data = file_get_contents($_FILES['bilde']['tmp_name']);
            }

            if ($isEdit) {
                if ($bilde_data !== null) {
                    $stmt = $savienojums->prepare("UPDATE it_speks_Vakances SET Lietotaj_ID=?, Amata_nosaukums=?, Uznemuma_nosaukums=?, Atrasanas_vieta=?, Alga=?, Prasibas=?, Darba_apraksts=?, Statuss=?, Publicesanas_datums=?, Beigu_datums=?, Tips=?, Bilde=? WHERE Vakances_ID=?");
                    $stmt->bind_param("isssdssssssbi", $izveletaisModeratorID, $amata_nosaukums, $uznemuma_nosaukums, $atrasanas_vieta, $alga, $prasibas, $darba_apraksts, $statuss, $publicesanas_datums, $beigu_datums, $tips, $bilde_data, $id);
                } else {
                    $stmt = $savienojums->prepare("UPDATE it_speks_Vakances SET Lietotaj_ID=?, Amata_nosaukums=?, Uznemuma_nosaukums=?, Atrasanas_vieta=?, Alga=?, Prasibas=?, Darba_apraksts=?, Statuss=?, Publicesanas_datums=?, Beigu_datums=?, Tips=? WHERE Vakances_ID=?");
                    $stmt->bind_param("isssdssssssi", $izveletaisModeratorID, $amata_nosaukums, $uznemuma_nosaukums, $atrasanas_vieta, $alga, $prasibas, $darba_apraksts, $statuss, $publicesanas_datums, $beigu_datums, $tips, $id);
                }
                $objekts = "Vakance ar ID $id";
                $notikums = "Rediģēta";
            } else {
                $stmt = $savienojums->prepare("INSERT INTO it_speks_Vakances (Lietotaj_ID, Amata_nosaukums, Uznemuma_nosaukums, Atrasanas_vieta, Alga, Prasibas, Darba_apraksts, Statuss, Publicesanas_datums, Beigu_datums, Tips, Bilde) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssdsssssss", $izveletaisModeratorID, $amata_nosaukums, $uznemuma_nosaukums, $atrasanas_vieta, $alga, $prasibas, $darba_apraksts, $statuss, $publicesanas_datums, $beigu_datums, $tips, $bilde_data);
                $objekts = "Jauna vakance";
                $notikums = "Pievienota";
            }

            if ($stmt->execute()) {
                $stmtHist = $savienojums->prepare("INSERT INTO it_speks_DarbibuVesture (Lietotajs, Objekts, Notikums, Datums) VALUES (?, ?, ?, NOW())");
                $stmtHist->bind_param("sss", $currentUserFullName, $objekts, $notikums);
                $stmtHist->execute();
                $successMessage = "Vakance veiksmīgi saglabāta!";
            } else {
                $errorMessage = "Kļūda saglabājot vakanci: " . $stmt->error;
            }
        }
    }
}
?>

<main>
    <div class="form-grid-card center">
        <div class="login-box">
            <h1><?= $isEdit ? "Rediģēt vakanci" : "Izveidot jaunu vakanci" ?></h1>
            <?php if ($successMessage): ?>
                <p style="color: green; font-weight: bold;"><?= htmlspecialchars($successMessage) ?></p>
            <?php elseif ($errorMessage): ?>
                <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorMessage) ?></p>
            <?php endif; ?>

            <form action="<?= $isEdit ? '?id=' . $id : '' ?>" method="POST" enctype="multipart/form-data" class="form-layout">

                <?php if ($isEdit): ?>
                    <label for="moderators">Lietotājs</label>
                    <select name="moderators" id="moderators" required>
                        <option value="">-- Izvēlies moderatoru --</option>
                        <?php foreach ($moderatori as $mod): ?>
                            <option value="<?= $mod['Lietotaj_ID'] ?>" <?= ($mod['Lietotaj_ID'] == $izveletaisModeratorID) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mod['Uzvards']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>


                <input
                    type="text"
                    name="amata_nosaukums"
                    id="amata_nosaukums"
                    placeholder="Amata nosaukums"
                    value="<?= htmlspecialchars($vakance['Amata_nosaukums']) ?>"
                    required />

                <input
                    type="text"
                    name="uznemuma_nosaukums"
                    id="uznemuma_nosaukums"
                    placeholder="Uzņēmuma nosaukums"
                    value="<?= htmlspecialchars($vakance['Uznemuma_nosaukums']) ?>"
                    required />

                <input
                    type="text"
                    name="atrasanas_vieta"
                    id="atrasanas_vieta"
                    placeholder="Atrašanās vieta"
                    value="<?= htmlspecialchars($vakance['Atrasanas_vieta']) ?>"
                    required />

                <input
                    type="text"
                    name="alga"
                    id="alga"
                    placeholder="Alga (EUR)"
                    value="<?= htmlspecialchars($vakance['Alga']) ?>"
                    min="0"
                    step="0.01"
                    required />

                <textarea
                    name="prasibas"
                    id="prasibas"
                    placeholder="Prasības"
                    rows="4"
                    required><?= htmlspecialchars($vakance['Prasibas']) ?></textarea>

                <textarea
                    name="darba_apraksts"
                    id="darba_apraksts"
                    placeholder="Darba apraksts"
                    rows="4"
                    required><?= htmlspecialchars($vakance['Darba_apraksts']) ?></textarea>

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
                    required />

                <label for="beigu_datums">Beigu datums</label>
                <input
                    type="date"
                    name="beigu_datums"
                    id="beigu_datums"
                    value="<?= htmlspecialchars($vakance['Beigu_datums']) ?>"
                    required />

                <select name="tips" id="tips" required>
                    <option value="Pilna laika" <?= $vakance['Tips'] === 'Pilna laika' ? 'selected' : '' ?>>Pilna laika</option>
                    <option value="Nepilna laika" <?= $vakance['Tips'] === 'Nepilna laika' ? 'selected' : '' ?>>Nepilna laika</option>
                    <option value="Prakse" <?= $vakance['Tips'] === 'Prakse' ? 'selected' : '' ?>>Prakse</option>
                </select>

                <h2>Attēls</h2>
                <?php if ($isEdit && $imagePreview): ?>
                    <label>Esošais attēls:</label>
                    <div><?= $imagePreview ?></div>
                <?php endif; ?>

                <label for="bilde" class="custom-file-label">
                    <i class="fas fa-image"></i> <?= $isEdit ? "Mainīt attēlu" : "Pievienot bildi" ?>
                </label>
                <input type="file" name="bilde" id="bilde" accept="image/*" />
                <button type="submit" class="ielogot" id="<?= $isEdit ? 'saglabat' : 'izveidot' ?>">
                    <i class="fas <?= $isEdit ? 'fa-save' : 'fa-plus-circle' ?>"></i>
                    <?= $isEdit ? 'Saglabāt izmaiņas' : 'Izveidot vakanci' ?>
                </button>
                <a href="crudVakances.php" class="back-to-main"><i class="fas fa-arrow-left"></i> Atpakaļ</a>
            </form>
        </div>
    </div>
</main>

<?php
require "../files/footer.php";
ob_end_flush();
?>