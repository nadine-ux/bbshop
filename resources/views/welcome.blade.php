<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{{ config('app.name', 'BB Shopping') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <style>
        :root {
            --bb-red: #E63946;
            --bb-red-dark: #C42C39;
            --bb-red-light: #F25C68;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: #f5f6fa;
            min-height: 100vh;
            color: #2c3e50;
            overflow-x: hidden;
        }

        /* Mobile App Container */
        .app-container {
            max-width: 480px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.1);
        }

        /* Status Bar (comme iOS/Android) */
        .status-bar {
            height: 44px;
            background: linear-gradient(135deg, var(--bb-red) 0%, var(--bb-red-light) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            color: white;
            font-size: 0.75rem;
        }

        .status-time {
            font-weight: 600;
        }

        .status-icons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        /* Header avec logo */
        .app-header {
            background: linear-gradient(135deg, var(--bb-red) 0%, var(--bb-red-light) 100%);
            padding: 2rem 1.5rem 3rem;
            position: relative;
            overflow: hidden;
        }

        .app-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .logo-mobile {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            display: inline-block;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .logo-mobile img {
            width: 100px;
            height: auto;
            display: block;
        }

        .welcome-text {
            color: white;
            position: relative;
            z-index: 1;
        }

        .welcome-text h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .welcome-text p {
            font-size: 1rem;
            opacity: 0.95;
            line-height: 1.5;
        }

        /* Main Content */
        .main-content {
            padding: 2rem 1.5rem;
            background: white;
            margin-top: -1.5rem;
            border-radius: 24px 24px 0 0;
            position: relative;
            z-index: 2;
        }

        /* Feature Cards */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .feature-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .feature-card:active {
            transform: scale(0.95);
            border-color: var(--bb-red);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--bb-red), var(--bb-red-light));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(230, 57, 70, 0.3);
        }

        .feature-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }

        .feature-desc {
            font-size: 0.75rem;
            color: #7f8c8d;
            line-height: 1.4;
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, rgba(230, 57, 70, 0.05), rgba(242, 92, 104, 0.05));
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .stats-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1rem;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            background: white;
            border-radius: 12px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--bb-red);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #7f8c8d;
        }

        /* CTA Buttons */
        .cta-section {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .btn-mobile {
            padding: 1rem;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-mobile:active {
            transform: scale(0.97);
        }

        .btn-primary-mobile {
            background: linear-gradient(135deg, var(--bb-red) 0%, var(--bb-red-light) 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(230, 57, 70, 0.3);
        }

        .btn-secondary-mobile {
            background: white;
            color: var(--bb-red);
            border: 2px solid var(--bb-red);
        }

        /* Testimonial */
        .testimonial {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            border-radius: 20px;
            padding: 1.5rem;
            color: white;
            text-align: center;
            margin-bottom: 2rem;
        }

        .testimonial-text {
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1rem;
            font-style: italic;
        }

        .testimonial-author {
            font-weight: 700;
            color: var(--bb-red-light);
        }

        /* Bottom Nav (comme app mobile) */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            max-width: 480px;
            width: 100%;
            background: white;
            border-top: 1px solid #e9ecef;
            padding: 1rem;
            display: flex;
            justify-content: space-around;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            color: #95a5a6;
            text-decoration: none;
            font-size: 0.7rem;
            transition: all 0.3s ease;
        }

        .nav-item.active {
            color: var(--bb-red);
        }

        .nav-icon {
            font-size: 1.5rem;
        }

        /* Padding bottom for bottom nav */
        .main-content {
            padding-bottom: 6rem;
        }

        /* Desktop Override */
        @media (min-width: 481px) {
            .app-container {
                margin-top: 2rem;
                margin-bottom: 2rem;
                border-radius: 30px;
                overflow: hidden;
            }

            .bottom-nav {
                border-radius: 0 0 30px 30px;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .feature-card {
            animation: fadeInUp 0.5s ease forwards;
        }

        .feature-card:nth-child(1) { animation-delay: 0.1s; }
        .feature-card:nth-child(2) { animation-delay: 0.2s; }
        .feature-card:nth-child(3) { animation-delay: 0.3s; }
        .feature-card:nth-child(4) { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <div class="app-container">
       
        <!-- Header -->
        <div class="app-header">
            <div class="logo-mobile">
                <img src="https://www.bbshoppingonline.com/wp-content/uploads/2025/09/10-remise-1-1.png" alt="BB Shopping">
            </div>
            <div class="welcome-text">
                <h1>Bienvenue sur<br>BB Shopping</h1>
                <p>Votre solution de gestion de stock intelligente</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            
            <!-- Features Grid -->
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📦</div>
                    <div class="feature-title">Stock</div>
                    <div class="feature-desc">Gestion en temps réel</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <div class="feature-title">Stats</div>
                    <div class="feature-desc">Rapports détaillés</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🚚</div>
                    <div class="feature-title">Fournisseurs</div>
                    <div class="feature-desc">Suivi complet</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔔</div>
                    <div class="feature-title">Alertes</div>
                    <div class="feature-desc">Notifications auto</div>
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-section">
                <h3 class="stats-title">Performances</h3>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">99%</div>
                        <div class="stat-label">Disponibilité</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Support</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Articles</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">∞</div>
                        <div class="stat-label">Évolutif</div>
                    </div>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="cta-section">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-mobile btn-primary-mobile">
                        <span>🚀</span>
                        Accéder au Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-mobile btn-primary-mobile">
                        <span>🔐</span>
                        Se connecter
                    </a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-mobile btn-secondary-mobile">
                        <span>✨</span>
                        Créer un compte
                    </a>
                    @endif
                @endauth
            </div>

            <!-- Testimonial -->
            <div class="testimonial">
                <p class="testimonial-text">
                    "Une application moderne qui a transformé la gestion de notre superette. Simple et efficace !"
                </p>
                <div class="testimonial-author">— Gérant BB Shopping</div>
            </div>
        </div>

        <!-- Bottom Navigation -->
        @if (Route::has('login'))
        <div class="bottom-nav">
            <a href="/" class="nav-item active">
                <div class="nav-icon">🏠</div>
                <div>Accueil</div>
            </a>
            @auth
                <a href="{{ url('/dashboard') }}" class="nav-item">
                    <div class="nav-icon">📱</div>
                    <div>Dashboard</div>
                </a>
            @else
                <a href="{{ route('login') }}" class="nav-item">
                    <div class="nav-icon">🔐</div>
                    <div>Connexion</div>
                </a>
            @endauth
            <a href="#" class="nav-item">
                <div class="nav-icon">ℹ️</div>
                <div>À propos</div>
            </a>
            <a href="#" class="nav-item">
                <div class="nav-icon">📞</div>
                <div>Contact</div>
            </a>
        </div>
        @endif
    </div>
</body>
</html>