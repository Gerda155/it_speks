<?php
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
require "../files/header.php";

$recordsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0
    ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Получаем общее количество записей
$countQuery = "SELECT COUNT(*) as total FROM it_speks_DarbibuVesture";
$countResult = mysqli_query($savienojums, $countQuery);
$totalRecords = 0;
if ($countResult) {
    $row = mysqli_fetch_assoc($countResult);
    $totalRecords = (int)$row['total'];
}
$totalPages = ceil($totalRecords / $recordsPerPage);

// Получаем данные для отображения
$query = "
    SELECT ID, Objekts, Notikums, Datums, Lietotajs
    FROM it_speks_DarbibuVesture
    ORDER BY Datums DESC
    LIMIT $recordsPerPage OFFSET $offset
";

$result = mysqli_query($savienojums, $query);
?>

<main>
    <div class="table_header">
        <h1><i class="fa-solid fa-list"></i> Izmaiņu saraksts</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th>Objekts</th>
                <th>Notikums</th>
                <th>Datums</th>
                <th>Lietotājs</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Objekts']) ?></td>
                        <td><?= htmlspecialchars($row['Notikums']) ?></td>
                        <td><?= htmlspecialchars($row['Datums']) ?></td>
                        <td><?= htmlspecialchars($row['Lietotajs']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Nav pieejamu datu</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($totalPages > 1): ?>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <a href="?page=<?= $p ?>" class="<?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
            <?php endfor; ?>
        <?php endif; ?>
    </div>
</main>

<?php require "../files/footer.php"; ?>
