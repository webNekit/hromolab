<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Domains\Auth\Models\Profile;
use App\Domains\Auth\Models\User;
use App\Domains\Laboratories\Actions\GetAvailableSlotsAction;
use App\Domains\Laboratories\DataTransferObjects\SlotDTO;
use App\Domains\Laboratories\Models\Laboratory;
use App\Domains\Orders\Actions\OrderCreationAction;
use App\Domains\Orders\DataTransferObjects\CheckoutDTO;
use App\Domains\Orders\Models\PromoCode;
use App\Domains\Orders\Services\CartServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CheckoutWizardComponent extends Component
{
    public int $step = 1;

    // Step 1: Cart
    public string $promoCode = '';

    public ?array $promoDiscount = null;

    public ?string $promoMessage = null;

    // Step 2: Laboratory & Slot
    public ?int $selectedLaboratoryId = null;

    public ?string $selectedDate = null;

    public ?string $selectedTime = null;

    // Step 3: Patient data
    public string $phone = '';

    public string $smsCode = '';

    public bool $smsSent = false;

    public ?int $authUserId = null;

    public string $firstName = '';

    public string $lastName = '';

    public string $middleName = '';

    public string $birthDate = '';

    public string $gender = 'male';

    // Step 4: Payment
    public string $paymentMethod = 'online';

    public function mount(): void
    {
        if (! auth()->check()) {
            $this->redirect(route('login'), true);

            return;
        }

        $this->authUserId = auth()->id();
        $profile = auth()->user()->profile;

        if ($profile !== null) {
            $this->firstName = $profile->first_name;
            $this->lastName = $profile->last_name;
            $this->middleName = $profile->middle_name ?? '';
            $this->birthDate = $profile->birth_date?->format('Y-m-d') ?? '';
            $this->gender = $profile->gender ?? 'male';
            $this->phone = auth()->user()->phone ?? '';
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 3) {
            $this->validate([
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'middleName' => 'nullable|string|max:255',
                'birthDate' => 'required|date|before:today|after:100 years ago',
                'gender' => 'required|in:male,female',
                'phone' => 'nullable|string|max:20',
            ]);
        }

        if ($this->validateStep($this->step)) {
            $this->step++;
        }
    }

    public function prevStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function selectLaboratory(int $laboratoryId): void
    {
        $this->selectedLaboratoryId = $laboratoryId;
        $this->selectedDate = null;
        $this->selectedTime = null;
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->selectedTime = null;
    }

    public function selectTime(string $time): void
    {
        $this->selectedTime = $time;
    }

    public function updatedPromoCode(): void
    {
        $this->promoMessage = null;
    }

    public function checkPromoCode(): void
    {
        $this->promoMessage = null;

        $promoCode = PromoCode::where('code', strtoupper($this->promoCode))
            ->where('is_active', true)
            ->first();

        if ($promoCode === null) {
            $this->promoDiscount = null;
            $this->promoMessage = 'Промокод не найден';

            return;
        }

        // Check validity dates
        $today = now()->format('Y-m-d');

        if ($promoCode->valid_from !== null && $today < $promoCode->valid_from) {
            $this->promoDiscount = null;
            $this->promoMessage = 'Промокод ещё не активен';

            return;
        }

        if ($promoCode->valid_until !== null && $today > $promoCode->valid_until) {
            $this->promoDiscount = null;
            $this->promoMessage = 'Срок действия промокода истёк';

            return;
        }

        // Check max uses
        if ($promoCode->max_uses > 0 && $promoCode->uses_count >= $promoCode->max_uses) {
            $this->promoDiscount = null;
            $this->promoMessage = 'Промокод больше не действует';

            return;
        }

        $this->promoDiscount = [
            'type' => $promoCode->discount_type,
            'value' => (float) $promoCode->discount_value,
        ];

        $discountLabel = $promoCode->discount_type === 'percent'
            ? $promoCode->discount_value.'%'
            : number_format((float) $promoCode->discount_value, 0, '.', ' ').' ₽';

        $this->promoMessage = "Промокод применён: скидка {$discountLabel}";
    }

    public function sendSmsCode(): void
    {
        if ($this->phone === '') {
            $this->addError('phone', 'Введите номер телефона');

            return;
        }

        // Simulate SMS code (in production, use actual SMS gateway)
        $code = (string) random_int(1000, 9999);
        $this->smsCode = $code; // In production, don't show this
        $this->smsSent = true;

        $this->dispatch('sms-sent', code: $code); // For testing
    }

    public function verifySmsCode(): void
    {
        // In production, compare with actual sent code
        if ($this->smsCode === '') {
            $this->addError('smsCode', 'Введите код из СМС');

            return;
        }

        // For testing, we accept any 4-digit code
        // Find or create user by phone
        $user = User::firstOrCreate(
            ['phone' => $this->phone],
            ['password' => bcrypt(str()->random(32))],
        );

        $this->authUserId = $user->id;
        auth()->login($user);
    }

    public function processPayment(): void
    {
        $cartService = app(CartServiceInterface::class);
        $cartItems = $cartService->getItems();

        if (empty($cartItems)) {
            $this->dispatch('cart-empty');

            return;
        }

        $itemData = [];

        foreach ($cartItems as $item) {
            $itemData[] = [
                'analysis_id' => $item['analysis_id'],
                'quantity' => $item['quantity'],
            ];
        }

        $appointmentDatetime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $this->selectedDate.' '.$this->selectedTime,
        );

        $dto = new CheckoutDTO(
            userId: $this->authUserId,
            laboratoryId: $this->selectedLaboratoryId,
            appointmentDatetime: $appointmentDatetime,
            items: $itemData,
            paymentMethod: $this->paymentMethod,
            promoCode: $this->promoDiscount !== null ? $this->promoCode : null,
            firstName: $this->firstName,
            lastName: $this->lastName,
            middleName: $this->middleName,
            birthDate: $this->birthDate,
            gender: $this->gender,
        );

        $orderCreationAction = app(OrderCreationAction::class);

        try {
            $order = $orderCreationAction->execute($dto);

            // Save profile data if provided
            if ($this->authUserId !== null) {
                Profile::updateOrCreate(
                    ['user_id' => $this->authUserId],
                    [
                        'first_name' => $this->firstName,
                        'last_name' => $this->lastName,
                        'middle_name' => $this->middleName,
                        'birth_date' => $this->birthDate,
                        'gender' => $this->gender,
                    ],
                );
            }

            $this->dispatch('order-created', orderId: $order->id);
            $this->step = 5; // Success step
        } catch (\RuntimeException $e) {
            $this->addError('checkout', $e->getMessage());
        }
    }

    #[Computed]
    public function laboratories(): Collection
    {
        return Laboratory::active()->get();
    }

    /**
     * @return array<int, SlotDTO>
     */
    #[Computed]
    public function availableSlots(): array
    {
        if ($this->selectedLaboratoryId === null || $this->selectedDate === null) {
            return [];
        }

        $action = app(GetAvailableSlotsAction::class);

        return $action->execute(
            $this->selectedLaboratoryId,
            Carbon::parse($this->selectedDate),
        );
    }

    #[Computed]
    public function cartItems(): array
    {
        return app(CartServiceInterface::class)->getItems();
    }

    #[Computed]
    public function cartTotal(): float
    {
        $total = app(CartServiceInterface::class)->getTotal();

        if ($this->promoDiscount !== null) {
            if ($this->promoDiscount['type'] === 'percent') {
                $total -= $total * ($this->promoDiscount['value'] / 100);
            } else {
                $total -= $this->promoDiscount['value'];
            }
        }

        return max(0, $total);
    }

    public function removeFromCart(int $analysisId): void
    {
        app(CartServiceInterface::class)->remove($analysisId);
        $this->dispatch('cart-removed');
    }

    public function updateCartQuantity(int $analysisId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeFromCart($analysisId);

            return;
        }

        app(CartServiceInterface::class)->updateQuantity($analysisId, $quantity);
        $this->dispatch('cart-updated');
    }

    private function validateStep(int $step): bool
    {
        return match ($step) {
            1 => ! empty($this->cartItems),
            2 => $this->selectedLaboratoryId !== null
                && $this->selectedDate !== null
                && $this->selectedTime !== null,
            3 => $this->authUserId !== null || ($this->firstName !== '' && $this->lastName !== ''),
            4 => true,
            default => false,
        };
    }

    public function render(): View
    {
        return view('livewire.checkout-wizard-component', [
            'laboratories' => $this->laboratories,
            'availableSlots' => $this->availableSlots,
            'cartItems' => $this->cartItems,
            'cartTotal' => $this->cartTotal,
        ])->layout('layouts.app');
    }
}
