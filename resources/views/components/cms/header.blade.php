<header class="animate-fade-in">
    <nav class="flex flex-wrap items-center justify-center py-5 px-8 mx-6 mb-10 mt-4">
        @foreach($items as $key => $item)
            @if($item->type == \App\Enums\Cms\CmsMenuItemType::Dropdown)
                <div class="flex justify-center animate-slide-up" style="animation-delay: {{ $key * 0.1 }}s">
                    <div
                        x-data="{
                            open: false,
                            toggle() {
                                if (this.open) {
                                    return this.close()
                                }

                                this.$refs.button.focus()

                                this.open = true
                            },
                            close(focusAfter) {
                                if (! this.open) return

                                this.open = false

                                focusAfter && focusAfter.focus()
                            }
                        }"
                        x-on:keydown.escape.prevent.stop="close($refs.button)"
                        x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
                        x-id="['dropdown-button']"
                        class="relative"
                    >
                        <!-- Button -->
                        <button
                                x-ref="button"
                                x-on:click="toggle()"
                                :aria-expanded="open"
                                :aria-controls="$id('dropdown-button')"
                                type="button"
                                class="dropdown-btn"
                                style="background: transparent !important;"
                        >
                            <span class="font-semibold">{{$item->title}}</span>

                            <!-- Heroicon: micro chevron-down -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4 transition-transform duration-300">
                                <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <!-- Panel -->
                        <div
                                x-ref="panel"
                                x-show="open"
                                x-transition.origin.top.left
                                x-on:click.outside="close($refs.button)"
                                :id="$id('dropdown-button')"
                                x-cloak
                                x-trap="open"
                                class="absolute left-0 min-w-52 rounded-xl shadow-xl mt-3 z-20 origin-top-left p-2 outline-none"
                                style="background: rgba(255, 255, 255, 0.95) !important; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2);"
                        >
                            @foreach($item->childItems as $child)
                                <a href="{{ $child->getUrl() }}" class="px-4 py-3 w-full flex items-center rounded-lg transition-all duration-200 text-left text-gray-800 hover:text-blue-600 hover:bg-blue-50 focus-visible:text-blue-600 focus-visible:bg-blue-50 disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                                    <span class="transform transition-transform duration-200 hover:translate-x-1">{{ $child->title }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @elseif($item->type == \App\Enums\Cms\CmsMenuItemType::Icon)
                <div class="flex justify-center animate-slide-up" style="animation-delay: {{ $key * 0.1 }}s">
                    <a href="{{ $item->getUrl() }}" class="social-link flex items-center justify-center w-16 h-16 rounded-full transition-all duration-300 hover:scale-110 bg-gradient-to-br from-blue-400/20 to-purple-400/20 backdrop-blur-sm border border-white/30 overflow-hidden">
                        @if($item->getIcon())
                            @if(str_contains($item->getIcon(), '<svg'))
                                {!! $item->getIcon() !!}
                            @else
                                <div class="w-16 h-16 rounded-full overflow-hidden flex items-center justify-center bg-white/20">
                                    <img src="{{ $item->getIcon() }}" alt="{{ $item->title }}" class="w-12 h-12 object-cover" style="filter: drop-shadow(0 0 10px rgba(255,255,255,0.3));">
                                </div>
                            @endif
                        @endif
                    </a>
                </div>
            @elseif($item->type == \App\Enums\Cms\CmsMenuItemType::Button)
                <div class="flex justify-center animate-slide-up" style="animation-delay: {{ $key * 0.1 }}s">
                    <a href="{{ $item->getUrl() }}" class="inline-flex items-center justify-center px-8 py-3 font-semibold text-white transition-all duration-300 rounded-full hover:scale-105 focus:outline-none focus:ring-2 focus:ring-white/20 shadow-lg hover:shadow-xl"
                       style="background: linear-gradient(135deg, #3991b3 0%, #2c5aa0 100%);">
                        {{ $item->title }}
                    </a>
                </div>
            @else
                <div class="flex justify-center animate-slide-up" style="animation-delay: {{ $key * 0.1 }}s">
                    <a href="{{ $item->getUrl() }}" class="nav-link font-semibold" style="background: transparent !important;">
                        {{ $item->title }}
                    </a>
                </div>
            @endif
        @endforeach

    </nav>
</header>