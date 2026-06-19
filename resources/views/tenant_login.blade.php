<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ $client->name }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        :root {
            --bg-color: #070913;
            --card-bg: rgba(16, 20, 38, 0.65);
            --primary: #06b6d4;
            --primary-glow: rgba(6, 182, 212, 0.15);
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --border: rgba(255, 255, 255, 0.08);
            --card-hover-border: rgba(6, 182, 212, 0.35);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(6, 182, 212, 0.07) 0%, transparent 45%),
                radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.04) 0%, transparent 45%);
        }

        .login-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border);
            border-radius: 24px;
            width: 100%;
            max-width: 440px;
            padding: 3rem 2.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
        }

        .login-card:hover {
            border-color: var(--card-hover-border);
            box-shadow: 0 20px 50px rgba(6, 182, 212, 0.08);
        }

        .header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .tenant-icon {
            background: linear-gradient(135deg, var(--primary), #10b981);
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.4);
            margin-bottom: 1rem;
        }

        .title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.25rem;
        }

        .subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            color: #d1d5db;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.8rem 1rem 0.8rem 2.8rem;
            color: #fff;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
            background: rgba(0, 0, 0, 0.35);
        }

        .btn {
            background: linear-gradient(135deg, var(--primary), #0891b2);
            color: white;
            border: none;
            padding: 0.85rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-top: 0.5rem;
            font-size: 0.95rem;
            box-shadow: 0 4px 14px rgba(6, 182, 212, 0.3);
        }

        .btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.1);
            box-shadow: 0 6px 20px rgba(6, 182, 212, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 0.8rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            display: none;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }

        .footer-note {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.8rem;
            color: var(--text-muted);
            border-top: 1px solid var(--border);
            padding-top: 1.25rem;
        }

        .footer-note a {
            color: var(--primary);
            text-decoration: none;
        }

        .footer-note a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="header">
            <div class="tenant-icon">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h1 class="title">{{ $client->name }}</h1>
            <p class="subtitle">Enter your tenant account credentials to log in.</p>
        </div>

        <div class="alert alert-error" id="errorAlert"></div>

        <form id="loginForm">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope input-icon"></i>
                    <input class="form-input" type="email" id="email" name="email" required placeholder="name@domain.com" autocomplete="email">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-key input-icon"></i>
                    <input class="form-input" type="password" id="password" name="password" required placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn" id="submitBtn">
                <span>Sign In</span> <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

        <div class="footer-note">
            Isolated Client Platform &bull; <a href="http://localhost:{{ request()->getPort() ?: '8000' }}">Main Portal</a>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                $('#errorAlert').hide();
                
                const $btn = $('#submitBtn');
                $btn.prop('disabled', true).find('span').text('Signing in...');

                const formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('tenant.login.post') }}",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.redirect_url;
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).find('span').text('Sign In');
                        let errorMsg = 'Invalid email or password.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        $('#errorAlert').text(errorMsg).fadeIn();
                    }
                });
            });
        });
    </script>
</body>
</html>
