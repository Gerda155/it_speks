<?php
session_start();

if (!isset($_SESSION['lietotajvards'])) {
    header("Location: login.php"); 
    exit();
}

require "../files/database.php"; 
require "../files/header.php";

$username = $_SESSION['lietotajvards'];

$sql = "SELECT Vards, Uzvards, Epasts, Lietotajvards, Talrunis, Izveides_datums, Statuss, Piezimes FROM it_speks_Lietotaji WHERE Lietotajvards = ?";
$stmt = $savienojums->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: login.php");
    exit();
}

$user = $result->fetch_assoc();
?>


<div class="dashboard">
    <h2 class="dashboard-title">Jūsu profils</h2>

    <div class="form-card">
        <form class="form" method="POST" action="/update-profile">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Vārds:</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['Vards']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="surname">Uzvārds:</label>
                    <input type="text" id="surname" name="surname" value="<?= htmlspecialchars($user['Uzvards']) ?>">
                </div>

                <div class="form-group">
                    <label for="email">E-pasts:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['Epasts']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="username">Lietotājvārds:</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['Lietotajvards']) ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="phone">Tālrunis:</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['Talrunis']) ?>">
                </div>

                <div class="form-group">
                    <label for="regdate">Reģistrēšanas datums:</label>
                    <input type="text" id="regdate" name="regdate" value="<?= htmlspecialchars($user['Izveides_datums']) ?>" disabled>
                </div>

                <div class="form-group simts">
                    <label for="notes">Piezīmes:</label>
                    <textarea id="notes" name="notes" rows="3"><?= htmlspecialchars($user['Piezimes']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="password">Jaunā parole:</label>
                    <input type="password" id="password" name="password" placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label for="confirm-password">Atkārtot paroli:</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="edit-button small-button">
                <i class="fas fa-save"></i> Saglabāt
            </button>
        </form>
    </div>
</div>
<?php
require "../files/footer.php";
?>