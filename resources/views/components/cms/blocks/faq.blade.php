@php
    $data = $content ?? [];
    $faqs = $data['faqs'] ?? [];
    $title = $data['title'] ?? null;
@endphp

@if(count($faqs) > 0)
<div class="cms-faq-block py-8">
    @if($title)
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold" style="color: #2563eb;">{{ $title }}</h2>
        </div>
    @endif

    <div class="max-w-4xl mx-auto">
        <div class="space-y-4">
            @foreach($faqs as $index => $faq)
                @php
                    $question = $faq['question'] ?? '';
                    $answer = $faq['answer'] ?? '';
                @endphp

                @if($question && $answer)
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                        <button
                            class="faq-question w-full text-left p-6 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition-colors duration-200"
                            onclick="toggleFaq('faq-{{ $index }}')"
                            style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="text-lg font-semibold pr-4" style="color: #2563eb;">{{ $question }}</span>
                            <svg
                                id="icon-faq-{{ $index }}"
                                class="faq-icon w-6 h-6 transform transition-transform duration-200"
                                style="color: #2563eb;"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            id="faq-{{ $index }}"
                            class="faq-answer hidden p-6 bg-white"
                            style="border-top: 1px solid #e5e7eb;">
                            <div class="prose prose-lg max-w-none">
                                {!! nl2br(e($answer)) !!}
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

<script>
function toggleFaq(faqId) {
    const answer = document.getElementById(faqId);
    const icon = document.getElementById('icon-' + faqId);

    if (answer.classList.contains('hidden')) {
        answer.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        answer.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>

<style>
.faq-question:hover {
    cursor: pointer;
}

.faq-icon {
    flex-shrink: 0;
}
</style>
@endif