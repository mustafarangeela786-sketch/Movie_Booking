<?php
/* Popup Login/Signup modal - included once by header.php so it's
   available on every page. Uses AJAX (auth_login.php / auth_register.php)
   so the user never leaves the current page to log in. */
$is_admin_folder = (basename(dirname($_SERVER['PHP_SELF'])) === 'admin');
$base = $is_admin_folder ? '../' : '';
require_once __DIR__ . '/../config/google.php';
?>
<div class="auth-overlay" id="authOverlay"> <div class="auth-modal"> <button class="auth-modal-close" onclick="closeAuth()" aria-label="Close">&times;</button> <div class="auth-tabs"> <button class="auth-tab active" id="tabLoginBtn" onclick="switchAuthTab('login')">Log In</button> <button class="auth-tab" id="tabSignupBtn" onclick="switchAuthTab('signup')">Sign Up</button> </div> <div id="authError" class="alert alert-error" style="display:none;"></div> <form id="loginForm" onsubmit="return handleAuthLogin(event)"> <label>Email</label> <input type="email" id="loginEmail" autocomplete="off" required> <label>Password</label> <input type="password" id="loginPassword" autocomplete="new-password" required> <button type="submit" class="btn" style="width:100%;">Log In</button> </form> <form id="signupForm" style="display:none;" onsubmit="return handleAuthSignup(event)"> <label>Full Name</label> <input type="text" id="signupName" autocomplete="off" required> <label>Email</label> <input type="email" id="signupEmail" autocomplete="off" required> <label>Phone</label> <input type="text" id="signupPhone" autocomplete="off" placeholder="03xx-xxxxxxx"> <label>Password</label> <input type="password" id="signupPassword" autocomplete="new-password" minlength="6" required> <button type="submit" class="btn" style="width:100%;">Create Account</button> </form> <div class="google-divider"><span>or</span></div> <div id="googleBtnContainer" class="google-btn-wrap"></div> <div id="googleNotice" class="alert alert-error" style="display:none;font-size:12px;"> Google Sign-In isn't configured yet. Set GOOGLE_CLIENT_ID in config/google.php. </div> </div>
</div> <script src="https://accounts.google.com/gsi/client" async defer></script>
<script> const AUTH_BASE = "<?php echo $base; ?>";
    const GOOGLE_CLIENT_ID = "<?php echo GOOGLE_CLIENT_ID; ?>";

    function openAuth(tab) {
        document.getElementById('authOverlay').classList.add('open');
        switchAuthTab(tab || 'login');
    }
    function closeAuth() {
        document.getElementById('authOverlay').classList.remove('open');
        document.getElementById('authError').style.display = 'none';
    }
    function switchAuthTab(tab) {
        const isLogin = tab === 'login';
        document.getElementById('loginForm').style.display = isLogin ? 'flex' : 'none';
        document.getElementById('signupForm').style.display = isLogin ? 'none' : 'flex';
        document.getElementById('tabLoginBtn').classList.toggle('active', isLogin);
        document.getElementById('tabSignupBtn').classList.toggle('active', !isLogin);
        document.getElementById('authError').style.display = 'none';
    }
    function showAuthError(msg) {
        const el = document.getElementById('authError');
        el.textContent = msg;
        el.style.display = 'block';
    }
    document.getElementById('authOverlay').addEventListener('click', function (e) {
        if (e.target.id === 'authOverlay') closeAuth();
    });

    async function handleAuthLogin(e) {
        e.preventDefault();
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;
        try {
            const res = await fetch(AUTH_BASE + 'auth_login.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await res.json();
            if (data.success) { window.location.reload(); }
            else { showAuthError(data.error || 'Login failed.'); }
        } catch (err) { showAuthError('Could not reach the server.'); }
        return false;
    }

    async function handleAuthSignup(e) {
        e.preventDefault();
        const full_name = document.getElementById('signupName').value;
        const email = document.getElementById('signupEmail').value;
        const phone = document.getElementById('signupPhone').value;
        const password = document.getElementById('signupPassword').value;
        try {
            const res = await fetch(AUTH_BASE + 'auth_register.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ full_name, email, phone, password })
            });
            const data = await res.json();
            if (data.success) { window.location.reload(); }
            else { showAuthError(data.error || 'Registration failed.'); }
        } catch (err) { showAuthError('Could not reach the server.'); }
        return false;
    }

    function initGoogleSignIn() {
        if (!GOOGLE_CLIENT_ID || GOOGLE_CLIENT_ID.includes('YOUR_GOOGLE_CLIENT_ID')) {
            document.getElementById('googleNotice').style.display = 'block';
            return;
        }
        if (typeof google === 'undefined' || !google.accounts) {
            setTimeout(initGoogleSignIn, 200);
            return;
        }
        google.accounts.id.initialize({ client_id: GOOGLE_CLIENT_ID, callback: handleGoogleCredential });
        google.accounts.id.renderButton(
            document.getElementById('googleBtnContainer'),
            { theme: 'filled_black', size: 'large', shape: 'pill', width: 320 }
        );
    }
    async function handleGoogleCredential(response) {
        try {
            const res = await fetch(AUTH_BASE + 'google_login.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ credential: response.credential })
            });
            const data = await res.json();
            if (data.success) { window.location.reload(); }
            else { showAuthError(data.error || 'Google sign-in failed.'); }
        } catch (err) { showAuthError('Could not reach the server.'); }
    }
    initGoogleSignIn();
</script>
