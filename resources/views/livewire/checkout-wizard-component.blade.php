<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Stepper -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                @foreach([1 => 'Корзина', 2 => 'Лаборатория и время', 3 => 'Данные пациента', 4 => 'Оплата', 5 => 'Готово'] as $num => $label)
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold
                            {{ $step >= $num ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            @if($step > $num)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @else
                                {{ $num }}
                            @endif
                        </div>
                        <span class="text-xs mt-1 text-center {{ $step >= $num ? 'text-emerald-600 font-medium' : 'text-gray-400' }}">
                            {{ $label }}
                        </span>
                    </div>
                    @if(!$loop->last)
                        <div class="flex-1 h-0.5 {{ $step > $num ? 'bg-emerald-600' : 'bg-gray-200' }} mx-2"></div>
                    @endif
                @endforeach
            </div>
        </div>

        @if($errors->has('checkout'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                {{ $errors->first('checkout') }}
            </div>
        @endif

        <!-- Step 1: Cart Review -->
        @if($step === 1)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Ваш заказ</h2>

                @if(empty($cartItems))
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                        </svg>
                        <p class="text-gray-500 mt-4">Корзина пуста</p>
                        <a href="{{ route('catalog') }}" class="mt-2 inline-block text-emerald-600 hover:underline">Перейти в каталог</a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($cartItems as $item)
                            <div class="flex items-center justify-between py-4 border-b">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900">{{ $item['name'] }}</h3>
                                    <p class="text-sm text-gray-500">SKU: {{ $item['sku'] }}</p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="updateCartQuantity({{ $item['analysis_id'] }}, {{ $item['quantity'] - 1 }})"
                                                class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-50">−</button>
                                        <span class="w-8 text-center font-medium">{{ $item['quantity'] }}</span>
                                        <button wire:click="updateCartQuantity({{ $item['analysis_id'] }}, {{ $item['quantity'] + 1 }})"
                                                class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-50">+</button>
                                    </div>
                                    <span class="font-medium w-24 text-right">{{ number_format($item['subtotal'], 0, '.', ' ') }} ₽</span>
                                    <button wire:click="removeFromCart({{ $item['analysis_id'] }})"
                                            class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Promo Code -->
                    <div class="mt-6">
                        <div class="flex items-center space-x-2">
                            <input type="text" wire:model.live="promoCode" placeholder="Промокод"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                            <button wire:click="checkPromoCode"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                Применить
                            </button>
                        </div>

                        @if($promoMessage)
                            <div wire:key="promo-{{ Str::random(8) }}" class="mt-2"
                                 x-data="{ show: true }"
                                 x-init="setTimeout(() => show = false, 4000)"
                                 x-show="show"
                                 x-transition:leave="transition ease-in duration-300"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0">
                                @if($promoDiscount)
                                    <p class="text-sm text-emerald-600 font-medium flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        {{ $promoMessage }}
                                    </p>
                                @else
                                    <p class="text-sm text-red-600 font-medium flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        {{ $promoMessage }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-between items-center pt-4 border-t">
                        <span class="text-lg font-medium text-gray-600">Итого:</span>
                        <span class="text-2xl font-bold text-gray-900">{{ number_format($cartTotal, 0, '.', ' ') }} ₽</span>
                    </div>

                    <button wire:click="nextStep"
                            class="mt-6 w-full py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                        Продолжить
                    </button>
                @endif
            </div>
        @endif

        <!-- Step 2: Lab & Slot Selection -->
        @if($step === 2)
            <div class="space-y-6">
                <!-- Lab Selection -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Выберите лабораторию</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($laboratories as $lab)
                            <button wire:click="selectLaboratory({{ $lab->id }})"
                                    class="p-4 border-2 rounded-xl text-left transition-all {{ $selectedLaboratoryId === $lab->id ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <h3 class="font-semibold text-gray-900">{{ $lab->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $lab->address }}</p>
                                @if($lab->phone)
                                    <p class="text-sm text-gray-500">{{ $lab->phone }}</p>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Date & Time Selection -->
                @if($selectedLaboratoryId)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Выберите дату и время</h2>

                        <!-- Date Picker -->
                        <div class="flex space-x-2 overflow-x-auto pb-2 mb-6">
                            @for($i = 1; $i <= 14; $i++)
                                @php
                                    $date = now()->addDays($i);
                                    $dateStr = $date->format('Y-m-d');
                                @endphp
                                <button wire:click="selectDate('{{ $dateStr }}')"
                                        class="flex-shrink-0 px-4 py-3 rounded-xl border-2 text-center transition-all {{ $selectedDate === $dateStr ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-gray-300' }}">
                                    <div class="text-xs text-gray-500">{{ $date->format('D') }}</div>
                                    <div class="text-lg font-bold text-gray-900">{{ $date->format('d') }}</div>
                                    <div class="text-xs text-gray-500">{{ $date->format('M') }}</div>
                                </button>
                            @endfor
                        </div>

                        <!-- Time Slots -->
                        @if($selectedDate && !empty($availableSlots))
                            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2">
                                @foreach($availableSlots as $slot)
                                    <button wire:click="selectTime('{{ $slot->time }}')"
                                            @disabled(!$slot->available)
                                            class="py-2 px-3 rounded-lg text-sm font-medium transition-all
                                                @if(!$slot->available)
                                                    bg-gray-100 text-gray-400 cursor-not-allowed
                                                @elseif($selectedTime === $slot->time)
                                                    bg-emerald-600 text-white
                                                @else
                                                    bg-emerald-50 text-emerald-700 hover:bg-emerald-100
                                                @endif">
                                        {{ $slot->time }}
                                    </button>
                                @endforeach
                            </div>
                        @elseif($selectedDate)
                            <p class="text-gray-500 text-center py-4">Нет доступных слотов на эту дату</p>
                        @endif
                    </div>
                @endif

                <div class="flex justify-between">
                    <button wire:click="prevStep" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Назад</button>
                    <button wire:click="nextStep"
                            @disabled(!$selectedLaboratoryId || !$selectedDate || !$selectedTime)
                            class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Далее
                    </button>
                </div>
            </div>
        @endif

        <!-- Step 3: Patient Data -->
        @if($step === 3)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Данные пациента</h2>

                @if(!auth()->check())
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-700">Войдите или зарегистрируйтесь по номеру телефона</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                            <input type="tel" wire:model="phone" placeholder="+7 (___) ___-__-__"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                            @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        @if($smsSent)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Код из СМС</label>
                                <input type="text" wire:model="smsCode" placeholder="____" maxlength="4"
                                       class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-center text-lg tracking-widest">
                                @error('smsCode') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                <button wire:click="verifySmsCode" class="mt-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Подтвердить</button>
                            </div>
                        @else
                            <button wire:click="sendSmsCode" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Получить код</button>
                        @endif
                    </div>
                @endif

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
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
                </div>

                <div class="mt-6 flex justify-between">
                    <button wire:click="prevStep" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Назад</button>
                    <button wire:click="nextStep" class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Далее</button>
                </div>
            </div>
        @endif

        <!-- Step 4: Payment -->
        @if($step === 4)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Способ оплаты</h2>

                <div class="space-y-3">
                    @foreach([
                        'online' => ['💳', 'Оплата картой онлайн'],
                        'sbp' => ['🏦', 'Оплата через СБП'],
                        'clinic' => ['🏥', 'Оплата в лаборатории'],
                    ] as $method => [$icon, $label])
                        <button wire:click="$set('paymentMethod', '{{ $method }}')"
                                class="w-full flex items-center p-4 border-2 rounded-xl text-left transition-all {{ $paymentMethod === $method ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <span class="text-2xl mr-4">{{ $icon }}</span>
                            <span class="font-medium text-gray-900">{{ $label }}</span>
                        </button>
                    @endforeach
                </div>

                <!-- Order Summary -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-medium text-gray-900 mb-2">Сводка заказа</h3>
                    <p class="text-sm text-gray-600">📍 {{ $laboratories->firstWhere('id', $selectedLaboratoryId)?->name ?? '' }}</p>
                    <p class="text-sm text-gray-600">📅 {{ $selectedDate }} в {{ $selectedTime }}</p>
                    <p class="text-lg font-bold text-gray-900 mt-3">Итого: {{ number_format($cartTotal, 0, '.', ' ') }} ₽</p>
                </div>

                <div class="mt-6 flex justify-between">
                    <button wire:click="prevStep" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Назад</button>
                    <button wire:click="processPayment" class="px-8 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium">
                        {{ $paymentMethod === 'clinic' ? 'Подтвердить запись' : 'Оплатить' }}
                    </button>
                </div>
            </div>
        @endif

        <!-- Step 5: Success -->
        @if($step === 5)
            <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 mx-auto bg-emerald-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Заказ оформлен!</h2>
                <p class="text-gray-600 mb-6">Мы отправили подтверждение на вашу почту. Ждём вас в назначенное время.</p>
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('patient.dashboard') }}" class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                        Перейти в личный кабинет
                    </a>
                    <a href="{{ route('catalog') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Вернуться в каталог
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
