<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Хромолаб' }} — Лаборатория здоровья</title>
    <meta name="description" content="Медицинская лаборатория Хромолаб — анализы быстро, качественно, с расшифровкой">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 flex flex-col">
    @include('components.header')

    <main class="flex-1">
        {{ $slot }}
    </main>

    @include('components.footer')

    @livewireScripts
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('cart-add', (event) => {
                // Cart add handled by Livewire
            });
            Livewire.on('cart-updated', () => {
                // Refresh cart widget
            });
            Livewire.on('download-started', (event) => {
                window.open(event.url, '_blank');
            });
        });
    </script>
</body>
</html>
