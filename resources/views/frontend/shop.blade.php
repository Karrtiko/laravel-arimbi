@extends('layouts.frontend')

@section('title', 'Shop - ArimbiStore.ID')

@section('content')
    <!-- Shop Header -->
    <section style="padding: 60px 0; background: var(--gray-50); text-align: center;">
        <div class="container">
            <h1>Eksplorasi Rasa & Perawatan.</h1>
            <p style="color: var(--gray-600); margin-top: 12px; max-width: 600px; margin-left: auto; margin-right: auto;">
                Lagi nyari cemilan luar negeri yang viral atau skincare harian yang cocok di kulit? Cari di sini, tinggal
                klik, langsung bungkus lewat WA.
            </p>

            <!-- Search -->
            <form action="{{ route('shop') }}" method="GET" style="margin-top: 32px;">
                <div class="search-box">
                    <input type="text" name="q" placeholder="Lagi pengen snack apa hari ini?" value="{{ request('q') }}">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Filters -->
    <section style="padding: 40px 0;">
        <div class="container">
            <!-- Country Filter -->
            <div style="margin-bottom: 24px;">
                <p
                    style="font-size: 12px; color: var(--gray-500); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;">
                    ASAL NEGARA:</p>
                <div class="country-tabs" style="justify-content: flex-start; gap: 16px;">
                    <a href="{{ route('shop', array_merge(request()->except('country'), [])) }}"
                        class="country-tab {{ !request('country') ? 'active' : '' }}">
                        <span class="flag"
                            style="background: linear-gradient(135deg, #8b5cf6, #6366f1); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px;">✦</span>
                        <span>Semua</span>
                    </a>
                    @foreach($countries as $country)
                        <a href="{{ route('shop', array_merge(request()->except('country'), ['country' => $country->code])) }}"
                            class="country-tab {{ request('country') == $country->code ? 'active' : '' }}">
                            <span class="flag">{{ $country->flag ?? '🌍' }}</span>
                            <span>{{ $country->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Category Filter -->
            <div>
                <p
                    style="font-size: 12px; color: var(--gray-500); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;">
                    KATEGORI:</p>
                <div class="category-pills">
                    <a href="{{ route('shop', array_merge(request()->except('category'), [])) }}"
                        class="category-pill {{ !request('category') ? 'active' : '' }}">Semua</a>
                    @foreach($categories as $category)
                        <a href="{{ route('shop', array_merge(request()->except('category'), ['category' => $category->slug])) }}"
                            class="category-pill {{ request('category') == $category->slug ? 'active' : '' }}">{{ $category->name }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section style="padding-bottom: 60px;">
        <div class="container">
            <div class="product-grid">
                @forelse($products as $product)
                    <div class="product-card">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <div class="product-card-image">
                                @if($product->stock <= 0)
                                    <span class="product-badge out-of-stock">Stok Habis</span>
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
                                    <span class="cat">🏷️ {{ strtoupper($product->category->name) }}</span>
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
                        <div class="icon">🔍</div>
                        <p>Tidak ada produk ditemukan</p>
                        <a href="{{ route('shop') }}" class="btn btn-secondary mt-4">Reset Filter</a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div style="margin-top: 40px; text-align: center;">
                    <p style="color: var(--gray-500); font-size: 14px;">
                        Menampilkan {{ $products->firstItem() }}-{{ $products->lastItem() }} dari {{ $products->total() }}
                        produk
                    </p>
                    <div style="margin-top: 16px;">
                        {{ $products->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection