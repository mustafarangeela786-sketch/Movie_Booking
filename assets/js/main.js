/* ==========================================================
   main.js - UI/UX Designer Interactions & Experience Engine
   ========================================================== */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // 1. Theme Toggle (Light / Dark)
    const themeBtn = document.getElementById('themeToggleBtn');
    if (themeBtn) {
        themeBtn.addEventListener('click', function () {
            const isLight = document.documentElement.classList.toggle('light-theme');
            try { localStorage.setItem('theme', isLight ? 'light' : 'dark'); } catch (e) {}
        });
    }

    // 2. Instant Live Search Dropdown
    const searchInputs = document.querySelectorAll('input[name="q"]');
    searchInputs.forEach(input => {
        const wrap = input.closest('.nav-search-wrap') || input.parentElement;
        let dropdown = wrap.querySelector('.search-results-dropdown');
        if (!dropdown) {
            dropdown = document.createElement('div');
            dropdown.className = 'search-results-dropdown';
            wrap.appendChild(dropdown);
        }

        let debounceTimer = null;
        input.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const val = input.value.trim();
            if (val.length < 2) {
                dropdown.classList.remove('is-active');
                dropdown.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(async () => {
                try {
                    const isSubdir = window.location.pathname.includes('/admin/');
                    const searchUrl = (isSubdir ? '../' : '') + 'api_search.php?q=' + encodeURIComponent(val);
                    const res = await fetch(searchUrl);
                    const data = await res.json();

                    if (data.results && data.results.length > 0) {
                        dropdown.innerHTML = data.results.map(m => `
                            <a href="${escapeHtml(m.url)}" class="search-res-item">
                                <img src="${escapeHtml(m.poster)}" alt="${escapeHtml(m.title)}" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/1/12/1925_Ford_Model_T_touring.jpg'">
                                <div class="search-res-info">
                                    <div class="search-res-title">${escapeHtml(m.title)}</div>
                                    <div class="search-res-meta">${escapeHtml(m.genre)} &bull; ${m.duration}m</div>
                                </div>
                                <span class="search-res-badge">${m.is_coming ? 'Coming Soon' : 'Book'}</span>
                            </a>
                        `).join('');
                        dropdown.classList.add('is-active');
                    } else {
                        dropdown.innerHTML = '<div style="padding:12px;font-size:13px;color:#8890a4;text-align:center;">No movies found. Press Enter to search all.</div>';
                        dropdown.classList.add('is-active');
                    }
                } catch (e) {
                    dropdown.classList.remove('is-active');
                }
            }, 180);
        });

        document.addEventListener('click', (e) => {
            if (!wrap.contains(e.target)) {
                dropdown.classList.remove('is-active');
            }
        });
    });

    // 3. Category Filter Tabs (Quick Filter on Homepage)
    const filterPills = document.querySelectorAll('.filter-tab-pill');
    if (filterPills.length) {
        filterPills.forEach(pill => {
            pill.addEventListener('click', () => {
                filterPills.forEach(p => p.classList.remove('is-active'));
                pill.classList.add('is-active');

                const targetCategory = pill.getAttribute('data-filter');
                const sections = document.querySelectorAll('.genre-section');

                sections.forEach(sec => {
                    const secGenre = (sec.getAttribute('data-genre') || '').toLowerCase();
                    const secTitle = (sec.querySelector('.genre-title')?.textContent || '').toLowerCase();

                    if (targetCategory === 'all') {
                        sec.style.display = '';
                    } else if (targetCategory === 'now-showing') {
                        sec.style.display = secTitle.includes('now showing') ? '' : 'none';
                    } else if (targetCategory === 'featured') {
                        sec.style.display = secTitle.includes('featured') ? '' : 'none';
                    } else if (targetCategory === 'coming-soon') {
                        sec.style.display = secTitle.includes('coming soon') ? '' : 'none';
                    } else {
                        sec.style.display = (secGenre === targetCategory || secTitle.includes(targetCategory)) ? '' : 'none';
                    }
                });
            });
        });
    }

    // 4. Kinetic Horizontal Scroll Row Controls
    document.querySelectorAll('.movie-row-wrap').forEach((wrap) => {
        const row = wrap.querySelector('.movie-row');
        const leftArrow = wrap.querySelector('.row-arrow-left');
        const rightArrow = wrap.querySelector('.row-arrow-right');
        if (!row || !leftArrow || !rightArrow) return;

        function updateArrowVisibility() {
            const maxScroll = row.scrollWidth - row.clientWidth - 4;
            leftArrow.classList.toggle('is-hidden', row.scrollLeft <= 4);
            rightArrow.classList.toggle('is-hidden', row.scrollLeft >= maxScroll);
        }

        leftArrow.addEventListener('click', () => {
            row.scrollBy({ left: -row.clientWidth * 0.75, behavior: 'smooth' });
        });
        rightArrow.addEventListener('click', () => {
            row.scrollBy({ left: row.clientWidth * 0.75, behavior: 'smooth' });
        });

        row.addEventListener('scroll', updateArrowVisibility);
        window.addEventListener('resize', updateArrowVisibility);
        updateArrowVisibility();
    });

    // 5. Toast Notifications
    let toastStack = document.getElementById('toastStack');
    if (!toastStack) {
        toastStack = document.createElement('div');
        toastStack.id = 'toastStack';
        toastStack.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:99999;display:flex;flex-direction:column;gap:10px;pointer-events:none;';
        document.body.appendChild(toastStack);
    }

    window.mvToast = function (message, duration = 3200) {
        const t = document.createElement('div');
        t.style.cssText = `
            background: rgba(17, 20, 28, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            pointer-events: auto;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: toastIn 0.25s ease;
        `;
        t.innerHTML = `<span>✓</span> <div>${escapeHtml(message)}</div>`;
        toastStack.appendChild(t);

        setTimeout(() => {
            t.style.transition = 'opacity 0.25s, transform 0.25s';
            t.style.opacity = '0';
            t.style.transform = 'translateY(8px)';
            setTimeout(() => t.remove(), 250);
        }, duration);
    };

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});
