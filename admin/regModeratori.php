<?php
ob_start();
session_start();

if (!isset($_SESSION['lietotajvards'])) {
    header("Location: login.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../files/database.php";

var_dump($_GET);

// Режим: редактирование или создание
$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$moderators = [
    'Vards' => '',
    'Uzvards' => '',
    'Epasts' => '',
    'Lietotajvards' => '',
    'Parole' => '',
    'Loma' => 'Moderators',
    'Statuss' => 'Aktīvs',
    'Piezimes' => '',
    'Talrunis' => ''
];

if ($isEdit) {
    $id = intval($_GET['id']);
    $query = "SELECT Vards, Uzvards, Epasts, Lietotajvards, Parole, Loma, Statuss, Piezimes, Talrunis FROM it_speks_Lietotaji WHERE Lietotaj_ID = $id LIMIT 1";
    $result = mysqli_query($savienojums, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $moderators = mysqli_fetch_assoc($result);
    } else {
        echo "<p style='color: red; text-align: center;'>Moderators nav atrasts</p>";
        $isEdit = false;
    }
}

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $vards = trim($_POST['vards']);
    $uzvards = trim($_POST['uzvards']);
    $epasts = trim($_POST['epasts']);
    $lietotajvards = trim($_POST['lietotajvards']);
    $statuss = $_POST['statuss'];
    $piezimes = trim($_POST['piezimes'] ?? '');
    $talrunis = trim($_POST['talrunis'] ?? '');
    $loma = $isEdit ? ($_POST['loma'] ?? 'Moderators') : 'Moderators';

    $parole1 = $_POST['parole1'] ?? '';
    $parole2 = $_POST['parole2'] ?? '';

    // Валидация пароля
    if ($isEdit) {
        if ($parole1 !== '' || $parole2 !== '') {
            if ($parole1 !== $parole2) {
                $errorMessage = "Paroles nesakrīt.";
            } else {
                $parole = password_hash($parole1, PASSWORD_DEFAULT);
            }
        } else {
            $parole = $moderators['Parole'];
        }
    } else {
        if ($parole1 === '' || $parole2 === '') {
            $errorMessage = "Lūdzu, ievadiet paroli divreiz.";
        } elseif ($parole1 !== $parole2) {
            $errorMessage = "Paroles nesakrīt.";
        } else {
            $parole = password_hash($parole1, PASSWORD_DEFAULT);
        }
    }

    // Проверка уникальности Lietotajvards
    if ($errorMessage === '') {
        $queryCheck = "SELECT Lietotaj_ID FROM it_speks_Lietotaji WHERE Lietotajvards = ?";
        $stmtCheck = $savienojums->prepare($queryCheck);
        $stmtCheck->bind_param("s", $lietotajvards);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->bind_result($existingId);
            $stmtCheck->fetch();
            if (!$isEdit || $existingId != $id) {
                $errorMessage = "Šāds lietotājvārds jau eksistē.";
            }
        }
        $stmtCheck->close();
    }

    if ($errorMessage === '') {
        if ($isEdit) {
            $stmt = $savienojums->prepare("UPDATE it_speks_Lietotaji SET Vards=?, Uzvards=?, Epasts=?, Lietotajvards=?, Parole=?, Loma=?, Statuss=?, Piezimes=?, Talrunis=? WHERE Lietotaj_ID=?");
            $stmt->bind_param("sssssssssi", $vards, $uzvards, $epasts, $lietotajvards, $parole, $loma, $statuss, $piezimes, $talrunis, $id);
        } else {
            $izveides_datums = date("Y-m-d H:i:s");
            $stmt = $savienojums->prepare("INSERT INTO it_speks_Lietotaji (Vards, Uzvards, Epasts, Lietotajvards, Parole, Loma, Izveides_datums, Statuss, Piezimes, Talrunis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssss", $vards, $uzvards, $epasts, $lietotajvards, $parole, $loma, $izveides_datums, $statuss, $piezimes, $talrunis);
        }

        if ($stmt->execute()) {
            header("Location: crudModeratori.php?msg=success");
            exit();
        } else {
            $errorMessage = "Kļūda datu saglabāšanā.";
        }
    }
}
require "../files/header.php";
ob_end_flush();
?>

<main>
    <div class="form-grid-card center">
        <div class="login-box">
            <h1><?= $isEdit ? "Rediģēt moderatoru" : "Izveidot jaunu moderatoru" ?></h1>
            <p class="login-subtitle">Aizpildi visus laukus</p>
            <?php if ($errorMessage): ?>
                <p style="color: red; text-align: center;"><?= htmlspecialchars($errorMessage) ?></p>
            <?php endif; ?>

            <form id="moderatorForm" action="<?= $isEdit ? '?id=' . $id : '' ?>" method="POST" class="form-layout">
                <input type="text" id="vards" name="vards" placeholder="Vārds" value="<?= htmlspecialchars($moderators['Vards']) ?>" required />
                <input type="text" id="uzvards" name="uzvards" placeholder="Uzvārds" value="<?= htmlspecialchars($moderators['Uzvards']) ?>" required />
                <input type="email" id="epasts" name="epasts" placeholder="E-pasts" value="<?= htmlspecialchars($moderators['Epasts']) ?>" required />
                <input type="text" id="lietotajvards" name="lietotajvards" placeholder="Lietotājvārds" value="<?= htmlspecialchars($moderators['Lietotajvards']) ?>" required />

                <label for="parole1">Parole</label>
                <div id="error-parole" class="error" style="color: red;"></div>
                <input type="password" id="parole1" name="parole1" placeholder="••••••••" <?= $isEdit ? '' : 'required' ?> />

                <label for="parole2">Atkārtot paroli</label>
                <input type="password" id="parole2" name="parole2" placeholder="••••••••" <?= $isEdit ? '' : 'required' ?> />

                <?php if ($isEdit): ?>
                    <label for="loma">Loma</label>
                    <select name="loma" required>
                        <option value="Moderators" <?= $moderators['Loma'] === 'Moderators' ? 'selected' : '' ?>>Moderators</option>
                        <option value="Administrators" <?= $moderators['Loma'] === 'Administrators' ? 'selected' : '' ?>>Administrators</option>
                    </select>
                <?php endif; ?>

                <label for="statuss">Statuss</label>
                <select name="statuss" required>
                    <option value="Aktīvs" <?= $moderators['Statuss'] === 'Aktīvs' ? 'selected' : '' ?>>Aktīvs</option>
                    <option value="Neaktīvs" <?= $moderators['Statuss'] === 'Neaktīvs' ? 'selected' : '' ?>>Neaktīvs</option>
                </select>

                <textarea name="piezimes" placeholder="Piezīmes"><?= htmlspecialchars($moderators['Piezimes']) ?></textarea>
                <input type="text" name="talrunis" placeholder="Tālrunis" value="<?= htmlspecialchars($moderators['Talrunis']) ?>" />

                <button class="ielogot" type="submit">
                    <i class="fas <?= $isEdit ? 'fa-save' : 'fa-plus-circle' ?>"></i>
                    <?= $isEdit ? 'Saglabāt izmaiņas' : 'Izveidot moderatoru' ?>
                </button>
            </form>
        </div>
    </div>
</main>

<script>
    document.getElementById('moderatorForm').addEventListener('submit', function(e) {
        e.preventDefault();

        let hasError = false;

        document.querySelectorAll('.error').forEach(el => el.textContent = '');

        const parole1 = document.querySelector('input[name="parole1"]').value;
        const parole2 = document.querySelector('input[name="parole2"]').value;

        if (parole1 || parole2) {
            if (parole1 !== parole2) {
                document.getElementById('error-parole').textContent = 'Paroles nesakrīt.';
                hasError = true;
            }
        }

        if (!hasError) {
            this.submit();
        }
    });
</script>
<?php require "../files/footer.php"; ?>