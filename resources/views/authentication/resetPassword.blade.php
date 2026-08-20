<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - Fitcoin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <h1 class="text-3xl font-bold text-white text-center mb-6">Reset Password</h1>
        
        <div id="message" class="hidden mb-4"></div>

        <form id="reset-form" class="space-y-4">
            <input type="hidden" id="reset-token" value="{{ request()->query('token') }}">
            <input type="hidden" id="reset-email" value="{{ request()->query('email') }}">

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">New Password</label>
                <input type="password" id="password" name="password" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white">
            </div>

            <button type="submit" id="reset-btn" class="w-full py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                Reset Password
            </button>
        </form>

        <p class="mt-6 text-sm text-center text-gray-400">
            <a href="/login" class="text-indigo-400 hover:underline">Back to Login</a>
        </p>
    </div>

    <script>
        (function() {
            const form = document.getElementById('reset-form');
            const messageEl = document.getElementById('message');
            const resetBtn = document.getElementById('reset-btn');
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');

            function showMessage(text, type = 'success') {
                messageEl.textContent = text;
                messageEl.className = 'mb-4 p-3 rounded-lg text-sm ' + (type === 'success' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400');
                messageEl.classList.remove('hidden');
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const token = document.getElementById('reset-token').value;
                const email = document.getElementById('reset-email').value;
                const password = passwordInput.value;
                const password_confirmation = confirmInput.value;

                if (!token || !email) {
                    showMessage('Invalid reset session.', 'error');
                    return;
                }

                if (password.length < 8) {
                    showMessage('Password must be at least 8 characters.', 'error');
                    return;
                }

                if (password !== password_confirmation) {
                    showMessage('Passwords do not match.', 'error');
                    return;
                }

                try {
                    resetBtn.disabled = true;
                    resetBtn.textContent = 'Resetting...';

                    const response = await fetch('/reset-password', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ email, token, password, password_confirmation })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        showMessage(data.error || data.message || 'Password reset failed.', 'error');
                        resetBtn.disabled = false;
                        resetBtn.textContent = 'Reset Password';
                        return;
                    }

                    showMessage('✅ Password reset successfully! Redirecting...', 'success');

                    setTimeout(() => {
                        const redirectUrl = data.user.role === 'super-admin' ? '/admin/dashboard' : '/user/dashboard';
                        window.location.href = redirectUrl;
                    }, 1500);

                } catch (error) {
                    showMessage('Network error, please try again.', 'error');
                    resetBtn.disabled = false;
                    resetBtn.textContent = 'Reset Password';
                }
            });
        })();
    </script>
</body>
</html>