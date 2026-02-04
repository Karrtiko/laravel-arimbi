@extends('layouts.frontend')

@section('title', $product->name . ' - ArimbiStore.ID')

@section('content')
    <!-- Breadcrumb -->
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">Home</a> /
            @if($product->category)
                <a href="{{ route('shop', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a> /
            @endif
            <span>{{ $product->name }}</span>
        </nav>
    </div>

    <!-- Product Detail -->
    <section class="container">
        <div class="product-detail">
            <!-- Gallery -->
            <div class="product-gallery">
                <div class="product-main-image" id="mainImage">
                    @if($product->media->first())
                        <img src="{{ asset('storage/' . $product->media->first()->file_path) }}" alt="{{ $product->name }}"
                            id="mainImg">
                    @else
                        <div
                            style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--gray-400);">
                            📷 No Image</div>
                    @endif
                </div>
                @if($product->media->count() > 1)
                    <div class="product-thumbnails">
                        @foreach($product->media as $index => $media)
                            <div class="product-thumbnail {{ $index === 0 ? 'active' : '' }}"
                                onclick="changeImage(this, '{{ asset('storage/' . $media->file_path) }}')">
                                <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $product->name }}">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Info -->
            <div class="product-info">
                <div class="category-badge">
                    @if($product->country)
                        <span>{{ $product->country->flag ?? '🌍' }}</span>
                        <span>{{ $product->country->name }}</span>
                    @endif
                </div>

                <h1>{{ $product->name }}</h1>

                @php
                    $settings = \App\Models\GeneralSetting::allSettings();
                    $lowStock = (int) ($settings['low_stock_threshold'] ?? 5);
                    $waNumber = $settings['whatsapp_number'] ?? '6281234567890';
                @endphp

                <div class="price">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                    @if($product->stock > 0 && $product->stock <= $lowStock)
                        <span
                            style="background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-left: 12px;">
                            ⚠️ Tinggal {{ $product->stock }} lagi!
                        </span>
                    @endif
                    <span class="viewers"
                        style="font-size: 14px; font-weight: normal; color: var(--gray-500); margin-left: 12px;">
                        🔥 {{ rand(5, 50) }} orang sedang melihat
                    </span>
                </div>

                <div class="product-buttons">
                    <button class="btn btn-secondary btn-lg"
                        onclick="addToCart({{ $product->id }}, 'product', '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->media->first() ? asset('storage/' . $product->media->first()->file_path) : '' }}')"
                        {{ $product->stock <= 0 ? 'disabled' : '' }}>
                        Tambah Keranjang
                    </button>
                    <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo kak! Saya mau order ' . $product->name . ' (Rp ' . number_format($product->price, 0, ',', '.') . ')') }}"
                        target="_blank" class="btn btn-primary btn-lg">
                        Beli Sekarang
                    </a>
                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="btn btn-success"
                        style="background: #25d366; padding: 16px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                    </a>
                </div>

                <div class="product-features">
                    <span>✅ Ready Stock</span>
                    <span>⭐ 100% Original</span>
                    <span>📦 Packing Aman</span>
                </div>

                <!-- Description -->
                <div class="product-description">
                    @php
                        $descLength = strlen($product->description ?? '');
                        $isLong = $descLength > 300;
                    @endphp

                    @if($isLong)
                        <!-- Tutorial-style for long descriptions -->
                        <h4>Deskripsi Produk</h4>
                        <div
                            style="margin-top: 12px; padding: 20px; background: var(--gray-50); border-radius: var(--radius); line-height: 1.8;">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    @else
                        <!-- Card-style for short descriptions -->
                        <p>{{ $product->description ?: 'Tidak ada deskripsi' }}</p>
                    @endif

                    <!-- Attributes if available -->
                    @if($product->attributes && is_array($product->attributes) && count($product->attributes) > 0)
                        <div style="margin-top: 20px;">
                            <h4 style="margin-bottom: 12px;">Spesifikasi</h4>
                            <div style="display: grid; gap: 8px;">
                                @foreach($product->attributes as $key => $value)
                                    <div
                                        style="display: flex; padding: 12px; background: var(--gray-50); border-radius: var(--radius-sm);">
                                        <span
                                            style="width: 120px; color: var(--gray-500); font-size: 14px;">{{ ucfirst($key) }}</span>
                                        <span style="font-weight: 500; font-size: 14px;">{{ $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <section class="related-section">
            <div class="container">
                <h3>Mungkin Kamu Suka Juga</h3>
                <div class="product-grid" style="grid-template-columns: repeat(4, 1fr);">
                    @foreach($relatedProducts as $related)
                        <div class="product-card">
                            <a href="{{ route('product.show', $related->slug) }}">
                                <div class="product-card-image">
                                    @if($related->stock <= 0)
                                        <span class="product-badge out-of-stock">Stok Habis</span>
                                    @endif
                                    @if($related->media->first())
                                        <img src="{{ asset('storage/' . $related->media->first()->file_path) }}"
                                            alt="{{ $related->name }}">
                                    @else
                                        <div class="no-image">📷 No Image</div>
                                    @endif
                                </div>
                            </a>
                            <div class="product-card-body">
                                <div class="product-category">
                                    @if($related->category)
                                        <span class="cat">🏷️ {{ strtoupper($related->category->name) }}</span>
                                    @endif
                                    @if($related->country)
                                        <span class="country">{{ $related->country->flag ?? '' }} {{ $related->country->name }}</span>
                                    @endif
                                </div>
                                <h4>{{ $related->name }}</h4>
                                <div class="product-card-footer">
                                    <span class="product-price">Rp {{ number_format($related->price, 0, ',', '.') }}</span>
                                    <button class="add-to-cart-btn"
                                        onclick="addToCart({{ $related->id }}, 'product', '{{ addslashes($related->name) }}', {{ $related->price }}, '{{ $related->media->first() ? asset('storage/' . $related->media->first()->file_path) : '' }}')"
                                        {{ $related->stock <= 0 ? 'disabled' : '' }}>+</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @push('scripts')
        <script>
            function changeImage(thumb, src) {
                document.getElementById('mainImg').src = src;
                document.querySelectorAll('.product-thumbnail').forEach(t => t.classList.remove('active'));
                thumb.classList.add('active');
            }
        </script>
    @endpush
@endsection