@php
if (!function_exists('hex2rgb')) {
    function hex2rgb($hex, $alpha = 1) {
        $hex = str_replace("#", "", $hex ?? '#1a6b3c');
        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        return "$r, $g, $b, $alpha";
    }
}

if (!function_exists('hex2darker')) {
    function hex2darker($hex, $percent) {
        $hex = str_replace("#", "", $hex ?? '#1a6b3c');
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));

        $r = max(0, $r - $r * $percent / 100);
        $g = max(0, $g - $g * $percent / 100);
        $b = max(0, $b - $b * $percent / 100);

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
}

// Pastikan variabel $color memiliki default agar tidak error saat dipanggil di atas
$color = $color ?? '#1a6b3c';
@endphp

<div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius:16px; transition: all 0.3s;"
    onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 30px rgba({{ hex2rgb($color, 0.15) }})';"
    onmouseout="this.style.transform=''; this.style.boxShadow='';">
    <div class="card-body position-relative d-flex flex-column justify-content-between" style="z-index:1; padding: 1rem;">
        <div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,{{ $color }},{{ hex2darker($color, 20) }});display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba({{ hex2rgb($color, 0.2) }}); flex-shrink: 0;">
                        <i class="fas {{ $icon ?? 'fa-info-circle' }} text-white" style="font-size: 0.9rem;"></i>
                    </div>
                    <div class="text-muted" style="font-size:0.65rem;font-weight:700;letter-spacing:0.5px;text-transform:uppercase; line-height: 1.2;">
                        {{ $title }}
                    </div>
                </div>
                @isset($badge)
                <span style="background:rgba({{ hex2rgb($color, 0.1) }});color:{{ $color }};font-size:0.6rem;font-weight:800;padding:2px 8px;border-radius:20px;white-space: nowrap;">
                    {!! $badge !!}
                </span>
                @endisset
            </div>
            
            <div style="font-size:1.6rem;font-weight:800;color:#1e293b;letter-spacing:-0.5px;line-height:1.1;">
                {{ $value }}
                @isset($unit)
                <span class="text-muted" style="font-size:0.75rem;font-weight:600;margin-left:2px;">{{ $unit }}</span>
                @endisset
            </div>
        </div>

        @isset($description)

        <div class="mt-1" style="font-size:0.72rem;color:#6c757d;">
            {!! $description !!}
        </div>
        @endisset
        {{ $slot }}
    </div>
    <div style="position:absolute;right:-10px;bottom:-10px;font-size:3.5rem;opacity:0.04;color:{{ $color ?? '#1a6b3c' }};transform:rotate(-15deg);pointer-events:none;">
        <i class="fas {{ $icon ?? 'fa-info-circle' }}"></i>
    </div>
</div>
