<?php use Aether\Session; ?>
<!doctype html>
<html lang="en" class="scroll-smooth">
<head>
    <?php Session::loadTemplate('core/_head'); ?>
</head>

<body class="antialiased selection:bg-cyan-500 selection:text-slate-950 overflow-x-hidden">
    <!-- ══════ ANIMATED AURORA BACKGROUND ══════ -->
    <div class="aurora-mesh">
        <div class="aurora-blob aurora-blob-1"></div>
        <div class="aurora-blob aurora-blob-2"></div>
        <div class="aurora-blob aurora-blob-3"></div>
        <div class="aurora-blob aurora-blob-4"></div>
    </div>

    <!-- Custom Ball Cursor (GSAP) -->
    <div class="ball" id="ball"></div>

    <?php Session::loadTemplate('core/_nav'); ?>

    <main id="main-content">
        <?php
        // Advanced Template Inheritance:
        // Individual views are buffered and injected here as $content.
        if (isset($content)) {
            echo $content;
        } else {
            // Fallback for non-inheritance legacy calls
            Session::loadTemplate(Session::currentScript());
        }
        ?>
    </main>

    <?php Session::loadTemplate('core/_footer'); ?>
    <?php Session::loadTemplate('core/_toastv3'); ?>

    <!-- jQuery & CDNs -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    
    <!-- Workspace JS -->
    <script src="<?= get_config('base_path') ?>assets/js/toastv3.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/ball.js"></script>
    
    <!-- Portal Login and Utility Scripts -->
    <script>
        // Open/Close Client Login Modal
        const loginModal = document.getElementById('login-modal');
        const openLoginBtns = document.querySelectorAll('.open-login-btn');
        const closeLoginBtn = document.getElementById('close-login-btn');
        const demoCodeBtn = document.getElementById('demo-code-btn');
        const eventPasscodeInput = document.getElementById('event-passcode');

        if (openLoginBtns) {
            openLoginBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (loginModal) loginModal.classList.add('active');
                });
            });
        }

        if (closeLoginBtn) {
            closeLoginBtn.addEventListener('click', () => {
                if (loginModal) loginModal.classList.remove('active');
            });
        }

        if (demoCodeBtn && eventPasscodeInput) {
            demoCodeBtn.addEventListener('click', () => {
                eventPasscodeInput.value = 'DEMO2026';
            });
        }

        // Handle client portal authentication via API
        const clientLoginForm = document.getElementById('client-login-form');
        const passcodeError = document.getElementById('passcode-error');

        if (clientLoginForm) {
            clientLoginForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const code = eventPasscodeInput.value.trim().toUpperCase();
                if (!code) return;

                if (passcodeError) passcodeError.classList.add('hidden');

                try {
                    const response = await fetch('<?= get_config('base_path') ?>api/auth/client_login', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ code })
                    });
                    const result = await response.json();

                    if (response.ok && result.success) {
                        // Redirect to the photo selection portal
                        window.location.href = '<?= Session::url("photo-selection") ?>';
                    } else {
                        if (passcodeError) {
                            passcodeError.innerText = result.message || 'Invalid passcode. Please try again.';
                            passcodeError.classList.remove('hidden');
                        }
                    }
                } catch (err) {
                    if (passcodeError) {
                        passcodeError.innerText = 'Network error occurred. Please try again.';
                        passcodeError.classList.remove('hidden');
                    }
                }
            });
        }

        // Client exit trigger
        function logoutClient() {
            fetch('<?= get_config('base_path') ?>api/auth/client_logout', { method: 'POST' })
                .then(() => {
                    window.location.href = '<?= Session::url("index") ?>';
                });
        }
    </script>
</body>
</html>
