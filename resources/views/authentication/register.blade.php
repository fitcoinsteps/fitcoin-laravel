<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Fitcoin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Register</h1>

        <!-- Message Display -->
        <div id="message" class="mb-4 hidden"></div>

        <form id="register-form" class="space-y-4">
            <!-- Name Fields -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" id="first_name" name="first_name" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <!-- Phone (Optional) -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone (optional)</label>
                <input type="text" id="phone" name="phone"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <!-- Register Button -->
            <button type="submit" id="register-btn"
                    class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition">
                Register
            </button>
        </form>

        <!-- Login Link -->
        <p class="mt-4 text-sm text-center text-gray-600">
            Already have an account? <a href="/login" class="text-indigo-600 hover:underline">Login</a>
        </p>
    </div>

    <script>
        const form = document.getElementById('register-form');
        const messageEl = document.getElementById('message');
        const registerBtn = document.getElementById('register-btn');

        function showMessage(text, type = 'success') {
            messageEl.textContent = text;
            messageEl.className = `mb-4 p-3 rounded-md text-sm ${
                type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
            }`;
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

                const response = await fetch('/api/register', {
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

                // Redirect to OTP verification page with email
                window.location.href = `/verify-otp?email=${encodeURIComponent(payload.email)}`;

            } catch (error) {
                showMessage('Network error, please try again.', 'error');
                registerBtn.disabled = false;
                registerBtn.textContent = 'Register';
            }
        });
    </script>
</body>
</html>