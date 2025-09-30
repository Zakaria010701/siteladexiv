<header>
    <nav class="flex flex-row bg-white border-b border-gray-100 justify-center py-2 shadow-md">
        @foreach($items as $key => $item)
            @if($item->type == \App\Enums\Cms\CmsMenuItemType::Dropdown)
                <div class="flex justify-center">
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
                        >
                            <span>{{$item->title}}</span>

                            <!-- Heroicon: micro chevron-down -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
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
                                class="absolute left-0 min-w-48 rounded-lg shadow-sm mt-2 z-10 origin-top-left bg-white p-1.5 outline-none border border-gray-200"
                        >
                            @foreach($item->childItems as $child)
                                <a href="{{ $child->getUrl() }}" class="px-2 lg:py-1.5 py-2 w-full flex items-center rounded-md transition-colors text-left text-gray-800 hover:bg-gray-50 focus-visible:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                    {{ $child->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @elseif($item->type == \App\Enums\Cms\CmsMenuItemType::Icon)
                <div class="flex justify-center">
                    <a href="{{ $item->getUrl() }}" class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100 transition-colors">
                        {!! $item->icon !!}
                    </a>
                </div>
            @else
                <div class="flex justify-center">
                    <a href="{{ $item->getUrl() }}" class="px-4 py-2 text-gray-800 hover:text-gray-600 transition-colors">
                        {{ $item->title }}
                    </a>
                </div>
            @endif
        @endforeach

        <div class="flex justify-center">
            <a href="{{ route('cart.index') }}" class="relative flex items-center px-4 py-2 text-gray-800 hover:text-gray-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 3.75h-.75m-.75 0H12m-9 0h.75m.75 0H9m12 0v.75m0 0v.75m0 0V21a9 9 0 11-18 0v-5.25m18 0v-5.25m-18 0V21" />
                </svg>
                @if(session('cart'))
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full h-5 w-5 flex items-center justify-center text-xs font-bold">
                        {{ count(session('cart')) }}
                    </span>
                @endif
            </a>
        </div>
    </nav>
</header>