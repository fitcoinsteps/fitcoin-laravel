<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fitcoin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        neonpink: '#ec4899',
                        neonpurple: '#8b5cf6',
                        neongold: '#d4af37',
                        darkbg: '#0a0512',
                        cardbg: '#1c0929'
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="min-h-screen flex items-center justify-center bg-[#0a0512] font-['Inter']">
    <div class="fixed inset-0 bg-[radial-gradient(circle_800px_at_50%_50%,_#4c1d95_0%,_transparent_80%)] opacity-60 z-0 pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-md p-8 rounded-3xl bg-cardbg/50 backdrop-blur-[14px] border border-white/10 shadow-[0_20px_80px_rgba(236,72,153,0.3)]">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black text-white">Welcome Back</h1>
            <p class="text-gray-400 mt-2">Login to your Fitcoin account</p>
        </div>

        <div id="error-message" class="hidden mb-4 p-4 bg-red-500/20 border border-red-500/50 rounded-xl text-red-400 text-sm"></div>
        <div id="success-message" class="hidden mb-4 p-4 bg-green-500/20 border border-green-500/50 rounded-xl text-green-400 text-sm"></div>

        <form id="login-form" class="space-y-5">
            <div>
                <label class="block text-gray-400 text-sm font-semibold mb-2">Email</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                    <input type="email" id="email" name="email" required
                        class="w-full pl-12 pr-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-neonpink focus:ring-1 focus:ring-neonpink transition"
                        placeholder="Enter your email">
                </div>
            </div>

            <div>
                <label class="block text-gray-400 text-sm font-semibold mb-2">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                    <input type="password" id="password" name="password" required
                        class="w-full pl-12 pr-12 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-neonpink focus:ring-1 focus:ring-neonpink transition"
                        placeholder="Enter your password">
                    <button type="button" id="toggle-password" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full py-4 bg-gradient-to-r from-neonpink to-neonpurple rounded-xl text-white font-bold text-lg shadow-[0_0_30px_rgba(236,72,153,0.5)] hover:shadow-[0_0_60px_rgba(236,72,153,0.8)] hover:scale-[1.02] transition-all duration-300">
                <i class="fas fa-sign-in-alt mr-2"></i> Login
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="/" class="text-gray-400 hover:text-neonpink transition text-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to Home
            </a>
        </div>
    </div>

    <script>
        const form = document.getElementById('login-form');
        const errorDiv = document.getElementById('error-message');
        const successDiv = document.getElementById('success-message');
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (response.ok) {
                    successDiv.classList.remove('hidden');
                    successDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Login successful! Welcome back, <strong>' + data.user.name + '</strong>.';

                    localStorage.setItem('token', data.token);
                    localStorage.setItem('user', JSON.stringify(data.user));

                    setTimeout(() => {
                        window.location.href = '/';
                    }, 1500);
                } else {
                    errorDiv.classList.remove('hidden');
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> ' + (data.message || 'Login failed. Please check your credentials.');
                }
            } catch (error) {
                errorDiv.classList.remove('hidden');
                errorDiv.textContent = 'Network error. Please try again.';
            }
        });
    </script>
</body>
</html>