<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - Fitcoin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #0a0512;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .glass-card {
            background: rgba(23, 10, 35, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }
        .neon-input {
            width: 100%;
            padding: 16px 20px 16px 48px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(236,72,153,0.15);
            border-radius: 12px;
            color: white;
            font-size: 15px;
            outline: none;
        }
        .neon-input:focus {
            border-color: #ec4899;
            box-shadow: 0 0 0 3px rgba(236,72,153,0.15);
        }
        .auth-btn {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            background: linear-gradient(135deg, #d946ef 0%, #ec4899 100%);
            color: white;
            font-weight: 700;
            border: none;
            cursor: pointer;
        }
        .auth-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        .alert-error {
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.3);
            color: #f87171;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            display: none;
        }
        .alert-error.show { display: block; }
    </style>
</head>
<body>
    <div class="glass-card">
        <h1 class="text-2xl font-bold text-center">Forgot Password</h1>
        <p class="text-sm text-gray-400 text-center mt-2 mb-6">
            Enter your email address and we'll send you a verification code to reset your password.
        </p>

        <div id="message" class="alert-error"></div>

        <form id="forgot-form">
            <div class="relative mb-4">
                <input type="email" id="email" name="email" placeholder="Email Address" required class="neon-input">
                <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
            </div>

            <button type="submit" id="submit-btn" class="auth-btn">
                Send Reset Code
            </button>
        </form>

        <p class="mt-6 text-sm text-center text-gray-400">
            Remember your password? <a href="/login" class="text-pink-500 hover:underline">Login</a>
        </p>
    </div>

    <script>
        (function() {
            const form = document.getElementById('forgot-form');
            const messageEl = document.getElementById('message');
            const submitBtn = document.getElementById('submit-btn');
            const emailInput = document.getElementById('email');

            function showMessage(text) {
                messageEl.textContent = text;
                messageEl.classList.add('show');
            }

            function hideMessage() {
                messageEl.classList.remove('show');
                messageEl.textContent = '';
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                hideMessage();

                const email = emailInput.value.trim();
                if (!email) {
                    showMessage('Please enter your email address.');
                    return;
                }

                try {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Sending...';

                    const response = await fetch('/forgot-password', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ email })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        showMessage(data.message || 'Failed to send reset code.');
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Send Reset Code';
                        return;
                    }

                    const token = data.token || '';
                    window.location.href = '/verifyOtp?email=' + encodeURIComponent(email) + '&type=password_reset&token=' + encodeURIComponent(token);

                } catch (error) {
                    showMessage('Network error, please try again.');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Send Reset Code';
                }
            });
        })();
    </script>
</body>
</html>