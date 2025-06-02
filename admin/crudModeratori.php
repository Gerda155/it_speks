<?php
// Включаем ошибки PHP и MySQLi в лог
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log'); // Лог в текущей папке, можно поменять путь

session_start();

if (!isset($_SESSION['lietotajvards'])) {
    header("Location: login.php");
    exit();
}

require "../files/database.php";

$msg = '';

// Удаление через GET
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    error_log("DELETE requested for ID: $delete_id");

    if ($delete_id > 0) {
        $stmt = $savienojums->prepare("DELETE FROM it_speks_Lietotaji WHERE Lietotaj_ID = ?");
        if ($stmt === false) {
            error_log("Prepare kļūda: " . $savienojums->error);
            $msg = "Neizdevās sagatavot dzēšanas pieprasījumu.";
        } else {
            $stmt->bind_param("i", $delete_id);
            if ($stmt->execute()) {
                error_log("Execute OK, affected rows: " . $stmt->affected_rows);
                if ($stmt->affected_rows > 0) {
                    // debug before redirect
                    error_log("Redirecting after successful delete");
                    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
                    exit();
                } else {
                    $msg = "Nav tādas ieraksta vai tas jau ir dzēsts.";
                }
            } else {
                error_log("Execute kļūda: " . $stmt->error);
                $msg = "Kļūda dzēšanas laikā.";
            }
            $stmt->close();
        }
    } else {
        $msg = "Nederīgs ID dzēšanai.";
    }
}


require "../files/header.php";


$allowedSortFields = [
    'id' => 'Lietotaj_ID',
    'name' => 'Uzvards',
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

$recordsPerPage = 7;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0
    ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

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
    <?php if (!empty($msg)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

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
