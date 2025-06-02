<?php
// Включаем ошибки PHP и MySQLi в лог
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

session_start();

if (!isset($_SESSION['lietotajvards'])) {
    header("Location: login.php");
    exit();
}

require "../files/database.php";

// Обработка удаления
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    // Защита: проверяем, существует ли такая запись
    $checkQuery = "SELECT * FROM it_speks_Lietotaji WHERE Lietotaj_ID = $delete_id";
    $checkResult = mysqli_query($savienojums, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Удаление
        $deleteQuery = "DELETE FROM it_speks_Lietotaji WHERE Lietotaj_ID = $delete_id";
        mysqli_query($savienojums, $deleteQuery);
        // Перенаправление, чтобы избежать повторного удаления при обновлении
        header("Location: crudModeratori.php?deleted=1");
        exit();
    }
}

require "../files/header.php";

// Сортировка
$allowedSortFields = [
    'id' => 'Lietotaj_ID',
    'name' => 'Uzvards',
    'date' => 'Izveides_datums'
];

$sortParam = $_GET['sort'] ?? 'id';
$sortField = $allowedSortFields[$sortParam] ?? 'Lietotaj_ID';

// Фильтр по статусу
$statusParam = $_GET['status'] ?? '';
$statusFilter = '';
if (!empty($statusParam)) {
    $status = mysqli_real_escape_string($savienojums, $statusParam);
    $statusFilter = "AND Statuss = '$status'";
}

// Заголовок страницы
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

// Подсчёт общего количества
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

// Основной запрос
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
                <option value="name" <?= $sortParam === 'name' ? 'selected' : '' ?>>Uzvards</option>
                <option value="date" <?= $sortParam === 'date' ? 'selected' : '' ?>>Datums</option>
            </select>
        </div>
    </div>

    <?php if (!empty($msg)): ?>
        <p id="deleteMessage" style="color: green;">Moderators veiksmīgi dzēsts.</p>
    <?php endif; ?>

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
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Vards'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['Uzvards'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['Epasts'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['Lietotajvards'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['Izveides_datums'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['Piezimes'] ?? '') ?></td>
                        <td class='action-buttons'>
                            <a href='regModeratori.php?id=<?= $row['Lietotaj_ID'] ?>' class='btn btn-edit'><i class='fas fa-edit'></i></a>
                        </td>
                        <td class='action-buttons'>
                            <a href="?delete_id=<?= $row['Lietotaj_ID'] ?>"
                                class="btn btn-delete"
                                onclick="return confirm('Tiešām dzēst šo ierakstu?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan='9'>Nav pievienotu moderātoru.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($totalPages > 1): ?>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php
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