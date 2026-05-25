<footer id="contacts" class="bg-gray-900 text-gray-300 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div>
                <div class="flex items-center space-x-2 mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    <span class="text-lg font-bold text-white">Хромолаб</span>
                </div>
                <p class="text-sm text-gray-400">Медицинская лаборатория нового поколения. Быстро, точно, доступно.</p>
                <p class="text-sm mt-2">ИНН: 7712345678</p>
                <p class="text-sm">ОГРН: 1234567890123</p>
            </div>

            <!-- Navigation -->
            <div>
                <h3 class="text-white font-semibold mb-4">Навигация</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-emerald-400 transition-colors">Главная</a></li>
                    <li><a href="{{ route('catalog') }}" class="hover:text-emerald-400 transition-colors">Каталог анализов</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-emerald-400 transition-colors">О компании</a></li>
                    <li><a href="{{ route('checkout') }}" class="hover:text-emerald-400 transition-colors">Оформление заказа</a></li>
                    <li><a href="{{ route('contacts') }}" class="hover:text-emerald-400 transition-colors">Контакты</a></li>
                    <li><a href="{{ route('patient.dashboard') }}" class="hover:text-emerald-400 transition-colors">Личный кабинет</a></li>
                </ul>
            </div>

            <!-- Contacts -->
            <div>
                <h3 class="text-white font-semibold mb-4">Контакты</h3>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center space-x-2">
                        <span>📞</span>
                        <a href="tel:88005553535" class="hover:text-emerald-400 transition-colors">8-800-555-35-35</a>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span>📧</span>
                        <a href="mailto:info@chromolab.ru" class="hover:text-emerald-400 transition-colors">info@chromolab.ru</a>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span>🕐</span>
                        <span>Пн-Пт: 7:00 - 20:00</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span>🕐</span>
                        <span>Сб: 8:00 - 18:00</span>
                    </li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div>
                <h3 class="text-white font-semibold mb-4">Подписка на рассылку</h3>
                <p class="text-sm text-gray-400 mb-3">Получайте информацию об акциях и новинках</p>
                <form x-data="{ email: '', sent: false }"
                      @submit.prevent="if (email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { sent = true; email = ''; setTimeout(() => sent = false, 4000) }">
                    <div class="flex">
                        <input type="email" x-model="email" placeholder="Ваш email"
                               class="flex-1 px-3 py-2 rounded-l-lg bg-gray-800 border border-gray-700 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               :class="email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) ? 'border-red-500' : 'border-gray-700'">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-r-lg hover:bg-emerald-700 transition-colors">OK</button>
                    </div>
                    <template x-if="email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)">
                        <p class="text-red-400 text-xs mt-1">Введите корректный email</p>
                    </template>
                    <template x-if="sent">
                        <p class="text-emerald-400 text-xs mt-1">Спасибо за подписку!</p>
                    </template>
                </form>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
            <p>© {{ date('Y') }} Хромолаб. Все права защищены.</p>
            <div class="flex space-x-4 mt-4 md:mt-0">
                <a href="#" class="hover:text-emerald-400 transition-colors">Политика конфиденциальности</a>
                <a href="#" class="hover:text-emerald-400 transition-colors">Согласие на обработку ПДн</a>
            </div>
        </div>
    </div>
</footer>
