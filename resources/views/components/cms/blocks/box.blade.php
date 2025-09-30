<div class="cms-box-block py-8">
    <div class="flex gap-4">
        @foreach($content['boxes'] ?? [] as $box)
            <div class="box-card p-8 rounded-b-lg shadow-md flex-1 text-center" style="background-color: {{ $box['color'] ?? '#3b82f6' }}; color: white;">
                @if(isset($box['icon']))
                    <div class="icon-wrapper mb-4 mx-auto p-4 rounded-full w-20 h-20 flex items-center justify-center" style="background-color: {{ $box['color'] ?? '#3b82f6' }}80;">
                        <i class="{{ $box['icon'] }} text-white text-2xl"></i>
                    </div>
                @endif
                @if(isset($box['title']))
                    <h3 class="text-2xl font-bold mb-4 text-white">{{ $box['title'] }}</h3>
                @endif
                @if(isset($box['description']))
                    <div class="prose prose-sm max-w-none">
                        {!! $box['description'] !!}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>