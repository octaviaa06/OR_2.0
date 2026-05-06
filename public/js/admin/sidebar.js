document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const hamburger = document.getElementById('hamburgerBtn');
    const toggleBtn = document.getElementById('toggleSidebarBtn');

    // Toggle Sidebar Function
    function toggleSidebar() {
        if (window.innerWidth < 992) {
            // Mobile/Tablet: Show/Hide with overlay
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            hamburger?.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        } else {
            // Desktop: Collapse/Expand
            sidebar.classList.toggle('collapsed');
            sidebar.classList.toggle('expanded');
        }
    }

    // Event Listeners
    hamburger?.addEventListener('click', toggleSidebar);
    overlay?.addEventListener('click', toggleSidebar);
    toggleBtn?.addEventListener('click', toggleSidebar);

    // Close sidebar when clicking a menu link (mobile only)
    document.querySelectorAll('#sidebar .nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                sidebar.classList.remove('show');
                overlay?.classList.remove('show');
                hamburger?.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.innerWidth >= 992) {
                // Desktop: reset mobile states
                sidebar.classList.remove('show');
                overlay?.classList.remove('show');
                hamburger?.classList.remove('active');
                document.body.style.overflow = '';
                if (!sidebar.classList.contains('collapsed')) {
                    sidebar.classList.add('expanded');
                }
            } else {
                // Mobile: reset desktop states
                sidebar.classList.remove('collapsed', 'expanded');
            }
        }, 200);
    });

    // Close on ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar.classList.contains('show')) {
            toggleSidebar();
        }
    });

    // Swipe to close (mobile touch)
    let startX = 0;
    sidebar.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
    }, { passive: true });

    sidebar.addEventListener('touchend', (e) => {
        const endX = e.changedTouches[0].clientX;
        if (startX - endX > 70 && sidebar.classList.contains('show')) {
            toggleSidebar();
        }
    }, { passive: true });
});