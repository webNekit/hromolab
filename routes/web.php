<?php

declare(strict_types=1);

use App\Http\Livewire\AboutPage;
use App\Http\Livewire\AuthPage;
use App\Http\Livewire\CatalogComponent;
use App\Http\Livewire\CheckoutWizardComponent;
use App\Http\Livewire\ContactsPage;
use App\Http\Livewire\HomePage;
use App\Http\Livewire\PatientDashboardComponent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Home
Route::get('/', HomePage::class)->name('home');

// Static pages
Route::get('/about', AboutPage::class)->name('about');
Route::get('/contacts', ContactsPage::class)->name('contacts');

// Public catalog
Route::get('/catalog', CatalogComponent::class)->name('catalog');

// Checkout (requires auth or guest access)
Route::get('/checkout', CheckoutWizardComponent::class)->name('checkout');

// Patient routes (requires auth)
Route::middleware(['auth'])->group(function (): void {
    Route::get('/dashboard', PatientDashboardComponent::class)->name('patient.dashboard');

    // Secure medical result download
    Route::get('/results/{medicalResult}/download', function (MedicalResult $medicalResult) {
        abort_unless($medicalResult->orderItem->order->user_id === auth()->id(), 403);

        // Log the download
        $medicalResult->incrementDownloads();

        if (! $medicalResult->pdf_path) {
            abort(404, 'PDF file not available');
        }

        return Storage::disk('secure_medical_results')
            ->download($medicalResult->pdf_path);
    })->name('patient.results.download');
});

// Auth for patient web
Route::middleware(['guest'])->group(function (): void {
    Route::get('/login', AuthPage::class)->name('login');
    Route::get('/register', AuthPage::class)->name('register');
});

Route::post('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('home');
})->middleware(['auth'])->name('logout');
