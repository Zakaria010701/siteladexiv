@php
    // Get data from the $content variable passed by the CMS builder
    $content = $content ?? [];
    $tabs = $content['tabs'] ?? [];
    $title = $content['title'] ?? '';

    // Tab configuration
    $tabStyle = $content['tab_style'] ?? 'underline';
    $tabPosition = $content['tab_position'] ?? 'top';

    // Colors
    $activeTabColor = $content['active_tab_color'] ?? '#3991B3';
    $inactiveTabColor = $content['inactive_tab_color'] ?? '#6b7280';
    $contentBackgroundColor = $content['content_background_color'] ?? '#ffffff';

    // Spacing
    $paddingTop = $content['padding_top'] ?? 4;
    $paddingBottom = $content['padding_bottom'] ?? 4;
    $marginTop = $content['margin_top'] ?? 0;
    $marginBottom = $content['margin_bottom'] ?? 0;

    // Styling
    $borderStyle = $content['border_style'] ?? 'none';
    $borderColor = $content['border_color'] ?? '#e5e7eb';
    $cornerRadius = $content['corner_radius'] ?? 'md';
    $shadowStyle = $content['shadow_style'] ?? 'sm';

    // Define corner radius styles
    $corners = [
        'none' => '0',
        'sm' => '0.25rem',
        'md' => '0.5rem',
        'lg' => '0.75rem',
        'xl' => '1rem',
        'full' => '9999px',
    ];

    // Define shadow styles
    $shadows = [
        'none' => '',
        'sm' => '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
        'md' => '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
        'lg' => '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
        'xl' => '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
    ];

    // Build border style
    $borderStyleValue = '';
    if ($borderStyle === 'solid') {
        $borderStyleValue = "border: 1px solid {$borderColor};";
    } elseif ($borderStyle === 'dashed') {
        $borderStyleValue = "border: 1px dashed {$borderColor};";
    }

    // Build corner radius style
    $cornerStyle = '';
    if ($cornerRadius !== 'none') {
        $cornerStyle = "border-radius: {$corners[$cornerRadius]};";
    }

    // Build shadow style
    $shadowStyleValue = '';
    if ($shadowStyle !== 'none') {
        $shadowStyleValue = "box-shadow: {$shadows[$shadowStyle]};";
    }

    // Build spacing styles
    $spacingStyle = "padding-top: {$paddingTop}rem; padding-bottom: {$paddingBottom}rem; margin-top: {$marginTop}rem; margin-bottom: {$marginBottom}rem;";

    // Default tabs if none provided
    if (empty($tabs)) {
        $tabs = [
            [
                'tab_title' => 'Tab 1',
                'tab_content' => 'Content for tab 1...',
                'custom_color' => null,
            ],
            [
                'tab_title' => 'Tab 2',
                'tab_content' => 'Content for tab 2...',
                'custom_color' => null,
            ],
        ];
    }

    // Generate unique ID for this tabs instance
    $uniqueId = 'tabs-' . uniqid();
@endphp

<div class="tabs-container" style="{{ $spacingStyle }} {{ $borderStyleValue }} {{ $cornerStyle }} {{ $shadowStyleValue }}">
    @if($title)
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-teal-400 bg-clip-text text-transparent mb-4">
                {{ $title }}
            </h2>
        </div>
    @endif

    <div class="statistics-wrapper">
        <div class="statistics-grid grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach($tabs as $index => $tab)
                <div class="statistic-card text-center group cursor-pointer"
                     data-tab="{{ $index }}"
                     style="background-color: {{ $contentBackgroundColor }}; {{ $cornerStyle }} {{ $shadowStyleValue }}">

                    {{-- Statistic Number --}}
                    <div class="statistic-number text-4xl md:text-5xl font-bold mb-2 group-hover:scale-110 transition-transform duration-300"
                         style="color: {{ $activeTabColor }};">
                        {{ $tab['tab_title'] }}
                    </div>

                    {{-- Statistic Label --}}
                    <div class="statistic-label text-sm md:text-base font-medium"
                         style="color: {{ $inactiveTabColor }};">
                        {!! nl2br(e($tab['tab_content'])) !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
/* Statistics Cards Styles */
.tabs-container {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.statistics-grid {
    display: grid;
    gap: 1.5rem;
    width: 100%;
}

.statistic-card {
    padding: 2rem 1rem;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: {{ $contentBackgroundColor }} !important;
}

.statistic-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border-color: {{ $activeTabColor }};
}

.statistic-number {
    font-size: 3rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 0.5rem;
    color: {{ $activeTabColor }} !important;
}

.statistic-label {
    font-size: 0.875rem;
    font-weight: 500;
    line-height: 1.4;
    color: {{ $inactiveTabColor }} !important;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Responsive Design */
@media (max-width: 768px) {
    .statistics-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1rem;
    }

    .statistic-card {
        padding: 1.5rem 0.75rem;
    }

    .statistic-number {
        font-size: 2rem;
    }

    .statistic-label {
        font-size: 0.75rem;
    }
}

@media (max-width: 480px) {
    .statistics-grid {
        grid-template-columns: 1fr !important;
    }
}

/* Dark theme support */
@media (prefers-color-scheme: dark) {
    .statistic-card {
        background: rgba(17, 24, 39, 0.8) !important;
        backdrop-filter: blur(10px);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Statistics cards hover effects and animations
    const cards = document.querySelectorAll('.statistic-card');

    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
</script>