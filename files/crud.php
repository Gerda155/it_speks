    <!-- Пагинация -->
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


    <div id="deleteModal" class="modal hidden">
        <div class="modal-content">
            <p>Vai tiešām vēlaties dzēst ierakstu?</p>
            <div class="modal-actions">
                <a href="#" id="confirmDelete" class="btn btn-delete"><i class="fas fa-trash"></i> Jā, dzēst</a>
                <a href="#" id="cancelDelete" class="btn btn-secondary"><i class="fas fa-xmark"></i> Atcelt</a>
            </div>
        </div>
    </div>
