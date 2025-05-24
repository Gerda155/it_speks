<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../files/header.php";
require "../files/database.php";

// Статистика по заявкам
$countAll = mysqli_fetch_assoc(mysqli_query($savienojums, "SELECT COUNT(*) as total FROM it_speks_Pieteiksanas"))['total'];
$countNew = mysqli_fetch_assoc(mysqli_query($savienojums, "SELECT COUNT(*) as total FROM it_speks_Pieteiksanas WHERE Statuss = 'Jauns'"))['total'];
$countApproved = mysqli_fetch_assoc(mysqli_query($savienojums, "SELECT COUNT(*) as total FROM it_speks_Pieteiksanas WHERE Statuss = 'Apstiprināts'"))['total'];
$countRejected = mysqli_fetch_assoc(mysqli_query($savienojums, "SELECT COUNT(*) as total FROM it_speks_Pieteiksanas WHERE Statuss = 'Noraidīts'"))['total'];

// Последние изменения (пример)
$changesQuery = "SELECT Objekts, Notikums, Datums, Lietotajs FROM it_speks_DarbibuVesture ORDER BY Datums DESC LIMIT 5";
$changesResult = mysqli_query($savienojums, $changesQuery);
$changes = mysqli_fetch_all($changesResult, MYSQLI_ASSOC);
?>

<main class="dashboard">
    <section class="stats-wrapper">
        <div class="stats-info">
            <i class="fas fa-chart-pie"></i>
            <div class="stats-info-info">
                <h2>Statistika</h2>
                <p>Kopējais iesniegumu skaits: <?= $countAll ?></p>
            </div>
        </div>
        <div class="stats-cards">
            <div class="card">
                <div class="card-info">
                    <h3><i class="fas fa-inbox"></i> Jaunie</h3>
                    <p><?= $countNew ?> pieteikumi</p>
                </div>
                <div class="progress-circle" data-value="<?= $countAll ? round(($countNew/$countAll)*100) : 0 ?>"></div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3><i class="fas fa-check"></i> Apstiprinātie</h3>
                    <p><?= $countApproved ?> pieteikumi</p>
                </div>
                <div class="progress-circle" data-value="<?= $countAll ? round(($countApproved/$countAll)*100) : 0 ?>"></div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3><i class="fas fa-times"></i> Noraidītie</h3>
                    <p><?= $countRejected ?> pieteikumi</p>
                </div>
                <div class="progress-circle" data-value="<?= $countAll ? round(($countRejected/$countAll)*100) : 0 ?>"></div>
            </div>
        </div>
    </section>

    <section class="bottom-sections">
        <div class="changes-section">
            <div class="changes-info">
                <i class="fas fa-poll-h"></i>
                <h2>5 Pēdējās izmaiņas</h2>
            </div>
            <div class="changes-table">
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
                        <?php foreach ($changes as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['Objekts']) ?></td>
                                <td><?= htmlspecialchars($row['Notikums']) ?></td>
                                <td><?= htmlspecialchars($row['Datums']) ?></td>
                                <td><?= htmlspecialchars($row['Lietotajs']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="chart-section">
            <div class="changes-info">
                <i class="fas fa-chart-line"></i>
                <h2>Aktivitātes diagramma</h2>
            </div>
            <canvas id="activityChart"></canvas>
        </div>
    </section>
</main>
<?php
require "../files/footer.php";
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('activityChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jauni', 'Apstiprināti', 'Noraidīti'],
            datasets: [{
                label: 'Pieteikumi',
                data: [<?= $countNew ?>, <?= $countApproved ?>, <?= $countRejected ?>],
                backgroundColor: ['#3b82f6', '#10b981', '#ef4444']
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    document.querySelectorAll('.progress-circle').forEach(el => {
        const percent = el.getAttribute('data-value');
        el.innerHTML = `<svg viewBox="0 0 36 36" class="circular-chart">
            <path class="circle-bg" d="M18 2.0845
                a 15.9155 15.9155 0 0 1 0 31.831
                a 15.9155 15.9155 0 0 1 0 -31.831" />
            <path class="circle" stroke-dasharray="${percent}, 100" d="M18 2.0845
                a 15.9155 15.9155 0 0 1 0 31.831
                a 15.9155 15.9155 0 0 1 0 -31.831" />
            <text x="18" y="20.35" class="percentage">${percent}%</text>
        </svg>`;
    });
</script>