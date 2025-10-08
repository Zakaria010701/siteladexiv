<header class="animate-fade-in">
    <!-- Mobile Hamburger Menu -->
    <div class="block md:hidden fixed top-4 left-4 z-50" x-data="{ mobileMenuOpen: false }">
        <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="bg-white p-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="{ 'bg-blue-50': mobileMenuOpen }"
        >
            <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-gray-700">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-gray-700">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Mobile Menu Panel -->
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform -translate-x-full"
            class="fixed top-0 left-0 w-80 h-full bg-white shadow-2xl z-40 overflow-y-auto"
            x-on:click.outside="mobileMenuOpen = false"
        >
            <div class="p-6 pt-20">
                @foreach($items as $key => $item)
                    @if($item->type == \App\Enums\Cms\CmsMenuItemType::Dropdown)
                        <div class="mb-4">
                            <div class="font-semibold text-gray-800 mb-2 px-3">{{ $item->title }}</div>
                            <div class="ml-3 space-y-1">
                                @foreach($item->childItems as $child)
                                    @if(($child->type == \App\Enums\Cms\CmsMenuItemType::Dropdown || $child->type == \App\Enums\Cms\CmsMenuItemType::Page) && $child->childItems->count() > 0)
                                        <!-- Nested Dropdown in Mobile (from Dropdown or Page parent) -->
                                        <div class="mb-2">
                                            <a href="{{ $child->getUrl() }}" class="font-medium text-gray-800 px-3 py-1 text-sm block hover:text-blue-600 transition-colors">
                                                {{ $child->title }}
                                            </a>
                                            <div class="ml-3 space-y-1">
                                                @foreach($child->childItems as $grandChild)
                                                    <a href="{{ $grandChild->getUrl() }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200">
                                                        {{ $grandChild->title }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <!-- Regular Menu Item in Mobile -->
                                        <a href="{{ $child->getUrl() }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200">
                                            {{ $child->title }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @elseif($item->type == \App\Enums\Cms\CmsMenuItemType::Icon)
                        <div class="mb-4">
                            <a href="{{ $item->getUrl() }}" class="flex items-center px-3 py-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200">
                                @if($item->getIcon())
                                    @if(str_contains($item->getIcon(), '<svg'))
                                        {!! $item->getIcon() !!}
                                    @else
                                        <img src="{{ $item->getIcon() }}" alt="{{ $item->title }}" class="w-5 h-5 mr-3">
                                    @endif
                                @endif
                                <span>{{ $item->title }}</span>
                            </a>
                        </div>
                    @elseif($item->type == \App\Enums\Cms\CmsMenuItemType::Button)
                        <div class="mb-4">
                            <a href="{{ $item->getUrl() }}" class="block w-full text-center px-4 py-3 text-white font-semibold rounded-lg transition-all duration-300 hover:scale-105 shadow-lg"
                               style="background: linear-gradient(135deg, #3991b3 0%, #2c5aa0 100%);">
                                {{ $item->title }}
                            </a>
                        </div>
                    @else
                        <div class="mb-4">
                            <a href="{{ $item->getUrl() }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 font-medium">
                                {{ $item->title }}
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Backdrop -->
        <div
            x-show="mobileMenuOpen"
            class="fixed inset-0 bg-black bg-opacity-50 z-30"
            x-on:click="mobileMenuOpen = false"
        ></div>
    </div>

    <!-- Desktop Navigation -->
    <nav class="hidden md:flex lg:flex flex-wrap items-center justify-center py-5 px-8 mx-6 mb-10 mt-4">
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
                        <div class="dropdown-btn" style="background: transparent !important; display: flex; align-items: center; gap: 0.5rem;">
                            <a href="{{ $item->getUrl() }}" class="font-semibold" style="text-decoration: none; color: inherit;">
                                {{$item->title}}
                            </a>

                            <!-- Arrow button - only controls dropdown -->
                            <button
                                x-ref="button"
                                x-on:click="toggle()"
                                :aria-expanded="open"
                                :aria-controls="$id('dropdown-button')"
                                type="button"
                                class="dropdown-arrow-btn"
                                style="background: transparent; border: none; padding: 0.25rem; color: inherit; cursor: pointer;"
                            >
                                <!-- Heroicon: micro chevron-down -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4 transition-transform duration-300">
                                    <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

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
                                @if(($child->type == \App\Enums\Cms\CmsMenuItemType::Dropdown || $child->type == \App\Enums\Cms\CmsMenuItemType::Page) && $child->childItems->count() > 0)
                                    <!-- Nested Dropdown (from Dropdown or Page parent) -->
                                    <div class="relative group">
                                        <div class="flex items-center justify-between px-4 py-3 w-full rounded-lg transition-all duration-200 text-left text-gray-800 hover:text-blue-600 hover:bg-blue-50 focus-visible:text-blue-600 focus-visible:bg-blue-50 disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                                            <a href="{{ $child->getUrl() }}" class="flex-1 transform transition-transform duration-200 hover:translate-x-1">
                                                {{ $child->title }}
                                            </a>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4 transition-transform duration-300 group-hover:translate-x-1">
                                                <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                            </svg>
                                        </div>

                                        <!-- Nested Panel -->
                                        <div class="absolute left-full top-0 min-w-52 rounded-xl shadow-xl ml-1 z-30 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 p-2"
                                             style="background: rgba(255, 255, 255, 0.95) !important; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2);">
                                            @foreach($child->childItems as $grandChild)
                                                <a href="{{ $grandChild->getUrl() }}" class="px-4 py-3 w-full flex items-center rounded-lg transition-all duration-200 text-left text-gray-800 hover:text-blue-600 hover:bg-blue-50 focus-visible:text-blue-600 focus-visible:bg-blue-50 disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                                                    <span class="transform transition-transform duration-200 hover:translate-x-1">{{ $grandChild->title }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <!-- Regular Menu Item -->
                                    <a href="{{ $child->getUrl() }}" class="px-4 py-3 w-full flex items-center rounded-lg transition-all duration-200 text-left text-gray-800 hover:text-blue-600 hover:bg-blue-50 focus-visible:text-blue-600 focus-visible:bg-blue-50 disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                                        <span class="transform transition-transform duration-200 hover:translate-x-1">{{ $child->title }}</span>
                                    </a>
                                @endif
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