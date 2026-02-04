<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ArimbiStore.ID - Snacks & Skincare')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}">
    @stack('styles')
    @php
        $generalSettings = \App\Models\GeneralSetting::allSettings();
        $waNumber = $generalSettings['whatsapp_number'] ?? '6281234567890';
    @endphp
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container navbar-content">
            <a href="{{ route('home') }}" class="logo">
                <span class="logo-icon">✦</span> ArimbiStore<span class="logo-accent">.ID</span>
            </a>
            <div class="nav-links">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('shop') }}" class="{{ request()->routeIs('shop') ? 'active' : '' }}">Shop</a>
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                <a href="{{ route('shop') }}" class="btn btn-primary btn-sm">Shop Now</a>
            </div>
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
        </div>
        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('shop') }}">Shop</a>
            <a href="{{ route('about') }}">About</a>
            <a href="{{ route('shop') }}" class="btn btn-primary">Shop Now</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Floating Cart Bar -->
    <div class="cart-bar" id="cartBar" style="display: none;">
        <div class="container cart-bar-content">
            <div class="cart-info">
                <span class="cart-icon">🛒</span>
                <span>Total Keranjang</span>
                <strong id="cartTotal">Rp 0</strong>
            </div>
            <button class="btn btn-primary" onclick="openCart()">Checkout →</button>
        </div>
    </div>

    <!-- Cart Sidebar -->
    <div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h3>🛒 Keranjang</h3>
            <button onclick="closeCart()">✕</button>
        </div>
        <div class="cart-items" id="cartItems">
            <!-- Items populated by JS -->
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Total</span>
                <strong id="cartTotalSidebar">Rp 0</strong>
            </div>
            <button class="btn btn-primary btn-block" onclick="checkout()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"
                    style="vertical-align: middle; margin-right: 8px;">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                </svg>
                Checkout via WhatsApp
            </button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h4>ArimbiStore.ID</h4>
                    <p>Destinasi utama untuk menemukan snack autentik dan skincare premium dari berbagai belahan dunia.
                        Curated with love.</p>
                </div>
                <div class="footer-links">
                    <h5>MENU</h5>
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('shop') }}">All Products</a>
                    <a href="{{ route('shop', ['category' => 'snacks']) }}">Snacks</a>
                    <a href="{{ route('shop', ['category' => 'skincare']) }}">Skincare</a>
                    <a href="{{ route('about') }}">About Us</a>
                </div>
                <div class="footer-contact">
                    <h5>CONTACT</h5>
                    <p>📞 {{ $waNumber }}</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© {{ date('Y') }} ArimbiStore.ID. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Chat Admin Button (WhatsApp) -->
    <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="chat-admin">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path
                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
        </svg>
        Chat Admin
    </a>

    <!-- Checkout Modal -->
    <div id="checkoutModal" class="checkout-modal">
        <div class="checkout-modal-content">
            <div class="modal-header">
                <h3>🛍️ Formulir Pemesanan</h3>
                <button onclick="closeCheckoutModal()">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Anda *</label>
                    <input type="text" id="buyerName" class="form-control" placeholder="Nama Lengkap" required>
                </div>
                <div class="form-group">
                    <label>No. WhatsApp *</label>
                    <input type="tel" id="buyerPhone" class="form-control" placeholder="08..." required>
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" id="isDropship" onchange="toggleDropship()">
                    <label for="isDropship">Kirim sebagai Dropship / Hadiah (Alamat Berbeda)</label>
                </div>

                <div id="dropshipFields"
                    style="display: none; padding-left: 12px; border-left: 2px solid var(--primary); margin-bottom: 20px;">
                    <div class="form-group">
                        <label>Nama Penerima</label>
                        <input type="text" id="receiverName" class="form-control" placeholder="Nama Penerima">
                    </div>
                    <div class="form-group">
                        <label>No. Telp Penerima</label>
                        <input type="tel" id="receiverPhone" class="form-control" placeholder="No. Telp Penerima">
                    </div>
                </div>

                <div class="form-group">
                    <label>Detail Pengiriman (Alamat Lengkap) *</label>
                    <textarea id="deliveryAddress" class="form-control" rows="3"
                        placeholder="Jalan, RT/RW, Kecamatan, Kota, Kode Pos" required></textarea>
                </div>

                <div class="form-group">
                    <label>Catatan Tambahan (Opsional)</label>
                    <textarea id="deliveryNote" class="form-control" rows="2"
                        placeholder="Contoh: Kirim segera, titip di satpam, dll."></textarea>
                </div>

                <button class="btn btn-primary btn-block" onclick="processCheckout()">
                    <span style="font-size: 1.2em">🚀</span> Kirim Pesanan (WA)
                </button>
            </div>
        </div>
    </div>

    <script>
        const WA_NUMBER = '{{ $waNumber }}';
    </script>
    <script src="{{ asset('js/cart.js') }}"></script>
    @stack('scripts')
    <script>
        function toggleMobileMenu() {
            document.getElementById('mobileMenu').classList.toggle('active');
        }
    </script>
</body>

</html>