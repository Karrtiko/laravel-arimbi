@extends('layouts.frontend')

@section('title', 'ArimbiStore.ID - Snacks & Skincare Import')

@section('content')
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-text">
                    <span class="hero-badge">🌟 Kerabat Viral</span>
                    <h1>{!! nl2br(e($heroTitle)) !!}</h1>
                    <p>{{ $heroSubtitle }}</p>
                    <div class="hero-buttons">
                        <a href="{{ route('shop', ['category' => 'snacks']) }}" class="btn btn-primary btn-lg">Lihat
                            Katalog Snack</a>
                        <a href="{{ route('shop', ['category' => 'skincare']) }}" class="btn btn-outline btn-lg">Cek
                            Skincare</a>
                    </div>
                </div>
                <div class="hero-image">
                    @if($heroImage)
                        <img src="{{ asset('storage/' . $heroImage) }}" alt="Hero Image"
                            style="border-radius: 24px; width: 100%; height: auto;">
                    @else
                        <!-- Fallback or Placeholder -->
                        <div style="aspect-ratio: 1; background: #ddd; border-radius: 24px;"></div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Country Filter -->
    <section class="country-filter">
        <div class="container">
            <h3>Mau 'Jalan-Jalan' ke mana hari ini?</h3>
            <p>Pilih negara favorit mu, cari temukan oleh-oleh khasnya</p>
            <div class="country-tabs">
                <a href="{{ route('shop') }}" class="country-tab active">
                    <span class="flag"
                        style="background: linear-gradient(135deg, #8b5cf6, #6366f1); display: flex; align-items: center; justify-content: center; color: white;">✦</span>
                    <span>Semua</span>
                </a>
                @foreach($countries as $country)
                    <a href="{{ route('shop', ['country' => $country->code]) }}" class="country-tab">
                        <span class="flag">{{ $country->flag ?? '🌍' }}</span>
                        <span>{{ $country->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2>Produk Terbaru</h2>
                    <p>Koleksi terbaru yang baru saja mendarat di gudang kami.</p>
                </div>
                <a href="{{ route('shop') }}">Lihat Semua →</a>
            </div>

            <div class="product-grid">
                @forelse($products as $product)
                    <div class="product-card">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <div class="product-card-image">
                                @if($product->stock <= 0)
                                    <span class="product-badge out-of-stock">Stok Habis</span>
                                @elseif($product->created_at->diffInDays() < 7)
                                    <span class="product-badge">Baru</span>
                                @endif
                                @if($product->media->first())
                                    <img src="{{ asset('storage/' . $product->media->first()->file_path) }}"
                                        alt="{{ $product->name }}">
                                @else
                                    <div class="no-image">📷 No Image</div>
                                @endif
                            </div>
                        </a>
                        <div class="product-card-body">
                            <div class="product-category">
                                @if($product->category)
                                    <span class="cat">🏷️ {{ $product->category->name }}</span>
                                @endif
                                @if($product->country)
                                    <span class="country">{{ $product->country->flag ?? '' }} {{ $product->country->name }}</span>
                                @endif
                            </div>
                            <h4>{{ $product->name }}</h4>
                            <div class="product-card-footer">
                                <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                <button class="add-to-cart-btn"
                                    onclick="addToCart({{ $product->id }}, 'product', '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->media->first() ? asset('storage/' . $product->media->first()->file_path) : '' }}')"
                                    {{ $product->stock <= 0 ? 'disabled' : '' }}>+</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <div class="icon">📦</div>
                        <p>Belum ada produk</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Bundles Section -->
    @if($bundles->count() > 0)
        <section class="bundle-section">
            <div class="container">
                <div class="section-header">
                    <div>
                        <h2>Paket Hemat</h2>
                        <p>Bundling pilihan dengan harga spesial!</p>
                    </div>
                </div>

                <div class="bundle-grid">
                    @foreach($bundles as $index => $bundle)
                        <div class="bundle-card">
                            <div class="bundle-card-image gradient-{{ ($index % 3) + 1 }}">
                                <span class="bundle-badge">BEST VALUE PROMO</span>
                                @if($bundle->media->first())
                                    <img src="{{ asset('storage/' . $bundle->media->first()->file_path) }}" alt="{{ $bundle->name }}">
                                @else
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                                        <span style="font-size: 60px;">🎁</span>
                                    </div>
                                @endif
                            </div>
                            <div class="bundle-card-body">
                                <h4>{{ $bundle->name }}</h4>
                                <p>{{ Str::limit($bundle->description, 50) }}</p>
                                <ul class="bundle-items">
                                    @foreach($bundle->bundleProducts->take(3) as $bp)
                                        <li>{{ $bp->product?->name ?? 'Product' }} x{{ $bp->quantity }}</li>
                                    @endforeach
                                    @if($bundle->bundleProducts->count() > 3)
                                        <li>+{{ $bundle->bundleProducts->count() - 3 }} item lainnya</li>
                                    @endif
                                </ul>
                                <div class="bundle-price">Rp {{ number_format($bundle->price, 0, ',', '.') }}</div>
                                <a href="{{ route('package.show', $bundle->slug) }}" class="btn btn-primary btn-block">View
                                    Details</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection