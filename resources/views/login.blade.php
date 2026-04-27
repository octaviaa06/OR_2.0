<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | OrtuConnect</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Background Animation -->
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>

    <div class="login-container">
        <div class="login-card">
            
            <div class="logo-wrapper">
                <img src="{{ asset('images/logo.png') }}" alt="Logo OrtuConnect" class="logo-anim">
            </div>

            <div class="card-header">
                <h2>Selamat Datang</h2>
                <p>Silakan masuk ke akun OrtuConnect Anda</p>
            </div>

            <form action="{{ route('login.process') }}" method="POST" class="login-form">
                @csrf

                @if(session('error'))
                    <div class="error-message">{{ session('error') }}</div>
                @endif

                <!-- Username -->
                <div class="input-group" style="--delay: 0.1s;">
                    <div class="icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <input type="text" name="username" placeholder="Email atau Username" required value="{{ old('username') }}">
                </div>

                <!-- Password with Toggle -->
                <div class="input-group" style="--delay: 0.2s;">
                    <div class="icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <input type="password" name="password" id="password" placeholder="Kata Sandi" required>
                    
                    <!-- Toggle Eye Button -->
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <svg id="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>

                <!-- Submit -->
                <div class="button-group" style="--delay: 0.3s;">
                    <button type="submit" id="submitBtn">
                        Masuk
                        <span class="arrow-icon">→</span>
                    </button>
                </div>
            </form>
            
            <div class="footer-text">
                <p>© 2026 OrtuConnect System</p>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                // Ganti icon jadi mata tertutup (slash)
                eyeIcon.innerHTML = `
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                `;
            } else {
                passwordInput.type = 'password';
                // Balik ke icon mata terbuka
                eyeIcon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                `;
            }
        }

        // Loading State Tombol
        document.querySelector('.login-form').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = 'Memproses...';
            btn.style.opacity = '0.7';
        });
    </script>
</body>
</html>