<?php
session_start();

if (!isset($_SESSION['lietotajvards'])) {
    header("Location: login.php"); 
    exit();
}

require "../files/header.php";
require "../files/database.php";

$statusFilter = "";
if (isset($_GET['status'])) {
    $status = mysqli_real_escape_string($savienojums, $_GET['status']);
    $statusFilter = "WHERE Statuss = '$status'";

    switch ($status) {
        case 'Neaktivs': $statusName = "Arhivētas vakances"; break;
        case 'Melnraksts': $statusName = "Melnraksti"; break;
        case 'Aktīvs': $statusName = "Publicētas vakances"; break;
        default: $statusName = "Vakances"; break;
    }
} else {
    $statusName = "Visas vakances";
}

$allowedSortFields = [
    'id' => 'Vakances_ID',
    'name' => 'Nosaukums',
    'date' => 'Publicesanas_datums'
];

$sortParam = $_GET['sort'] ?? 'id';
$sortField = $allowedSortFields[$sortParam] ?? 'Vakances_ID';

// Пагинация
$recordsPerPage = 7;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0
    ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Считаем общее количество
$countQuery = "SELECT COUNT(*) as total FROM it_speks_Vakances $statusFilter";
$countResult = mysqli_query($savienojums, $countQuery);
$totalRecords = 0;
if ($countResult) {
    $row = mysqli_fetch_assoc($countResult);
    $totalRecords = (int)$row['total'];
}
$totalPages = ceil($totalRecords / $recordsPerPage);

// Основной запрос с лимитом и оффсетом
$vaicajums = "
    SELECT *
    FROM it_speks_Vakances
    $statusFilter
    ORDER BY $sortField DESC
    LIMIT $recordsPerPage OFFSET $offset
";

$rezultats = mysqli_query($savienojums, $vaicajums);
?>

<main>
    <div class="table_header">
        <h1><i class="fa-solid fa-list"></i> <?= $statusName ?></h1>
        <div class="sort-dropdown">
            <a href="regVakances.php" class='add-button'><i class="fa-solid fa-square-plus"></i></a>
            <label for="sort"><i class="fa-solid fa-filter"></i> Kārtot pēc:</label>
            <select id="sort">
                <option value="id" <?= $sortParam === 'id' ? 'selected' : '' ?>>ID</option>
                <option value="name" <?= $sortParam === 'name' ? 'selected' : '' ?>>Nosaukums</option>
                <option value="date" <?= $sortParam === 'date' ? 'selected' : '' ?>>Datums</option>
            </select>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nosaukums</th>
                <th>Uzņemums</th>
                <th>Pilsēta</th>
                <th>Alga</th>
                <th>Prasības</th>
                <th>Apraksts</th>
                <th>Publicēšanas datums</th>
                <th>Tips</th>
                <th>Bilde</th>
                <th>Rediģēt</th>
                <th>Dzēst</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($rezultats) > 0) {
                while ($row = mysqli_fetch_assoc($rezultats)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Amata_nosaukums']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Uznemuma_nosaukums']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Atrasanas_vieta']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Alga']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Prasibas']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Darba_apraksts']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Publicesanas_datums']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Tips']) . "</td>";
                    $attels = $rinda['Bilde'] !== null ? base64_encode($rinda['Bilde']) : null;
                    if ($attels) {
                        echo "<td><i class='fa-solid fa-check'></i></td>";
                    } else {
                        echo "<td><i class='fa-solid fa-xmark'></i></td>";
                    }
                    echo "<td class='action-buttons'><a href='regVakances.php?id=" . $row['Vakances_ID'] . "' class='btn btn-edit'><i class='fas fa-edit'></i></a></td>";
                    echo "<td class='action-buttons'><a href='regVakances.php?id=" . $row['Vakances_ID'] . "' class='btn btn-delete'><i class='fas fa-trash'></i></a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nav pievienotu vakances.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>
<?php
require "../files/crud.php";
require "../files/footer.php";
?>