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

                    {{-- Floating Share Button --}}
                    <button class="floating-share-btn" onclick="openShareModal()" title="Bagikan Produk">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="18" cy="5" r="3"></circle>
                            <circle cx="6" cy="12" r="3"></circle>
                            <circle cx="18" cy="19" r="3"></circle>
                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                        </svg>
                    </button>
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

    {{-- Share Modal Popup --}}
    <div id="shareModal" class="share-modal" onclick="closeShareModal(event)">
        <div class="share-modal-content" onclick="event.stopPropagation()">
            <div class="share-modal-header">
                <h3>🔗 Bagikan Produk</h3>
                <button class="share-modal-close" onclick="closeShareModal()">&times;</button>
            </div>
            <div class="share-modal-body">
                <p style="text-align: center; color: var(--gray-600); margin-bottom: 20px; font-size: 14px;">Mau bagikan
                    lewat mana hari ini?</p>
                <div class="share-buttons-grid">
                    <button class="share-btn share-whatsapp" onclick="shareProduct('whatsapp')" title="WhatsApp">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                        <span>WhatsApp</span>
                    </button>
                    <button class="share-btn share-instagram" onclick="shareProduct('instagram')" title="Instagram">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                        </svg>
                        <span>Instagram</span>
                    </button>
                    <button class="share-btn share-tiktok" onclick="shareProduct('tiktok')" title="TikTok">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z" />
                        </svg>
                        <span>TikTok</span>
                    </button>
                    <button class="share-btn share-facebook" onclick="shareProduct('facebook')" title="Facebook">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                        <span>Facebook</span>
                    </button>
                    <button class="share-btn share-twitter" onclick="shareProduct('twitter')" title="X / Twitter">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                        </svg>
                        <span>X</span>
                    </button>
                    <button class="share-btn share-copy" onclick="shareProduct('copy')" title="Salin Link">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <span>Salin Link</span>
                    </button>
                    <button class="share-btn share-email" onclick="shareProduct('email')" title="E-Mail">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <span>E-Mail</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function changeImage(thumb, src) {
                document.getElementById('mainImg').src = src;
                document.querySelectorAll('.product-thumbnail').forEach(t => t.classList.remove('active'));
                thumb.classList.add('active');
            }

            // Modal Functions
            function openShareModal() {
                document.getElementById('shareModal').classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeShareModal(event) {
                if (!event || event.target.id === 'shareModal' || event.target.classList.contains('share-modal-close')) {
                    document.getElementById('shareModal').classList.remove('active');
                    document.body.style.overflow = '';
                }
            }

            // Share Functions
            function shareProduct(platform) {
                const productName = "{{ addslashes($product->name) }}";
                const productPrice = "Rp {{ number_format($product->price, 0, ',', '.') }}";
                const productUrl = "{{ url()->current() }}";
                const shareText = `Check out ${productName} di ArimbiStore! Harga cuma ${productPrice}. ${productUrl}`;

                const emailSubject = encodeURIComponent(`Lihat ${productName} di ArimbiStore`);
                const emailBody = encodeURIComponent(shareText);

                switch (platform) {
                    case 'whatsapp':
                        window.open(`https://wa.me/?text=${encodeURIComponent(shareText)}`, '_blank');
                        break;
                    case 'instagram':
                        // Instagram doesn't have direct web share, use Web Share API or copy link
                        if (navigator.share) {
                            navigator.share({
                                title: productName,
                                text: `${productName} - ${productPrice}`,
                                url: productUrl
                            }).catch(() => {
                                copyToClipboard(productUrl);
                                closeShareModal();
                            });
                        } else {
                            copyToClipboard(productUrl);
                            closeShareModal();
                        }
                        break;
                    case 'tiktok':
                        // TikTok doesn't have simple web share, use Web Share API or copy link
                        if (navigator.share) {
                            navigator.share({
                                title: productName,
                                text: shareText,
                                url: productUrl
                            }).catch(() => {
                                copyToClipboard(productUrl);
                                closeShareModal();
                            });
                        } else {
                            copyToClipboard(productUrl);
                            closeShareModal();
                        }
                        break;
                    case 'facebook':
                        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(productUrl)}`, '_blank');
                        break;
                    case 'twitter':
                        window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(`${productName} - ${productPrice}`)}&url=${encodeURIComponent(productUrl)}`, '_blank');
                        break;
                    case 'copy':
                        copyToClipboard(productUrl);
                        closeShareModal();
                        break;
                    case 'email':
                        window.location.href = `mailto:?subject=${emailSubject}&body=${emailBody}`;
                        break;
                }
            }

            function copyToClipboard(text) {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(() => {
                        showToast('✓ Link berhasil disalin!');
                    }).catch(() => {
                        fallbackCopy(text);
                    });
                } else {
                    fallbackCopy(text);
                }
            }

            function fallbackCopy(text) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();

                try {
                    document.execCommand('copy');
                    showToast('✓ Link berhasil disalin!');
                } catch (err) {
                    showToast('❌ Gagal menyalin link');
                }

                document.body.removeChild(textarea);
            }

            function showToast(message) {
                const existing = document.querySelector('.toast-notification');
                if (existing) existing.remove();

                const toast = document.createElement('div');
                toast.className = 'toast-notification';
                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        toast.remove();
                    }
                }, 3000);
            }

            // Close modal on ESC key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && document.getElementById('shareModal').classList.contains('active')) {
                    closeShareModal();
                }
            });
        </script>
    @endpush
@endsection