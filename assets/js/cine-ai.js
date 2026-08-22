/* ==========================================================
   cine-ai.js - CineAI Interactive Movie Assistant & Recommender
   Features:
   - Floating AI trigger button with pulse ripple
   - Glassmorphic AI dialog / drawer with neural avatar
   - Quick Mood / Vibe chips
   - Real-time simulated neural typing streaming
   - Interactive movie recommendation cards with AI match score
   ========================================================== */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        initCineAI();
    });

    function initCineAI() {
        // Prevent duplicate injection
        if (document.getElementById('cineAiWidget')) return;

        // 1. Create the floating trigger & dialog markup
        const widget = document.createElement('div');
        widget.id = 'cineAiWidget';
        widget.className = 'cine-ai-widget';
        widget.innerHTML = `
            <!-- Floating Trigger Button -->
            <button type="button" class="cine-ai-btn" id="cineAiToggleBtn" aria-label="Open CineAI Assistant" title="Ask CineAI Movie Assistant">
                <span class="cine-ai-btn-glow"></span>
                <span class="cine-ai-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L14.39 8.26L21 9.27L16.14 14.14L17.29 20.73L12 17.27L6.71 20.73L7.86 14.14L3 9.27L9.61 8.26L12 2Z" fill="url(#aiSparkleGrad)"/>
                        <circle cx="19" cy="5" r="2" fill="#00f2fe"/>
                        <circle cx="5" cy="19" r="1.5" fill="#ff007a"/>
                        <defs>
                            <linearGradient id="aiSparkleGrad" x1="3" y1="2" x2="21" y2="20.73" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#00f2fe"/>
                                <stop offset="0.5" stop-color="#8a2be2"/>
                                <stop offset="1" stop-color="#ff007a"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </span>
                <span class="cine-ai-btn-text">CineAI <span class="cine-ai-pill">PRO</span></span>
            </button>

            <!-- CineAI Drawer / Modal -->
            <div class="cine-ai-modal" id="cineAiModal" aria-hidden="true">
                <div class="cine-ai-backdrop" id="cineAiBackdrop"></div>
                <div class="cine-ai-panel">
                    <!-- Panel Header -->
                    <div class="cine-ai-header">
                        <div class="cine-ai-brand">
                            <div class="cine-ai-orb">
                                <span class="orb-core"></span>
                                <span class="orb-ring"></span>
                            </div>
                            <div class="cine-ai-titles">
                                <div class="cine-ai-title">CineAI Assistant <span class="cine-ai-status"><span class="status-dot"></span> Neural Engine Active</span></div>
                                <div class="cine-ai-subtitle">Ask anything or pick a vibe to find your next movie</div>
                            </div>
                        </div>
                        <button type="button" class="cine-ai-close" id="cineAiCloseBtn" aria-label="Close CineAI">&times;</button>
                    </div>

                    <!-- Conversation History -->
                    <div class="cine-ai-body" id="cineAiBody">
                        <div class="cine-ai-msg ai-msg">
                            <div class="msg-avatar">✨</div>
                            <div class="msg-content">
                                <p>Hey there! I am <strong>CineAI</strong>, your smart cinema concierge. Tell me what mood you're in, or choose one of the quick vibes below!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Mood Chips -->
                    <div class="cine-ai-chips-wrap">
                        <div class="cine-ai-chips" id="cineAiChips">
                            <button type="button" class="ai-chip" data-mood="action-thriller">⚡ Adrenaline & Action</button>
                            <button type="button" class="ai-chip" data-mood="romantic-date">💖 Date Night Romance</button>
                            <button type="button" class="ai-chip" data-mood="sci-fi-mindbend">🚀 Mind-Bending Sci-Fi</button>
                            <button type="button" class="ai-chip" data-mood="top-rated">🍿 Top Rated Hits (4.5+ ⭐)</button>
                            <button type="button" class="ai-chip" data-mood="comedy-laughs">😂 Feel-Good Comedy</button>
                            <button type="button" class="ai-chip" data-mood="horror-fear">👻 Late-Night Horror</button>
                            <button type="button" class="ai-chip" data-mood="hollywood-blockbuster">🎬 Hollywood Blockbusters</button>
                            <button type="button" class="ai-chip" data-mood="bollywood-hits">🌟 Bollywood Hits</button>
                        </div>
                    </div>

                    <!-- Input Bar -->
                    <form class="cine-ai-footer" id="cineAiForm">
                        <div class="cine-ai-input-wrap">
                            <input type="text" id="cineAiInput" placeholder="e.g. 'Exciting sci-fi with fast action' or 'Romantic movie'..." autocomplete="off">
                            <button type="submit" class="cine-ai-send" id="cineAiSendBtn" aria-label="Send prompt">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;

        document.body.appendChild(widget);

        // 2. DOM Elements
        const toggleBtn  = document.getElementById('cineAiToggleBtn');
        const modal      = document.getElementById('cineAiModal');
        const backdrop   = document.getElementById('cineAiBackdrop');
        const closeBtn   = document.getElementById('cineAiCloseBtn');
        const form       = document.getElementById('cineAiForm');
        const input      = document.getElementById('cineAiInput');
        const body       = document.getElementById('cineAiBody');
        const chips      = document.querySelectorAll('.ai-chip');

        // Determine base API path (accounts for /admin/ subfolder if used there)
        const isSubdir = window.location.pathname.includes('/admin/');
        const apiEndpoint = (isSubdir ? '../' : '') + 'api_ai_assistant.php';

        function openModal() {
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            setTimeout(() => input.focus(), 300);
        }

        function closeModal() {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
        }

        toggleBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        backdrop.addEventListener('click', closeModal);

        // Handle Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('is-open')) {
                closeModal();
            }
        });

        // Chip click handler
        chips.forEach(chip => {
            chip.addEventListener('click', () => {
                const text = chip.textContent.trim();
                const mood = chip.getAttribute('data-mood');
                sendPrompt(text, mood);
            });
        });

        // Form submit handler
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const text = input.value.trim();
            if (!text) return;
            input.value = '';
            sendPrompt(text, text);
        });

        // Function to send prompt to backend
        async function sendPrompt(userText, queryParam) {
            // Append user bubble
            appendMessage('user', userText);

            // Append AI thinking bubble with animated glowing dots
            const thinkingId = 'thinking_' + Date.now();
            appendThinking(thinkingId);
            scrollBodyToBottom();

            try {
                const res = await fetch(apiEndpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ query: queryParam, mood: queryParam })
                });

                const data = await res.json();
                removeThinking(thinkingId);

                if (data.success && data.movies && data.movies.length > 0) {
                    appendAiRecommendation(data.commentary, data.movies);
                } else {
                    appendMessage('ai', data.message || "I couldn't find exact matches right now, but feel free to browse our home catalog!");
                }
            } catch (err) {
                removeThinking(thinkingId);
                appendMessage('ai', "Neural connection interrupted. Please ensure your internet/server is online.");
            }

            scrollBodyToBottom();
        }

        function appendMessage(sender, text) {
            const row = document.createElement('div');
            row.className = `cine-ai-msg ${sender === 'user' ? 'user-msg' : 'ai-msg'}`;
            row.innerHTML = `
                <div class="msg-avatar">${sender === 'user' ? '👤' : '✨'}</div>
                <div class="msg-content"><p>${escapeHtml(text)}</p></div>
            `;
            body.appendChild(row);
            scrollBodyToBottom();
        }

        function appendThinking(id) {
            const row = document.createElement('div');
            row.id = id;
            row.className = 'cine-ai-msg ai-msg thinking-msg';
            row.innerHTML = `
                <div class="msg-avatar">✨</div>
                <div class="msg-content">
                    <div class="ai-typing-indicator">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            `;
            body.appendChild(row);
        }

        function removeThinking(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        function appendAiRecommendation(commentary, movies) {
            const row = document.createElement('div');
            row.className = 'cine-ai-msg ai-msg';

            let moviesHtml = '<div class="ai-movie-cards-grid">';
            movies.forEach(m => {
                const isComing = m.is_coming_soon;
                const starDisplay = m.rating ? `★ ${m.rating}` : (isComing ? 'Coming Soon' : 'Featured');
                moviesHtml += `
                    <div class="ai-movie-card">
                        <div class="ai-card-poster">
                            <img src="${escapeHtml(m.poster)}" alt="${escapeHtml(m.title)}" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/1/12/1925_Ford_Model_T_touring.jpg'">
                            <span class="ai-match-badge">✨ ${m.match_score}% Match</span>
                        </div>
                        <div class="ai-card-info">
                            <div class="ai-card-title">${escapeHtml(m.title)}</div>
                            <div class="ai-card-meta">
                                <span>${escapeHtml(m.genre)}</span> &bull; 
                                <span>${m.duration} min</span> &bull; 
                                <span class="ai-star">${starDisplay}</span>
                            </div>
                            <p class="ai-card-desc">${escapeHtml(m.description)}</p>
                            <a href="${escapeHtml(m.book_url)}" class="ai-card-btn">
                                ⚡ Book Tickets
                            </a>
                        </div>
                    </div>
                `;
            });
            moviesHtml += '</div>';

            row.innerHTML = `
                <div class="msg-avatar">✨</div>
                <div class="msg-content">
                    <p>${commentary}</p>
                    ${moviesHtml}
                </div>
            `;

            body.appendChild(row);
            scrollBodyToBottom();
        }

        function scrollBodyToBottom() {
            setTimeout(() => {
                body.scrollTop = body.scrollHeight;
            }, 50);
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    }
})();
