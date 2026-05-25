<div>
    {{-- Hero Section --}}
    <section class="relative bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 text-white overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2240%22 fill=%22white%22/></svg>'); background-size: 60px 60px;"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32 lg:py-40">
            <div class="max-w-3xl">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight">
                    Ваше здоровье —<br>
                    <span class="text-emerald-200">наша главная забота</span>
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-emerald-100 max-w-2xl">
                    Современная лабораторная диагностика с точными результатами.
                    Более 500 видов анализов. Результаты за 1–14 дней.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('catalog') }}"
                       class="inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold text-emerald-900 bg-white rounded-xl hover:bg-emerald-50 transition-colors shadow-lg">
                        Перейти к каталогу
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="#advantages"
                       class="inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold text-white border-2 border-emerald-400 rounded-xl hover:bg-emerald-600 transition-colors">
                        Узнать больше
                    </a>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-gray-50 to-transparent"></div>
    </section>

    {{-- Advantages --}}
    <section id="advantages" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">Почему выбирают нас</h2>
                <p class="mt-4 text-lg text-gray-600">Нам доверяют тысячи пациентов по всей России</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Быстрые результаты</h3>
                    <p class="text-sm text-gray-600">Срочные анализы от 1 дня. Средний срок выполнения — 3 дня.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Точность гарантирована</h3>
                    <p class="text-sm text-gray-600">Современное оборудование и строгий контроль качества на каждом этапе.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Удобная запись</h3>
                    <p class="text-sm text-gray-600">Выбирайте удобное время и лабораторию онлайн без очередей.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">30+ лабораторий</h3>
                    <p class="text-sm text-gray-600">Филиалы по всей Москве и Московской области. Работаем без выходных.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-20 bg-emerald-700">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold text-white">Готовы проверить здоровье?</h2>
            <p class="mt-4 text-lg text-emerald-100">Выберите нужные анализы и запишитесь на удобное время</p>
            <div class="mt-10">
                <a href="{{ route('catalog') }}"
                   class="inline-flex items-center justify-center px-10 py-4 text-lg font-semibold text-emerald-900 bg-white rounded-xl hover:bg-emerald-50 transition-colors shadow-lg">
                    Начать выбор анализов
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Steps --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">Как это работает</h2>
                <p class="mt-4 text-lg text-gray-600">Всего 4 простых шага</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-emerald-600">1</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Выберите анализы</h3>
                    <p class="text-sm text-gray-600">Просмотрите каталог и добавьте нужные анализы в корзину</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-emerald-600">2</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Запишитесь</h3>
                    <p class="text-sm text-gray-600">Выберите удобную лабораторию и время для сдачи</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-emerald-600">3</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Сдайте биоматериал</h3>
                    <p class="text-sm text-gray-600">Придите в лабораторию в назначенное время</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-emerald-600">4</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Получите результат</h3>
                    <p class="text-sm text-gray-600">Результаты придут в личный кабинет и на email</p>
                </div>
            </div>
        </div>
    </section>
</div>
