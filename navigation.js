(function() {
    'use strict';
    
    const navStyles = document.createElement('style');
    navStyles.textContent = `
        nav {
            position: relative;
        }

        .nav-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        nav li {
            margin: 0 20px;
            position: relative;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 16px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            display: block;
        }

        nav a:hover {
            color: #ff0000;
            border-bottom: 2px solid #ff0000;
            text-shadow: 0 0 8px rgba(255, 0, 0, 0.5);
        }

        .admin-link {
            background: linear-gradient(135deg, #8b0000, #cc0000);
            border-radius: 20px;
            margin-left: 10px;
        }

        .admin-link:hover {
            background: linear-gradient(135deg, #cc0000, #ff0000);
            transform: scale(1.05);
        }

        .rhythm-menu-toggle {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #8b0000, #cc0000);
            border: 2px solid #fff;
            border-radius: 50%;
            cursor: pointer;
            padding: 8px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1001;
            box-shadow: 0 4px 12px rgba(139, 0, 0, 0.4);
        }

        .rhythm-menu-toggle:hover {
            background: linear-gradient(135deg, #cc0000, #ff0000);
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(255, 0, 0, 0.3);
        }

        .rhythm-note {
            width: 20px;
            height: 3px;
            background: #fff;
            margin: 2px 0;
            border-radius: 2px;
            transition: all 0.3s ease;
            position: relative;
        }

        .rhythm-note::before {
            content: '';
            position: absolute;
            width: 6px;
            height: 6px;
            background: #fff;
            border-radius: 50%;
            top: -1.5px;
            right: -3px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .rhythm-note:nth-child(1)::before {
            opacity: 1;
        }

        .rhythm-note:nth-child(2) {
            width: 16px;
        }

        .rhythm-note:nth-child(3) {
            width: 12px;
        }

        .rhythm-menu-toggle.active {
            background: linear-gradient(135deg, #cc0000, #ff0000);
            transform: rotate(90deg);
        }

        .rhythm-menu-toggle.active .rhythm-note:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
            width: 16px;
        }

        .rhythm-menu-toggle.active .rhythm-note:nth-child(2) {
            opacity: 0;
            transform: scale(0);
        }

        .rhythm-menu-toggle.active .rhythm-note:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
            width: 16px;
        }

        .mobile-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, rgba(26, 26, 26, 0.98), rgba(42, 10, 10, 0.98));
            backdrop-filter: blur(10px);
            z-index: 1000;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .mobile-nav.active {
            display: flex;
            opacity: 1;
            transform: translateY(0);
        }

        .mobile-nav ul {
            flex-direction: column;
            align-items: center;
            gap: 25px;
        }

        .mobile-nav li {
            margin: 0;
            opacity: 0;
            transform: translateX(-30px);
            transition: all 0.4s ease;
        }

        .mobile-nav.active li {
            opacity: 1;
            transform: translateX(0);
        }

        .mobile-nav li:nth-child(1) { transition-delay: 0.1s; }
        .mobile-nav li:nth-child(2) { transition-delay: 0.15s; }
        .mobile-nav li:nth-child(3) { transition-delay: 0.2s; }
        .mobile-nav li:nth-child(4) { transition-delay: 0.25s; }
        .mobile-nav li:nth-child(5) { transition-delay: 0.3s; }
        .mobile-nav li:nth-child(6) { transition-delay: 0.35s; }

        .mobile-nav a {
            font-size: 1.4em;
            padding: 15px 30px;
            border: 2px solid transparent;
            border-radius: 25px;
            background: rgba(139, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-align: center;
            min-width: 200px;
        }

        .mobile-nav a:hover {
            background: rgba(139, 0, 0, 0.3);
            border-color: #ff0000;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 0, 0, 0.2);
        }

        .mobile-nav .admin-link {
            background: linear-gradient(135deg, #8b0000, #cc0000);
            margin-top: 10px;
        }

        @media screen and (max-width: 768px) {

            .nav-container {
                justify-content: flex-end;
                padding: 0 20px;
            }
            
            .desktop-nav {
                display: none;
            }
            
            .rhythm-menu-toggle {
                display: flex;
                margin-bottom: 20px;
            }
            
            header {
                position: relative;
                z-index: 1002;
                display: flex;
                align-items: center;
                justify-content: space-evenly;
            }
            
            .menu-open h1 {
                opacity: 0.7;
                transform: scale(0.95);
                transition: all 0.3s ease;
            }
        }

        @media screen and (max-width: 480px) {
            .mobile-nav a {
                font-size: 1.2em;
                padding: 12px 25px;
                min-width: 180px;
            }
            
            .rhythm-menu-toggle {
                width: 40px;
                height: 40px;
            }
        }

        @media screen and (max-width: 320px) {
            .mobile-nav a {
                font-size: 1.1em;
                padding: 10px 20px;
                min-width: 160px;
            }
        }
    `;
    
    function isIndexPage() {
        const currentPath = window.location.pathname;
        return currentPath.endsWith('/index.php') || currentPath.endsWith('/') || currentPath.endsWith('index.php');
    }
    
    function generateNavHTML() {
        const isIndex = isIndexPage();
        
        const basePath = isIndex ? '' : '../';
        
        return `
        <nav>
            <div class="nav-container">
                <ul class="desktop-nav">
                    <li><a href="${basePath}index.php"><span>Главная</span></a></li>
                    <li><a href="${basePath}albums/albums.php"><span>Альбомы</span></a></li>
                    <li><a href="${basePath}gallery/gallery.php"><span>Галерея</span></a></li>
                    <li><a href="${basePath}shop/shop.php"><span>Магазин</span></a></li>
                    <li><a href="${basePath}concerts/concerts.php"><span>Концерты</span></a></li>
                    <li><a href="${basePath}admin/admin.php" class="admin-link"><span>Админка</span></a></li>
                </ul>
                
                <button class="rhythm-menu-toggle" id="rhythmMenuToggle" aria-label="Открыть меню">
                    <span class="rhythm-note"></span>
                    <span class="rhythm-note"></span>
                    <span class="rhythm-note"></span>
                </button>
            </div>
            
            <div class="mobile-nav" id="mobileNav">
                <ul>
                    <li><a href="${basePath}index.php"><span>Главная</span></a></li>
                    <li><a href="${basePath}albums/albums.php"><span>Альбомы</span></a></li>
                    <li><a href="${basePath}gallery/gallery.php"><span>Галерея</span></a></li>
                    <li><a href="${basePath}shop/shop.php"><span>Магазин</span></a></li>
                    <li><a href="${basePath}concerts/concerts.php"><span>Концерты</span></a></li>
                    <li><a href="${basePath}admin/admin.php" class="admin-link"><span>Админка</span></a></li>
                </ul>
            </div>
        </nav>
        `;
    }
    
    function initNavigation() {
        document.head.appendChild(navStyles);
        
        const header = document.querySelector('header');
        if (header) {
            const existingNav = header.querySelector('nav');
            if (!existingNav) {
                const navHTML = generateNavHTML();
                header.insertAdjacentHTML('beforeend', navHTML);
                
                initMobileMenu();
            }
        }
    }
    
    function initMobileMenu() {
        const rhythmMenuToggle = document.getElementById('rhythmMenuToggle');
        const mobileNav = document.getElementById('mobileNav');
        const body = document.body;
        
        if (rhythmMenuToggle && mobileNav) {
            rhythmMenuToggle.addEventListener('click', function() {
                const isActive = this.classList.contains('active');
                
                this.classList.toggle('active');
                mobileNav.classList.toggle('active');
                body.classList.toggle('menu-open');
                
                if (!isActive) {
                    body.style.overflow = 'hidden';
                } else {
                    body.style.overflow = '';
                }
            });
            
            const mobileLinks = mobileNav.querySelectorAll('a');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function() {
                    rhythmMenuToggle.classList.remove('active');
                    mobileNav.classList.remove('active');
                    body.classList.remove('menu-open');
                    body.style.overflow = '';
                });
            });
            
            document.addEventListener('click', function(event) {
                if (mobileNav.classList.contains('active') && 
                    !rhythmMenuToggle.contains(event.target) && 
                    !mobileNav.contains(event.target)) {
                    rhythmMenuToggle.classList.remove('active');
                    mobileNav.classList.remove('active');
                    body.classList.remove('menu-open');
                    body.style.overflow = '';
                }
            });
            
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && mobileNav.classList.contains('active')) {
                    rhythmMenuToggle.classList.remove('active');
                    mobileNav.classList.remove('active');
                    body.classList.remove('menu-open');
                    body.style.overflow = '';
                }
            });
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNavigation);
    } else {
        initNavigation();
    }
    
    window.Navigation = {
        init: initNavigation,
        initMobileMenu: initMobileMenu
    };

})();
