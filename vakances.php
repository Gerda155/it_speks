<?php
require "files/database.php";
require "files/header_klients.php";

$recordsPerPage = 6;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $recordsPerPage;

// Поиск и фильтры
$search = $_GET['search'] ?? '';
$typeFilter = $_GET['tips'] ?? '';

$conditions = ["Statuss = 'Aktīvs'"]; // Только активные

if ($search !== '') {
    $searchEscaped = mysqli_real_escape_string($savienojums, $search);
    $conditions[] = "(Amata_nosaukums LIKE '%$searchEscaped%' OR Uznemuma_nosaukums LIKE '%$searchEscaped%' OR Atrasanas_vieta LIKE '%$searchEscaped%')";
}
if ($typeFilter !== '') {
    $typeEscaped = mysqli_real_escape_string($savienojums, $typeFilter);
    $conditions[] = "Tips = '$typeEscaped'";
}

$whereClause = 'WHERE ' . implode(' AND ', $conditions);

// Общее количество
$countQuery = "SELECT COUNT(*) as total FROM it_speks_Vakances $whereClause";
$countResult = mysqli_query($savienojums, $countQuery);
$totalRecords = mysqli_fetch_assoc($countResult)['total'] ?? 0;
$totalPages = ceil($totalRecords / $recordsPerPage);

// Данные
$vakancesQuery = "
    SELECT * FROM it_speks_Vakances
    $whereClause
    ORDER BY Publicesanas_datums DESC
    LIMIT $recordsPerPage OFFSET $offset
";
$vakances = mysqli_query($savienojums, $vakancesQuery);

if (!$vakances) {
    die("Kļūda vaicājumā: " . mysqli_error($savienojums));
}
?>

<body class="client-bg">
    <div class="container">
        <h1 class="page-title">Aktuālās vakances</h1>

        <form method="GET" class="filters">
            <input type="text" name="search" placeholder="Meklēt pēc amata, uzņēmuma..." value="<?= htmlspecialchars($search) ?>">
            <select name="tips">
                <option value="">Darba tips</option>
                <option value="Pilna slodze" <?= $typeFilter === 'Pilna slodze' ? 'selected' : '' ?>>Pilna slodze</option>
                <option value="Nepilna slodze" <?= $typeFilter === 'Nepilna slodze' ? 'selected' : '' ?>>Nepilna slodze</option>
                <option value="Prakse" <?= $typeFilter === 'Prakse' ? 'selected' : '' ?>>Prakse</option>
            </select>
            <button type="submit">Filtrēt</button>
        </form>

        <div class="vacancy-list">
            <?php while ($v = mysqli_fetch_assoc($vakances)): ?>
                <a class="vacancy-card" href="vakance.php?id=<?= $v['Vakances_ID'] ?>">
                    <?php
                    if ($v['Bilde'] && strlen($v['Bilde']) > 100) {
                        echo '<img src="data:image/jpeg;base64,' . base64_encode($v['Bilde']) . '" alt="Vakances bilde">';
                    } elseif ($v['Bilde']) {
                        echo '<img src="uploads/' . htmlspecialchars($v['Bilde']) . '" alt="Vakances bilde">';
                    } else {
                        echo '<img src="files/no-image.jpg" alt="Nav attēla">';
                    }
                    ?>
                    <div class="vacancy-info">
                        <h2><?= htmlspecialchars($v['Amata_nosaukums']) ?></h2>
                        <p><strong>Uzņēmums:</strong> <?= htmlspecialchars($v['Uznemuma_nosaukums']) ?></p>
                        <p><strong>Vieta:</strong> <?= htmlspecialchars($v['Atrasanas_vieta']) ?></p>
                        <p><strong>Alga:</strong> <?= htmlspecialchars($v['Alga']) ?></p>
                        <p><strong>Tips:</strong> <?= htmlspecialchars($v['Tips']) ?></p>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>

        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>

    <?php require "files/footer.php"; ?>
