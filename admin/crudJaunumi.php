<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../files/header.php";
require "../files/database.php";

// Получаем статус
$statusFilter = "";
$statusName = "Visi jaunumi"; // по умолчанию — Все новости

if (isset($_GET['status'])) {
    $status = mysqli_real_escape_string($savienojums, $_GET['status']);
    $statusFilter = "WHERE Statuss = '$status'";

    switch ($status) {
        case 'Neaktivs': $statusName = "Arhivētie jaunumi"; break;
        case 'Melnraksts': $statusName = "Melnraksti"; break;
        case 'Aktīvs': $statusName = "Publicētie jaunumi"; break;
        default: $statusName = "Jaunumi"; break;
    }
} else {
    $statusName = "Visi jaunumi";
}

// Разрешённые поля для сортировки
$allowedSortFields = [
    'id' => 'Jaunumi_ID',
    'nosaukums' => 'Nosaukums',
    'datums' => 'Publicesanas_datums'
];

// Определяем сортировку
$sortParam = $_GET['sort'] ?? 'id';
$sortField = $allowedSortFields[$sortParam] ?? 'Jaunumi_ID';

// Пагинация
$recordsPerPage = 7;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0
    ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Считаем общее количество записей (для пагинации)
$countQuery = "SELECT COUNT(*) as total FROM it_speks_Jaunumi $statusFilter";
$countResult = mysqli_query($savienojums, $countQuery);
$totalRecords = 0;
if ($countResult) {
    $row = mysqli_fetch_assoc($countResult);
    $totalRecords = (int)$row['total'];
}
$totalPages = ceil($totalRecords / $recordsPerPage);

// Основной запрос с лимитом и оффсетом
$vaicajums = "SELECT * FROM it_speks_Jaunumi $statusFilter ORDER BY $sortField DESC LIMIT $recordsPerPage OFFSET $offset";
$rezultats = mysqli_query($savienojums, $vaicajums);
?>

<main>
    <div class="table_header">
        <h1><i class="fa-solid fa-list"></i> <?= $statusName ?></h1>
        <div class="sort-dropdown">
            <a href="regJaunumi.php" class='add-button' title="Pievienot jaunumu"><i class="fa-solid fa-square-plus"></i></a>
            <label for="sort"><i class="fa-solid fa-filter"></i> Kārtot pēc:</label>
            <select id="sort">
                <option value="id" <?= $sortParam === 'id' ? 'selected' : '' ?>>ID</option>
                <option value="nosaukums" <?= $sortParam === 'nosaukums' ? 'selected' : '' ?>>Nosaukums</option>
                <option value="datums" <?= $sortParam === 'datums' ? 'selected' : '' ?>>Datums</option>
            </select>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nosaukums</th>
                <th>Text</th>
                <th>Attēls</th>
                <th>Publicēšanas datums</th>
                <th>Rediģēt</th>
                <th>Dzēst</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($rezultats) > 0) {
                while ($rinda = mysqli_fetch_assoc($rezultats)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($rinda['Nosaukums']) . "</td>";
                    echo "<td>" . htmlspecialchars($rinda['Text']) . "</td>";

                    $attels = $rinda['Bilde'] !== null ? base64_encode($rinda['Bilde']) : null;
                    echo "<td>" . ($attels ? "<i class='fa-solid fa-check'></i>" : "<i class='fa-solid fa-xmark'></i>") . "</td>";

                    echo "<td>" . htmlspecialchars($rinda['Publicesanas_datums']) . "</td>";
                    echo "<td class='action-buttons'><a href='regJaunumi.php?id=" . $rinda['Jaunumi_ID'] . "' class='btn btn-edit'><i class='fas fa-edit'></i></a></td>";
                    echo "<td class='action-buttons'><a href='regJaunumi?id=" . $rinda['Jaunumi_ID'] . "' class='btn btn-delete'><i class='fas fa-trash'></i></a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nav pievienotu jaunumu.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>

<?php
require "../files/crud.php";
require "../files/footer.php";
?>
