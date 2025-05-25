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

// Разрешённые поля для сортировки
$allowedSortFields = [
    'id' => 'Lietotaj_ID',
    'name' => 'Vards',
    'date' => 'Izveides_datums'
];

$sortParam = $_GET['sort'] ?? 'id';
$sortField = $allowedSortFields[$sortParam] ?? 'Lietotaj_ID';

$statusParam = $_GET['status'] ?? '';
$statusFilter = '';
if (!empty($statusParam)) {
    $status = mysqli_real_escape_string($savienojums, $statusParam);
    $statusFilter = "AND Statuss = '$status'";
}

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

// Пагинация
$recordsPerPage = 7;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0
    ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Считаем общее количество
$countQuery = "
    SELECT COUNT(*) as total
    FROM it_speks_Lietotaji
    WHERE Loma = 'Moderators' $statusFilter
";
$countResult = mysqli_query($savienojums, $countQuery);
$totalRecords = 0;
if ($countResult) {
    $row = mysqli_fetch_assoc($countResult);
    $totalRecords = (int)$row['total'];
}
$totalPages = ceil($totalRecords / $recordsPerPage);

// Основной запрос с лимитом и оффсетом
$query = "
    SELECT Lietotaj_ID, Vards, Uzvards, Epasts, Lietotajvards, Izveides_datums, Statuss, Piezimes
    FROM it_speks_Lietotaji
    WHERE Loma = 'Moderators' $statusFilter
    ORDER BY $sortField DESC
    LIMIT $recordsPerPage OFFSET $offset
";

$result = mysqli_query($savienojums, $query);
?>

<main>
    <div class="table_header">
        <h1><i class="fa-solid fa-list"></i> <?= htmlspecialchars($title) ?></h1>
        <div class="sort-dropdown">
            <a href="regModeratori.php" class='add-button'><i class="fa-solid fa-square-plus"></i></a>
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
                    echo "<td class='action-buttons'><a href='regModeratori.php?id=" . $row['Lietotaj_ID'] . "' class='btn btn-edit'><i class='fas fa-edit'></i></a></td>";
                    echo "<td class='action-buttons'><a href='regModeratori.php?id=" . $row['Lietotaj_ID'] . "' class='btn btn-delete'><i class='fas fa-trash'></i></a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Nav pievienotu moderātoru.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <div class="pagination">
        <?php if ($totalPages > 1): ?>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php
                // Сохраняем get параметры sort и status для навигации
                $params = $_GET;
                $params['page'] = $p;
                $queryString = http_build_query($params);
                ?>
                <a href="?<?= $queryString ?>" class="<?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
            <?php endfor; ?>
        <?php endif; ?>
    </div>
</main>
<?php
require "../files/footer.php";
?>