<!-- Sidebar Scroll Restoration Script -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebarNav = document.querySelector('.sidebar-nav');
        if (sidebarNav) {
            // Restore scroll position
            const scrollPos = localStorage.getItem('sidebar_scroll_position');
            if (scrollPos) {
                sidebarNav.scrollTop = scrollPos;
            }

            // Save scroll position on scroll
            sidebarNav.addEventListener('scroll', function() {
                localStorage.setItem('sidebar_scroll_position', sidebarNav.scrollTop);
            });
        }

        // Sidebar Module Search
        const searchInput = document.getElementById('sidebarSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                const menuItems = document.querySelectorAll('.sidebar-nav > a, .sidebar-nav > .has-submenu');
                const categories = document.querySelectorAll('.sidebar-category-label');

                menuItems.forEach(item => {
                    let text = '';
                    
                    if (item.classList.contains('has-submenu')) {
                        // It's a submenu parent
                        const labelEl = item.querySelector('.sidebar-label');
                        text = labelEl ? labelEl.textContent.toLowerCase() : '';
                        
                        const subItems = item.querySelectorAll('.sidebar-submenu a');
                        const submenu = item.querySelector('.sidebar-submenu');
                        let subItemVisible = false;
                        
                        subItems.forEach(sub => {
                            const subText = sub.textContent.toLowerCase();
                            if (subText.includes(filter)) {
                                sub.style.display = '';
                                subItemVisible = true;
                            } else {
                                sub.style.display = 'none';
                            }
                        });

                        if (text.includes(filter) || subItemVisible) {
                            item.style.display = '';
                            if (filter.length > 0) {
                                item.classList.add('open');
                                if (submenu) {
                                    submenu.style.maxHeight = '1000px';
                                    submenu.style.opacity = '1';
                                    submenu.style.paddingTop = '2px';
                                    submenu.style.paddingBottom = '8px';
                                }
                            }
                        } else {
                            item.style.display = 'none';
                            item.classList.remove('open');
                            if (submenu) {
                                submenu.style.maxHeight = '0';
                                submenu.style.opacity = '0';
                                submenu.style.paddingTop = '0';
                                submenu.style.paddingBottom = '0';
                            }
                        }
                    } else {
                        // It's a direct link
                        const labelEl = item.querySelector('.sidebar-label');
                        text = labelEl ? labelEl.textContent.toLowerCase() : '';
                        
                        if (text.includes(filter)) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    }
                });

                // Hide categories when searching
                categories.forEach(cat => {
                    if (filter.length > 0) {
                        cat.style.display = 'none';
                    } else {
                        cat.style.display = '';
                    }
                });
            });
        }

        // Expand sidebar if search icon clicked when mini
        const searchIconOnly = document.querySelector('.search-icon-only');
        if (searchIconOnly && searchInput) {
            searchIconOnly.addEventListener('click', function() {
                const sidebar = document.getElementById('modernSidebar');
                if (sidebar && sidebar.classList.contains('mini')) {
                    if (typeof toggleSidebar === 'function') {
                        toggleSidebar();
                        setTimeout(() => searchInput.focus(), 300);
                    }
                }
            });
        }
    });
</script>
