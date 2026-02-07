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

                    {{-- Floating Share Button --}}
                    <button class="floating-share-btn" onclick="openShareModal()" title="Bagikan Paket">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="18" cy="5" r="3"></circle>
                            <circle cx="6" cy="12" r="3"></circle>
                            <circle cx="18" cy="19" r="3"></circle>
                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                        </svg>
                    </button>
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

                {{-- Social Share Buttons --}}
                <div class="share-section">
                    <p class="share-label">🔗 Bagikan Paket</p>
                    <div class="share-buttons">
                        <button class="share-btn share-whatsapp" onclick="shareBundle('whatsapp')" title="WhatsApp">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            <span>WhatsApp</span>
                        </button>
                        <button class="share-btn share-telegram" onclick="shareBundle('telegram')" title="Telegram">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18 1.897-.962 6.502-1.359 8.627-.168.9-.5 1.201-.82 1.23-.697.064-1.226-.461-1.901-.903-1.056-.692-1.653-1.123-2.678-1.799-1.185-.781-.417-1.21.258-1.911.177-.184 3.247-2.977 3.307-3.23.007-.032.015-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.139-5.062 3.345-.479.329-.913.489-1.302.481-.428-.009-1.252-.241-1.865-.44-.752-.244-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.831-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635.099-.002.321.023.465.14.121.099.155.232.171.353.016.12.036.395.02.609z" />
                            </svg>
                            <span>Telegram</span>
                        </button>
                        <button class="share-btn share-line" onclick="shareBundle('line')" title="Line">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314" />
                            </svg>
                            <span>Line</span>
                        </button>
                        <button class="share-btn share-facebook" onclick="shareBundle('facebook')" title="Facebook">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                            <span>Facebook</span>
                        </button>
                        <button class="share-btn share-twitter" onclick="shareBundle('twitter')" title="X / Twitter">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                            <span>X</span>
                        </button>
                        <button class="share-btn share-copy" onclick="shareBundle('copy')" title="Salin Link">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                            <span>Salin Link</span>
                        </button>
                        <button class="share-btn share-email" onclick="shareBundle('email')" title="E-Mail">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                </path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <span>E-Mail</span>
                        </button>
                    </div>
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

    {{-- Share Modal Popup --}}
    <div id="shareModal" class="share-modal" onclick="closeShareModal(event)">
        <div class="share-modal-content" onclick="event.stopPropagation()">
            <div class="share-modal-header">
                <h3>🔗 Bagikan Paket</h3>
                <button class="share-modal-close" onclick="closeShareModal()">&times;</button>
            </div>
            <div class="share-modal-body">
                <p style="text-align: center; color: var(--gray-600); margin-bottom: 20px; font-size: 14px;">Mau bagikan
                    lewat mana hari ini?</p>
                <div class="share-buttons-grid">
                    <button class="share-btn share-whatsapp" onclick="shareBundle('whatsapp')" title="WhatsApp">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                        <span>WhatsApp</span>
                    </button>
                    <button class="share-btn share-instagram" onclick="shareBundle('instagram')" title="Instagram">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                        </svg>
                        <span>Instagram</span>
                    </button>
                    <button class="share-btn share-tiktok" onclick="shareBundle('tiktok')" title="TikTok">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z" />
                        </svg>
                        <span>TikTok</span>
                    </button>
                    <button class="share-btn share-facebook" onclick="shareBundle('facebook')" title="Facebook">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                        <span>Facebook</span>
                    </button>
                    <button class="share-btn share-twitter" onclick="shareBundle('twitter')" title="X / Twitter">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                        </svg>
                        <span>X</span>
                    </button>
                    <button class="share-btn share-copy" onclick="shareBundle('copy')" title="Salin Link">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <span>Salin Link</span>
                    </button>
                    <button class="share-btn share-email" onclick="shareBundle('email')" title="E-Mail">
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

            // Close modal on ESC key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && document.getElementById('shareModal').classList.contains('active')) {
                    closeShareModal();
                }
            });

            <script>
                function shareBundle(platform) {
                        const bundleName = "{{ addslashes($bundle->name) }}";
                const bundlePrice = "Rp {{ number_format($bundle->price, 0, ',', '.') }}";
                const bundleUrl = "{{ url()->current() }}";
                const shareText = `Check out ${bundleName} di ArimbiStore! Harga cuma ${bundlePrice}. ${bundleUrl}`;

                const emailSubject = encodeURIComponent(`Lihat ${bundleName} di ArimbiStore`);
                const emailBody = encodeURIComponent(shareText);

                switch(platform) {
                            case 'whatsapp':
                window.open(`https://wa.me/?text=${encodeURIComponent(shareText)}`, '_blank');
                break;
                case 'instagram':
                if (navigator.share) {
                    navigator.share({
                        title: bundleName,
                        text: `${bundleName} - ${bundlePrice}`,
                        url: bundleUrl
                    }).catch(() => {
                        copyToClipboard(bundleUrl);
                        closeShareModal();
                    });
                                } else {
                    copyToClipboard(bundleUrl);
                closeShareModal();
                                }
                break;
                case 'tiktok':
                if (navigator.share) {
                    navigator.share({
                        title: bundleName,
                        text: shareText,
                        url: bundleUrl
                    }).catch(() => {
                        copyToClipboard(bundleUrl);
                        closeShareModal();
                    });
                                } else {
                    copyToClipboard(bundleUrl);
                closeShareModal();
                                }
                break;
                case 'facebook':
                window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(bundleUrl)}`, '_blank');
                break;
                case 'twitter':
                window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(`${bundleName} - ${bundlePrice}`)}&url=${encodeURIComponent(bundleUrl)}`, '_blank');
                break;
                case 'copy':
                copyToClipboard(bundleUrl);
                break;
                case 'email':
                window.location.href = `mailto:?subject=${emailSubject}&body=${emailBody}`;
                break;
                default:
                if (navigator.share) {
                    navigator.share({
                        title: bundleName,
                        text: `${bundleName} - ${bundlePrice}`,
                        url: bundleUrl
                    }).catch(() => { });
                                }
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
        </script>
    @endpush
@endsection