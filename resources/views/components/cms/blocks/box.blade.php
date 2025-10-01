<div class="cms-box-block py-8">
    <div class="flex gap-4">
        @foreach($content['boxes'] ?? [] as $box)
            <div class="box-card p-8 rounded-b-lg shadow-md flex-1 text-center" style="background-color: {{ $box['color'] ?? '#3b82f6' }}; color: white; position: relative;">
                @if(isset($box['icon']))
                    <div class="icon-wrapper mb-4 mx-auto p-4 rounded-full w-20 h-20 flex items-center justify-center" style="background-color: {{ $box['color'] ?? '#3b82f6' }}80;">
                        <i class="{{ $box['icon'] }} text-white text-2xl"></i>
                    </div>
                @endif
                @if(isset($box['title']))
                    <h3 class="text-2xl font-bold mb-4 text-white">{{ $box['title'] }}</h3>
                @endif
                @if(isset($box['description']))
                    <div class="prose prose-sm max-w-none table-container" style="width: 100% !important; overflow-x: auto !important;">
                        {!! preg_replace_callback('/<table([^>]*)>/i', function($matches) {
                            $table = $matches[0];
                            // Remove existing style and width attributes if present
                            $table = preg_replace('/style="[^"]*"/i', '', $table);
                            $table = preg_replace('/width="[^"]*"/i', '', $table);
                            // Add new style and width attributes with blue background
                            $table = preg_replace('/<table([^>]*)>/i', '<table$1 style="width: 100% !important; table-layout: fixed !important; border-collapse: separate !important; border-spacing: 0 !important; background: #3991B3 !important; border-radius: 8px !important; overflow: hidden !important; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important; border: 1px solid #3991B3 !important;" width="100%">', $table);
                            return $table;
                        }, preg_replace_callback('/<td([^>]*)>/i', function($matches) {
                            $td = $matches[0];
                            // Remove existing style attributes
                            $td = preg_replace('/style="[^"]*"/i', '', $td);
                            // Add proper styling with white text and blue background
                            $td = preg_replace('/<td([^>]*)>/i', '<td$1 style="padding: 12px 16px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important; border-right: 1px solid rgba(255, 255, 255, 0.2) !important; color: white !important; background: #3991B3 !important; font-weight: 600 !important;">', $td);
                            return $td;
                        }, preg_replace_callback('/<td([^>]*)>(.*)<\/td>/i', function($matches) {
                            $td = $matches[0];
                            $content = $matches[2];
                            // If this is the last td in a row, make it right-aligned (times column)
                            if (strpos($td, '<td') !== false && substr_count($td, '<td') === 1) {
                                $td = preg_replace('/<td([^>]*)>/i', '<td$1 style="text-align: right !important; padding: 12px 16px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important; color: white !important; background: #3991B3 !important; font-weight: 400 !important;">', $td);
                            }
                            return $td;
                        }, $box['description']))) !!}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>