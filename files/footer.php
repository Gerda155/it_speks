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

        // Автоматическое скрытие сообщения через 2 секунды
        window.addEventListener('DOMContentLoaded', () => {
            const message = document.getElementById('deleteMessage');
            if (message) {
                setTimeout(() => {
                    message.style.display = 'none';
                }, 2000);
            }
        });

        // Автоматически убираем ?deleted=1 из URL после показа сообщения
        const deleteMsg = document.getElementById('deleteMessage');
        if (deleteMsg) {
            setTimeout(() => {
                const url = new URL(window.location);
                url.searchParams.delete('deleted');
                window.history.replaceState({}, document.title, url);
            }, 2000); // 2 секунды на прочитать сообщение, можно поменять
        }
    </script>
    </body>

    </html>