        </main>
    </div>
    <script>
        if (window.lucide) { lucide.createIcons(); }
        const menuButton = document.querySelector('.admin-menu-toggle');
        const sidebar = document.querySelector('.admin-sidebar');
        if (menuButton && sidebar) {
            menuButton.addEventListener('click', function () {
                const open = sidebar.classList.toggle('is-open');
                menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        }
    </script>
</body>
</html>
