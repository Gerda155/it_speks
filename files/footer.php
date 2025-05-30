    <footer class="footer">
        <div class="footer-content">
            &copy; 2025 Liepājas Valsts tehnikums. Visas tiesības aizsargātas.
        </div>
    </footer>
    <script>
        document.querySelectorAll('.sidebar-header').forEach(header => {
            header.addEventListener('click', () => {
                const item = header.parentElement;
                const allItems = document.querySelectorAll('.sidebar-item');

                allItems.forEach(other => {
                    if (other !== item) other.classList.remove('active');
                });

                item.classList.toggle('active');
            });
        }); //Боковая панель

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }; //Рефреш

        document.getElementById('sort').addEventListener('change', function() {
            const selected = this.value;
            window.location.href = '?sort=' + selected;
        }); //сортировка

        document.getElementById('sort').addEventListener('change', function() {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort', this.value);
            window.location.search = urlParams.toString();
        }); //страница с сортировкой

        /*______MODAL_________*/
        const modal = document.getElementById('deleteModal');
        const confirmBtn = document.getElementById('confirmDelete');
        const cancelBtn = document.getElementById('cancelDelete');
        let selectedId = null;

        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', e => {
                e.preventDefault();
                selectedId = button.getAttribute('data-id');
                modal.classList.remove('hidden');
            });
        });

        confirmBtn.addEventListener('click', e => {
            e.preventDefault();
            if (selectedId) {
                window.location.href = `dzest.php?id=${selectedId}`;
            }
        });

        cancelBtn.addEventListener('click', e => {
            e.preventDefault();
            modal.classList.add('hidden');
            selectedId = null;
        });
    </script>
    </body>

    </html>