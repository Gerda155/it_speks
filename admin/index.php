<?php
require "../files/header.php";
?>

<main class="dashboard">
    <section class="stats-wrapper">
        <div class="stats-info">
            <i class="fas fa-chart-pie"></i>
            <div class="stats-info-info">
                <h2>Statistika</h2>
                <p>Lorem, ipsum dolor.</p>
            </div>
        </div>
        <div class="stats-cards">
            <div class="card">
                <div class="card-info">
                    <h3><i class="fas fa-chart-pie"></i> NOSAUKUMS</h3>
                    <p>Lorem ipsum dolor sit amet consectetur.</p>
                </div>
                <div class="progress-circle"></div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3><i class="fas fa-chart-pie"></i> NOSAUKUMS</h3>
                    <p>Lorem ipsum dolor sit amet consectetur.</p>
                </div>
                <div class="progress-circle"></div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3><i class="fas fa-chart-pie"></i> NOSAUKUMS</h3>
                    <p>Lorem ipsum dolor sit amet consectetur.</p>
                </div>
                <div class="progress-circle"></div>
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
                    <tbody id="changes-table-body">
                        <!-- JS вставит сюда строки -->
                        <tr>
                            <td>Lorem ipsum dolor sit.</td>
                            <td>Lorem ipsum dolor sit.</td>
                            <td>Lorem ipsum dolor sit</td>
                            <td>Lorem ipsum dolor sit.</td>
                        </tr>
                        <tr>
                            <td>Lorem ipsum dolor sit.</td>
                            <td>Lorem ipsum dolor sit</td>
                            <td>Lorem ipsum dolor sit.</td>
                            <td>Lorem ipsum dolor sit</td>
                        </tr>
                        <tr>
                            <td>Lorem ipsum dolor sit</td>
                            <td>Lorem ipsum dolor sit</td>
                            <td>Lorem ipsum dolor sit.</td>
                            <td>Lorem ipsum dolor sit</td>
                        </tr>
                        <tr>
                            <td>Lorem ipsum dolor sit</td>
                            <td>Lorem ipsum dolor sit</td>
                            <td>Lorem ipsum dolor sit.</td>
                            <td>Lorem ipsum dolor sit</td>
                        </tr>
                        <tr>
                            <td>Lorem ipsum dolor sit</td>
                            <td>Lorem ipsum dolor sit</td>
                            <td>Lorem ipsum dolor sit</td>
                            <td>Lorem ipsum dolor sit.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="chart-section">
            <div class="changes-info">
                <i class="fas fa-chart-line"></i>
                <h2>Aktivitātes diagramma</h2>
            </div>
            <div class="chart">
                <!-- График -->
            </div>
        </div>
    </section>
</main>
</div>
</body>

</html>