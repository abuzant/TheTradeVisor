<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | TheTradeVisor</title>
    
    <!-- Prevent caching -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    
    @if(config('services.google_analytics.enabled'))
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.tracking_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google_analytics.tracking_id') }}', {
            'page_title': '404 - Page Not Found',
            'page_path': '{{ request()->path() }}',
            'page_location': '{{ request()->fullUrl() }}',
            'custom_map': {
                'dimension1': 'referrer',
                'dimension2': 'requested_url'
            },
            'referrer': document.referrer || 'direct',
            'requested_url': '{{ request()->fullUrl() }}'
        });
        
        // Track 404 as an event
        gtag('event', '404_error', {
            'event_category': 'Error',
            'event_label': '{{ request()->path() }}',
            'referrer': document.referrer || 'direct',
            'requested_url': '{{ request()->fullUrl() }}'
        });
    </script>
    @endif
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            background: #000;
            color: #fff;
            overflow: hidden;
            cursor: none;
        }
        
        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
            background: rgba(0, 0, 0, 0.8);
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo::before {
            content: '⚡';
            font-size: 28px;
        }
        
        /* Error Badge */
        .error-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #ff6b6b;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .error-badge::before {
            content: '●';
            font-size: 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        
        /* Main Content */
        .container {
            position: relative;
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .content {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 40px;
            max-width: 600px;
        }
        
        h1 {
            font-size: 48px;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .home-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            background: #fff;
            color: #000;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 2px solid #fff;
            transition: all 0.3s;
            margin-top: 20px;
        }
        
        .home-btn:hover {
            background: transparent;
            color: #fff;
        }
        
        .home-btn::after {
            content: '→';
            font-size: 18px;
        }
        
        /* Game Canvas */
        #gameCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        
        /* Custom Cursor */
        .cursor {
            position: fixed;
            width: 40px;
            height: 40px;
            pointer-events: none;
            z-index: 1000;
            transform: translate(-50%, -50%);
        }
        
        .cursor::before {
            content: '▲';
            font-size: 30px;
            color: #00ff00;
            display: block;
            text-align: center;
            filter: drop-shadow(0 0 10px #00ff00);
        }
        
        /* Instructions */
        .instructions {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            font-size: 12px;
            color: #888;
            z-index: 100;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .instructions span {
            display: block;
            margin-top: 5px;
            color: #00ff00;
        }
        
        /* Score */
        .score {
            position: fixed;
            top: 100px;
            right: 40px;
            font-size: 14px;
            color: #00ff00;
            z-index: 100;
            text-align: right;
        }
        
        .score-label {
            color: #888;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .score-value {
            font-size: 32px;
            font-weight: bold;
            margin-top: 5px;
        }
        
        /* Mobile Styles */
        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
            }
            
            .logo {
                font-size: 18px;
            }
            
            h1 {
                font-size: 32px;
            }
            
            .content {
                padding: 20px;
            }
            
            .home-btn {
                padding: 12px 24px;
                font-size: 12px;
            }
            
            .score {
                top: 80px;
                right: 20px;
            }
            
            .score-value {
                font-size: 24px;
            }
            
            .instructions {
                bottom: 20px;
                font-size: 10px;
            }
            
            .cursor {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <a href="/" class="logo">TheTradeVisor</a>
        <div class="error-badge">Error</div>
    </div>
    
    <!-- Score -->
    <div class="score">
        <div class="score-label">Hits</div>
        <div class="score-value" id="scoreValue">0</div>
    </div>
    
    <!-- Game Canvas -->
    <canvas id="gameCanvas"></canvas>
    
    <!-- Custom Cursor -->
    <div class="cursor" id="cursor"></div>
    
    <!-- Main Content -->
    <div class="container">
        <div class="content">
            <h1>These are not the pages you are looking for...</h1>
            <a href="/" class="home-btn">Back to Home</a>
        </div>
    </div>
    
    <!-- Instructions -->
    <div class="instructions">
        Move mouse to aim • Click to shoot
        <span>Destroy the invaders!</span>
    </div>
    
    <script>
        // Canvas setup
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        const cursor = document.getElementById('cursor');
        const scoreElement = document.getElementById('scoreValue');
        
        let score = 0;
        let mouseX = 0;
        let mouseY = 0;
        
        // Resize canvas
        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
        
        // Invader class
        class Invader {
            constructor(x, y, char) {
                this.x = x;
                this.y = y;
                this.char = char;
                this.size = 8;
                this.speedX = Math.random() * 0.5 + 0.2;
                this.speedY = Math.random() * 0.3;
                this.directionX = Math.random() > 0.5 ? 1 : -1;
                this.opacity = Math.random() * 0.5 + 0.5;
                this.color = this.getColor();
                this.alive = true;
                this.hitAnimation = 0;
            }
            
            getColor() {
                const colors = ['#00ff00', '#00ffff', '#ff00ff', '#ffff00', '#ff6b6b'];
                return colors[Math.floor(Math.random() * colors.length)];
            }
            
            update() {
                if (!this.alive) {
                    this.hitAnimation++;
                    return;
                }
                
                this.x += this.speedX * this.directionX;
                this.y += Math.sin(Date.now() * 0.001 + this.x * 0.01) * this.speedY;
                
                // Bounce off edges
                if (this.x < 0 || this.x > canvas.width) {
                    this.directionX *= -1;
                }
                
                // Wrap around vertically
                if (this.y < 0) this.y = canvas.height;
                if (this.y > canvas.height) this.y = 0;
            }
            
            draw() {
                if (!this.alive && this.hitAnimation > 30) return;
                
                ctx.save();
                
                if (!this.alive) {
                    // Explosion effect
                    ctx.globalAlpha = 1 - (this.hitAnimation / 30);
                    ctx.fillStyle = '#ff0000';
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size * (1 + this.hitAnimation / 10), 0, Math.PI * 2);
                    ctx.fill();
                } else {
                    ctx.globalAlpha = this.opacity;
                    ctx.fillStyle = this.color;
                    ctx.font = `${this.size}px monospace`;
                    ctx.fillText(this.char, this.x, this.y);
                }
                
                ctx.restore();
            }
            
            checkHit(bulletX, bulletY) {
                if (!this.alive) return false;
                const distance = Math.sqrt(
                    Math.pow(this.x - bulletX, 2) + 
                    Math.pow(this.y - bulletY, 2)
                );
                return distance < this.size * 2;
            }
        }
        
        // Bullet class
        class Bullet {
            constructor(x, y) {
                this.x = x;
                this.y = y;
                this.speed = 10;
                this.alive = true;
            }
            
            update() {
                this.y -= this.speed;
                if (this.y < 0) this.alive = false;
            }
            
            draw() {
                ctx.fillStyle = '#00ff00';
                ctx.shadowBlur = 10;
                ctx.shadowColor = '#00ff00';
                ctx.fillRect(this.x - 2, this.y, 4, 15);
                ctx.shadowBlur = 0;
            }
        }
        
        // Create invaders forming "404"
        const invaders = [];
        const chars = ['●', '◆', '■', '▲', '▼'];
        
        // Create random invaders across the screen
        function createInvaders() {
            const rows = Math.floor(canvas.height / 40);
            const cols = Math.floor(canvas.width / 40);
            
            for (let row = 0; row < rows; row++) {
                for (let col = 0; col < cols; col++) {
                    if (Math.random() > 0.7) {
                        const x = col * 40 + Math.random() * 20;
                        const y = row * 40 + Math.random() * 20;
                        const char = chars[Math.floor(Math.random() * chars.length)];
                        invaders.push(new Invader(x, y, char));
                    }
                }
            }
        }
        
        createInvaders();
        
        // Bullets array
        const bullets = [];
        
        // Mouse tracking
        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
            cursor.style.left = mouseX + 'px';
            cursor.style.top = mouseY + 'px';
        });
        
        // Shooting
        document.addEventListener('click', (e) => {
            bullets.push(new Bullet(mouseX, canvas.height - 50));
            
            // Track shooting event
            @if(config('services.google_analytics.enabled'))
            gtag('event', '404_game_shoot', {
                'event_category': '404 Game',
                'event_label': 'Shot Fired'
            });
            @endif
        });
        
        // Touch support for mobile
        let touchX = canvas.width / 2;
        let touchY = canvas.height / 2;
        
        canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            const touch = e.touches[0];
            touchX = touch.clientX;
            touchY = touch.clientY;
            bullets.push(new Bullet(touchX, canvas.height - 50));
        });
        
        canvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            const touch = e.touches[0];
            touchX = touch.clientX;
            touchY = touch.clientY;
        });
        
        // Game loop
        function gameLoop() {
            // Clear canvas
            ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Update and draw invaders
            invaders.forEach(invader => {
                invader.update();
                invader.draw();
            });
            
            // Update and draw bullets
            bullets.forEach((bullet, bulletIndex) => {
                bullet.update();
                bullet.draw();
                
                // Check collisions
                invaders.forEach(invader => {
                    if (bullet.alive && invader.checkHit(bullet.x, bullet.y)) {
                        invader.alive = false;
                        bullet.alive = false;
                        score++;
                        scoreElement.textContent = score;
                        
                        // Track hit
                        @if(config('services.google_analytics.enabled'))
                        if (score % 10 === 0) {
                            gtag('event', '404_game_milestone', {
                                'event_category': '404 Game',
                                'event_label': 'Score Milestone',
                                'value': score
                            });
                        }
                        @endif
                    }
                });
                
                // Remove dead bullets
                if (!bullet.alive) {
                    bullets.splice(bulletIndex, 1);
                }
            });
            
            // Remove dead invaders after animation
            for (let i = invaders.length - 1; i >= 0; i--) {
                if (!invaders[i].alive && invaders[i].hitAnimation > 30) {
                    invaders.splice(i, 1);
                }
            }
            
            // Spawn new invaders if too few
            if (invaders.length < 50) {
                const x = Math.random() * canvas.width;
                const y = Math.random() * canvas.height;
                const char = chars[Math.floor(Math.random() * chars.length)];
                invaders.push(new Invader(x, y, char));
            }
            
            requestAnimationFrame(gameLoop);
        }
        
        gameLoop();
        
        // Track time spent on 404 page
        @if(config('services.google_analytics.enabled'))
        let startTime = Date.now();
        window.addEventListener('beforeunload', () => {
            const timeSpent = Math.floor((Date.now() - startTime) / 1000);
            gtag('event', '404_time_spent', {
                'event_category': '404 Game',
                'event_label': 'Time on Page',
                'value': timeSpent
            });
        });
        @endif
    </script>
</body>
</html>
