<?php require_once __DIR__ . '/../config/google.php'; ?>
<div class="google-divider"><span>or</span></div>
<div id="googleBtnContainer" class="google-btn-wrap"></div>
<div id="googleNotice" class="alert alert-error" style="display:none;"> Google Sign-In isn't configured yet. Set GOOGLE_CLIENT_ID in config/google.php.
</div> <script src="https://accounts.google.com/gsi/client" async defer></script>
<script> const GOOGLE_CLIENT_ID = "<?php echo GOOGLE_CLIENT_ID; ?>";

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
            const res = await fetch('<?php echo (basename(dirname($_SERVER['PHP_SELF'])) === 'admin') ? '../' : ''; ?>google_login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ credential: response.credential })
            });
            const data = await res.json();
            if (data.success) {
                window.location.href = data.redirect || 'index.php';
            } else {
                alert(data.error || 'Google sign-in failed.');
            }
        } catch (err) {
            alert('Could not reach the server. Is PHP/MySQL running?');
        }
    }

    initGoogleSignIn();
</script>
