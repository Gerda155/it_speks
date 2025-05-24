<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../files/header.php";
require "../files/database.php";

$statusFilter = "";
if (isset($_GET['status'])) {
    $status = mysqli_real_escape_string($savienojums, $_GET['status']);
    $statusFilter = "WHERE p.Statuss = '$status'";

    switch ($status) {
        case 'Jauns': $statusName = "Jaunie pieteikumi"; break;
        case 'Gaida atbildi': $statusName = "Gaida atbildi"; break;
        case 'Apstiprināts': $statusName = "Apstiprinātie pieteikumi"; break;
        case 'Noraidīts': $statusName = "Noraidītie pieteikumi"; break;
        default: $statusName = "Pieteikumi"; break;
    }
} else {
    $statusName = "Visi pieteikumi";
}

$allowedSortFields = [
    'id' => 'p.Pieteiksanas_ID',
    'name' => 'p.Vards',
    'date' => 'p.Pieteiksanas_datums'
];

$sortParam = $_GET['sort'] ?? 'id';
$sortField = $allowedSortFields[$sortParam] ?? 'p.Pieteiksanas_ID';

$vaicajums = "SELECT p.*, v.Amata_nosaukums FROM it_speks_Pieteiksanas p JOIN it_speks_Vakances v ON p.Vakances_ID = v.Vakances_ID $statusFilter ORDER BY $sortField DESC";
$rezultats = mysqli_query($savienojums, $vaicajums);
?>

<main>
    <div class="table_header">
        <h1><i class="fa-solid fa-list"></i> <?= $statusName ?></h1>
        <div class="sort-dropdown">
            <label for="sort"><i class="fa-solid fa-filter"></i> Kārtot pēc:</label>
            <select id="sort" onchange="location.href='?sort=' + this.value + '<?= isset($_GET['status']) ? '&status=' . $_GET['status'] : '' ?>'">
                <option value="id" <?= $sortParam === 'id' ? 'selected' : '' ?>>ID</option>
                <option value="name" <?= $sortParam === 'name' ? 'selected' : '' ?>>Vārds</option>
                <option value="date" <?= $sortParam === 'date' ? 'selected' : '' ?>>Datums</option>
            </select>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Vārds</th>
                <th>Uzvārds</th>
                <th>Vakance</th>
                <th>E-pasts</th>
                <th>Izgl.</th>
                <th>Pieredze</th>
                <th>CV</th>
                <th>Datums</th>
                <th>Statuss</th>
                <th>Koment.</th>
                <th>Rediģēt</th>
                <th>Dzēst</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($rezultats) > 0) {
                while ($row = mysqli_fetch_assoc($rezultats)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Vards']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Uzvards']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Amata_nosaukums']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Epasts']) . "</td>";
                    echo "<td>" . htmlspecialchars(mb_strimwidth($row['Izglitiba'], 0, 20, '...')) . "</td>";
                    echo "<td>" . htmlspecialchars(mb_strimwidth($row['Darba_pieredze'], 0, 20, '...')) . "</td>";
                    echo "<td>" . ($row['CV'] ? '<i class="fa-solid fa-check"></i>' : '<i class="fa-solid fa-xmark"></i>') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Pieteiksanas_datums']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Statuss']) . "</td>";
                    echo "<td>" . ($row['Komentars'] ? '<i class="fa-solid fa-check"></i>' : '<i class="fa-solid fa-xmark"></i>') . "</td>";
                    echo "<td class='action-buttons'><a href='redigetPieteikumu.php?id=" . $row['Pieteiksanas_ID'] . "' class='btn btn-edit'><i class='fas fa-edit'></i></a></td>";
                    echo "<td class='action-buttons'><a href='dzestPieteikumu.php?id=" . $row['Pieteiksanas_ID'] . "' class='btn btn-delete'><i class='fas fa-trash'></i></a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='12'>Nav pieteikumu.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>
