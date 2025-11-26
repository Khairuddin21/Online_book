// Landing Page JavaScript - Interactive & Smooth Animations

document.addEventListener('DOMContentLoaded', function() {
    
    // ========== ADVANCED HERO EFFECTS ==========
    
    // Generate floating particles
    const generateParticles = () => {
        const heroSection = document.querySelector('.hero-section');
        if (!heroSection) return;
        
        // Create particles container
        if (!document.querySelector('.hero-particles')) {
            const particlesContainer = document.createElement('div');
            particlesContainer.className = 'hero-particles';
            
            for (let i = 0; i < 10; i++) {
                const particle = document.createElement('div');
                particle.className = 'light-particle';
                particlesContainer.appendChild(particle);
            }
            
            heroSection.appendChild(particlesContainer);
        }
    };
    
    // Mouse tracking parallax effect
    const heroParallax = () => {
        const heroSection = document.querySelector('.hero-section');
        if (!heroSection) return;
        
        heroSection.addEventListener('mousemove', (e) => {
            const { clientX, clientY } = e;
            const { innerWidth, innerHeight } = window;
            
            const xPercent = (clientX / innerWidth - 0.5) * 2;
            const yPercent = (clientY / innerHeight - 0.5) * 2;
            
            // Move orbit particles
            const orbitRings = document.querySelectorAll('.orbit-ring');
            orbitRings.forEach((ring, index) => {
                const speed = (index + 1) * 5;
                ring.style.transform = `translate(-50%, -50%) rotate(${xPercent * speed}deg)`;
            });
            
            // Move hero image
            const heroImage = document.querySelector('.hero-image img');
            if (heroImage) {
                heroImage.style.transform = `translate(${xPercent * 15}px, ${yPercent * 15}px)`;
            }
            
            // Move particles
            const particles = document.querySelectorAll('.light-particle');
            particles.forEach((particle, index) => {
                const speed = (index % 3 + 1) * 3;
                particle.style.transform = `translate(${xPercent * speed}px, ${yPercent * speed}px)`;
            });
        });
        
        // Reset on mouse leave
        heroSection.addEventListener('mouseleave', () => {
            const heroImage = document.querySelector('.hero-image img');
            if (heroImage) {
                heroImage.style.transform = 'translate(0, 0)';
            }
        });
    };
    
    // Magnetic button effect (exclude CTA buttons)
    const magneticButtons = () => {
        const buttons = document.querySelectorAll('.btn:not(.btn-cta-primary):not(.btn-cta-outline)');
        
        buttons.forEach(button => {
            button.addEventListener('mousemove', (e) => {
                const rect = button.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                
                button.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px)`;
            });
            
            button.addEventListener('mouseleave', () => {
                button.style.transform = 'translate(0, 0)';
            });
        });
    };
    
    // 3D tilt effect for cards
    const tiltCards = () => {
        const cards = document.querySelectorAll('.book-card, .feature-card');
        
        cards.forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;
                
                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.05)`;
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale(1)';
            });
        });
    };
    
    // Scroll-based parallax
    const scrollParallax = () => {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            
            // Parallax for hero background
            const heroSection = document.querySelector('.hero-section');
            if (heroSection) {
                heroSection.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
            
            // Parallax for orbit container
            const orbitContainer = document.querySelector('.orbit-container');
            if (orbitContainer) {
                orbitContainer.style.transform = `translate(-50%, -50%) scale(${1 - scrolled * 0.0005})`;
            }
        });
    };
    
    // Initialize advanced effects
    generateParticles();
    heroParallax();
    magneticButtons();
    tiltCards();
    scrollParallax();
    
    // ========== EXISTING EFFECTS ==========
    
    // Navbar scroll effect
    const navbar = document.querySelector('.landing-navbar');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

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
            const revealPoint = 100;
            
            if (elementTop < windowHeight - revealPoint) {
                element.classList.add('active');
            }
        });
    };
    
    window.addEventListener('scroll', revealOnScroll);
    revealOnScroll(); // Initial check

    // Counter animation for stats
    const animateCounter = (element, target, duration = 2000) => {
        let start = 0;
        const increment = target / (duration / 16);
        
        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                element.textContent = formatNumber(target);
                clearInterval(timer);
            } else {
                element.textContent = formatNumber(Math.floor(start));
            }
        }, 16);
    };

    const formatNumber = (num) => {
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K+';
        }
        return num.toString();
    };

    // Trigger counter animation when stats section is visible
    const statsSection = document.querySelector('.stats-section');
    let statsAnimated = false;

    const animateStats = () => {
        if (!statsAnimated && statsSection) {
            const rect = statsSection.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom >= 0) {
                statsAnimated = true;
                
                const counters = document.querySelectorAll('.stat-number');
                counters.forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-target'));
                    animateCounter(counter, target);
                });
            }
        }
    };

    window.addEventListener('scroll', animateStats);
    animateStats();

    // Book card hover effect
    const bookCards = document.querySelectorAll('.book-card');
    
    bookCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.zIndex = '10';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.zIndex = '1';
        });
    });

    // Feature cards staggered animation
    const featureCards = document.querySelectorAll('.feature-card');
    
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

    // Set initial state for feature cards
    featureCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
    });

    window.addEventListener('scroll', animateFeatures);
    animateFeatures();

    // Mobile menu toggle (if needed)
    const createMobileMenu = () => {
        const navbar = document.querySelector('.landing-navbar');
        const menu = document.querySelector('.navbar-menu');
        
        if (window.innerWidth <= 768) {
            if (!document.querySelector('.mobile-menu-toggle')) {
                const toggleBtn = document.createElement('button');
                toggleBtn.className = 'mobile-menu-toggle';
                toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
                toggleBtn.style.cssText = `
                    background: none;
                    border: none;
                    font-size: 24px;
                    color: var(--primary-blue);
                    cursor: pointer;
                    display: block;
                `;
                
                toggleBtn.addEventListener('click', () => {
                    menu.classList.toggle('active');
                });
                
                navbar.querySelector('.navbar-content').appendChild(toggleBtn);
            }
        }
    };

    createMobileMenu();
    window.addEventListener('resize', createMobileMenu);

    // Add to cart animation
    const addToCartButtons = document.querySelectorAll('.book-btn');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Create ripple effect
            const ripple = document.createElement('span');
            ripple.style.cssText = `
                position: absolute;
                background: rgba(255, 255, 255, 0.6);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;
            
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = e.clientX - rect.left - size / 2 + 'px';
            ripple.style.top = e.clientY - rect.top - size / 2 + 'px';
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
            
            // Show notification
            showNotification('Buku berhasil ditambahkan!');
        });
    });

    // Notification system
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 30px;
            background: ${type === 'success' ? '#10b981' : '#ef4444'};
            color: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            animation: slideInRight 0.4s ease;
            font-weight: 600;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.4s ease';
            setTimeout(() => notification.remove(), 400);
        }, 3000);
    }

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        
        @media (max-width: 768px) {
            .navbar-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                flex-direction: column;
                padding: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }
            
            .navbar-menu.active {
                display: flex;
            }
        }
    `;
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

    console.log('✨ Landing page loaded with advanced effects!');
});

// Scroll progress indicator
window.addEventListener('scroll', () => {
    const scrollProgress = document.querySelector('.scroll-progress');
    if (scrollProgress) {
        const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrolled = (window.scrollY / scrollHeight) * 100;
        scrollProgress.style.width = scrolled + '%';
    }
});
