<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Tentang Kami - Gramedia.com">
    
    <title>Tentang Kami - Toko Buku Online</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    
    <style>
        body {
            padding-top: 0;
        }

        .about-page {
            min-height: 100vh;
            background: #ffffff;
            padding-top: 100px;
        }

        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        .about-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .about-header img {
            width: 100%;
            max-width: 1000px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .about-header h1 {
            font-size: 48px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 20px;
        }

        .about-header p {
            font-size: 20px;
            color: #64748b;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.8;
        }

        .about-content {
            background: white;
            border-radius: 20px;
            padding: 60px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            margin-bottom: 40px;
        }

        .about-section {
            margin-bottom: 50px;
        }

        .about-section:last-child {
            margin-bottom: 0;
        }

        .about-section h2 {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
            display: inline-block;
        }

        .about-section h3 {
            font-size: 24px;
            font-weight: 600;
            color: #334155;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        .about-section p {
            font-size: 16px;
            line-height: 1.9;
            color: #475569;
            margin-bottom: 20px;
            text-align: justify;
        }

        .about-section ul {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .about-section ul li {
            font-size: 16px;
            color: #475569;
            padding: 12px 0;
            padding-left: 30px;
            position: relative;
            line-height: 1.6;
        }

        .about-section ul li:before {
            content: "•";
            color: #667eea;
            font-weight: bold;
            font-size: 24px;
            position: absolute;
            left: 0;
            top: 8px;
        }

        .social-links-section {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: #f1f5f9;
            border-radius: 10px;
            color: #334155;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .social-link:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .social-link i {
            font-size: 18px;
        }

        .contact-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .contact-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .contact-info i {
            margin-right: 8px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
        }

        @media (max-width: 768px) {
            .about-content {
                padding: 40px 30px;
            }

            .about-header h1 {
                font-size: 36px;
            }

            .about-header p {
                font-size: 18px;
            }

            .about-section h2 {
                font-size: 26px;
            }

            .about-section h3 {
                font-size: 20px;
            }

            .social-links-section {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="landing-navbar">
        <div class="navbar-content">
            <a href="{{ route('home') }}" class="navbar-logo">
                <i class="fas fa-book-reader"></i>
                <span>Toko Buku</span>
            </a>

            <ul class="navbar-menu">
                <li><a href="{{ route('home') }}#home">Beranda</a></li>
                <li><a href="{{ route('home') }}#features">Fitur</a></li>
                <li><a href="{{ route('home') }}#books">Katalog</a></li>
                <li><a href="{{ route('about') }}" class="active">Tentang</a></li>
                <li><a href="{{ route('home') }}#contact">Kontak</a></li>
            </ul>

            <div class="navbar-actions">
                <a href="{{ route('login') }}" class="btn btn-outline">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </a>
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </a>
            </div>
        </div>
    </nav>

    <div class="about-page">
        <div class="about-container">
            <!-- Header Section -->
            <div class="about-header">
                <img src="{{ asset('gambar/home asset/about-us.jpg') }}" alt="Tentang Kami - Toko Buku Online">
                <h1>Tentang Kami</h1>
                <p>Sekilas informasi mengenai Toko Buku Online kami</p>
            </div>

            <!-- Content Section -->
            <div class="about-content">
                <!-- Apa itu Toko Buku Online? -->
                <div class="about-section">
                    <h2>Apa itu Toko Buku Online?</h2>
                    <p>
                        Toko Buku Online adalah platform e-commerce yang menyediakan berbagai koleksi buku berkualitas dari berbagai genre dan kategori. Kami hadir untuk memudahkan Anda dalam menemukan dan membeli buku favorit dengan mudah dan cepat.
                    </p>
                    <p>
                        Platform kami dirancang dengan antarmuka yang user-friendly dan fitur pencarian yang canggih, sehingga Anda dapat dengan mudah menemukan buku yang Anda cari berdasarkan judul, penulis, kategori, atau kata kunci tertentu.
                    </p>
                    <p>
                        Misi kami adalah meningkatkan literasi dan memberikan kemudahan akses pada dunia pengetahuan di seluruh Indonesia dengan memanfaatkan teknologi. Kami percaya bahwa membaca adalah jendela dunia yang membuka wawasan dan pengetahuan.
                    </p>
                    <p>
                        Kenyamanan dan kepuasan para pelanggan merupakan prioritas utama kami. Kami berkomitmen untuk memberikan pengalaman berbelanja online yang terbaik dengan sistem pembayaran yang aman, pengiriman cepat, dan layanan pelanggan yang responsif. Setiap transaksi dijamin aman dan data pribadi Anda terlindungi dengan baik.
                    </p>
                </div>

                <!-- Di mana saya bisa mengetahui update di media sosial? -->
                <div class="about-section">
                    <h2>Di mana saya bisa mengetahui update di media sosial?</h2>
                    <p>Kamu bisa temukan kami di media sosial berikut:</p>
                    <div class="social-links-section">
                        <a href="#" class="social-link">
                            <i class="fab fa-facebook"></i>
                            <span>Facebook</span>
                        </a>
                        <a href="#" class="social-link">
                            <i class="fab fa-instagram"></i>
                            <span>Instagram</span>
                        </a>
                        <a href="#" class="social-link">
                            <i class="fab fa-twitter"></i>
                            <span>X (Twitter)</span>
                        </a>
                    </div>
                </div>

                <!-- Bagaimana cara menghubungi Customer Service? -->
                <div class="about-section">
                    <h2>Bagaimana cara menghubungi Customer Service?</h2>
                    <p>Silakan hubungi layanan Customer Service kami melalui email:</p>
                    <p style="font-size: 18px; color: #667eea; font-weight: 600;">
                        <i class="fas fa-envelope" style="margin-right: 8px;"></i>
                        <a href="mailto:info@tokobuku.com" style="color: #667eea; text-decoration: none;">info@tokobuku.com</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/landing.js') }}?v={{ time() }}" defer></script>
</body>

</html>
