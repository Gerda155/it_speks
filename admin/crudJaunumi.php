<?php
session_start();

if (!isset($_SESSION['lietotajvards'])) {
    header("Location: login.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../files/database.php";

// Обработка удаления
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    // Проверяем, существует ли такая запись
    $checkQuery = "SELECT * FROM it_speks_Jaunumi WHERE Jaunumi_ID = $delete_id";
    $checkResult = mysqli_query($savienojums, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Удаление
        $deleteQuery = "DELETE FROM it_speks_Jaunumi WHERE Jaunumi_ID = $delete_id";
        mysqli_query($savienojums, $deleteQuery);

        // Запись в историю действий
        $lietotajs = $_SESSION['lietotajvards'];

        // Получаем имя и фамилию пользователя
        $userQuery = "SELECT Vards, Uzvards FROM it_speks_Lietotaji WHERE Lietotajvards = ?";
        $stmt = mysqli_prepare($savienojums, $userQuery);
        mysqli_stmt_bind_param($stmt, "s", $lietotajs);
        mysqli_stmt_execute($stmt);
        $userResult = mysqli_stmt_get_result($stmt);

        if ($userRow = mysqli_fetch_assoc($userResult)) {
            $pilnsVards = $userRow['Vards'] . " " . $userRow['Uzvards'];
            $objekts = "Jaunums ar ID $delete_id";
            $notikums = "Dzēsts";
            $datums = date("Y-m-d H:i:s");

            $insertHistory = "INSERT INTO it_speks_DarbibuVesture (Objekts, Notikums, Datums, Lietotajs) 
                              VALUES (?, ?, ?, ?)";
            $stmt2 = mysqli_prepare($savienojums, $insertHistory);
            mysqli_stmt_bind_param($stmt2, "ssss", $objekts, $notikums, $datums, $pilnsVards);
            mysqli_stmt_execute($stmt2);
        }

        // Перенаправление
        header("Location: crudJaunumi.php?deleted=1");
        exit();
    }
}

require "../files/header.php";

// Получаем статус
$statusFilter = "";
$statusName = "Visi jaunumi"; // по умолчанию — Все новости

if (isset($_GET['status'])) {
    $status = mysqli_real_escape_string($savienojums, $_GET['status']);
    $statusFilter = "WHERE Statuss = '$status'";

    switch ($status) {
        case 'Neaktivs':
            $statusName = "Arhivētie jaunumi";
            break;
        case 'Melnraksts':
            $statusName = "Melnraksti";
            break;
        case 'Aktīvs':
            $statusName = "Publicētie jaunumi";
            break;
        default:
            $statusName = "Visi jaunumi";
            break;
    }
} else {
    $statusName = "Visi jaunumi";
}

// Разрешённые поля для сортировки
$allowedSortFields = [
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
                <option value="nosaukums" <?= $sortParam === 'nosaukums' ? 'selected' : '' ?>>Nosaukums</option>
                <option value="datums" <?= $sortParam === 'datums' ? 'selected' : '' ?>>Datums</option>
            </select>
        </div>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <p id="deleteMessage" style="color: green;">Jaunums veiksmīgi dzēsts.</p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Nosaukums</th>
                <th>Teksts</th>
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

                    echo "<td>" . htmlspecialchars($rinda['Publicesanas_datums'] ?? '') . "</td>";
                    echo "<td class='action-buttons'><a href='regJaunumi.php?id=" . $rinda['Jaunumi_ID'] . "' class='btn btn-edit'><i class='fas fa-edit'></i></a></td>";
                    echo "<td class='action-buttons'>
                            <a href='crudJaunumi.php?delete_id=" . $rinda['Jaunumi_ID'] . "' class='btn btn-delete' onclick='return confirm(\"Vai tiešām dzēst šo jaunumu?\");'>
                                <i class='fas fa-trash'></i>
                            </a>
                        </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nav pievienotu jaunumu.</td></tr>";
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