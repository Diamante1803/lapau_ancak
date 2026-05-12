
@php
    $badgeConfig = match($status) {
        'draft'     => ['bg' => '#e8f5ee', 'color' => '#1a6b3c', 'label' => '📝 Draft'],
        'revision'  => ['bg' => '#fff3cd', 'color' => '#856404', 'label' => '🔄 Revisi'],
        'submitted' => ['bg' => '#d1ecf1', 'color' => '#0c5460', 'label' => '📤 Dikirim'],
        'approved'  => ['bg' => '#d4edda', 'color' => '#155724', 'label' => '✅ Disetujui'],
        'rejected'  => ['bg' => '#f8d7da', 'color' => '#721c24', 'label' => '❌ Ditolak'],
        default     => ['bg' => '#e9ecef', 'color' => '#6c757d', 'label' => $status],
    };
@endphp
<span class="badge px-3 py-2"
    style="border-radius:20px; font-size:0.78rem;
           background: {{ $badgeConfig['bg'] }};
           color: {{ $badgeConfig['color'] }};">
    {{ $badgeConfig['label'] }}
</span>