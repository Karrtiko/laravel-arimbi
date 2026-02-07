<!DOCTYPE html>
<html>
<head>
    <title>Watermark Settings</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 10px; color: #333; }
        .subtitle { color: #666; margin-bottom: 30px; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .form-group { margin-bottom: 24px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        input[type="range"] { width: 100%; }
        input[type="checkbox"] { width: auto; margin-right: 8px; }
        .range-value { display: inline-block; min-width: 60px; margin-left: 10px; color: #666; }
        .position-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; max-width: 300px; }
        .position-btn { padding: 16px; border: 2px solid #ddd; background: white; border-radius: 8px; cursor: pointer; transition: all 0.2s; }
        .position-btn:hover { border-color: #8b5cf6; background: #f3f4f6; }
        .position-btn.active { border-color: #8b5cf6; background: #ede9fe; }
        .btn { padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.2s; }
        .btn-primary { background: #8b5cf6; color: white; }
        .btn-primary:hover { background: #7c3aed; }
        .btn-secondary { background: #6b7280; color: white; margin-left: 10px; }
        .btn-secondary:hover { background: #4b5563; }
        .btn-danger { background: #ef4444; color: white; margin-left: 10px; }
        .btn-danger:hover { background: #dc2626; }
        .preview-box { margin-top: 20px; padding: 20px; background: #f9fafb; border-radius: 8px; text-align: center; }
        .toggle { display: flex; align-items: center; gap: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚙️ Pengaturan Watermark</h1>
        <p class="subtitle">Kelola watermark untuk gambar produk Anda</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('watermark.update') }}">
            @csrf
            
            <div class="form-group">
                <div class="toggle">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ $settings->is_active ? 'checked' : '' }}>
                    <label for="is_active" style="margin: 0;">Aktifkan Watermark</label>
                </div>
            </div>

            <div class="form-group">
                <label>Text Watermark</label>
                <input type="text" name="text" value="{{ $settings->text }}" required>
            </div>

            <div class="form-group">
                <label>Font</label>
                <select name="font_family">
                    @foreach(['Arial', 'Verdana', 'Georgia', 'Times New Roman', 'Courier New'] as $font)
                        <option value="{{ $font }}" {{ $settings->font_family == $font ? 'selected' : '' }}>{{ $font }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Ukuran Font</label>
                <input type="range" name="font_size" id="font_size" min="10" max="200" value="{{ $settings->font_size }}" oninput="document.getElementById('font_size_val').textContent = this.value + 'px'">
                <span class="range-value" id="font_size_val">{{ $settings->font_size }}px</span>
            </div>

            <div class="form-group">
                <label>Unit Ukuran</label>
                <select name="font_size_unit">
                    <option value="px" {{ $settings->font_size_unit == 'px' ? 'selected' : '' }}>Pixel (px)</option>
                    <option value="percent" {{ $settings->font_size_unit == 'percent' ? 'selected' : '' }}>Persen (%)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Posisi</label>
                <div class="position-grid">
                    @foreach(['top-left' => '↖', 'top-center' => '↑', 'top-right' => '↗', 
                              'center' => '●', '', '',
                              'bottom-left' => '↙', 'bottom-center' => '↓', 'bottom-right' => '↘'] as $pos => $icon)
                        @if($pos)
                            <button type="button" class="position-btn {{ $settings->position == $pos ? 'active' : '' }}" 
                                    onclick="selectPosition('{{ $pos }}', this)">
                                {{ $icon }}
                            </button>
                        @else
                            <div></div>
                        @endif
                    @endforeach
                </div>
                <input type="hidden" name="position" id="position" value="{{ $settings->position }}">
            </div>

            <div class="form-group">
                <label>Opacity</label>
                <input type="range" name="opacity" id="opacity" min="10" max="100" value="{{ $settings->opacity }}" oninput="document.getElementById('opacity_val').textContent = this.value + '%'">
                <span class="range-value" id="opacity_val">{{ $settings->opacity }}%</span>
            </div>

            <div class="form-group">
                <label>Warna</label>
                <input type="text" name="color" value="{{ $settings->color }}" pattern="#[0-9A-Fa-f]{6}" placeholder="#FFFFFF">
            </div>

            <div class="form-group">
                <div class="toggle">
                    <input type="checkbox" name="shadow" id="shadow" value="1" {{ $settings->shadow ? 'checked' : '' }}>
                    <label for="shadow" style="margin: 0;">Tambahkan Shadow</label>
                </div>
            </div>

            <div class="form-group">
                <label>Sudut Rotasi</label>
                <input type="range" name="angle" id="angle" min="0" max="360" value="{{ $settings->angle }}" oninput="document.getElementById('angle_val').textContent = this.value + '°'">
                <span class="range-value" id="angle_val">{{ $settings->angle }}°</span>
            </div>

            <button type="submit" class="btn btn-primary">💾 Simpan Pengaturan</button>
            <button type="button" class="btn btn-secondary" onclick="if(confirm('Clear semua cache watermark?')) document.getElementById('clear-form').submit();">🔄 Clear Cache</button>
            <button type="button" class="btn btn-danger" onclick="if(confirm('Hapus gambar tidak terpakai?')) document.getElementById('cleanup-form').submit();">🗑️ Cleanup Unused</button>
        </form>

        <form id="clear-form" method="POST" action="{{ route('watermark.clear') }}" style="display:none;">@csrf</form>
        <form id="cleanup-form" method="POST" action="{{ route('watermark.cleanup') }}" style="display:none;">@csrf</form>

        <div class="preview-box">
            <p><strong>Preview akan muncul saat mengakses gambar produk</strong></p>
            <p style="color: #666; font-size: 14px;">Watermark akan diterapkan secara otomatis pada /storage/watermarked/*</p>
        </div>
    </div>

    <script>
        function selectPosition(pos, btn) {
            document.querySelectorAll('.position-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('position').value = pos;
        }
    </script>
</body>
</html>
