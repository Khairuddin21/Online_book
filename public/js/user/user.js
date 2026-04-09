// JavaScript Frontend User/Customer

document.addEventListener('DOMContentLoaded', function() {
    // Toggle menu HP
    const menuToggle = document.getElementById('menuToggle');
    const mainNavbar = document.getElementById('mainNavbar');
    const navMenu = document.getElementById('navMenu');
    
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            }
        });

        // Tutup menu HP pas klik link navigasi
        navMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                const icon = menuToggle.querySelector('i');
                if (icon) {
                    icon.classList.add('fa-bars');
                    icon.classList.remove('fa-times');
                }
            });
        });
    }

    // Efek navbar pas scroll
    if (mainNavbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 20) {
                mainNavbar.classList.add('scrolled');
            } else {
                mainNavbar.classList.remove('scrolled');
            }
        });
    }

    // Update badge keranjang pas halaman dimuat
    updateCartBadge();

    // Fitur tambah ke keranjang - pake event delegation biar ga dobel listener
    document.body.removeEventListener('click', handleAddToCart); // Remove any existing listener
    document.body.addEventListener('click', handleAddToCart);

    // Fitur pencarian
    const searchInput = document.getElementById('navSearchInput');
    const searchButton = document.getElementById('navSearchBtn');
    
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

    // Toggle dropdown menu user (aksi klik)
    const userMenuButton = document.getElementById('userMenuButton');
    const userMenuDropdown = document.querySelector('.user-menu .dropdown');
    
    if (userMenuButton && userMenuDropdown) {
        // Toggle dropdown pas tombol diklik
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

        // Tutup dropdown kalo klik di luar
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-menu')) {
                userMenuDropdown.classList.remove('show');
                const container = userMenuButton.closest('.user-menu');
                if (container) container.setAttribute('aria-expanded', 'false');
            }
        });

        // Cegah dropdown nutup pas diklik di dalamnya
        userMenuDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Tutup pas pencet tombol Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                userMenuDropdown.classList.remove('show');
                const container = userMenuButton.closest('.user-menu');
                if (container) container.setAttribute('aria-expanded', 'false');
            }
        });
    }
});

// Handle tambah keranjang pake event delegation (cegah listener dobel)
function handleAddToCart(e) {
    const button = e.target.closest('.add-to-cart');
    if (!button) return;
    
    e.preventDefault();
    const bookId = button.dataset.bookId;
    addToCart(bookId, button);
}

// Track request yang lagi jalan biar ga submit dobel
const ongoingRequests = new Set();

// Fungsi tambah ke keranjang pake feedback visual
function addToCart(bookId, buttonElement) {
    // Cegah request dobel buat buku yang sama
    const requestKey = `cart-${bookId}`;
    if (ongoingRequests.has(requestKey)) {
        console.log('Request already in progress for book:', bookId);
        return;
    }
    
    const originalHTML = buttonElement.innerHTML;
    
    // Tampilin state loading
    buttonElement.disabled = true;
    buttonElement.innerHTML = '<span class="loading"></span> Menambahkan...';
    
    // Tandai request lagi jalan
    ongoingRequests.add(requestKey);
    
    fetch((window.APP_URL || '') + '/api/cart/add', {
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
            buttonElement.style.background = 'var(--green-dark)';
            showNotification('Buku berhasil ditambahkan ke keranjang!', 'success');
            updateCartBadge();
            
            // Reset tombol setelah 2 detik
            setTimeout(() => {
                buttonElement.innerHTML = originalHTML;
                buttonElement.style.background = '';
                buttonElement.disabled = false;
                // Hapus dari request yang lagi jalan
                ongoingRequests.delete(requestKey);
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
        // Hapus dari request yang lagi jalan pas error
        ongoingRequests.delete(requestKey);
    });
}

// Update badge keranjang
function updateCartBadge() {
    const badge = document.querySelector('.cart-badge');
    if (!badge) return;
    
    fetch((window.APP_URL || '') + '/api/cart/count')
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
        success: '#6b9e65',
        error: '#e74c3c',
        warning: '#f39c12',
        info: '#a8d5a2'
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
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        font-family: 'Inter', sans-serif;
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

// Carousel Buku
(function() {
    const carousel = document.getElementById('booksCarousel');
    const prevBtn = document.getElementById('booksCarouselPrev');
    const nextBtn = document.getElementById('booksCarouselNext');
    if (!carousel || !prevBtn || !nextBtn) return;

    const scrollAmount = 420; // ~2 cards per click

    function updateButtons() {
        prevBtn.style.opacity = carousel.scrollLeft <= 10 ? '0' : '1';
        prevBtn.style.pointerEvents = carousel.scrollLeft <= 10 ? 'none' : 'auto';
        const maxScroll = carousel.scrollWidth - carousel.clientWidth - 10;
        nextBtn.style.opacity = carousel.scrollLeft >= maxScroll ? '0' : '1';
        nextBtn.style.pointerEvents = carousel.scrollLeft >= maxScroll ? 'none' : 'auto';
    }

    nextBtn.addEventListener('click', () => {
        carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    });
    prevBtn.addEventListener('click', () => {
        carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    });

    carousel.addEventListener('scroll', updateButtons);
    updateButtons();
})();

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
