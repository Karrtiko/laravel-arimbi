@extends('layouts.frontend')

@section('title', $bundle->name . ' - ArimbiStore.ID')

@section('content')
    @php
        $settings = \App\Models\GeneralSetting::allSettings();
        $waNumber = $settings['whatsapp_number'] ?? '6281234567890';
    @endphp

    <!-- Breadcrumb -->
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">Home</a> /
            <a href="{{ route('shop') }}">Paket</a> /
            <span>{{ $bundle->name }}</span>
        </nav>
    </div>

    <!-- Package Detail -->
    <section class="container">
        <div class="product-detail">
            <!-- Gallery -->
            <div class="product-gallery">
                <div class="product-main-image" style="background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);">
                    @if($bundle->media->first())
                        <img src="{{ asset('storage/' . $bundle->media->first()->file_path) }}" alt="{{ $bundle->name }}">
                    @else
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                            <span style="font-size: 120px;">🎁</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Info -->
            <div class="product-info">
                <span class="hero-badge">🎁 PAKET HEMAT</span>

                <h1>{{ $bundle->name }}</h1>

                <div class="price">
                    Rp {{ number_format($bundle->price, 0, ',', '.') }}
                </div>

                <div class="product-buttons">
                    <button class="btn btn-secondary btn-lg"
                        onclick="addToCart({{ $bundle->id }}, 'bundle', '{{ addslashes($bundle->name) }}', {{ $bundle->price }}, '{{ $bundle->media->first() ? asset('storage/' . $bundle->media->first()->file_path) : '' }}')"
                        {{ $bundle->stock <= 0 ? 'disabled' : '' }}>
                        Tambah Keranjang
                    </button>
                    <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo kak! Saya mau order paket ' . $bundle->name . ' (Rp ' . number_format($bundle->price, 0, ',', '.') . ')') }}"
                        target="_blank" class="btn btn-primary btn-lg">
                        Beli Sekarang
                    </a>
                </div>

                <div class="product-features">
                    <span>✅ Ready Stock</span>
                    <span>⭐ 100% Original</span>
                    <span>📦 Packing Aman</span>
                </div>

                <!-- Description -->
                <div class="product-description">
                    <h4>Tentang Paket Ini</h4>
                    <p>{{ $bundle->description ?: 'Paket bundling dengan harga hemat!' }}</p>
                </div>

                <!-- Included Items -->
                <div style="margin-top: 24px;">
                    <h4 style="margin-bottom: 16px;">📦 Isi Paket:</h4>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach($bundle->bundleProducts as $bp)
                            <div
                                style="display: flex; align-items: center; gap: 12px; padding: 16px; background: var(--gray-50); border-radius: var(--radius);">
                                <div
                                    style="width: 60px; height: 60px; background: white; border-radius: var(--radius-sm); overflow: hidden;">
                                    @if($bp->product?->media->first())
                                        <img src="{{ asset('storage/' . $bp->product->media->first()->file_path) }}" alt=""
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <div
                                            style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--gray-400);">
                                            📦</div>
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <h5 style="font-size: 14px; margin-bottom: 4px;">{{ $bp->product?->name ?? 'Product' }}</h5>
                                    <p style="font-size: 12px; color: var(--gray-500);">Qty: {{ $bp->quantity }}x</p>
                                </div>
                                <div style="font-weight: 600; color: var(--primary);">
                                    Rp {{ number_format(($bp->product?->price ?? 0) * $bp->quantity, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Savings -->
                    @php
                        $originalTotal = $bundle->bundleProducts->sum(fn($bp) => ($bp->product?->price ?? 0) * $bp->quantity);
                        $savings = $originalTotal - $bundle->price;
                    @endphp
                    @if($savings > 0)
                        <div
                            style="margin-top: 16px; padding: 16px; background: linear-gradient(135deg, #dcfce7, #bbf7d0); border-radius: var(--radius); text-align: center;">
                            <span style="color: #166534; font-weight: 700;">🎉 Hemat Rp
                                {{ number_format($savings, 0, ',', '.') }}!</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Other Packages -->
    @if($otherBundles->count() > 0)
        <section class="related-section">
            <div class="container">
                <h3>Paket Lainnya</h3>
                <div class="bundle-grid">
                    @foreach($otherBundles as $index => $other)
                        <div class="bundle-card">
                            <div class="bundle-card-image gradient-{{ ($index % 3) + 1 }}">
                                @if($other->media->first())
                                    <img src="{{ asset('storage/' . $other->media->first()->file_path) }}" alt="{{ $other->name }}">
                                @else
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                                        <span style="font-size: 60px;">🎁</span>
                                    </div>
                                @endif
                            </div>
                            <div class="bundle-card-body">
                                <h4>{{ $other->name }}</h4>
                                <p>{{ Str::limit($other->description, 50) }}</p>
                                <div class="bundle-price">Rp {{ number_format($other->price, 0, ',', '.') }}</div>
                                <a href="{{ route('package.show', $other->slug) }}" class="btn btn-primary btn-block">View
                                    Details</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection