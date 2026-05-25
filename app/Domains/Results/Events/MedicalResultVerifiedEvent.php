<?php

declare(strict_types=1);

namespace App\Domains\Results\Events;

use App\Domains\Results\Models\MedicalResult;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MedicalResultVerifiedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly MedicalResult $result,
    ) {}
}
