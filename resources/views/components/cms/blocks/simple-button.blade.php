@php
    // Get data from content (Filament Builder Block way)
    $data = $content ?? [];
    $buttons = $data['buttons'] ?? [];

    // If single button format (legacy support), convert to array format
    if (isset($data['button_text']) && !isset($data['buttons'])) {
        $buttons = [[
            'button_text' => $data['button_text'] ?? 'Click here',
            'button_title' => $data['button_title'] ?? null,
            'button_description' => $data['button_description'] ?? null,
            'target_page_id' => $data['target_page_id'] ?? null,
            'button_style' => $data['button_style'] ?? 'primary',
        ]];
    }

    // Define button styles based on style
    $getButtonStyles = function($style) {
        return match($style) {
            'primary' => 'background-color: #2563eb; color: white; padding: 12px 24px; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-block; border: none; cursor: pointer; transition: background-color 0.2s ease;',
            'secondary' => 'background-color: #4b5563; color: white; padding: 12px 24px; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-block; border: none; cursor: pointer; transition: background-color 0.2s ease;',
            'outline' => 'background-color: transparent; color: #2563eb; padding: 12px 24px; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-block; border: 2px solid #2563eb; cursor: pointer; transition: all 0.2s ease;',
            default => 'background-color: #2563eb; color: white; padding: 12px 24px; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-block; border: none; cursor: pointer; transition: background-color 0.2s ease;'
        };
    };

    $getHoverStyles = function($style) {
        return match($style) {
            'primary' => 'onmouseover="this.style.backgroundColor=\'#1d4ed8\'" onmouseout="this.style.backgroundColor=\'#2563eb\'"',
            'secondary' => 'onmouseover="this.style.backgroundColor=\'#374151\'" onmouseout="this.style.backgroundColor=\'#4b5563\'"',
            'outline' => 'onmouseover="this.style.backgroundColor=\'#2563eb\'; this.style.color=\'white\'" onmouseout="this.style.backgroundColor=\'transparent\'; this.style.color=\'#2563eb\'"',
            default => 'onmouseover="this.style.backgroundColor=\'#1d4ed8\'" onmouseout="this.style.backgroundColor=\'#2563eb\'"'
        };
    };
@endphp

@if(count($buttons) > 0)
<div class="cms-simple-button py-8">
    <div class="flex flex-wrap justify-center gap-6">
        @foreach($buttons as $button)
            @php
                $button_text = $button['button_text'] ?? 'Click here';
                $button_title = $button['button_title'] ?? null;
                $button_description = $button['button_description'] ?? null;
                $target_page_id = $button['target_page_id'] ?? null;
                $button_style = $button['button_style'] ?? 'primary';

                // Find target page
                $targetPage = null;
                if ($target_page_id) {
                    $targetPage = \App\Models\CmsPage::find($target_page_id);
                }

                // Determine URL priority: custom_url > target_page > fallback to '#'
                $custom_url = $button['custom_url'] ?? null;
                if ($custom_url) {
                    $url = $custom_url;
                } elseif ($targetPage) {
                    $url = route('cms.page', ['slug' => $targetPage->slug]);
                } else {
                    $url = '#';
                }

                $buttonStyles = $getButtonStyles($button_style);
                $hoverStyles = $getHoverStyles($button_style);
            @endphp

            <div class="text-center" style="flex: 0 0 auto; display: flex; flex-direction: column; align-items: center;">
                @if($button_title)
                    <h3 class="text-xl font-bold mb-3" style="color: #2563eb;">{{ $button_title }}</h3>
                @endif

                @php
                    $isDownload = $custom_url && (str_contains(strtolower($custom_url), 'download') ||
                                   str_contains($custom_url, '.pdf') ||
                                   str_contains($custom_url, '.doc') ||
                                   str_contains($custom_url, '.docx') ||
                                   str_contains($custom_url, '.zip') ||
                                   str_contains($custom_url, '.rar'));
                    $arrowIcon = $isDownload ? 'M12 10v6m0 0l4-4m-4 4l-4-4' : 'M9 5l7 7-7 7';
                @endphp

                <div style="display: inline-block;">
                    <a href="{{ $url }}"
                       style="{{ $buttonStyles }}; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px; line-height: 20px;"
                       {{ $hoverStyles }}
                       @if(!$targetPage && !$custom_url) onclick="return false;" @endif
                       @if($custom_url) target="_blank" rel="noopener noreferrer" @endif
                       @if($isDownload) download @endif>
                        <span>{{ $button_text }}</span>
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $arrowIcon }}"/>
                        </svg>
                    </a>
                </div>

                @if($button_description)
                    <p class="text-sm mt-3 max-w-xs leading-relaxed" style="color: #4b5563; text-align: center;">{{ $button_description }}</p>
                @endif

                @if(!$targetPage && $target_page_id && !$custom_url)
                    <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">Warning: Target page not found</p>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif