<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - Fitcoin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-2 text-center">Reset Password</h1>
        <p class="text-gray-500 text-sm text-center mb-6">
            Enter your new password below.
        </p>

        <div id="message" class="mb-4 hidden"></div>

        <form id="reset-form" class="space-y-4">
            <input type="hidden" id="reset-token" value="{{ request()->query('token') }}">
            <input type="hidden" id="reset-email" value="{{ request()->query('email') }}">

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" id="password" name="password" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition">
                Reset Password
            </button>
        </form>

        <p class="mt-4 text-sm text-center text-gray-500">
            <a href="/login" class="text-indigo-600 hover:underline">Back to Login</a>
        </p>
    </div>

    <script>
        const form = document.getElementById('reset-form');
        const messageEl = document.getElementById('message');

        function showMessage(text, type = 'success') {
            messageEl.textContent = text;
            messageEl.className = `mb-4 p-3 rounded-md text-sm ${
                type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
            }`;
            messageEl.classList.remove('hidden');
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const token = document.getElementById('reset-token').value;
            const email = document.getElementById('reset-email').value;
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;

            if (!token || !email) {
                showMessage('Invalid reset session. Please request a new reset.', 'error');
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
                const response = await fetch('/api/reset-password', {
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
                    return;
                }

                if (data.access_token) {
                    localStorage.setItem('access_token', data.access_token);
                    localStorage.setItem('refresh_token', data.refresh_token);
                }

                showMessage('Password reset successfully! Redirecting...', 'success');
                document.getElementById('password').disabled = true;
                document.getElementById('password_confirmation').disabled = true;

                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 2000);

            } catch (error) {
                showMessage('Network error. Please try again.', 'error');
            }
        });
    </script>
</body>
</html>