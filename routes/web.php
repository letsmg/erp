<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SelfClientAuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SearchSuggestionsController;
use App\Http\Controllers\RedisMonitorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| 1. VITRINE (PÚBLICO)
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [SitemapController::class, 'index']);

Route::get('/', [StoreController::class, 'index'])->name('store.index');

// Endpoint JSON para Load More
Route::get('/store/products', [StoreController::class, 'getProducts'])->name('store.products.json');

// ✅ STORE USA SLUG
Route::get('/store/product/{product:slug}', [StoreController::class, 'show'])
    ->name('store.product');

// (opcional futuro)
// Route::get('/store/category/{category:slug}', ...);

Route::post('/terms/accept', [StoreController::class, 'acceptTerms'])
    ->name('store.terms.accept');

/*
|--------------------------------------------------------------------------
| 2. AUTH (GUEST)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');    
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/forgot-password', [LoginController::class, 'showForgotPassword'])
        ->name('password.request');

    Route::post('/forgot-password', [LoginController::class, 'sendResetLinkEmail'])
        ->name('password.email');
});

/*
|--------------------------------------------------------------------------
| 2.1. CLIENT AUTH (GUEST)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->prefix('cliente')->name('client.')->group(function () {
    Route::get('/login', [SelfClientAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [SelfClientAuthController::class, 'login'])->name('login.post');
    Route::get('/registrar', [SelfClientAuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/registrar', [SelfClientAuthController::class, 'register'])->name('register.post');
    Route::get('/esqueci-senha', [SelfClientAuthController::class, 'showForgotPassword'])->name('forgot.password');
    Route::post('/esqueci-senha', [SelfClientAuthController::class, 'sendResetLinkEmail'])->name('forgot.password.post');
    Route::get('/verificar-email/{id}/{hash}', [SelfClientAuthController::class, 'verifyEmail'])
        ->middleware(['signed'])
        ->name('verification.verify');
});

/*
|--------------------------------------------------------------------------
| 3. ADMIN (AUTH + STAFF)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'staff'])->group(function () {

    // Dashboard
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | PRODUCTS
    |--------------------------------------------------------------------------
    */

    // 👁️ PREVIEW (FORÇANDO ID)
    Route::get('/products/{product:id}/preview', [ProductController::class, 'preview'])
        ->name('products.preview');

    // ⭐ FEATURED (FORÇANDO ID)
    Route::patch('/products/{product:id}/toggle-featured', [ProductController::class, 'toggleFeatured'])
        ->name('products.toggle-featured');

    // 🔥 ATIVAR / DESATIVAR (IMPORTANTE: também forçar ID)
    Route::patch('/products/{product:id}/toggle', [ProductController::class, 'toggle'])
        ->name('products.toggle');

    // CRUD
    Route::resource('products', ProductController::class);

    /*
    |--------------------------------------------------------------------------
    | OUTROS
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth', 'staff')->prefix('suppliers')->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::get('/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
});

    /*
    |--------------------------------------------------------------------------
    | RELATÓRIOS
    |--------------------------------------------------------------------------
    */

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/products', [ReportController::class, 'products'])->name('reports.products');
    Route::get('/reports/clients', [ReportController::class, 'clients'])->name('reports.clients');

    /*
    |--------------------------------------------------------------------------
    | USERS
    |--------------------------------------------------------------------------
    */

    Route::resource('users', UserController::class);    

    Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.reset');

    Route::patch('/users/{user}/toggle', [UserController::class, 'toggleStatus'])
        ->name('users.toggle');

    /*
    |--------------------------------------------------------------------------
    | CLIENTS (ADMIN)
    |--------------------------------------------------------------------------
    */

    Route::resource('clients', ClientController::class);
    Route::get('/clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])->name('clients.toggle.status');
    Route::post('/clients/validate-document', [ClientController::class, 'validateDocument'])->name('clients.validate.document');
    Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::get('/clients/export', [ClientController::class, 'export'])->name('clients.export');

    /*
    |--------------------------------------------------------------------------
    | VENDAS (ADMIN)
    |--------------------------------------------------------------------------
    */

    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::get('/{id}', [SaleController::class, 'show'])->name('show');
        Route::patch('/{id}/status', [SaleController::class, 'updateStatus'])->name('update.status');
        Route::post('/{id}/cancel', [SaleController::class, 'cancel'])->name('cancel');
    });

    /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {
        // rotas exclusivas
    });
});

/*
|--------------------------------------------------------------------------
| 4. CLIENT AREA (AUTH)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'client'])->prefix('cliente')->name('client.')->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Client/Dashboard'))->name('dashboard');
    Route::get('/meus-dados', [SelfClientAuthController::class, 'profile'])->name('profile');
    Route::get('/editar', [SelfClientAuthController::class, 'edit'])->name('edit');
    Route::put('/meus-dados', [SelfClientAuthController::class, 'update'])->name('profile.update');
    Route::put('/senha', [SelfClientAuthController::class, 'updatePassword'])->name('password.update');
    Route::get('/excluir', [SelfClientAuthController::class, 'showDeleteForm'])->name('delete.form');
    Route::delete('/excluir', [SelfClientAuthController::class, 'destroy'])->name('delete');
    Route::get('/api/dados', [SelfClientAuthController::class, 'getClientData'])->name('api.data');
    Route::post('/logout', [SelfClientAuthController::class, 'logout'])->name('logout');
    
    // Gerenciamento de Endereços
    Route::post('/endereco', [SelfClientAuthController::class, 'storeAddress'])->name('address.store');
    Route::put('/endereco/{address}', [SelfClientAuthController::class, 'updateAddress'])->name('address.update');
    Route::delete('/endereco/{address}', [SelfClientAuthController::class, 'destroyAddress'])->name('address.destroy');
    Route::patch('/endereco/{address}/entrega', [SelfClientAuthController::class, 'setDeliveryAddress'])->name('address.set-delivery');

    // Pedidos (Compras)
    Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pedidos/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/pedidos/{id}/cancelar', [OrderController::class, 'cancel'])->name('orders.cancel');
});

/*
|--------------------------------------------------------------------------
| CHECKOUT (AUTH - CLIENT)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'client'])->prefix('checkout')->name('checkout.')->group(function () {
    Route::post('/pedido', [CheckoutController::class, 'createOrder'])->name('create.order');
    Route::post('/pagamento/{saleId}', [CheckoutController::class, 'createPaymentIntent'])->name('create.payment');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/sucesso/{saleId}', [CheckoutController::class, 'checkoutSuccess'])->name('success');
    Route::get('/falha/{saleId}', [CheckoutController::class, 'failure'])->name('failure');
    Route::get('/pendente/{saleId}', [CheckoutController::class, 'pending'])->name('pending');
    Route::post('/webhook', [CheckoutController::class, 'webhook'])->name('webhook');
});

/*
|--------------------------------------------------------------------------
| STRIPE WEBHOOK (PÚBLICO)
|--------------------------------------------------------------------------
*/

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook')
    ->withoutMiddleware(['web', 'auth']);

/*
|--------------------------------------------------------------------------
| SEARCH SUGGESTIONS (INTERNO)
|--------------------------------------------------------------------------
*/

Route::prefix('search')->group(function () {
    // Obtém sugestões para autocomplete
    Route::get('/suggestions', [SearchSuggestionsController::class, 'index'])
        ->name('search.suggestions');
    
    // Registra busca do usuário
    Route::post('/register', [SearchSuggestionsController::class, 'register'])
        ->name('search.register');
    
    // Obtém palavras mais pesquisadas (trending)
    Route::get('/trending', [SearchSuggestionsController::class, 'trending'])
        ->name('search.trending');
    
    // Estatísticas das buscas
    Route::get('/stats', [SearchSuggestionsController::class, 'stats'])
        ->name('search.stats');
});

/*
|--------------------------------------------------------------------------
| REDIS MONITORING (INTERNO)
|--------------------------------------------------------------------------
*/

Route::prefix('redis')->group(function () {
    // Métricas atuais do Redis
    Route::get('/metrics', [RedisMonitorController::class, 'metrics'])
        ->name('redis.metrics');
    
    // Histórico de métricas
    Route::get('/history', [RedisMonitorController::class, 'history'])
        ->name('redis.history');
});