<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\InvitationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\AccountantDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\MoneyFusionController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\PenaltyController;
use App\Http\Controllers\LitigeController;
use App\Http\Controllers\EtatDesLieuxController;
use Illuminate\Support\Facades\Route;

// Home redirect
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:3,1');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update')->middleware('throttle:5,1');
});

// Invitation — accessible sans être connecté (le token fait office d'auth)
Route::get('/invitation/{token}', [InvitationController::class, 'show'])->name('invitation.show');
Route::post('/invitation/{token}', [InvitationController::class, 'accept'])->name('invitation.accept');

// Webhooks de paiement — sans authentification (appelés par des serveurs tiers)
Route::post('/moneyfusion/webhook', [MoneyFusionController::class, 'webhook'])->name('moneyfusion.webhook');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Routes administratives - Interdites aux propriétaires et locataires
    Route::middleware('restrict.admin')->group(function () {
        // Properties
        Route::resource('properties', PropertyController::class);
        
        // Tenants
        Route::resource('tenants', TenantController::class);
        
        // Contracts
        Route::resource('contracts', ContractController::class);
        Route::get('/contracts/{contract}/download', [ContractController::class, 'download'])->name('contracts.download');
        Route::post('/contracts/{contract}/sign', [ContractController::class, 'sign'])->name('contracts.sign');
        
        // Payments (Rents)
        Route::resource('rents', PaymentController::class);
        Route::get('/rents/{payment}/receipt', [PaymentController::class, 'downloadReceipt'])->name('rents.receipt');
        
        // Owners
        Route::resource('owners', OwnerController::class);
        
        // Expenses
        Route::resource('expenses', ExpenseController::class);
        
        // Accounts
        Route::resource('accounts', AccountController::class);
        
        // Transactions
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        
        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
        
        // Document Templates
        Route::resource('document-templates', DocumentTemplateController::class);
        Route::get('/document-templates/{documentTemplate}/generate', [DocumentTemplateController::class, 'generate'])->name('document-templates.generate');
        Route::post('/document-templates/{documentTemplate}/generate', [DocumentTemplateController::class, 'storeGenerated'])->name('document-templates.store-generated');
        
        // OCR
        Route::get('/ocr', [OcrController::class, 'index'])->name('ocr.index');
        Route::post('/ocr/process', [OcrController::class, 'process'])->name('ocr.process');
        Route::get('/ocr/download/{path}', [OcrController::class, 'download'])->name('ocr.download');
        
        // MoneyFusion (Fusion Pay)
        Route::post('/moneyfusion/initiate', [MoneyFusionController::class, 'initiate'])->name('moneyfusion.initiate');
        Route::get('/moneyfusion/return', [MoneyFusionController::class, 'return'])->name('moneyfusion.return');
        Route::get('/moneyfusion/check-status', [MoneyFusionController::class, 'checkStatus'])->name('moneyfusion.check-status');
        
        // Administration : réservé à super_admin et admin_agence (gestionnaire bloqué)
        Route::middleware('admin.only')->group(function () {
            Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
            Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
            Route::resource('agencies', AgencyController::class);

            // Gestion des utilisateurs
            Route::resource('users', UserController::class)->except(['show']);
            Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
            Route::post('/users/{user}/resend-invitation', [UserController::class, 'resendInvitation'])->name('users.resend-invitation');
        });
        
        // Penalties
        Route::get('/penalties', [PenaltyController::class, 'index'])->name('penalties.index');
        Route::get('/penalties/create', [PenaltyController::class, 'create'])->name('penalties.create');
        Route::post('/penalties', [PenaltyController::class, 'store'])->name('penalties.store');
        Route::get('/penalties/{penalty}', [PenaltyController::class, 'show'])->name('penalties.show');
        Route::get('/penalties/{penalty}/edit', [PenaltyController::class, 'edit'])->name('penalties.edit');
        Route::put('/penalties/{penalty}', [PenaltyController::class, 'update'])->name('penalties.update');
        Route::put('/penalties/{penalty}/mark-as-paid', [PenaltyController::class, 'markAsPaid'])->name('penalties.mark-as-paid');
        Route::delete('/penalties/{penalty}', [PenaltyController::class, 'destroy'])->name('penalties.destroy');

        // États des lieux
        Route::get('/etat-des-lieux', [EtatDesLieuxController::class, 'index'])->name('etat-des-lieux.index');
        Route::get('/etat-des-lieux/create', [EtatDesLieuxController::class, 'create'])->name('etat-des-lieux.create');
        Route::post('/etat-des-lieux', [EtatDesLieuxController::class, 'store'])->name('etat-des-lieux.store');
        Route::get('/etat-des-lieux/{etatDesLieux}', [EtatDesLieuxController::class, 'show'])->name('etat-des-lieux.show');
        Route::get('/etat-des-lieux/{etatDesLieux}/edit', [EtatDesLieuxController::class, 'edit'])->name('etat-des-lieux.edit');
        Route::put('/etat-des-lieux/{etatDesLieux}', [EtatDesLieuxController::class, 'update'])->name('etat-des-lieux.update');
        Route::delete('/etat-des-lieux/{etatDesLieux}', [EtatDesLieuxController::class, 'destroy'])->name('etat-des-lieux.destroy');

        // Litiges immobiliers
        Route::get('/litiges/rapport', [LitigeController::class, 'rapport'])->name('litiges.rapport');
        Route::get('/litiges/export/excel', [LitigeController::class, 'exportExcel'])->name('litiges.export.excel');
        Route::get('/litiges/export/pdf', [LitigeController::class, 'exportPdf'])->name('litiges.export.pdf');
        Route::get('/litiges/export/word', [LitigeController::class, 'exportWord'])->name('litiges.export.word');
        Route::resource('litiges', LitigeController::class);

    });
    
    // Reports - Accessible aux admins et propriétaires
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPDF'])->name('reports.export.pdf');
    
    // Owner Dashboard - Accès réservé aux propriétaires
    Route::prefix('owner')->name('owner.')->middleware('role.owner')->group(function () {
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    });
    
    // Accountant Dashboard - Accès réservé aux comptables
    Route::prefix('accountant')->name('accountant.')->middleware('role.accountant')->group(function () {
        Route::get('/dashboard', [AccountantDashboardController::class, 'index'])->name('dashboard');
    });
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/agency', [SettingsController::class, 'updateAgency'])->name('settings.agency');
    Route::put('/settings/application', [SettingsController::class, 'updateApplication'])->name('settings.application');
    Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');
    Route::put('/settings/payment-gateway', [SettingsController::class, 'updatePaymentGateway'])->name('settings.payment-gateway');
    Route::delete('/settings/logo', [SettingsController::class, 'deleteLogo'])->name('settings.logo.delete');
    Route::delete('/settings/favicon', [SettingsController::class, 'deleteFavicon'])->name('settings.favicon.delete');
    Route::put('/settings/email', [SettingsController::class, 'updateEmail'])->name('settings.email');
    Route::post('/settings/email/test', [SettingsController::class, 'testEmail'])->name('settings.email.test');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});
