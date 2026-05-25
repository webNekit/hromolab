<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Domains\Orders\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmationNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected readonly Order $order,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Заказ №'.$this->order->order_number.' принят')
            ->greeting('Здравствуйте!')
            ->line('Ваш заказ №'.$this->order->order_number.' успешно создан.')
            ->line('Дата и время визита: '.$this->order->appointment_datetime->format('d.m.Y H:i'))
            ->line('Лаборатория: '.$this->order->laboratory->name)
            ->line('Адрес: '.$this->order->laboratory->address)
            ->line('Сумма: '.number_format((float) $this->order->total_price, 2, '.', ' ').' ₽')
            ->action('Перейти в личный кабинет', route('patient.dashboard'))
            ->line('Спасибо, что выбрали Хромолаб!');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total_price' => $this->order->total_price,
            'appointment_datetime' => $this->order->appointment_datetime->toIso8601String(),
        ];
    }
}
