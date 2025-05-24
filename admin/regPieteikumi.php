<?php
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
    'Lietotajvards' => '',
    'Vakances_ID' => '',
    'Statuss' => 'Jauns'
];

// Получаем данные из БД, если редактируем
if ($isEdit) {
    $query = "SELECT `Pieteiksanas_ID`, `Vards`, `Uzvards`, `Vakances_ID`, `Epasts`, `Talrunis`, `Izglitiba`, `Darba_pieredze`, `CV`, `Pieteiksanas_datums`, `Statuss`, `Komentars` FROM `it_speks_Pieteiksanas` WHERE Pieteiksanas_ID = $id LIMIT 1";
    $result = mysqli_query($savienojums, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $pieteikums = mysqli_fetch_assoc($result);
    } else {
        echo "<p style='color: red; text-align: center;'>Pieteikums nav atrasts</p>";
        $isEdit = false;
    }
}

// Получаем вакансии для селекта
$vakancesResult = mysqli_query($savienojums, "SELECT Vakances_ID, Amata_nosaukums FROM it_speks_Vakances");
$vakances = [];
while ($row = mysqli_fetch_assoc($vakancesResult)) {
    $vakances[] = $row;
}
?>

<main>
    <div class="form-grid-card center">
        <div class="login-box">
            <h1><?= $isEdit ? "Rediģēt pieteikumu" : "Izveidot jaunu pieteikumu" ?></h1>
            <p class="login-subtitle">Aizpildi visus laukus</p>
            <div class="kluda"></div>

            <form action="<?= $isEdit ? '?id=' . $id : '' ?>" method="POST">
                <input type="text" name="vards" id="vards" placeholder="Vārds" value="<?= htmlspecialchars($pieteikums['Vards'] ?? '') ?>" required />
                <input type="text" name="uzvards" id="uzvards" placeholder="Uzvārds" value="<?= htmlspecialchars($pieteikums['Uzvards'] ?? '') ?>" required />
                <input type="email" name="epasts" id="epasts" placeholder="E-pasts" value="<?= htmlspecialchars($pieteikums['Epasts'] ?? '') ?>" required />
                <input type="text" name="lietotajvards" id="lietotajvards" placeholder="Lietotājvārds" value="<?= htmlspecialchars($pieteikums['Lietotajvards'] ?? '') ?>" required />

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
                    foreach ($statusi as $statuss) {
                        $selected = (isset($pieteikums['Statuss']) && $statuss === $pieteikums['Statuss']) ? 'selected' : '';
                        echo "<option value=\"" . htmlspecialchars($statuss) . "\" $selected>$statuss</option>";
                    }
                    ?>
                </select>

                <button class="ielogot" type="submit" id="<?= $isEdit ? 'saglabat' : 'izveidot' ?>">
                    <i class="fas <?= $isEdit ? 'fa-save' : 'fa-plus-circle' ?>"></i>
                    <?= $isEdit ? 'Saglabāt izmaiņas' : 'Izveidot pieteikumu' ?>
                </button>
            </form>
        </div>
    </div>
</main>

<?php require "../files/footer.php"; ?>
