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

// Проверяем, редактирование это или создание
$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$ziņa = [
    'Nosaukums' => '',
    'Text' => '',
    'Statuss' => 'Aktīvs',
    'Bilde' => null
];
$imagePreview = "";

if ($isEdit) {
    $id = intval($_GET['id']);
    $query = "SELECT Nosaukums, Text, Statuss, Bilde FROM it_speks_Jaunumi WHERE Jaunumi_ID = $id LIMIT 1";
    $result = mysqli_query($savienojums, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $ziņa = mysqli_fetch_assoc($result);

        // Если есть изображение, создаём превью
        if (!empty($ziņa['Bilde'])) {
            $base64 = base64_encode($ziņa['Bilde']);
            $imagePreview = '<img src="data:image/jpeg;base64,' . $base64 . '" alt="Pašreizējais attēls" style="max-width:100%; margin-bottom: 10px; border-radius: 8px;">';
        }
    } else {
        echo "<p style='color: red; text-align: center;'>Ziņa nav atrasta</p>";
        $isEdit = false;
    }
}

// Получаем список модераторов для дропдауна
$moderatori = [];
$modQuery = "SELECT Lietotaj_ID, Vards, Uzvards FROM it_speks_Lietotaji ORDER BY Uzvards ASC";
$modResult = mysqli_query($savienojums, $modQuery);
if ($modResult) {
    while ($row = mysqli_fetch_assoc($modResult)) {
        $moderatori[] = $row;
    }
}

// Если редактируем, вытаскиваем выбранного модератора
$izvelētaisModeratorID = null;
if ($isEdit) {
    $id = intval($_GET['id']);
    $query = "SELECT Nosaukums, Text, Statuss, Bilde, Lietotaj_ID FROM it_speks_Jaunumi WHERE Jaunumi_ID = $id LIMIT 1";
    $result = mysqli_query($savienojums, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $ziņa = mysqli_fetch_assoc($result);

        $izvelētaisModeratorID = $ziņa['Lietotaj_ID'];

        if (!empty($ziņa['Bilde'])) {
            $base64 = base64_encode($ziņa['Bilde']);
            $imagePreview = '<img src="data:image/jpeg;base64,' . $base64 . '" alt="Pašreizējais attēls" style="max-width:100%; margin-bottom: 10px; border-radius: 8px;">';
        }
    } else {
        echo "<p style='color: red; text-align: center;'>Ziņa nav atrasta</p>";
        $isEdit = false;
    }
}
?>

<main>
    <div class="form-grid-card">
        <!-- Карточка 1: Основные поля -->
        <div class="login-box">
            <h1><?= $isEdit ? "Rediģēt ziņu" : "Izveidot jaunu ziņu" ?></h1>
            <p class="login-subtitle">Aizpildi visus laukus</p>
            <div class="kluda"></div>

            <form action="<?= $isEdit ? '?id=' . $id : '' ?>" method="POST" enctype="multipart/form-data" class="form-layout">
                <input type="text" name="nosaukums" id="nosaukums" placeholder="Ziņas nosaukums" value="<?= htmlspecialchars($ziņa['Nosaukums']) ?>" required />

                <textarea name="text" id="text" placeholder="Ziņas saturs" rows="6" required><?= htmlspecialchars($ziņa['Text']) ?></textarea>

                <label for="statuss">Statuss</label>
                <select name="statuss" id="statuss" required>
                    <option value="Aktīvs" <?= $ziņa['Statuss'] === 'Aktīvs' ? 'selected' : '' ?>>Aktīvs</option>
                    <option value="Neaktīvs" <?= $ziņa['Statuss'] === 'Neaktīvs' ? 'selected' : '' ?>>Neaktīvs</option>
                    <option value="Melnraksts" <?= $ziņa['Statuss'] === 'Melnraksts' ? 'selected' : '' ?>>Melnraksts</option>
                </select>

                <label for="moderators">Lietotājs</label>
                <select name="moderators" id="moderators" required>
                    <option value="">-- Izvēlies moderatoru --</option>
                    <?php foreach ($moderatori as $mod): ?>
                        <option value="<?= $mod['Lietotaj_ID'] ?>" <?= ($mod['Lietotaj_ID'] == $izvelētaisModeratorID) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mod['Uzvards']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="ielogot" id="<?= $isEdit ? 'saglabat' : 'izveidot' ?>">
                    <i class="fas <?= $isEdit ? 'fa-save' : 'fa-plus-circle' ?>"></i>
                    <?= $isEdit ? 'Saglabāt izmaiņas' : 'Izveidot ziņu' ?>
                </button>
        </div>

        <!-- Карточка 2: Изображение -->
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

<?php require "../files/footer.php"; ?>