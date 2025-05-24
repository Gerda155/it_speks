<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../files/header.php";
require "../files/database.php";
?>

<main>
    <div class="login-container">
        <div class="login-box">
            <h1>Izveidot jaunu ziņu</h1>
            <p class="login-subtitle">Aizpildi visus laukus</p>
            <div class="kluda"></div>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="text" name="nosaukums" id="nosaukums" placeholder="Ziņas nosaukums" required />
                <textarea name="text" id="text" placeholder="Ziņas saturs" rows="6" required></textarea>
                <label for="bilde" class="custom-file-label"><i class="fas fa-image"></i> Pievienot bildi</label>
                <input type="file" name="bilde" id="bilde" accept="image/*" />

                <label for="statuss">Statuss</label>
                <select name="statuss" id="statuss" required>
                    <option value="Aktīvs" selected>Aktīvs</option>
                    <option value="Neaktīvs">Neaktīvs</option>
                    <option value="Melnraksts">Melnraksts</option>
                </select>

                <button type="submit" id="izveidot"><i class="fas fa-plus-circle"></i> Izveidot ziņu</button>
            </form>
            <a href="/admin" class="back-to-main"><i class="fas fa-arrow-left"></i> Atpakaļ uz admin paneli</a>
        </div>
    </div>
</main>

<?php
require "../files/footer.php";
?>