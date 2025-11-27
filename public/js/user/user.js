// User/Customer Frontend JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const navbarMenu = document.querySelector('.navbar-menu');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            navbarMenu.classList.toggle('active');
        });
    }

    // Update cart badge on page load
    updateCartBadge();

    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const bookId = this.dataset.bookId;
            const buttonElement = this;
            addToCart(bookId, buttonElement);
        });
    });

    // Search functionality
    const searchInput = document.querySelector('.navbar-search input');
    const searchButton = document.querySelector('.navbar-search button');
    
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch(this.value);
            }
        });
    }
    
    if (searchButton) {
        searchButton.addEventListener('click', function(e) {
            e.preventDefault();
            if (searchInput) {
                performSearch(searchInput.value);
            }
        });
    }

    // Category card click handlers
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            const categoryName = this.querySelector('h3').textContent;
            window.location.href = `/user/categories?search=${encodeURIComponent(categoryName)}`;
        });
    });

    // Smooth scroll for anchor links
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

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.animation = 'slideDown 0.3s ease reverse';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // User menu dropdown toggle (click action)
    const userMenuButton = document.getElementById('userMenuButton');
    const userMenuDropdown = document.querySelector('.user-menu .dropdown');
    
    if (userMenuButton && userMenuDropdown) {
        // Toggle dropdown on button click
        userMenuButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            userMenuDropdown.classList.toggle('show');
            const container = userMenuButton.closest('.user-menu');
            if (container) {
                const expanded = userMenuDropdown.classList.contains('show');
                container.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-menu')) {
                userMenuDropdown.classList.remove('show');
                const container = userMenuButton.closest('.user-menu');
                if (container) container.setAttribute('aria-expanded', 'false');
            }
        });

        // Prevent dropdown from closing when clicking inside it
        userMenuDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                userMenuDropdown.classList.remove('show');
                const container = userMenuButton.closest('.user-menu');
                if (container) container.setAttribute('aria-expanded', 'false');
            }
        });
    }
});

// Add to cart function with visual feedback
function addToCart(bookId, buttonElement) {
    const originalHTML = buttonElement.innerHTML;
    
    // Show loading state
    buttonElement.disabled = true;
    buttonElement.innerHTML = '<span class="loading"></span> Menambahkan...';
    
    fetch('/api/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            book_id: bookId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Success state
            buttonElement.innerHTML = '<i class="fas fa-check"></i> Ditambahkan!';
            buttonElement.style.background = 'var(--user-success)';
            showNotification('Buku berhasil ditambahkan ke keranjang!', 'success');
            updateCartBadge();
            
            // Reset button after 2 seconds
            setTimeout(() => {
                buttonElement.innerHTML = originalHTML;
                buttonElement.style.background = '';
                buttonElement.disabled = false;
            }, 2000);
        } else {
            throw new Error(data.message || 'Gagal menambahkan ke keranjang');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(error.message || 'Terjadi kesalahan', 'error');
        buttonElement.innerHTML = originalHTML;
        buttonElement.disabled = false;
    });
}

// Update cart badge
function updateCartBadge() {
    const badge = document.querySelector('.cart-badge');
    if (!badge) return;
    
    fetch('/api/cart/count')
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'flex';
                
                // Add bounce animation
                badge.style.animation = 'bounce 0.5s ease';
                setTimeout(() => {
                    badge.style.animation = '';
                }, 500);
            } else {
                badge.textContent = '0';
            }
        })
        .catch(error => {
            console.error('Error updating cart:', error);
            badge.textContent = '0';
        });
}

// Perform search
function performSearch(query) {
    if (!query || query.trim().length < 2) {
        showNotification('Masukkan minimal 2 karakter untuk pencarian', 'warning');
        return;
    }
    
    window.location.href = `/user/books?search=${encodeURIComponent(query.trim())}`;
}

// Show notification with auto-dismiss
function showNotification(message, type = 'info') {
    // Remove existing notifications
    document.querySelectorAll('.notification-toast').forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification-toast notification-${type}`;
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    const colors = {
        success: '#2ecc71',
        error: '#e74c3c',
        warning: '#f39c12',
        info: '#3498db'
    };
    
    notification.innerHTML = `
        <i class="fas ${icons[type] || icons.info}"></i>
        <span>${message}</span>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        padding: 15px 25px;
        background: ${colors[type] || colors.info};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        animation: slideInRight 0.3s ease;
        max-width: 350px;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Format price in Indonesian Rupiah
function formatPrice(price) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price);
}

// Debounce function for performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Add CSS animations dynamically
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100px);
        }
    }
    
    @keyframes bounce {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }
`;
document.head.appendChild(style);

// Promotional Banners Slider
(function() {
    const promoBannerSlider = document.querySelector('.promo-banners-slider');
    if (!promoBannerSlider) return;

    const slides = promoBannerSlider.querySelectorAll('.promo-banner-slide');
    const dots = promoBannerSlider.querySelectorAll('.promo-banner-dot');
    const prevBtn = promoBannerSlider.querySelector('.promo-banner-prev');
    const nextBtn = promoBannerSlider.querySelector('.promo-banner-next');
    
    let currentSlide = 0;
    let autoSlideInterval;

    function showSlide(index) {
        // Hide all slides
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        // Show current slide
        if (slides[index]) {
            slides[index].classList.add('active');
            dots[index].classList.add('active');
            currentSlide = index;
        }
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(currentSlide);
    }

    function startAutoSlide() {
        stopAutoSlide();
        autoSlideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
    }

    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
        }
    }

    // Event listeners
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            startAutoSlide(); // Restart auto-slide
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            startAutoSlide(); // Restart auto-slide
        });
    }

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
            startAutoSlide(); // Restart auto-slide
        });
    });

    // Pause on hover
    promoBannerSlider.addEventListener('mouseenter', stopAutoSlide);
    promoBannerSlider.addEventListener('mouseleave', startAutoSlide);

    // Initialize
    showSlide(0);
    startAutoSlide();
})();
