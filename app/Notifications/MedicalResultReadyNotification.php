<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Domains\Results\Models\MedicalResult;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MedicalResultReadyNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected readonly MedicalResult $result,
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
        $orderItem = $this->result->orderItem;

        return (new MailMessage)
            ->subject('Результаты анализа '.$orderItem->analysis->name.' готовы')
            ->greeting('Здравствуйте!')
            ->line('Результаты вашего анализа "'.$orderItem->analysis->name.'" готовы.')
            ->line('Заказ №'.$orderItem->order->order_number)
            ->action('Скачать результаты', route('patient.results.download', $this->result->id))
            ->line('Результат доступен для скачивания в течение 30 минут по ссылке.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'result_id' => $this->result->id,
            'order_number' => $this->result->orderItem->order->order_number,
            'analysis_name' => $this->result->orderItem->analysis->name,
        ];
    }
}
