<?php

use App\Livewire\Auth\VerifyCode;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\ServiceBids as AdminServiceBids;
use App\Livewire\Admin\ServiceCategories as AdminServiceCategories;
use App\Livewire\Admin\ServiceFormFields as AdminServiceFormFields;
use App\Livewire\Admin\ServiceRequests as AdminServiceRequests;
use App\Livewire\Admin\Tenants as AdminTenants;
use App\Livewire\Admin\Users as AdminUsers;
use App\Livewire\Client\ServiceRequests\Index as ClientServiceRequestsIndex;
use App\Livewire\Client\ServiceRequests\Show as ClientServiceRequestsShow;
use App\Livewire\Marketing\Landing;
use App\Livewire\Marketing\PublicServicesBrowse as PublicServicesBrowse;
use App\Livewire\Services\Browse as ServicesBrowse;
use App\Livewire\Services\Show as ServicesShow;
use App\Http\Controllers\ServiceRequestAttachmentController;
use Illuminate\Support\Facades\Route;

Route::livewire('/', Landing::class)->name('home');

Route::livewire('explorar-servicios', PublicServicesBrowse::class)->name('public.services.browse');

Route::livewire('verify-code', VerifyCode::class)
    ->middleware(['auth'])
    ->name('verify-code');

Route::middleware(['auth', 'email.code'])->group(function () {
    Route::livewire('services', ServicesBrowse::class)->name('services.browse');
    Route::livewire('services/{serviceRequest}', ServicesShow::class)->name('services.show');

    Route::get('attachments/{attachment}', ServiceRequestAttachmentController::class)
        ->name('attachments.show');

    Route::livewire('client/requests', ClientServiceRequestsIndex::class)->name('client.requests.index');
    Route::livewire('client/requests/{serviceRequest}', ClientServiceRequestsShow::class)->name('client.requests.show');
});

Route::middleware(['auth', 'email.code', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::livewire('/', AdminDashboard::class)->name('dashboard');
        Route::livewire('tenants', AdminTenants::class)->name('tenants');
        Route::livewire('users', AdminUsers::class)->name('users');
        Route::livewire('service-requests', AdminServiceRequests::class)->name('service-requests');
        Route::livewire('service-bids', AdminServiceBids::class)->name('service-bids');
        Route::livewire('service-categories', AdminServiceCategories::class)->name('service-categories');
        Route::livewire('service-form', AdminServiceFormFields::class)->name('service-form');
    });

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'email.code'])
    ->name('dashboard');

require __DIR__.'/settings.php';
