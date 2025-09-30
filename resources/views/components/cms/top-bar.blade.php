<?php
use App\Models\HeaderContact;

$headerContact = HeaderContact::getDefault();
?>

@isset($headerContact)
<div class="text-white py-2" style="background-color: #3991B3;">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center">
            <div class="text-sm">
                {{ $headerContact->welcome_text ?? 'Welcome to our website' }}
            </div>
            <div class="flex items-center space-x-6 text-sm">
                @if($headerContact->phone)
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                        </svg>
                        <span>{{ $headerContact->phone }}</span>
                    </div>
                @endif

                @if($headerContact->email)
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        <a href="mailto:{{ $headerContact->email }}" class="hover:text-gray-300">{{ $headerContact->email }}</a>
                    </div>
                @endif

                @if($headerContact->address)
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $headerContact->address }}</span>
                    </div>
                @endif

                <!-- Social Media Links -->
                @if($headerContact->facebook_url || $headerContact->instagram_url || $headerContact->tiktok_url)
                    <div class="flex items-center space-x-3">
                        @if($headerContact->facebook_url)
                            <a href="{{ $headerContact->facebook_url }}" target="_blank" class="hover:text-gray-300" title="Facebook">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        @endif

                        @if($headerContact->instagram_url)
                            <a href="{{ $headerContact->instagram_url }}" target="_blank" class="hover:text-gray-300" title="Instagram">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 0C7.284 0 6.944.012 5.877.06 4.814.108 4.086.278 3.45.525a7.017 7.017 0 00-2.533 1.65A7.017 7.017 0 00.525 3.45C.278 4.086.108 4.814.06 5.877.012 6.944 0 7.284 0 10s.012 3.056.06 4.123c.048 1.063.218 1.791.465 2.427a7.017 7.017 0 001.65 2.533 7.017 7.017 0 002.533 1.65c.636.247 1.364.417 2.427.465C6.944 19.988 7.284 20 10 20s3.056-.012 4.123-.06c1.063-.048 1.791-.218 2.427-.465a7.017 7.017 0 002.533-1.65 7.017 7.017 0 001.65-2.533c.247-.636.417-1.364.465-2.427C19.988 13.056 20 12.716 20 10s-.012-3.056-.06-4.123c-.048-1.063-.218-1.791-.465-2.427a7.017 7.017 0 00-1.65-2.533A7.017 7.017 0 0016.55.525C15.914.278 15.186.108 14.123.06 13.056.012 12.716 0 10 0zm0 1.802c2.67 0 2.986.01 4.04.058.975.045 1.505.207 1.858.344.467.182.8.399 1.15.748.35.35.566.683.748 1.15.137.353.3.883.344 1.858.048 1.054.058 1.37.058 4.04s-.01 2.986-.058 4.04c-.045.975-.207 1.505-.344 1.858-.182.467-.399.8-.748 1.15-.35.35-.683.566-1.15.748-.353.137-.883.3-1.858.344-1.054.048-1.37.058-4.04.058s-2.986-.01-4.04-.058c-.975-.045-1.505-.207-1.858-.344a3.1 3.1 0 01-1.15-.748 3.1 3.1 0 01-.748-1.15c-.137-.353-.3-.883-.344-1.858C1.812 12.986 1.802 12.67 1.802 10s.01-2.986.058-4.04c.045-.975.207-1.505.344-1.858.182-.467.399-.8.748-1.15.35-.35.683-.566 1.15-.748.353-.137.883-.3 1.858-.344C7.014 1.812 7.33 1.802 10 1.802zm0 3.065a5.133 5.133 0 100 10.266A5.133 5.133 0 0010 4.867zm0 8.464a3.333 3.333 0 110-6.666 3.333 3.333 0 010 6.666zm6.533-8.653a1.2 1.2 0 11-2.4 0 1.2 1.2 0 012.4 0z"/>
                                </svg>
                            </a>
                        @endif

                        @if($headerContact->tiktok_url)
                            <a href="{{ $headerContact->tiktok_url }}" target="_blank" class="hover:text-gray-300" title="TikTok">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm3.39 5.39v3.39h-2.61V5.39h2.61zm-3.39 0v3.39H7.39V5.39h2.61zm-3.39 0V8H4.39V5.39h2.61z"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                @endif

                <!-- Language Flags -->
                @if($headerContact->german_flag_icon || $headerContact->english_flag_icon)
                    <div class="flex items-center space-x-2 border-l border-gray-600 pl-4">
                        @if($headerContact->german_flag_icon)
                            <div title="Deutsch">
                                {!! $headerContact->german_flag_icon !!}
                            </div>
                        @endif

                        @if($headerContact->english_flag_icon)
                            <div title="English">
                                {!! $headerContact->english_flag_icon !!}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endisset