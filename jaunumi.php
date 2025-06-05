<?php
require "files/database.php";
require "files/header_klients.php";

$recordsPerPage = 6;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $recordsPerPage;

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'desc'; // desc или asc

$conditions = ["Statuss = 'Aktīvs'"];
if ($search !== '') {
    $searchEscaped = mysqli_real_escape_string($savienojums, $search);
    $conditions[] = "(Nosaukums LIKE '%$searchEscaped%' OR Text LIKE '%$searchEscaped%')";
}
$whereClause = 'WHERE ' . implode(' AND ', $conditions);

// Подсчёт
$countQuery = "SELECT COUNT(*) as total FROM it_speks_Jaunumi $whereClause";
$countResult = mysqli_query($savienojums, $countQuery);
$totalRecords = mysqli_fetch_assoc($countResult)['total'] ?? 0;
$totalPages = ceil($totalRecords / $recordsPerPage);

// Получение новостей
$sortOrder = ($sort === 'asc') ? 'ASC' : 'DESC';
$newsQuery = "
    SELECT * FROM it_speks_Jaunumi
    $whereClause
    ORDER BY Publicesanas_datums $sortOrder
    LIMIT $recordsPerPage OFFSET $offset
";
$news = mysqli_query($savienojums, $newsQuery);
if (!$news) die("Kļūda vaicājumā: " . mysqli_error($savienojums));
?>

<body class="client-bg">
    <div class="container">
        <h1 class="page-title">Jaunumi</h1>

        <form method="GET" class="filters">
            <input type="text" name="search" placeholder="Meklēt pēc virsraksta vai satura..." value="<?= htmlspecialchars($search) ?>">
            <select name="sort">
                <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>Jaunākie vispirms</option>
                <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>Vecākie vispirms</option>
            </select>
            <button type="submit">Filtrēt</button>
        </form>

        <div class="news-list">
            <?php while ($n = mysqli_fetch_assoc($news)): ?>
                <a class="news-card" href="jaunums.php?id=<?= $n['Jaunumi_ID'] ?>">
                    <div class="news-img">
                        <?php
                        if ($n['Bilde'] && strlen($n['Bilde']) > 100) {
                            echo '<img src="data:image/jpeg;base64,' . base64_encode($n['Bilde']) . '" alt="Ziņas attēls">';
                        } elseif ($n['Bilde']) {
                            echo '<img src="uploads/' . htmlspecialchars($n['Bilde']) . '" alt="Ziņas attēls">';
                        } else {
                            echo '<img src="files/no-image.jpg" alt="Nav attēla">';
                        }
                        ?>
                    </div>
                    <div class="news-info">
                        <h2><?= htmlspecialchars($n['Nosaukums']) ?></h2>
                        <?php
                        $preview = trim(strip_tags($n['Text']));
                        echo $preview !== '' ? '<p>' . mb_strimwidth($preview, 0, 200, "...") . '</p>' : '<p class="no-content">Nav pieejama priekšskatījuma.</p>';
                        ?>
                        <p class="date"><?= date("d.m.Y", strtotime($n['Publicesanas_datums'])) ?></p>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>

        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <div class="back-to-home">
            <a href="index.php" class="back-arrow">⬅</a>
            <a href="index.php" class="back-text">Atpakaļ uz galveno lapu</a>
        </div>
    </div>
    </div>

    <?php require "files/footer.php"; ?>