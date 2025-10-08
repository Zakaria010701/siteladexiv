@props(['heading' => '', 'left_column' => '', 'right_column' => ''])

<section class="cms-block">
    <div class="container mx-auto px-6">
        @if($heading)
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">
                {{ $heading }}
            </h2>
        @endif

        <div class="grid md:grid-cols-2 gap-8 max-w-6xl mx-auto">
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="prose prose-lg max-w-none text-gray-700">
                    {!! nl2br(e($left_column)) !!}
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="prose prose-lg max-w-none text-gray-700">
                    {!! nl2br(e($right_column)) !!}
                </div>
            </div>
        </div>
    </div>
</section>