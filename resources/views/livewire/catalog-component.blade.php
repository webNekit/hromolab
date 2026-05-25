<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Sidebar --}}
            <aside class="w-full lg:w-72 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sticky top-24 space-y-5">

                    {{-- Search --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Поиск</p>
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="search"
                                   placeholder="Название, артикул..."
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Categories --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Категории</p>
                        <div class="space-y-1 max-h-64 overflow-y-auto">
                            @foreach($categories as $category)
                                <button wire:click="$set('selectedCategory', {{ $category->id === $selectedCategory ? 'null' : $category->id }})"
                                        class="w-full text-left px-3 py-2 rounded-lg text-sm transition-colors {{ $selectedCategory === $category->id ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
                                    {{ $category->name }}
                                    @if($category->children->count() > 0)
                                        <span class="text-xs text-gray-400 ml-1">({{ $category->children->count() }})</span>
                                    @endif
                                </button>
                                @foreach($category->children as $child)
                                    <button wire:click="$set('selectedCategory', {{ $child->id === $selectedCategory ? 'null' : $child->id }})"
                                            class="w-full text-left px-3 py-1.5 rounded-lg text-sm transition-colors {{ $selectedCategory === $child->id ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-gray-500 hover:bg-gray-100' }}">
                                        {{ $child->name }}
                                    </button>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Popular toggle --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Фильтры</p>
                        <button wire:click="togglePopular"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition-colors {{ $popularOnly ? 'bg-amber-50 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                Популярные
                            </span>
                            @if($popularOnly)
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </button>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Price range --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Цена, ₽</p>
                        <div class="flex gap-2">
                            <input type="number" wire:change="applyPriceFilter($event.target.value, null)" placeholder="от"
                                   class="w-1/2 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 bg-gray-50" min="0">
                            <input type="number" wire:change="applyPriceFilter(null, $event.target.value)" placeholder="до"
                                   class="w-1/2 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 bg-gray-50" min="0">
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Lead time --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Срок выполнения</p>
                        <select wire:change="applyLeadTimeFilter($event.target.value)"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 bg-gray-50">
                            <option value="">Любой</option>
                            <option value="1">1 день</option>
                            <option value="2">до 2 дней</option>
                            <option value="3">до 3 дней</option>
                            <option value="5">до 5 дней</option>
                            <option value="7">до 7 дней</option>
                            <option value="14">до 14 дней</option>
                        </select>
                    </div>

                    {{-- Clear --}}
                    <button wire:click="clearFilters"
                            class="w-full py-2.5 text-sm font-medium text-gray-500 bg-gray-50 rounded-lg hover:bg-gray-100 hover:text-gray-700 transition-colors">
                        Сбросить фильтры
                    </button>
                </div>
            </aside>

            {{-- Main --}}
            <div class="flex-1 min-w-0">
                {{-- Header --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Каталог анализов</h1>
                        <p class="text-sm text-gray-500 mt-1">Найдено: {{ $analyses->total() }} анализов</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-500 whitespace-nowrap">Сортировка:</label>
                        <select wire:model.live="sortBy" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 bg-gray-50">
                            <option value="name_asc">По названию А-Я</option>
                            <option value="name_desc">По названию Я-А</option>
                            <option value="price_asc">По цене ↑</option>
                            <option value="price_desc">По цене ↓</option>
                            <option value="popular">По популярности</option>
                        </select>
                    </div>
                </div>

                {{-- Loading skeleton --}}
                <div wire:loading class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @for($i = 0; $i < 6; $i++)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 animate-pulse">
                            <div class="h-4 bg-gray-200 rounded w-3/4 mb-3"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/2 mb-2"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/4 mb-4"></div>
                            <div class="h-9 bg-gray-200 rounded"></div>
                        </div>
                    @endfor
                </div>

                {{-- Cards --}}
                <div wire:loading.remove class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @forelse($analyses as $analysis)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow p-5 flex flex-col">
                            <div class="flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-semibold text-gray-900 line-clamp-2">{{ $analysis->name }}</h3>
                                    @if($analysis->is_popular)
                                        <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">Популярное</span>
                                    @endif
                                </div>
                                <div class="mt-3 space-y-1">
                                    <p class="text-sm text-gray-500">Арт. {{ $analysis->sku }}</p>
                                    <p class="text-xs text-gray-400">{{ $analysis->category->name }}</p>
                                    <p class="text-xs text-gray-400">Биоматериал: {{ $analysis->biomaterial }}</p>
                                    <p class="text-xs text-gray-400">Готовность: {{ $analysis->lead_time_days }} дн.</p>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-xl font-bold text-gray-900">{{ number_format($analysis->price, 0, '.', ' ') }} ₽</span>
                                <div class="flex gap-2">
                                    <button wire:click="selectAnalysis({{ $analysis->id }})"
                                            class="px-3 py-2 text-sm font-medium text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors">
                                        Подробнее
                                    </button>
                                    <button wire:click="addToCart({{ $analysis->id }})"
                                            class="px-3 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                                        В корзину
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-16">
                            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-500 mt-4">Ничего не найдено</p>
                            <button wire:click="clearFilters" class="mt-2 text-sm text-emerald-600 hover:underline">Сбросить фильтры</button>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($analyses->hasPages())
                    <div class="mt-8">
                        {{ $analyses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal --}}
    @if($selectedAnalysis)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:key="analysis-modal">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity" wire:click="closeModal"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden">
                <div class="flex items-start justify-between px-6 pt-6 pb-4 border-b border-gray-100 shrink-0">
                    <div class="pr-4">
                        <h3 class="text-xl font-bold text-gray-900">{{ $selectedAnalysis->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Арт. {{ $selectedAnalysis->sku }}</p>
                    </div>
                    <button wire:click="closeModal" class="shrink-0 p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-4 overflow-y-auto">
                    <div class="space-y-5">
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Описание</h4>
                            <p class="text-sm text-gray-700 mt-1 leading-relaxed">{{ $selectedAnalysis->description ?? 'Описание не указано' }}</p>
                        </div>

                        @if($selectedAnalysis->preparation)
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Подготовка</h4>
                                <p class="text-sm text-gray-700 mt-1 leading-relaxed">{{ $selectedAnalysis->preparation }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 rounded-lg p-3.5">
                                <p class="text-xs text-gray-500">Биоматериал</p>
                                <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $selectedAnalysis->biomaterial }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3.5">
                                <p class="text-xs text-gray-500">Готовность</p>
                                <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $selectedAnalysis->lead_time_days }} раб. дн.</p>
                            </div>
                        </div>

                        @if($selectedAnalysis->category)
                            <div class="bg-gray-50 rounded-lg p-3.5">
                                <p class="text-xs text-gray-500">Категория</p>
                                <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $selectedAnalysis->category->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between shrink-0">
                    <span class="text-2xl font-bold text-gray-900">{{ number_format($selectedAnalysis->price, 0, '.', ' ') }} ₽</span>
                    <button wire:click="addSelectedToCart"
                            class="px-6 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                        Добавить в корзину
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
