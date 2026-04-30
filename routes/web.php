<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicAdvertController;
use Illuminate\Support\Facades\Route;

// ----------------------------------------------------------------
// Public
// ----------------------------------------------------------------
Route::get('/', HomeController::class)->name('home');
Route::get('/watches', [PublicAdvertController::class, 'index'])->name('market.index');
Route::get('/watches/{advert}', [PublicAdvertController::class, 'show'])->name('market.show');
Route::get('/sell-watch', [App\Http\Controllers\SellerController::class, 'index'])->name('sell-watch');
Route::post('/stripe/webhook', [App\Http\Controllers\StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// ----------------------------------------------------------------
// Authenticated (all roles)
// ----------------------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', fn() => redirect()->route('my-account'))->name('dashboard');
    Route::get('/my-account', [App\Http\Controllers\AccountController::class, 'index'])->name('my-account');
    Route::patch('/my-account/profile', [App\Http\Controllers\AccountController::class, 'updateProfile'])->name('my-account.profile.update');
    Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::post('/adverts/{advert}/enquire', [App\Http\Controllers\MessageController::class, 'sendFromEnquiry'])->name('messages.enquire');
    Route::get('/messages/list', [App\Http\Controllers\MessageController::class, 'list'])->name('messages.list');
    Route::get('/messages/unread-count', [App\Http\Controllers\MessageController::class, 'unreadCount'])->name('messages.unread-count');
    Route::get('/messages/{conversation}/items', [App\Http\Controllers\MessageController::class, 'messages'])->name('messages.items');
    Route::post('/messages/{conversation}/items', [App\Http\Controllers\MessageController::class, 'storeMessage'])->name('messages.items.store');
    Route::get('/invoices/{order}/pdf', [App\Http\Controllers\InvoiceController::class, 'download'])
        ->name('invoices.download');

    Route::get('/choose-account-type', [App\Http\Controllers\SellerController::class, 'chooseAccountType'])->name('seller.choose-account-type');
    Route::post('/update-account-type', [App\Http\Controllers\SellerController::class, 'updateAccountType'])->name('seller.update-account-type');
    Route::get('/trade/packages', [App\Http\Controllers\SellerController::class, 'tradePackages'])->name('seller.trade.packages');
    Route::get('/trade/checkout/{level}', [App\Http\Controllers\SellerController::class, 'tradeCheckout'])->name('seller.trade.checkout');
    Route::post('/trade/checkout/{level}', [App\Http\Controllers\SellerController::class, 'processTradeCheckout'])->name('seller.trade.checkout.process');
    Route::get('/trade/checkout/{level}/cancel/{order}', [App\Http\Controllers\SellerController::class, 'cancelTradeCheckout'])->name('seller.trade.checkout.cancel');
    Route::get('/trade/thank-you/{order}', [App\Http\Controllers\SellerController::class, 'tradeThankYou'])->name('seller.trade.thank-you');
    Route::get('/private/packages/{advert}', [App\Http\Controllers\SellerController::class, 'privatePackages'])->name('seller.private.packages');
    Route::get('/private/checkout/{advert}/{level}', [App\Http\Controllers\SellerController::class, 'privateCheckout'])->name('seller.private.checkout');
    Route::post('/private/checkout/{advert}/{level}', [App\Http\Controllers\SellerController::class, 'processPrivateCheckout'])->name('seller.private.checkout.process');
    Route::get('/private/checkout/{advert}/{level}/cancel/{order}', [App\Http\Controllers\SellerController::class, 'cancelPrivateCheckout'])->name('seller.private.checkout.cancel');
    Route::get('/private/thank-you/{order}', [App\Http\Controllers\SellerController::class, 'privateThankYou'])->name('seller.private.thank-you');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Seller: advert CRUD
    Route::resource('adverts', App\Http\Controllers\AdvertController::class);
    Route::post('/adverts/draft-photos', [App\Http\Controllers\AdvertController::class, 'uploadDraftPhoto'])
        ->name('adverts.draft-photos.upload');
    Route::delete('/adverts/draft-photos', [App\Http\Controllers\AdvertController::class, 'deleteDraftPhoto'])
        ->name('adverts.draft-photos.delete');
    Route::delete('/adverts/{advert}/images/{image}', [App\Http\Controllers\AdvertController::class, 'deleteImage'])
        ->name('adverts.images.destroy');
    Route::patch('/adverts/{advert}/pause', [App\Http\Controllers\AdvertController::class, 'togglePause'])
        ->name('adverts.pause');
    Route::patch('/adverts/{advert}/mark-sold', [App\Http\Controllers\AdvertController::class, 'markSold'])
        ->name('adverts.mark-sold');

    // Dynamic models dropdown (AJAX)
    Route::get('/api/brands/{brand}/models', [App\Http\Controllers\AdvertController::class, 'modelsByBrand'])
        ->name('api.brands.models');
});

// ----------------------------------------------------------------
// Admin panel
// ----------------------------------------------------------------
Route::middleware(['auth', 'verified', App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', App\Http\Controllers\Admin\DashboardController::class)->name('dashboard');
        Route::get('/users', [App\Http\Controllers\AdminUserController::class, 'index'])->name('users');

        Route::resource('brands', App\Http\Controllers\Admin\BrandController::class)->except(['show']);
        Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->except(['show']);

        Route::get('/tags', [App\Http\Controllers\Admin\TagController::class, 'index'])->name('tags.index');
        Route::post('/tags', [App\Http\Controllers\Admin\TagController::class, 'store'])->name('tags.store');
        Route::put('/tags/{tag}', [App\Http\Controllers\Admin\TagController::class, 'update'])->name('tags.update');
        Route::delete('/tags/{tag}', [App\Http\Controllers\Admin\TagController::class, 'destroy'])->name('tags.destroy');

        // All watch attribute types share one controller — /admin/attributes/{type}
        Route::get('/attributes/{type}', [App\Http\Controllers\Admin\AttributeOptionController::class, 'index'])->name('attributes.index');
        Route::post('/attributes/{type}', [App\Http\Controllers\Admin\AttributeOptionController::class, 'store'])->name('attributes.store');
        Route::put('/attributes/{type}/{option}', [App\Http\Controllers\Admin\AttributeOptionController::class, 'update'])->name('attributes.update');
        Route::delete('/attributes/{type}/{option}', [App\Http\Controllers\Admin\AttributeOptionController::class, 'destroy'])->name('attributes.destroy');

        Route::get('/adverts', [App\Http\Controllers\Admin\AdvertController::class, 'index'])->name('adverts.index');
        Route::patch('/adverts/{advert}/status', [App\Http\Controllers\Admin\AdvertController::class, 'updateStatus'])->name('adverts.status');
        Route::patch('/adverts/{advert}/featured', [App\Http\Controllers\Admin\AdvertController::class, 'updateFeatured'])->name('adverts.featured');
        Route::get('/adverts/{advert}', [App\Http\Controllers\Admin\AdvertController::class, 'show'])->name('adverts.show');
        Route::get('/adverts/{advert}/edit', [App\Http\Controllers\Admin\AdvertController::class, 'edit'])->name('adverts.edit');
        Route::put('/adverts/{advert}', [App\Http\Controllers\Admin\AdvertController::class, 'update'])->name('adverts.update');
        Route::delete('/adverts/{advert}', [App\Http\Controllers\Admin\AdvertController::class, 'destroy'])->name('adverts.destroy');

        Route::resource('membership-levels', App\Http\Controllers\Admin\MembershipLevelController::class)
            ->except(['show']);
        Route::get('/membership-members', [App\Http\Controllers\Admin\MembershipMemberController::class, 'index'])
            ->name('membership-members.index');
        Route::get('/membership-orders', [App\Http\Controllers\Admin\MembershipOrderController::class, 'index'])
            ->name('membership-orders.index');
        Route::get('/membership-orders/{order}/invoice', [App\Http\Controllers\InvoiceController::class, 'download'])
            ->name('membership-orders.invoice');
        Route::get('/membership-settings', [App\Http\Controllers\Admin\MembershipSettingController::class, 'edit'])
            ->name('membership-settings.edit');
        Route::put('/membership-settings', [App\Http\Controllers\Admin\MembershipSettingController::class, 'update'])
            ->name('membership-settings.update');
        Route::get('/seo-meta', [App\Http\Controllers\Admin\SeoMetaController::class, 'index'])
            ->name('seo.index');
        Route::post('/seo-meta', [App\Http\Controllers\Admin\SeoMetaController::class, 'update'])
            ->name('seo.update');
    });

require __DIR__.'/auth.php';
