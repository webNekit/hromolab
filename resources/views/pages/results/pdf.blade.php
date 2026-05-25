<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результат анализа — Хромолаб</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 0; padding: 20px; color: #333; }
        .header { border-bottom: 2px solid #059669; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #059669; font-size: 20px; }
        .header p { margin: 5px 0 0; color: #666; font-size: 12px; }
        .patient-info { background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .patient-info h3 { margin: 0 0 10px; font-size: 14px; }
        .patient-info p { margin: 3px 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        th { background: #f3f4f6; font-weight: 600; }
        .out-of-range { color: #dc2626; font-weight: 600; }
        .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔬 Хромолаб</h1>
        <p>Медицинская лаборатория | Заказ №{{ $order->order_number }}</p>
        <p>Дата формирования: {{ $generatedAt->format('d.m.Y H:i') }}</p>
    </div>

    @if($patient)
    <div class="patient-info">
        <h3>Данные пациента</h3>
        <p><strong>ФИО:</strong> {{ $patient->last_name }} {{ $patient->first_name }} {{ $patient->middle_name }}</p>
        <p><strong>Дата рождения:</strong> {{ $patient->birth_date->format('d.m.Y') }}</p>
        <p><strong>Пол:</strong> {{ $patient->gender === 'male' ? 'Мужской' : 'Женский' }}</p>
    </div>
    @endif

    <div>
        <h3>Результаты исследования: {{ $analysis->name }}</h3>
        <p style="font-size: 12px; color: #666;">Биоматериал: {{ $analysis->biomaterial }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Показатель</th>
                <th>Результат</th>
                <th>Ед. изм.</th>
                <th>Референсные значения</th>
                <th>Статус</th>
            </tr>
        </thead>
        <tbody>
            @foreach($parameterValues as $key => $data)
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                    <td class="{{ isset($data['out_of_range']) && $data['out_of_range'] ? 'out-of-range' : '' }}">
                        {{ $data['value'] ?? '—' }}
                    </td>
                    <td>{{ $data['unit'] ?? '—' }}</td>
                    <td>{{ $data['reference'] ?? '—' }}</td>
                    <td>
                        @if(isset($data['out_of_range']) && $data['out_of_range'])
                            <span style="color: #dc2626;">⚠ Отклонение</span>
                        @else
                            <span style="color: #059669;">✓ Норма</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ $labAssistant }} | Подписано электронной подписью</p>
        <p>Данный документ является результатом лабораторного исследования и не заменяет консультацию врача.</p>
        <p>© {{ date('Y') }} Хромолаб. Все права защищены.</p>
    </div>
</body>
</html>
