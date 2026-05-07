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

    <!-- 1. Background Grid Pattern -->
    <div class="bg-grid"></div>

    <!-- 2. Background Blobs -->
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>

    <!-- 3. Star Dust -->
    <div class="star-dust"></div>

    <!-- 4. Floating Icons (POSISI DISEBAR MERATA) -->
    <div class="floating-icons-wrapper">
        
        <!-- Layer 1: Belakang (Kecil & Blur) -->
        <div class="parallax-layer layer-back">
            <!-- Disebar ke tengah-tengah, tidak cuma di pojok -->
            <img src="{{ asset('images/icon/abc-block.png') }}" class="float-icon" style="--x: 12%; --y: 15%; --depth: 0.2; --delay: 0s; --duration: 10s;">
            <img src="{{ asset('images/icon/car.png') }}" class="float-icon" style="--x: 45%; --y: 25%; --depth: 0.3; --delay: 1s; --duration: 12s;">
            <img src="{{ asset('images/icon/toys.png') }}" class="float-icon" style="--x: 78%; --y: 18%; --depth: 0.2; --delay: 2s; --duration: 11s;">
            <img src="{{ asset('images/icon/chick.png') }}" class="float-icon" style="--x: 25%; --y: 85%; --depth: 0.3; --delay: 0.5s; --duration: 13s;">
            <img src="{{ asset('images/icon/strawberry.png') }}" class="float-icon" style="--x: 65%; --y: 80%; --depth: 0.2; --delay: 1.5s; --duration: 14s;">
            <img src="{{ asset('images/icon/rubber-duck.png') }}" class="float-icon" style="--x: 88%; --y: 50%; --depth: 0.3; --delay: 2.5s; --duration: 15s;">
            <img src="{{ asset('images/icon/animal.png') }}" class="float-icon" style="--x: 5%; --y: 60%; --depth: 0.2; --delay: 3s; --duration: 16s;">
        </div>

        <!-- Layer 2: Tengah (Ukuran Normal) -->
        <div class="parallax-layer layer-mid">
            <!-- Disebar lebih rapat di sekitar form -->
            <img src="{{ asset('images/icon/abc-block.png') }}" class="float-icon" style="--x: 8%; --y: 35%; --depth: 0.5; --delay: 0s; --duration: 6s;">
            <img src="{{ asset('images/icon/animal.png') }}" class="float-icon" style="--x: 92%; --y: 35%; --depth: 0.6; --delay: 1s; --duration: 7s;">
            <img src="{{ asset('images/icon/car.png') }}" class="float-icon" style="--x: 20%; --y: 55%; --depth: 0.5; --delay: 2s; --duration: 8s;">
            <img src="{{ asset('images/icon/chick.png') }}" class="float-icon" style="--x: 80%; --y: 65%; --depth: 0.6; --delay: 0.5s; --duration: 6s;">
            <img src="{{ asset('images/icon/lolipop.png') }}" class="float-icon" style="--x: 35%; --y: 12%; --depth: 0.5; --delay: 1.5s; --duration: 7s;">
            <img src="{{ asset('images/icon/rubber-duck.png') }}" class="float-icon" style="--x: 60%; --y: 90%; --depth: 0.6; --delay: 2.5s; --duration: 8s;">
            <img src="{{ asset('images/icon/strawberry.png') }}" class="float-icon" style="--x: 45%; --y: 45%; --depth: 0.5; --delay: 0.8s; --duration: 6s;">
            <img src="{{ asset('images/icon/toys.png') }}" class="float-icon" style="--x: 15%; --y: 75%; --depth: 0.6; --delay: 1.2s; --duration: 7s;">
        </div>

        <!-- Layer 3: Depan (Besar & Blur Berat) -->
        <div class="parallax-layer layer-front">
            <!-- Dibuat agak menjauh dari tengah form agar tidak menghalangi -->
            <img src="{{ asset('images/icon/animal.png') }}" class="float-icon" style="--x: 85%; --y: 75%; --depth: 1.2; --delay: 0s; --duration: 15s;">
            <img src="{{ asset('images/icon/car.png') }}" class="float-icon" style="--x: 12%; --y: 20%; --depth: 1.5; --delay: 1s; --duration: 18s;">
            <img src="{{ asset('images/icon/lollipop.png') }}" class="float-icon" style="--x: 90%; --y: 15%; --depth: 1.3; --delay: 2s; --duration: 16s;">
            <img src="{{ asset('images/icon/toys.png') }}" class="float-icon" style="--x: 10%; --y: 85%; --depth: 1.4; --delay: 0.5s; --duration: 17s;">
        </div>
    </div>

    <!-- 5. Login Container & Mascot -->
    <div class="login-container">
        
        <!-- Mascot Mengintip -->
        <div class="mascot-peek">
            <svg viewBox="0 0 100 100" class="mascot-svg">
                <circle cx="50" cy="15" r="8" fill="#d946ef"/>
                <rect x="48" y="15" width="4" height="10" fill="#a855f7"/>
                <rect x="20" y="25" width="60" height="50" rx="20" fill="#ffffff" stroke="#a855f7" stroke-width="3"/>
                <circle cx="35" cy="45" r="6" fill="#09090b"/>
                <circle cx="65" cy="45" r="6" fill="#09090b"/>
                <circle cx="28" cy="55" r="5" fill="#fca5a5" opacity="0.6"/>
                <circle cx="72" cy="55" r="5" fill="#fca5a5" opacity="0.6"/>
                <path d="M40 60 Q50 70 60 60" stroke="#09090b" stroke-width="3" fill="none" stroke-linecap="round"/>
                <path d="M10 40 Q-10 20 20 10" stroke="#ffffff" stroke-width="15" fill="none" stroke-linecap="round"/>
                <path d="M90 40 Q110 20 80 10" stroke="#ffffff" stroke-width="15" fill="none" stroke-linecap="round"/>
            </svg>
        </div>

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
                    
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <svg id="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>

                <!-- Submit Button -->
                <div class="button-group" style="--delay: 0.3s;">
                    <button type="submit" id="submitBtn">
                        <span class="btn-text">Masuk</span>
                        <span class="arrow-icon">→</span>
                        
                        <span class="loading-content">
                            <span class="loading-text">Memproses...</span>
                            <span class="car-group">
                                <img src="{{ asset('images/icon/car.png') }}" class="mini-car">
                                <img src="{{ asset('images/icon/car.png') }}" class="mini-car">
                                <img src="{{ asset('images/icon/car.png') }}" class="mini-car">
                            </span>
                        </span>
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
                eyeIcon.innerHTML = `
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                `;
            }
        }

        // Loading State
        document.querySelector('.login-form').addEventListener('submit', function() {
            document.getElementById('submitBtn').classList.add('loading');
        });

        // Parallax Effect
        document.addEventListener('DOMContentLoaded', () => {
            if (window.innerWidth <= 768) return; 

            const icons = document.querySelectorAll('.float-icon');
            let mouseX = 0, mouseY = 0;
            let currentX = 0, currentY = 0;

            document.addEventListener('mousemove', (e) => {
                mouseX = (e.clientX - window.innerWidth / 2) / 40;
                mouseY = (e.clientY - window.innerHeight / 2) / 40;
            });

            function animateParallax() {
                currentX += (mouseX - currentX) * 0.08;
                currentY += (mouseY - currentY) * 0.08;

                icons.forEach(icon => {
                    const depth = parseFloat(icon.style.getPropertyValue('--depth')) || 1;
                    const offsetX = currentX * depth;
                    const offsetY = currentY * depth;
                    
                    icon.style.setProperty('--px', `${offsetX}px`);
                    icon.style.setProperty('--py', `${offsetY}px`);
                });

                requestAnimationFrame(animateParallax);
            }
            animateParallax();
        });
    </script>
</body>
</html>