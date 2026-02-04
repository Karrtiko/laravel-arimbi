@extends('layouts.frontend')

@section('title', 'About Us - ArimbiStore.ID')

@section('content')
    <!-- About Hero -->
    <section class="about-hero">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-text">
                    <span class="hero-badge">🌟 Kerabat Viral</span>
                    <h1>Bawa Pulang Jajanan Dunia ke Rumahmu!</h1>
                    <p>Hai! Selamat datang di Arimbi Store. Tempatnya kamu cari cemilan unik dari luar negeri dan rahasia
                        kulit sehat yang sudah kami pilihkan khusus buat kamu.</p>
                </div>
                <div class="hero-image">
                    @if($heroImage)
                        <img src="{{ asset('storage/' . $heroImage) }}" alt="About Hero"
                            style="border-radius: 24px; width: 100%; height: auto;">
                    @else
                        <div
                            style="aspect-ratio: 1; background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); border-radius: 24px; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 100px;">🛍️</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section class="section">
        <div class="container">
            <div class="hero-grid" style="gap: 80px;">
                <div>
                    <h2>Gimana Awalnya? 🤔</h2>
                    <p style="margin-top: 20px; color: var(--gray-600); line-height: 1.8;">
                        Berawal dari hobi jajan dan sering dilitipin oleh-oleh sama temen, kami sadar kau cari snack luar
                        negeri yang asli itu gampang-gampang susah. Akhirnya, Arimbi Store hadir supaya kamu nggak perlu
                        ribet jajah-jauh cuma buat ngerasain Pocky Jepang atau Keripik Korea.
                    </p>
                    <p style="margin-top: 16px; color: var(--gray-600); line-height: 1.8;">
                        Oh iya, nggak cuma buat pipi perut, kami juga peduli sama penampilan kamu. Makanya, kami juga
                        sediain koleksi <strong>skincare pilihan</strong> yang emang sudah terbukti bagus dan aman dipakai.
                    </p>
                </div>
                <div class="about-stats">
                    <div class="stat-card">
                        <div class="number">500+</div>
                        <div class="label">Happy Customers</div>
                    </div>
                    <div class="stat-card">
                        <div class="number">100%</div>
                        <div class="label">Original Items</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2>🤝 Janji Kami</h2>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">✅</div>
                    <h4>Pasti Asli</h4>
                    <p>Nggak perlu kirim-err, semua barang 100% original. Ini AVA High class!</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🌍</div>
                    <h4>Snack Luar Negeri Asli</h4>
                    <p>Semua jajanan kami bawa langsung dari negara asalnya: Jepang, Korea, Thailand, dll.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💄</div>
                    <h4>Skincare Pilihan</h4>
                    <p>Skincare yang kami jual sudah kami cek dulu komitmennya, jadi kamu tinggal pakai aja.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📦</div>
                    <h4>Packing Anti-Remuk</h4>
                    <p>Kami tahu rasanya deh sedih kalau sampai rumah snacknya hancur. Tenang, kami urus serius!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <div class="container">
        <section class="cta-section">
            <h2>Udah laper atau mau mulai perawatan?</h2>
            <p>Yuk, jangan cuma dilihat. Stok terbatas lho!</p>
            <div class="cta-buttons">
                <a href="{{ route('shop', ['category' => 'snacks']) }}" class="btn btn-accent btn-lg">Lihat Katalog
                    Snack</a>
                <a href="{{ route('shop', ['category' => 'skincare']) }}" class="btn btn-secondary btn-lg"
                    style="background: white;">Cek Produk Skincare</a>
            </div>
        </section>
    </div>
@endsection