<div class="cms-block animate-fade-in">
    <div class="max-w-4xl mx-auto">
        @if(isset($content['title']) && $content['title'])
        <div class="text-center mb-8">
            <h2 class="text-4xl font-bold mb-4 bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">{{ $content['title'] }}</h2>
            @if(isset($content['description']) && $content['description'])
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">{{ $content['description'] }}</p>
            @endif
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <form action="{{ route('cms.contact-form.submit') }}" method="POST" class="space-y-6">
                @csrf
                <div class="{{ $content['layout'] === 'two_columns' ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : 'space-y-6' }}">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-Mail *</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    </div>
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Betreff</label>
                    <input type="text" id="subject" name="subject"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Nachricht *</label>
                    <textarea id="message" name="message" rows="6" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-vertical"></textarea>
                </div>

                <div class="text-center pt-4">
                    <button type="submit"
                            class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-200 transform hover:scale-105 hover:shadow-lg">
                        {{ $content['submit_button_text'] ?? 'Nachricht senden' }}
                    </button>
                </div>

                <!-- Hidden field to pass the recipient email -->
                <input type="hidden" name="email_to" value="{{ $content['email_to'] ?? 'info@example.com' }}">
            </form>
        </div>
    </div>
</div>