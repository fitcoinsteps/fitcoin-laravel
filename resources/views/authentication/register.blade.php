<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Fitcoin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Create Account</h1>
            <p class="text-gray-400">Join Fitcoin today</p>
        </div>

        <div id="message" class="hidden mb-4"></div>

        <form id="register-form" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-300 mb-1">First Name</label>
                    <input type="text" id="first_name" name="first_name" required
                           class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-300 mb-1">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required
                           class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition">
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                <input type="email" id="email" name="email" required
                       class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition">
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-300 mb-1">Phone (optional)</label>
                <input type="text" id="phone" name="phone"
                       class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition">
            </div>

            <button type="submit" id="register-btn"
                    class="w-full py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-medium hover:from-indigo-700 hover:to-purple-700 transition">
                Register
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-400">
            Already have an account? <a href="/login" class="text-indigo-400 hover:underline">Login</a>
        </div>
    </div>

    <script>
        const form = document.getElementById('register-form');
        const messageEl = document.getElementById('message');
        const registerBtn = document.getElementById('register-btn');

        function showMessage(text, type = 'success') {
            messageEl.textContent = text;
            messageEl.className = 'mb-4 p-3 rounded-lg text-sm ' + (type === 'success' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400');
            messageEl.classList.remove('hidden');
        }

        function hideMessage() {
            messageEl.classList.add('hidden');
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideMessage();

            const payload = {
                first_name: document.getElementById('first_name').value.trim(),
                last_name: document.getElementById('last_name').value.trim(),
                email: document.getElementById('email').value.trim(),
                phone: document.getElementById('phone').value.trim(),
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value
            };

            if (!payload.first_name || !payload.last_name || !payload.email || !payload.password) {
                showMessage('Please fill in all required fields.', 'error');
                return;
            }

            if (payload.password !== payload.password_confirmation) {
                showMessage('Passwords do not match.', 'error');
                return;
            }

            if (payload.password.length < 6) {
                showMessage('Password must be at least 6 characters.', 'error');
                return;
            }

            try {
                registerBtn.disabled = true;
                registerBtn.textContent = 'Registering...';

                const response = await fetch('/web-register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok) {
                    const errorMsg = data.message || 'Registration failed';
                    showMessage(errorMsg, 'error');
                    registerBtn.disabled = false;
                    registerBtn.textContent = 'Register';
                    return;
                }

                window.location.href = '/verifyOtp?email=' + encodeURIComponent(payload.email);

            } catch (error) {
                showMessage('Network error, please try again.', 'error');
                registerBtn.disabled = false;
                registerBtn.textContent = 'Register';
            }
        });
    </script>
</body>
</html>