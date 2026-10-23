<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventTypeController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\GalleryCategoryController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\InventoryCategoryController;
use App\Http\Controllers\Admin\InventoryItemController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Client\BookingController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\EventController as ClientEventController;
use App\Http\Controllers\Client\QuotationController as ClientQuotationController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Website\AboutController;
use App\Http\Controllers\Website\ContactController;
use App\Http\Controllers\Website\GalleryController as WebsiteGalleryController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\PackagesController;
use App\Http\Controllers\Website\ServicesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Website
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/services', [ServicesController::class, 'index'])->name('services');
Route::get('/packages', [PackagesController::class, 'index'])->name('packages');
Route::get('/gallery', [WebsiteGalleryController::class, 'index'])->name('gallery');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');

    Route::get('/register', [RegisterController::class, 'create'])->name('client.register');
    Route::post('/register', [RegisterController::class, 'store'])->name('client.register.store');
});

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');
        Route::post('/users/{user}/send-password-reset', [UserController::class, 'sendPasswordReset'])
            ->name('users.send-password-reset');

        // Roles
        Route::resource('roles', RoleController::class);
        Route::patch('/roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])
            ->name('roles.toggle-status');

        // Permissions
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');

        // Clients
        Route::resource('clients', ClientController::class);
        Route::patch('/clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])
            ->name('clients.toggle-status');

        // Event Types
        Route::resource('event-types', EventTypeController::class);

        // Packages
        Route::resource('packages', PackageController::class);

        // Services
        Route::patch('/services/{service}/toggle-status', [
            ServiceController::class,
            'toggleStatus',
        ])->whereNumber('service')->name('services.toggle-status');

        Route::resource('services', ServiceController::class)
            ->except(['show', 'destroy']);

        // Inventory Module
        Route::prefix('inventory')->name('inventory.')->group(function (): void {
            Route::resource('categories', InventoryCategoryController::class)->except(['show', 'create', 'edit']);
            Route::resource('items', InventoryItemController::class);
        });

        // Bookings
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'changeStatus'])->name('bookings.change-status');
        Route::patch('/bookings/{booking}/notes', [AdminBookingController::class, 'updateNotes'])->name('bookings.update-notes');
        Route::delete('/bookings/{booking}', [AdminBookingController::class, 'destroy'])->name('bookings.destroy');

        // Quotations
        Route::get('/quotations', [QuotationController::class, 'index'])
            ->name('quotations.index');
        Route::get('/quotations/create', [QuotationController::class, 'create'])
            ->name('quotations.create');
        Route::post('/quotations', [QuotationController::class, 'store'])
            ->name('quotations.store');
        Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])
            ->name('quotations.edit');
        Route::put('/quotations/{quotation}', [QuotationController::class, 'update'])
            ->name('quotations.update');
        Route::patch('/quotations/{quotation}/status', [QuotationController::class, 'changeStatus'])
            ->name('quotations.change-status');
        Route::delete('/quotations/{quotation}', [QuotationController::class, 'destroy'])
            ->name('quotations.destroy');

        // Quotation Items Routes
        Route::post('/quotations/{quotation}/items', [QuotationController::class, 'addItem'])
            ->name('quotations.items.store');
        Route::put('/quotations/{quotation}/items/{item}', [QuotationController::class, 'updateItem'])
            ->name('quotations.items.update');
        Route::delete('/quotations/{quotation}/items/{item}', [QuotationController::class, 'removeItem'])
            ->name('quotations.items.destroy');

        // Quotation Details
        Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])
            ->name('quotations.show');

        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'index'])
            ->name('invoices.index');
        Route::get('/invoices/create', [InvoiceController::class, 'create'])
            ->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])
            ->name('invoices.store');
        Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])
            ->name('invoices.edit');
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])
            ->name('invoices.update');
        Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'changeStatus'])
            ->name('invoices.status');
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])
            ->name('invoices.destroy');

        // Invoice Items Routes
        Route::post('/invoices/{invoice}/items', [InvoiceController::class, 'addItem'])
            ->name('invoices.items.store');
        Route::put('/invoices/{invoice}/items/{item}', [InvoiceController::class, 'updateItem'])
            ->name('invoices.items.update');
        Route::delete('/invoices/{invoice}/items/{item}', [InvoiceController::class, 'removeItem'])
            ->name('invoices.items.destroy');

        // Invoice Details
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])
            ->name('invoices.show');

        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])
            ->name('payments.index');
        Route::get('/payments/create', [PaymentController::class, 'create'])
            ->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])
            ->name('payments.store');
        Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])
            ->name('payments.edit');
        Route::put('/payments/{payment}', [PaymentController::class, 'update'])
            ->name('payments.update');
        Route::patch('/payments/{payment}/status', [PaymentController::class, 'changeStatus'])
            ->name('payments.change-status');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])
            ->name('payments.destroy');

        // Payment Details
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])
            ->whereNumber('payment')
            ->name('payments.show');

        // Events
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::patch('/events/{event}/status', [EventController::class, 'changeStatus'])->name('events.change-status');

        // Event Services
        Route::post('/events/{event}/services', [EventController::class, 'addService'])->name('events.services.store');
        Route::put('/events/{event}/services/{eventService}', [EventController::class, 'updateService'])->name('events.services.update');
        Route::delete('/events/{event}/services/{eventService}', [EventController::class, 'removeService'])->name('events.services.destroy');

        // Event Details
        Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

        // Gallery Categories
        Route::get('/gallery/categories', [GalleryCategoryController::class, 'index'])
            ->name('gallery.categories.index');
        Route::post('/gallery/categories', [GalleryCategoryController::class, 'store'])
            ->name('gallery.categories.store');
        Route::put('/gallery/categories/{category}', [GalleryCategoryController::class, 'update'])
            ->name('gallery.categories.update');
        Route::delete('/gallery/categories/{category}', [GalleryCategoryController::class, 'destroy'])
            ->name('gallery.categories.destroy');

        // Gallery Images
        Route::get('/gallery', [GalleryController::class, 'index'])
            ->name('gallery.index');
        Route::get('/gallery/create', [GalleryController::class, 'create'])
            ->name('gallery.create');
        Route::post('/gallery', [GalleryController::class, 'store'])
            ->name('gallery.store');
        Route::get('/gallery/{image}/edit', [GalleryController::class, 'edit'])
            ->name('gallery.edit');
        Route::put('/gallery/{image}', [GalleryController::class, 'update'])
            ->name('gallery.update');
        Route::patch('/gallery/{image}/featured', [GalleryController::class, 'toggleFeatured'])
            ->name('gallery.featured');
        Route::patch('/gallery/{image}/status', [GalleryController::class, 'toggleStatus'])
            ->name('gallery.status');
        Route::delete('/gallery/{image}', [GalleryController::class, 'destroy'])
            ->name('gallery.destroy');

        // Testimonials
        Route::get('/testimonials', [TestimonialController::class, 'index'])
            ->name('testimonials.index');
        Route::get('/testimonials/create', [TestimonialController::class, 'create'])
            ->name('testimonials.create');
        Route::post('/testimonials', [TestimonialController::class, 'store'])
            ->name('testimonials.store');
        Route::get('/testimonials/{testimonial}/edit', [TestimonialController::class, 'edit'])
            ->name('testimonials.edit');
        Route::put('/testimonials/{testimonial}', [TestimonialController::class, 'update'])
            ->name('testimonials.update');
        Route::patch('/testimonials/{testimonial}/approval', [TestimonialController::class, 'toggleApproval'])
            ->name('testimonials.approval');
        Route::delete('/testimonials/{testimonial}', [TestimonialController::class, 'destroy'])
            ->name('testimonials.destroy');

        // FAQs
        Route::get('/faqs', [FaqController::class, 'index'])->name('faqs.index');
        Route::get('/faqs/create', [FaqController::class, 'create'])->name('faqs.create');
        Route::post('/faqs', [FaqController::class, 'store'])->name('faqs.store');
        Route::get('/faqs/{faq}/edit', [FaqController::class, 'edit'])->name('faqs.edit');
        Route::put('/faqs/{faq}', [FaqController::class, 'update'])->name('faqs.update');
        Route::patch('/faqs/{faq}/status', [FaqController::class, 'toggleStatus'])->name('faqs.status');
        Route::delete('/faqs/{faq}', [FaqController::class, 'destroy'])->name('faqs.destroy');
    });

/*
|--------------------------------------------------------------------------
| Staff
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function (): void {
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    });

/*
|--------------------------------------------------------------------------
| Client Portal
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('client')
    ->name('client.')
    ->group(function (): void {
        Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
        Route::resource('bookings', BookingController::class);

        // Client Events
        Route::get('/events', [ClientEventController::class, 'index'])->name('events.index');
        Route::get('/events/{event}', [ClientEventController::class, 'show'])->name('events.show');

        // Client Quotations
        Route::get('/quotations', [ClientQuotationController::class, 'index'])->name('quotations.index');
        Route::get('/quotations/{quotation}', [ClientQuotationController::class, 'show'])->name('quotations.show');

        // Client Invoices & Payments
        Route::get('/invoices', [ClientDashboardController::class, 'index'])->name('invoices.index');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/invoices/{invoice}/pay', [PaymentController::class, 'store'])->name('payments.store');
    });
