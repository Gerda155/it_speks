<?php
require "../files/header.php";
?>

<div class="dashboard">
    <h2 class="dashboard-title">Jūsu profils</h2>

    <div class="form-card">
        <form class="form" method="POST" action="/update-profile">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Vārds:</label>
                    <input type="text" id="name" name="name" value="Anna" required>
                </div>

                <div class="form-group">
                    <label for="surname">Uzvārds:</label>
                    <input type="text" id="surname" name="surname" value="Admina">
                </div>

                <div class="form-group">
                    <label for="email">E-pasts:</label>
                    <input type="email" id="email" name="email" value="admin@example.com" required>
                </div>

                <div class="form-group">
                    <label for="username">Lietotājvārds:</label>
                    <input type="text" id="username" name="username" value="admin123">
                </div>

                <div class="form-group">
                    <label for="phone">Tālrunis:</label>
                    <input type="tel" id="phone" name="phone" value="+37100000000">
                </div>

                <div class="form-group">
                    <label for="regdate">Reģistrēšanas datums:</label>
                    <input type="text" id="regdate" name="regdate" value="2024-01-01" disabled>
                </div>

                <div class="form-group">
                    <label for="status">Statuss:</label>
                    <input type="text" id="status" name="status" value="Aktīvs">
                </div>

                <div class="form-group">
                    <label for="notes">Piezīmes:</label>
                    <textarea id="notes" name="notes" rows="3">Nav piezīmju</textarea>
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