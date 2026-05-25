<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Domains\Auth\Models\Profile;
use App\Domains\Auth\Models\User;
use App\Domains\Orders\Models\Order;
use App\Domains\Results\Models\MedicalResult;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PatientDashboardComponent extends Component
{
    public string $activeTab = 'orders';

    public string $firstName = '';

    public string $lastName = '';

    public string $middleName = '';

    public string $birthDate = '';

    public string $gender = 'male';

    public string $phone = '';

    public ?string $saved = null;

    public function updated(string $property): void
    {
        if (str_starts_with($property, 'first') || str_starts_with($property, 'last') || str_starts_with($property, 'middle') || $property === 'phone' || $property === 'birthDate' || $property === 'gender') {
            $this->saved = null;
        }
    }

    public function mount(): void
    {
        if (! auth()->check()) {
            $this->redirect(route('catalog'), true);

            return;
        }

        $user = auth()->user();
        $profile = $user->profile;

        $this->phone = $user->phone ?? '';

        if ($profile !== null) {
            $this->firstName = $profile->first_name;
            $this->lastName = $profile->last_name;
            $this->middleName = $profile->middle_name ?? '';
            $this->birthDate = $profile->birth_date?->format('Y-m-d') ?? '';
            $this->gender = $profile->gender ?? 'male';
        }
    }

    public function selectTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function saveProfile(): void
    {
        $this->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'birthDate' => 'required|date|before:today|after:100 years ago',
            'gender' => 'required|in:male,female',
            'phone' => 'nullable|string|max:20',
        ]);

        Profile::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'middle_name' => $this->middleName,
                'birth_date' => $this->birthDate,
                'gender' => $this->gender,
            ],
        );

        auth()->user()->update([
            'phone' => $this->phone,
        ]);

        $this->saved = 'Данные сохранены';
        $this->dispatch('profile-updated');
    }

    public function downloadResult(int $resultId): void
    {
        $result = MedicalResult::findOrFail($resultId);

        // Ensure the user owns this order
        if ($result->orderItem->order->user_id !== auth()->id()) {
            abort(403);
        }

        $result->incrementDownloads();

        if ($result->pdf_path !== null) {
            $url = $result->temporaryDownloadUrl;

            if ($url !== null) {
                $this->dispatch('download-started', url: $url);
            }
        }
    }

    #[Computed]
    public function orders(): Collection
    {
        return Order::forUser(auth()->id())
            ->with(['items.analysis', 'laboratory', 'statusHistories'])
            ->orderByDesc('appointment_datetime')
            ->get();
    }

    #[Computed]
    public function user(): ?User
    {
        return auth()->user();
    }

    #[Computed]
    public function notifications(): Collection
    {
        return auth()->user()->notifications()->take(20)->get();
    }

    #[Computed]
    public function totalOrders(): int
    {
        return $this->orders->count();
    }

    #[Computed]
    public function completedOrders(): int
    {
        return $this->orders->where('current_status', 'completed')->count();
    }

    #[Computed]
    public function activeOrders(): int
    {
        return $this->orders->whereIn('current_status', ['processing', 'ready_for_lab', 'analyzing'])->count();
    }

    #[Computed]
    public function totalDownloads(): int
    {
        return $this->orders
            ->flatMap->items
            ->filter(fn ($item) => $item->hasVerifiedResult())
            ->sum(fn ($item) => $item->medicalResult->download_count);
    }

    #[Computed]
    public function totalSpent(): float
    {
        return $this->orders->sum('total_price');
    }

    public function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'new' => 'bg-blue-100 text-blue-800',
            'processing' => 'bg-yellow-100 text-yellow-800',
            'ready_for_lab' => 'bg-orange-100 text-orange-800',
            'analyzing' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabel(string $status): string
    {
        return match ($status) {
            'new' => 'Новый',
            'processing' => 'В обработке',
            'ready_for_lab' => 'Готов к лаборатории',
            'analyzing' => 'Анализируется',
            'completed' => 'Завершён',
            'cancelled' => 'Отменён',
            default => $status,
        };
    }

    public function render(): View
    {
        return view('livewire.patient-dashboard-component', [
            'orders' => $this->orders,
            'user' => $this->user,
            'notifications' => $this->notifications,
        ])->layout('layouts.app');
    }
}
