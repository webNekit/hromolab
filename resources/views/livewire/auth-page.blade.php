<div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        {{-- Tabs --}}
        <div class="flex bg-white rounded-t-xl border border-gray-200 border-b-0 overflow-hidden">
            <button wire:click="switchTab('login')"
                    class="flex-1 py-3.5 text-sm font-semibold text-center transition-colors {{ $tab === 'login' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                Вход
            </button>
            <button wire:click="switchTab('register')"
                    class="flex-1 py-3.5 text-sm font-semibold text-center transition-colors {{ $tab === 'register' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                Регистрация
            </button>
        </div>

        <div class="bg-white rounded-b-xl shadow-sm border border-gray-200 p-6 sm:p-8">
            {{-- Error --}}
            @if($error)
                <div class="mb-4 p-3 text-sm text-red-700 bg-red-50 rounded-lg border border-red-200">
                    {{ $error }}
                </div>
            @endif

            {{-- Login Form --}}
            @if($tab === 'login')
                <form wire:submit="login" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" wire:model="loginEmail" required autocomplete="email"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50"
                               placeholder="ivan@example.ru">
                        @error('loginEmail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Пароль</label>
                        <input type="password" wire:model="loginPassword" required autocomplete="current-password"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50"
                               placeholder="••••••••">
                        @error('loginPassword') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" wire:model="loginRemember" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            Запомнить меня
                        </label>
                    </div>
                    <button type="submit"
                            class="w-full py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                        Войти
                    </button>
                </form>
            @endif

            {{-- Register Form --}}
            @if($tab === 'register')
                <form wire:submit="register" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ФИО</label>
                        <input type="text" wire:model="registerName" required
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50"
                               placeholder="Иванов Иван Иванович">
                        @error('registerName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" wire:model="registerEmail" required autocomplete="email"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50"
                               placeholder="ivan@example.ru">
                        @error('registerEmail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                        <input type="tel" wire:model="registerPhone" autocomplete="tel"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50"
                               placeholder="+7 (999) 123-45-67">
                        @error('registerPhone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Пароль</label>
                        <input type="password" wire:model="registerPassword" required autocomplete="new-password"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50"
                               placeholder="Минимум 6 символов">
                        @error('registerPassword') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Подтверждение пароля</label>
                        <input type="password" wire:model="registerPasswordConfirmation" required autocomplete="new-password"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50"
                               placeholder="Повторите пароль">
                        @error('registerPasswordConfirmation') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit"
                            class="w-full py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                        Зарегистрироваться
                    </button>
                    <p class="text-xs text-gray-400 text-center">
                        Регистрируясь, вы принимаете условия обработки персональных данных
                    </p>
                </form>
            @endif
        </div>
    </div>
</div>
