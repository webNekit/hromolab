<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900">Контакты</h1>
    <p class="mt-2 text-gray-600">Свяжитесь с нами удобным способом</p>

    <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900">Телефон</h2>
                <p class="mt-2 text-2xl font-bold text-emerald-600">
                    <a href="tel:88005553535">8-800-555-35-35</a>
                </p>
                <p class="text-sm text-gray-500 mt-1">Бесплатно по России</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900">Email</h2>
                <p class="mt-2 text-lg text-emerald-600">
                    <a href="mailto:info@chromolab.ru">info@chromolab.ru</a>
                </p>
                <p class="text-sm text-gray-500 mt-1">Ответим в течение 2 часов</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900">Адрес центрального офиса</h2>
                <p class="mt-2 text-gray-700">г. Москва, ул. Тверская, д. 10, БЦ «Тверской»</p>
                <p class="text-sm text-gray-500 mt-1">Пн–Пт: 9:00 – 19:00</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Напишите нам</h2>

            @if($contactSent)
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-700 text-sm font-medium flex items-center gap-2 mb-4"
                     x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)"
                     x-show="show" x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $contactSent }}
                </div>
            @endif

            <form class="space-y-4" wire:submit="submitContact">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ваше имя</label>
                    <input type="text" wire:model="contactName"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50 @error('contactName') border-red-400 @enderror"
                           placeholder="Иван Иванов">
                    @error('contactName') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" wire:model="contactEmail"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50 @error('contactEmail') border-red-400 @enderror"
                           placeholder="ivan@example.ru">
                    @error('contactEmail') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Сообщение</label>
                    <textarea rows="4" wire:model="contactMessage"
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50 @error('contactMessage') border-red-400 @enderror"
                              placeholder="Ваше сообщение..."></textarea>
                    @error('contactMessage') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit"
                        class="w-full py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                    Отправить
                </button>
            </form>
        </div>
    </div>

    {{-- Laboratories list --}}
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900">Наши лаборатории</h2>
        <p class="mt-1 text-gray-600">Приём биоматериала во всех пунктах</p>
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $labs = [
                    ['address' => 'ул. Тверская, д. 10', 'metro' => 'Тверская', 'hours' => '7:00–20:00'],
                    ['address' => 'пр-т Мира, д. 45', 'metro' => 'Проспект Мира', 'hours' => '7:00–20:00'],
                    ['address' => 'Кутузовский пр-т, д. 22', 'metro' => 'Кутузовская', 'hours' => '7:00–20:00'],
                    ['address' => 'ул. Профсоюзная, д. 56', 'metro' => 'Калужская', 'hours' => '8:00–18:00'],
                    ['address' => 'Ленинградский пр-т, д. 37', 'metro' => 'Динамо', 'hours' => '7:00–20:00'],
                ];
            @endphp
            @foreach($labs as $lab)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">{{ $lab['address'] }}</p>
                            <p class="text-sm text-gray-500">м. {{ $lab['metro'] }}</p>
                            <p class="text-sm text-gray-500">{{ $lab['hours'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
