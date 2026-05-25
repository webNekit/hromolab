<?php

declare(strict_types=1);

namespace App\Domains\Results\Services;

use App\Domains\Orders\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class MedicalPdfGenerator
{
    /**
     * Generate a PDF for medical results.
     *
     * @param  array<string, mixed>  $parameterValues
     * @return string Path to the generated PDF in secure storage
     */
    public function generate(OrderItem $orderItem, array $parameterValues): string
    {
        $analysis = $orderItem->analysis;
        $order = $orderItem->order;
        $patient = $order->user?->profile;

        $html = view('pages.results.pdf', [
            'order' => $order,
            'orderItem' => $orderItem,
            'analysis' => $analysis,
            'patient' => $patient,
            'parameterValues' => $parameterValues,
            'generatedAt' => now(),
            'labAssistant' => 'Лаборант: '.($order->user?->name ?? 'Не указан'),
        ])->render();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        $filename = 'result_'.$orderItem->id.'_'.now()->format('Ymd_His').'.pdf';
        $path = 'medical_results/'.now()->format('Y/m/d').'/'.$filename;

        Storage::disk('secure_medical_results')->put(
            $path,
            $pdf->output(),
            'private',
        );

        return $path;
    }
}
