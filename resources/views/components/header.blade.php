<header class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                <span class="text-xl font-bold text-gray-900">Хромолаб</span>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center space-x-6">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-emerald-600 transition-colors">Главная</a>
                <a href="{{ route('catalog') }}" class="text-gray-600 hover:text-emerald-600 transition-colors">Каталог анализов</a>
                <a href="{{ route('about') }}" class="text-gray-600 hover:text-emerald-600 transition-colors">О компании</a>
                <a href="{{ route('checkout') }}" class="text-gray-600 hover:text-emerald-600 transition-colors">Оформление</a>
                <a href="{{ route('contacts') }}" class="text-gray-600 hover:text-emerald-600 transition-colors">Контакты</a>
            </nav>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                <!-- Cart Widget -->
                @livewire('cart-widget')

                <!-- Auth Links -->
                @auth
                    <a href="{{ route('patient.dashboard') }}" class="hidden sm:flex items-center space-x-1 text-gray-600 hover:text-emerald-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Кабинет</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center px-3 py-1.5 text-sm font-medium text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors">
                        Войти
                    </a>
                @endauth

                <!-- Mobile menu button -->
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <nav class="flex flex-col space-y-2">
                    <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">Главная</a>
                    <a href="{{ route('catalog') }}" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">Каталог анализов</a>
                    <a href="{{ route('about') }}" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">О компании</a>
                    <a href="{{ route('checkout') }}" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">Оформление заказа</a>
                    <a href="{{ route('contacts') }}" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">Контакты</a>
                @auth
                    <a href="{{ route('patient.dashboard') }}" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">Личный кабинет</a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-2 rounded-lg text-emerald-700 bg-emerald-50 font-medium">Войти / Регистрация</a>
                @endauth
            </nav>
            <div class="mt-3 px-3 text-sm text-gray-500">
                <p>📞 8-800-555-35-35 (бесплатно)</p>
                <p>🕐 Пн-Пт: 7:00 - 20:00, Сб: 8:00 - 18:00</p>
            </div>
        </div>
    </div>
</header>
