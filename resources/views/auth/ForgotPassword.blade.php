<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - Fitcoin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .card {
            background: #ffffff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 28rem;
        }

        .card h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .card .subtitle {
            color: #6b7280;
            font-size: 0.875rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        #message {
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            display: none;
        }
        #message.success {
            display: block;
            background-color: #d1fae5;
            color: #065f46;
        }
        #message.error {
            display: block;
            background-color: #fee2e2;
            color: #991b1b;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        .form-group input {
            display: block;
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            font-size: 1rem;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.4);
        }

        .btn-submit {
            display: block;
            width: 100%;
            background-color: #4f46e5;
            color: white;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.15s;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background-color: #4338ca;
        }

        .footer-link {
            margin-top: 1rem;
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .footer-link a {
            color: #4f46e5;
            text-decoration: none;
        }

        .footer-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Forgot Password</h1>
        <p class="subtitle">Enter your email address and we'll send you a verification code.</p>

        <div id="message"></div>

        <form id="forgot-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <button type="submit" class="btn-submit">Send Reset Code</button>
        </form>

        <p class="footer-link">
            Remember your password? <a href="/login">Login</a>
        </p>
    </div>

    <script>
        const form = document.getElementById('forgot-form');
        const messageEl = document.getElementById('message');

        function showMessage(text, type = 'success') {
            messageEl.textContent = text;
            messageEl.className = type;
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value.trim();

            if (!email || !email.includes('@')) {
                showMessage('Please enter a valid email address.', 'error');
                return;
            }

            try {
                const response = await fetch('/api/forgot-password', {
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
                    showMessage(data.message || 'Failed to send reset code.', 'error');
                    return;
                }

                // ✅ Capture token and pass it to verify-otp page
                const token = data.token || '';
                window.location.href = `/verify-otp?email=${encodeURIComponent(email)}&type=password_reset&token=${encodeURIComponent(token)}`;

            } catch (error) {
                showMessage('Network error. Please try again.', 'error');
            }
        });
    </script>
</body>
</html>