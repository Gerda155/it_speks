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

document.getElementById('sort').addEventListener('change', function () {
    const selected = this.value;
    window.location.href = '?sort=' + selected;
}); //сортировка

document.getElementById('sort').addEventListener('change', function() {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sort', this.value);
    window.location.search = urlParams.toString();
}); //страница с сортировкой