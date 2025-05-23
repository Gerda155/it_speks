<?php
    require "../files/header.php";
    ?>

    <main>
        <div class="table_header">
            <h1><i class="fa-solid fa-list"></i> Moderatori</h1>
            <div class="sort-dropdown">
                <label for="sort"><i class="fa-solid fa-filter"></i> Kārtot pēc:</label>
                <select id="sort">
                    <option value="id">ID</option>
                    <option value="name">Nosaukums</option>
                    <option value="date">Datums</option>
                </select>
            </div>
        </div>        
    <table>
        <thead>
            <tr>
                <th>Vārds</th>
                <th>Uzvārds</th>
                <th>E-pasts</th>
                <th>Lietotājvārds</th>
                <th>Tālrunis</th>
                <th>Registrēšanas datums</th>
                <th>Statuss</th>
                <th>Piezīmes</th>
                <th>Darbības</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Nosaukums A</td>
                <td>Apraksts A</td>
                <td>2025-05-13</td>
                <td>14:00</td>
                <td><img src="attels.jpg" alt="Attēls" width="50"></td>
                <td class="action-buttons">
                    <a href="rediget.html?id=1" class="btn btn-edit"><i class="fas fa-edit"></i> Rediģēt</a>
                    <a href="dzest.html?id=1" class="btn btn-delete"><i class="fas fa-trash"></i> Dzēst</a>
                </td>
            </tr>
        </tbody>
    </table>
    </main>
</body>

</html>