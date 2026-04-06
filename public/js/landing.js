// Landing Page JavaScript - Clean & Smooth

document.addEventListener('DOMContentLoaded', function() {

    // Navbar scroll effect
    const navbar = document.querySelector('.landing-navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Scroll reveal animation
    const revealElements = document.querySelectorAll('.reveal');

    const revealOnScroll = () => {
        const windowHeight = window.innerHeight;
        revealElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            if (elementTop < windowHeight - 100) {
                element.classList.add('active');
            }
        });
    };

    window.addEventListener('scroll', revealOnScroll);
    revealOnScroll();

    // Feature cards staggered animation
    const featureCards = document.querySelectorAll('.feature-card');
    featureCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
    });

    const animateFeatures = () => {
        featureCards.forEach((card, index) => {
            const rect = card.getBoundingClientRect();
            if (rect.top < window.innerHeight - 100) {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            }
        });
    };

    window.addEventListener('scroll', animateFeatures);
    animateFeatures();

    // Book card hover z-index + 3D tilt
    document.querySelectorAll('.book-card').forEach(card => {
        card.addEventListener('mouseenter', function() { this.style.zIndex = '10'; });
        card.addEventListener('mouseleave', function() { 
            this.style.zIndex = '1';
            this.style.transform = '';
        });
        
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateY = ((x - centerX) / centerX) * -12;
            const rotateX = ((y - centerY) / centerY) * 8;
            
            this.style.transform = 'rotateY(' + rotateY + 'deg) rotateX(' + rotateX + 'deg) translateY(-12px) scale(1.02)';
        });
    });

    // 3D Book Stack - Drag to Rotate
    var bookStack = document.getElementById('book3dStack');
    var bookScene = document.getElementById('book3dScene');
    if (bookStack && bookScene) {
        var isDragging = false;
        var previousX = 0;
        var previousY = 0;
        var rotateX = -20;
        var rotateY = -30;
        var autoRotateId = null;

        function updateTransform() {
            bookStack.style.transform = 'rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) scale(1.35)';
        }

        function onPointerDown(e) {
            isDragging = true;
            previousX = e.clientX || (e.touches && e.touches[0].clientX) || 0;
            previousY = e.clientY || (e.touches && e.touches[0].clientY) || 0;
            bookStack.classList.add('dragging');
        }

        function onPointerMove(e) {
            if (!isDragging) return;
            var clientX = e.clientX || (e.touches && e.touches[0].clientX) || 0;
            var clientY = e.clientY || (e.touches && e.touches[0].clientY) || 0;
            var deltaX = clientX - previousX;
            var deltaY = clientY - previousY;

            rotateY += deltaX * 0.5;
            rotateX -= deltaY * 0.3;
            rotateX = Math.max(-60, Math.min(20, rotateX));

            updateTransform();
            previousX = clientX;
            previousY = clientY;
            e.preventDefault();
        }

        function onPointerUp() {
            isDragging = false;
            bookStack.classList.remove('dragging');
        }

        // Mouse events
        bookScene.addEventListener('mousedown', onPointerDown);
        window.addEventListener('mousemove', onPointerMove);
        window.addEventListener('mouseup', onPointerUp);

        // Touch events
        bookScene.addEventListener('touchstart', function(e) { onPointerDown(e.touches[0]); }, { passive: false });
        window.addEventListener('touchmove', function(e) {
            if (isDragging) {
                onPointerMove(e);
            }
        }, { passive: false });
        window.addEventListener('touchend', onPointerUp);

        // Floating icons parallax on mouse move over hero
        var heroImage = document.querySelector('.hero-image');
        var floatingIcons = heroImage ? heroImage.querySelectorAll('.floating-icon') : [];
        if (heroImage) {
            heroImage.addEventListener('mousemove', function(e) {
                if (isDragging) return;
                var rect = this.getBoundingClientRect();
                var x = (e.clientX - rect.left) / rect.width - 0.5;
                var y = (e.clientY - rect.top) / rect.height - 0.5;
                floatingIcons.forEach(function(icon, i) {
                    var depth = (i + 1) * 6;
                    icon.style.transform = 'translate(' + (x * depth) + 'px, ' + (y * depth) + 'px)';
                });
            });
            heroImage.addEventListener('mouseleave', function() {
                floatingIcons.forEach(function(icon) {
                    icon.style.transform = '';
                });
            });
        }
    }

    // Feature card 3D tilt on hover
    document.querySelectorAll('.feature-card').forEach(function(card) {
        card.addEventListener('mousemove', function(e) {
            var rect = this.getBoundingClientRect();
            var x = e.clientX - rect.left;
            var y = e.clientY - rect.top;
            var centerX = rect.width / 2;
            var centerY = rect.height / 2;
            var rotateY = ((x - centerX) / centerX) * 8;
            var rotateX = ((y - centerY) / centerY) * -6;
            this.style.transform = 'rotateY(' + rotateY + 'deg) rotateX(' + rotateX + 'deg) translateY(-10px) scale(1.02)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });

    // Mobile menu toggle
    const createMobileMenu = () => {
        if (window.innerWidth <= 768 && !document.querySelector('.mobile-menu-toggle')) {
            const navContent = document.querySelector('.navbar-content');
            const menu = document.querySelector('.navbar-menu');
            if (!navContent || !menu) return;

            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'mobile-menu-toggle';
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            toggleBtn.style.cssText = 'background:none;border:none;font-size:24px;color:#1a1a2e;cursor:pointer;';
            toggleBtn.addEventListener('click', () => menu.classList.toggle('active'));
            navContent.appendChild(toggleBtn);
        }
    };

    createMobileMenu();
    window.addEventListener('resize', createMobileMenu);

    // Add mobile menu CSS
    const style = document.createElement('style');
    style.textContent = [
        '@media (max-width: 768px) {',
        '  .navbar-menu { display:none;position:absolute;top:100%;left:0;right:0;background:white;flex-direction:column;padding:20px;box-shadow:0 10px 30px rgba(0,0,0,0.1); }',
        '  .navbar-menu.active { display:flex; }',
        '}'
    ].join('\n');
    document.head.appendChild(style);

    // Lazy loading for images
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    images.forEach(img => imageObserver.observe(img));

    console.log('Landing page loaded.');
});
