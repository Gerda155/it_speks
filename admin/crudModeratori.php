<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../files/header.php";
require "../files/database.php";

// Разрешённые поля для сортировки
$allowedSortFields = [
    'id' => 'Lietotaj_ID',
    'name' => 'Vards',
    'date' => 'Izveides_datums'
];

// Получаем параметр сортировки
$sortParam = $_GET['sort'] ?? 'id';
$sortField = $allowedSortFields[$sortParam] ?? 'Lietotaj_ID';

// Получаем параметр фильтрации по статусу (необязательный)
$statusParam = $_GET['status'] ?? '';
$statusFilter = '';
if (!empty($statusParam)) {
    $status = mysqli_real_escape_string($savienojums, $statusParam);
    $statusFilter = "AND Statuss = '$status'";
}

// Заголовок по умолчанию
$title = "Visi moderatori";

if (!empty($statusParam)) {
    switch ($statusParam) {
        case 'Aktivs':
            $title = "Aktīvie moderatori";
            break;
        case 'Neaktivs':
            $title = "Neaktīvie moderatori";
            break;
    }
}

// Финальный SQL-запрос
$query = "
    SELECT Lietotaj_ID, Vards, Uzvards, Epasts, Lietotajvards, Izveides_datums, Statuss, Piezimes
    FROM it_speks_Lietotaji
    WHERE Loma = 'Moderators' $statusFilter
    ORDER BY $sortField DESC
";

$result = mysqli_query($savienojums, $query);
?>

<main>
    <div class="table_header">
        <h1><i class="fa-solid fa-list"></i> <?= htmlspecialchars($title) ?></h1>
        <div class="sort-dropdown">
            <label for="sort"><i class="fa-solid fa-filter"></i> Kārtot pēc:</label>
            <select id="sort" onchange="location.href='?sort=' + this.value">
                <option value="id" <?= $sortParam === 'id' ? 'selected' : '' ?>>ID</option>
                <option value="name" <?= $sortParam === 'name' ? 'selected' : '' ?>>Nosaukums</option>
                <option value="date" <?= $sortParam === 'date' ? 'selected' : '' ?>>Datums</option>
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
                <th>Registrēšanas datums</th>
                <th>Piezīmes</th>
                <th>Rediģēt</th>
                <th>Dzēst</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Vards'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Uzvards'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Epasts'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Lietotajvards'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Izveides_datums'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Piezimes'] ?? '') . "</td>";
                    echo "<td class='action-buttons'><a href='redigetLietotaju.php?id=" . $row['Lietotaj_ID'] . "' class='btn btn-edit'><i class='fas fa-edit'></i></a></td>";
                    echo "<td class='action-buttons'><a href='dzestLietotaju.php?id=" . $row['Lietotaj_ID'] . "' class='btn btn-delete'><i class='fas fa-trash'></i></a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Nav pievienotu moderātoru.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>
<?php
require "../files/footer.php";
?>