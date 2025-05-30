<?php
session_start();

if (!isset($_SESSION['lietotajvards'])) {
    header("Location: login.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../files/header.php";
require "../files/database.php";

$currentUsername = $_SESSION['lietotajvards'];
$stmtUser = $savienojums->prepare("SELECT Lietotaj_ID, Vards, Uzvards FROM it_speks_Lietotaji WHERE Lietotajvards = ?");
$stmtUser->bind_param("s", $currentUsername);
$stmtUser->execute();
$resUser = $stmtUser->get_result();
if ($resUser->num_rows === 0) {
    die("Lietotājs nav atrasts.");
}
$userData = $resUser->fetch_assoc();
$currentUserId = $userData['Lietotaj_ID'];
$currentUserFullName = $userData['Vards'] . ' ' . $userData['Uzvards'];

$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$id = $isEdit ? intval($_GET['id']) : null;

$ziņa = [
    'Nosaukums' => '',
    'Text' => '',
    'Statuss' => 'Aktīvs',
    'Bilde' => null,
    'Lietotaj_ID' => null,
    'Publicesanas_datums' => null,
];

$imagePreview = "";
$successMessage = "";
$errorMessage = "";

// Редактирование — загружаем текущие данные
if ($isEdit) {
    $stmt = $savienojums->prepare("SELECT Nosaukums, Text, Statuss, Bilde, Lietotaj_ID, Publicesanas_datums FROM it_speks_Jaunumi WHERE Jaunumi_ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $ziņa = $result->fetch_assoc();
        if (!empty($ziņa['Bilde'])) {
            $base64 = base64_encode($ziņa['Bilde']);
            $imagePreview = '<img src="data:image/jpeg;base64,' . $base64 . '" style="max-width:100%; margin-bottom: 10px; border-radius: 8px;">';
        }
    } else {
        $errorMessage = "Ziņa nav atrasta";
        $isEdit = false;
    }
}

$currentDateTime = date('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nosaukums = $_POST['nosaukums'] ?? '';
    $text = $_POST['text'] ?? '';
    $statuss = $_POST['statuss'] ?? 'Aktīvs';
    $datums = $_POST['datums'] ?? '';
    $publicesanasDatums = $datums ? date('Y-m-d H:i:s', strtotime($datums)) : date('Y-m-d H:i:s');

    $lietotaj_ID = $isEdit ? ($_POST['moderators'] ?? null) : $currentUserId;

    if ($isEdit && (empty($lietotaj_ID) || !is_numeric($lietotaj_ID))) {
        $errorMessage = "Lūdzu izvēlieties lietotāju!";
    }

    if (!$errorMessage) {
        $imageData = null;
        if (isset($_FILES['bilde']) && $_FILES['bilde']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['bilde']['tmp_name'];
            $fileSize = $_FILES['bilde']['size'];
            $fileType = mime_content_type($fileTmpPath);

            if (strpos($fileType, 'image/') === 0 && $fileSize <= 2 * 1024 * 1024) {
                $imageData = file_get_contents($fileTmpPath);
            } else {
                $errorMessage = "Nederīgs attēls.";
            }
        }

        if (!$errorMessage) {
            if ($isEdit) {
                $query = $imageData !== null ?
                    "UPDATE it_speks_Jaunumi SET Nosaukums=?, Text=?, Statuss=?, Bilde=?, Lietotaj_ID=?  WHERE Jaunumi_ID=?" :
                    "UPDATE it_speks_Jaunumi SET Nosaukums=?, Text=?, Statuss=?, Lietotaj_ID=? WHERE Jaunumi_ID=?";
                $stmtUpdate = $savienojums->prepare($query);

                if ($imageData !== null) {
                    $stmtUpdate->bind_param("sssssi", $nosaukums, $text, $statuss, $imageData, $lietotaj_ID, $id);
                } else {
                    $stmtUpdate->bind_param("sssii", $nosaukums, $text, $statuss, $lietotaj_ID, $id);
                }

                if ($stmtUpdate->execute()) {
                    $successMessage = "Ziņa veiksmīgi atjaunināta.";

                    // Запись в историю
                    $objekts = 'Jaunums ar ID ' . $id;
                    $notikums = 'Ziņa atjaunināta';
                    $date = date('Y-m-d H:i:s');
                    $stmtHist = $savienojums->prepare("INSERT INTO it_speks_DarbibuVesture (Lietotajs, Objekts, Notikums, Datums) VALUES (?, ?, ?, ?)");
                    $stmtHist->bind_param("ssss", $currentUserFullName, $objekts, $notikums, $date);
                    $stmtHist->execute();
                } else {
                    $errorMessage = "Kļūda atjauninot ziņu.";
                }
            } else {
                $stmtInsert = $savienojums->prepare("INSERT INTO it_speks_Jaunumi (Nosaukums, Text, Statuss, Bilde, Lietotaj_ID, Publicesanas_datums) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtInsert->bind_param("ssssss", $nosaukums, $text, $statuss, $imageData, $lietotaj_ID, $publicesanasDatums);

                if ($stmtInsert->execute()) {
                    $successMessage = "Ziņa veiksmīgi izveidota.";
                    $newId = $savienojums->insert_id;

                    $objekts = 'Jaunums ar ID ' . $newId;
                    $notikums = 'Jauna ziņa pievienota';
                    $stmtHist = $savienojums->prepare("INSERT INTO it_speks_DarbibuVesture (Lietotajs, Objekts, Notikums, Datums) VALUES (?, ?, ?, ?)");
                    $stmtHist->bind_param("ssss", $currentUserFullName, $objekts, $notikums, $publicesanasDatums);
                    $stmtHist->execute();

                    $ziņa = ['Nosaukums' => '', 'Text' => '', 'Statuss' => 'Aktīvs', 'Bilde' => null, 'Lietotaj_ID' => null, 'Publicesanas_datums' => null];
                    $imagePreview = "";
                } else {
                    $errorMessage = "Kļūda saglabājot ziņu.";
                }
            }
        }
    }
}

// Получаем список пользователей
$moderatori = [];
$modResult = mysqli_query($savienojums, "SELECT Lietotaj_ID, Vards, Uzvards FROM it_speks_Lietotaji ORDER BY Uzvards ASC");
if ($modResult) {
    while ($row = mysqli_fetch_assoc($modResult)) {
        $moderatori[] = $row;
    }
}

$izvelētaisModeratorID = $isEdit ? $ziņa['Lietotaj_ID'] : null;

?>

<main>
    <div class="form-grid-card center">
        <div class="login-box">
            <h1><?= $isEdit ? "Rediģēt ziņu" : "Izveidot jaunu ziņu" ?></h1>
            <p class="login-subtitle">Aizpildi visus laukus</p>

            <?php if ($successMessage): ?>
                <p style="color: green; font-weight: bold;"><?= htmlspecialchars($successMessage) ?></p>
            <?php elseif ($errorMessage): ?>
                <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorMessage) ?></p>
            <?php endif; ?>

            <form action="<?= $isEdit ? '?id=' . $id : '' ?>" method="POST" enctype="multipart/form-data" class="form-layout">
                <input type="text" name="nosaukums" id="nosaukums" placeholder="Ziņas nosaukums" value="<?= htmlspecialchars($ziņa['Nosaukums']) ?>" required />

                <textarea name="text" id="text" placeholder="Ziņas saturs" rows="6" required><?= htmlspecialchars($ziņa['Text']) ?></textarea>

                <label for="statuss">Statuss</label>
                <select name="statuss" id="statuss" required>
                    <option value="Aktīvs" <?= $ziņa['Statuss'] === 'Aktīvs' ? 'selected' : '' ?>>Aktīvs</option>
                    <option value="Neaktīvs" <?= $ziņa['Statuss'] === 'Neaktīvs' ? 'selected' : '' ?>>Neaktīvs</option>
                    <option value="Melnraksts" <?= $ziņa['Statuss'] === 'Melnraksts' ? 'selected' : '' ?>>Melnraksts</option>
                </select>

                <label for="bilde">Pievienot attēlu</label>
                <?= $imagePreview ?>

                <input type="file" id="bilde" name="bilde" accept="image/*" />

                <label for="datums">Publicēšanas datums:</label>
                <input type="datetime-local" id="datums" name="datums"
                    value="<?= isset($ziņa['Publicesanas_datums']) && $ziņa['Publicesanas_datums'] ? date('Y-m-d\TH:i', strtotime($ziņa['Publicesanas_datums'])) : date('Y-m-d\TH:i') ?>" required>

                <?php if ($isEdit): ?>
                    <label for="moderators">Izvēlies moderatoru</label>
                    <select name="moderators" id="moderators" required>
                        <option value="">-- izvēlies --</option>
                        <?php foreach ($moderatori as $mods): ?>
                            <option value="<?= $mods['Lietotaj_ID'] ?>" <?= $izvelētaisModeratorID == $mods['Lietotaj_ID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mods['Vards'] . " " . $mods['Uzvards']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>

                <button class="ielogot" type="submit"><?= $isEdit ? "Atjaunināt" : "Izveidot" ?></button>
            </form>
        </div>
    </div>
</main>

<?php require "../files/footer.php"; ?>