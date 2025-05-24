<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../files/header.php";
require "../files/database.php";
?>

<main>
    <div class="login-container">
        <div class="login-box">
            <h1>Izveidot jaunu pieteikumu</h1>
            <p class="login-subtitle">Aizpildi visus laukus</p>
            <div class="kluda"></div>
            <form action="" method="POST">
                <input type="text" name="vards" id="vards" placeholder="Vārds" required />
                <input type="text" name="uzvards" id="uzvards" placeholder="Uzvārds" required />
                <input type="email" name="epasts" id="epasts" placeholder="E-pasts" required />
                <input type="text" name="lietotajvards" id="lietotajvards" placeholder="Lietotājvārds" required />

                <label for="vakances_id">Vakance</label>
                <select name="vakances_id" id="vakances_id" required>
                    <!-- Опции здесь подгружаются из базы, пример: -->
                    <option value="">Izvēlies vakanci</option>
                    <option value="1">Programmatūras izstrādātājs</option>
                    <option value="2">Testētājs</option>
                    <!-- ... -->
                </select>

                <label for="statuss">Statuss</label>
                <select name="statuss" id="statuss" required>
                    <option value="Jauns" selected>Jauns</option>
                    <option value="Gaida atbildi">Gaida atbildi</option>
                    <option value="Apstiprināts">Apstiprināts</option>
                    <option value="Noraidīts">Noraidīts</option>
                </select>

                <button type="submit" id="izveidot"><i class="fas fa-plus-circle"></i> Izveidot pieteikumu</button>
            </form>
            <a href="/admin" class="back-to-main"><i class="fas fa-arrow-left"></i> Atpakaļ uz admin paneli</a>
        </div>
    </div>
</main>

<?php
require "../files/footer.php";
?>