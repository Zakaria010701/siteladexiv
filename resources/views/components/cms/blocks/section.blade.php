<div class="cms-section">
    @php
        // Get data from the $content variable passed by the CMS builder
        $content = $content ?? [];
        $title = $content['title'] ?? '';
        $backgroundType = $content['background_type'] ?? 'transparent';
        $backgroundColor = $content['background_color'] ?? '#ffffff';
        $gradientType = $content['gradient_type'] ?? 'primary';
        $paddingTop = $content['padding_top'] ?? 8;
        $paddingBottom = $content['padding_bottom'] ?? 8;
        $marginTop = $content['margin_top'] ?? 0;
        $marginBottom = $content['margin_bottom'] ?? 0;
        $borderStyle = $content['border_style'] ?? 'none';
        $borderColor = $content['border_color'] ?? '#3991B3';
        $cornerRadius = $content['corner_radius'] ?? 'lg';
        $shadowStyle = $content['shadow_style'] ?? 'md';

        // Define gradient styles
        $gradients = [
            'primary' => 'linear-gradient(135deg, #3991B3 0%, #4da6c7 50%, #5db3d4 100%)',
            'medical' => 'linear-gradient(135deg, #3991B3 0%, #ffffff 50%, #3991B3 100%)',
            'warm' => 'linear-gradient(135deg, #f97316 0%, #ec4899 50%, #8b5cf6 100%)',
            'cool' => 'linear-gradient(135deg, #14b8a6 0%, #3b82f6 50%, #6366f1 100%)',
            'rainbow' => 'linear-gradient(135deg, #ff0000 0%, #ff7f00 14%, #ffff00 28%, #00ff00 42%, #0000ff 57%, #4b0082 71%, #9400d3 85%, #ff0000 100%)',
        ];

        // Define shadow styles
        $shadows = [
            'none' => '',
            'sm' => '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
            'md' => '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
            'lg' => '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
            'xl' => '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
        ];

        // Define corner radius styles
        $corners = [
            'none' => '0',
            'sm' => '0.25rem',
            'md' => '0.5rem',
            'lg' => '0.75rem',
            'xl' => '1rem',
            'full' => '9999px',
        ];

        // Build background style
        $backgroundStyle = '';
        if ($backgroundType === 'solid') {
            $backgroundStyle = "background-color: {$backgroundColor};";
        } elseif ($backgroundType === 'gradient') {
            $backgroundStyle = "background: {$gradients[$gradientType]};";
        } elseif ($backgroundType === 'glass') {
            $backgroundStyle = "background: rgba({$backgroundColor}, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1);";
        }

        // Build border style
        $borderStyleValue = '';
        if ($borderStyle === 'solid') {
            $borderStyleValue = "border: 2px solid {$borderColor};";
        } elseif ($borderStyle === 'dashed') {
            $borderStyleValue = "border: 2px dashed {$borderColor};";
        } elseif ($borderStyle === 'gradient') {
            $borderStyleValue = "border: 2px solid; border-image: {$gradients[$gradientType]} 1;";
        }

        // Build shadow style
        $shadowStyleValue = '';
        if ($shadowStyle !== 'none') {
            $shadowStyleValue = "box-shadow: {$shadows[$shadowStyle]};";
        }

        // Build corner radius style
        $cornerStyle = '';
        if ($cornerRadius !== 'none') {
            $cornerStyle = "border-radius: {$corners[$cornerRadius]};";
        }

        // Build spacing styles
        $spacingStyle = "padding-top: {$paddingTop}rem; padding-bottom: {$paddingBottom}rem; margin-top: {$marginTop}rem; margin-bottom: {$marginBottom}rem;";
    @endphp

    <div class="section-container section-animate-in {{ $backgroundType === 'gradient' ? 'gradient' : '' }} {{ $backgroundType === 'glass' ? 'glass' : '' }} {{ $backgroundType === 'solid' ? 'solid' : '' }}"
         style="{{ $spacingStyle }} {{ $backgroundStyle }} {{ $borderStyleValue }} {{ $shadowStyleValue }} {{ $cornerStyle }}">
        @if($title)
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-teal-400 bg-clip-text text-transparent mb-4 animate-fade-in relative z-10">
                    {{ $title }}
                </h2>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-400 to-teal-400 mx-auto rounded-full relative z-10"></div>
            </div>
        @endif

        <!-- This section will contain other blocks -->
        <div class="section-content relative z-10">
            <!-- Child blocks will be rendered here -->
        </div>
    </div>
</div>