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

// Режим: редактирование или создание
$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$moderators = [
    'Vards' => '',
    'Uzvards' => '',
    'Epasts' => '',
    'Lietotajvards' => '',
    'Parole' => '',
    'Statuss' => 'Aktivs'
];

if ($isEdit) {
    $id = intval($_GET['id']);
    $query = "SELECT Vards, Uzvards, Epasts, Lietotajvards, Parole, Statuss FROM it_speks_Lietotaji WHERE Lietotaj_ID = $id LIMIT 1";
    $result = mysqli_query($savienojums, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $moderators = mysqli_fetch_assoc($result);
    } else {
        echo "<p style='color: red; text-align: center;'>Moderators nav atrasts</p>";
        $isEdit = false;
    }
}
?>

<main>
    <div class="form-grid-card center">
        <div class="login-box">
            <h1><?= $isEdit ? "Rediģēt moderatoru" : "Izveidot jaunu moderatoru" ?></h1>
            <p class="login-subtitle">Aizpildi visus laukus</p>
            <div class="kluda"></div>

            <form action="<?= $isEdit ? '?id=' . $id : '' ?>" method="POST" class="form-layout">
                <input type="text" name="vards" id="vards" placeholder="Vārds" value="<?= htmlspecialchars($moderators['Vards']) ?>" required />
                <input type="text" name="uzvards" id="uzvards" placeholder="Uzvārds" value="<?= htmlspecialchars($moderators['Uzvards']) ?>" required />
                <input type="email" name="epasts" id="epasts" placeholder="E-pasts" value="<?= htmlspecialchars($moderators['Epasts']) ?>" required />
                <input type="text" name="lietotajvards" id="lietotajvards" placeholder="Lietotājvārds" value="<?= htmlspecialchars($moderators['Lietotajvards']) ?>" required />
                <input type="password" name="parole" id="parole" placeholder="Parole" value="********" required />

                <label for="statuss">Statuss</label>
                <select name="statuss" id="statuss" required>
                    <option value="Aktivs" <?= $moderators['Statuss'] === 'Aktivs' ? 'selected' : '' ?>>Aktīvs</option>
                    <option value="Neaktivs" <?= $moderators['Statuss'] === 'Neaktivs' ? 'selected' : '' ?>>Neaktīvs</option>
                </select>

                <button class="ielogot" type="submit" id="<?= $isEdit ? 'saglabat' : 'izveidot' ?>">
                    <i class="fas <?= $isEdit ? 'fa-save' : 'fa-plus-circle' ?>"></i>
                    <?= $isEdit ? 'Saglabāt izmaiņas' : 'Izveidot moderatoru' ?>
                </button>
        </div>
        </form>
    </div>
</main>

<?php require "../files/footer.php"; ?>