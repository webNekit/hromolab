<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation -->
            <aside class="w-full lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <nav class="space-y-1">
                        @foreach([
                            'orders' => ['📋', 'Мои анализы'],
                            'profile' => ['👤', 'Профиль'],
                            'notifications' => ['🔔', 'Уведомления'],
                        ] as $tab => [$icon, $label])
                            <button wire:click="selectTab('{{ $tab }}')"
                                    class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $activeTab === $tab ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                <span>{{ $icon }}</span>
                                <span>{{ $label }}</span>
                                @if($tab === 'notifications' && $notifications->where('read_at', null)->count() > 0)
                                    <span class="ml-auto bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                        {{ $notifications->where('read_at', null)->count() }}
                                    </span>
                                @endif
                            </button>
                        @endforeach
                    </nav>

                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-400">С нами с {{ $user?->created_at?->format('d.m.Y') ?? '—' }}</p>
                        <p class="text-xs text-gray-400 mt-1">Потрачено: {{ number_format($this->totalSpent, 0, '.', ' ') }} ₽</p>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <p class="text-sm text-gray-500">Всего заказов</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $this->totalOrders }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <p class="text-sm text-gray-500">Завершено</p>
                        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $this->completedOrders }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <p class="text-sm text-gray-500">В работе</p>
                        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $this->activeOrders }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <p class="text-sm text-gray-500">Скачиваний</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $this->totalDownloads }}</p>
                    </div>
                </div>

                <!-- Orders Tab -->
                @if($activeTab === 'orders')
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Мои анализы</h2>

                        @if($orders->isEmpty())
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-gray-500 mt-4">У вас пока нет заказов</p>
                                <a href="{{ route('catalog') }}" class="mt-2 inline-block text-emerald-600 hover:underline">Перейти в каталог</a>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($orders as $order)
                                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                                        <!-- Order Header -->
                                        <div class="p-4 bg-gray-50 flex flex-wrap items-center justify-between gap-2">
                                            <div>
                                                <span class="font-semibold text-gray-900">Заказ {{ $order->order_number }}</span>
                                                <span class="ml-2 text-sm text-gray-500">{{ $order->appointment_datetime->format('d.m.Y H:i') }}</span>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusBadgeClass($order->current_status) }}">
                                                    {{ $this->getStatusLabel($order->current_status) }}
                                                </span>
                                                <span class="font-bold text-gray-900">{{ number_format($order->total_price, 0, '.', ' ') }} ₽</span>
                                            </div>
                                        </div>

                                        <!-- Order Details -->
                                        <div class="p-4">
                                            <p class="text-sm text-gray-500 mb-3">
                                                📍 {{ $order->laboratory->name }} — {{ $order->laboratory->address }}
                                            </p>
                                            <div class="space-y-2">
                                                @foreach($order->items as $item)
                                                    <div class="flex items-center justify-between py-2 border-t border-gray-100">
                                                        <div>
                                                            <p class="font-medium text-gray-900">{{ $item->analysis->name }}</p>
                                                            <p class="text-xs text-gray-500">{{ $item->analysis->biomaterial }} • {{ $item->analysis->lead_time_days }} дн.</p>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            @if($item->hasVerifiedResult())
                                                                <button wire:click="downloadResult({{ $item->medicalResult->id }})"
                                                                        class="flex items-center space-x-1 px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg hover:bg-emerald-100 text-sm">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                    </svg>
                                                                    <span>PDF</span>
                                                                    @if($item->medicalResult->download_count > 0)
                                                                        <span class="text-xs text-gray-400">({{ $item->medicalResult->download_count }})</span>
                                                                    @endif
                                                                </button>
                                                            @else
                                                                <span class="text-xs text-gray-400">В обработке</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- Status History -->
                                            @if($order->statusHistories->count() > 1)
                                                <div class="mt-4 pt-4 border-t">
                                                    <h4 class="text-sm font-medium text-gray-700 mb-2">История статусов</h4>
                                                    <div class="space-y-1">
                                                        @foreach($order->statusHistories->take(3) as $history)
                                                            <p class="text-xs text-gray-500">
                                                                {{ $this->getStatusLabel($history->status) }}
                                                                — {{ $history->created_at->format('d.m.Y H:i') }}
                                                                @if($history->comment)
                                                                    <span class="text-gray-400">({{ $history->comment }})</span>
                                                                @endif
                                                            </p>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Profile Tab -->
                @if($activeTab === 'profile')
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Профиль</h2>

                        <div class="space-y-4 max-w-lg">
                            @if($saved)
                                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-700 text-sm font-medium flex items-center gap-2"
                                     x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)"
                                     x-show="show" x-transition:leave="transition ease-in duration-300"
                                     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $saved }}
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" value="{{ $user?->email }}" disabled
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Фамилия</label>
                                <input type="text" wire:model="lastName"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 @error('lastName') border-red-400 @enderror">
                                @error('lastName') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                                <input type="text" wire:model="firstName"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 @error('firstName') border-red-400 @enderror">
                                @error('firstName') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Отчество</label>
                                <input type="text" wire:model="middleName"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 @error('middleName') border-red-400 @enderror">
                                @error('middleName') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                                <input type="tel" wire:model="phone"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 @error('phone') border-red-400 @enderror">
                                @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Дата рождения</label>
                                <input type="date" wire:model="birthDate"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 @error('birthDate') border-red-400 @enderror">
                                @error('birthDate') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Пол</label>
                                <select wire:model="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 @error('gender') border-red-400 @enderror">
                                    <option value="male">Мужской</option>
                                    <option value="female">Женский</option>
                                </select>
                                @error('gender') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <button wire:click="saveProfile"
                                    class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                                Сохранить
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Notifications Tab -->
                @if($activeTab === 'notifications')
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Уведомления</h2>

                        @if($notifications->isEmpty())
                            <p class="text-gray-500 text-center py-8">Нет уведомлений</p>
                        @else
                            <div class="space-y-3">
                                @foreach($notifications as $notification)
                                    <div class="p-4 rounded-lg {{ $notification->read_at ? 'bg-gray-50' : 'bg-blue-50 border border-blue-100' }}">
                                        <p class="font-medium text-gray-900">{{ $notification->data['order_number'] ?? 'Уведомление' }}</p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $notification->data['analysis_name'] ?? ($notification->data['total_price'] ? 'Сумма: '.number_format($notification->data['total_price'], 0, '.', ' ').' ₽' : '') }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
