// JavaScript Frontend User/Customer

document.addEventListener('DOMContentLoaded', function() {
    // Toggle menu HP
    const menuToggle = document.getElementById('menuToggle');
    const navbarMenu = document.querySelector('.navbar-menu');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            navbarMenu.classList.toggle('active');
        });
    }

    // Update badge keranjang pas halaman dimuat
    updateCartBadge();

    // Fitur tambah ke keranjang
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const bookId = this.dataset.bookId;
            const buttonElement = this;
            addToCart(bookId, buttonElement);
        });
    });

    // Fitur pencarian
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

    // Handler klik kartu kategori
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            const categoryName = this.querySelector('h3').textContent;
            window.location.href = `/user/categories?search=${encodeURIComponent(categoryName)}`;
        });
    });

    // Scroll halus buat link anchor
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (!href || !href.startsWith('#') || href.length <= 1) return;
            e.preventDefault();
            try {
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            } catch (err) {}
        });
    });

    // Auto-sembunyiin notifikasi setelah 5 detik
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.animation = 'slideDown 0.3s ease reverse';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // Carousel promo (banner kiri)
    const carousel = document.querySelector('.promo-carousel');
    if (carousel) {
        const interval = parseInt(carousel.getAttribute('data-interval') || '5000', 10);
        const slides = Array.from(carousel.querySelectorAll('.promo-slide'));
        const dots = Array.from(carousel.querySelectorAll('.carousel-dots .dot'));
        let current = 0;

        const goTo = (idx) => {
            slides.forEach((s, i) => s.classList.toggle('active', i === idx));
            dots.forEach((d, i) => d.classList.toggle('active', i === idx));
            current = idx;
        };

        let timer = null;
        const start = () => {
            if (timer) clearInterval(timer);
            timer = setInterval(() => {
                const next = (current + 1) % slides.length;
                goTo(next);
            }, interval);
        };

        dots.forEach((dot, i) => {
            dot.addEventListener('click', () => {
                goTo(i);
                start();
            });
        });

        if (slides.length > 0) {
            goTo(0);
            if (slides.length > 1) start();
        }
    }
});

// Fungsi tambah ke keranjang pake feedback visual
function addToCart(bookId, buttonElement) {
    const originalHTML = buttonElement.innerHTML;
    
    // Tampilin state loading
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
            // State berhasil
            buttonElement.innerHTML = '<i class="fas fa-check"></i> Ditambahkan!';
            buttonElement.style.background = 'var(--user-success)';
            showNotification('Buku berhasil ditambahkan ke keranjang!', 'success');
            updateCartBadge();
            
            // Reset tombol setelah 2 detik
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
                
                // Tambahin animasi bounce
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

// Jalanin pencarian
function performSearch(query) {
    if (!query || query.trim().length < 2) {
        showNotification('Masukkan minimal 2 karakter untuk pencarian', 'warning');
        return;
    }
    
    window.location.href = `/user/books?search=${encodeURIComponent(query.trim())}`;
}

// Tampilin notifikasi yang otomatis ilang
function showNotification(message, type = 'info') {
    // Hapus notifikasi yang udah ada
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

// Format harga dalam Rupiah Indonesia
function formatPrice(price) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price);
}

// Fungsi debounce buat performa
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

// Tambahin animasi CSS secara dinamis
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

// Slider Banner Promo
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
        // Sembunyiin semua slide
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        // Tampilin slide yang sekarang
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

    // Event listener
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

    // Pause pas hover
    promoBannerSlider.addEventListener('mouseenter', stopAutoSlide);
    promoBannerSlider.addEventListener('mouseleave', startAutoSlide);

    // Inisialisasi
    showSlide(0);
    startAutoSlide();
})();
