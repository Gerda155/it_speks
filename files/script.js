document.querySelectorAll('.sidebar-header').forEach(header => {
    header.addEventListener('click', () => {
        const item = header.parentElement;
        const allItems = document.querySelectorAll('.sidebar-item');

        allItems.forEach(other => {
            if (other !== item) other.classList.remove('active');
        });

        item.classList.toggle('active');
    });
});

if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
};

