<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Staff\ProductionStaffController;
use App\Http\Controllers\Staff\Warehouse\{
    StockController,
    InventoryController,
    BatchController,
    MovementController
};
use App\Http\Controllers\Staff\QC\{QCController, QCReviewController, QCLogController};
use App\Http\Controllers\Staff\Production\ProductionController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware("guest")->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin only
    Route::middleware(['role.dept:admin'])->group(function () {
        Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
            ->name('dashboard.admin');
    });

    // Manager only
    Route::middleware(['role.dept:manager'])->group(function () {
        Route::get('/dashboard/manager', [DashboardController::class, 'manager'])
            ->name('dashboard.manager');
    });

    // Route::middleware(['role.dept:staff,production'])->group(function () {
    //     // Production staff routes
    //     Route::get('/staff/production', [ProductionStaffController::class, 'index'])->name('production.index');
    //     Route::get('/staff/production/create', [ProductionStaffController::class, 'create'])->name('production.create');
    //     Route::get('/staff/production/{id}', [ProductionStaffController::class, 'show'])->name('production.show');

    //     Route::post('/staff/production', [ProductionStaffController::class, 'store'])->name('production.store');
    //     Route::put('/staff/production/{id}', [ProductionStaffController::class, 'update'])->name('production.update');
    //     Route::delete('/staff/production/{id}', [ProductionStaffController::class, 'destroy'])->name('production.destroy');

    //     Route::post('/staff/production/{id}/add-material', [ProductionStaffController::class, 'addMaterial'])->name('production.addMaterial');
    //     Route::post('/staff/production/{id}/start', [ProductionStaffController::class, 'start'])->name('production.start');
    //     Route::post('/staff/production/{id}/complete', [ProductionStaffController::class, 'complete'])->name('production.complete');
    // });

    // Staff only
    Route::middleware(['role.dept:staff,production'])->prefix('production')->group(function () {
        // List & Details
        Route::get('/', [ProductionController::class, 'index'])->name('production.index');

        // Create / Store new batch (static route must be before dynamic {id})
        Route::get('/create', [ProductionController::class, 'create'])->name('production.create');
        Route::post('/', [ProductionController::class, 'store'])->name('production.store');

        // Details (dynamic) - keep after static routes to avoid catching paths like 'create'
        Route::get('/{id}', [ProductionController::class, 'show'])->name('production.show');

        // Start / Complete / Update Status
        Route::post('/{id}/start', [ProductionController::class, 'start'])->name('production.start');
        Route::post('/{id}/complete', [ProductionController::class, 'complete'])->name('production.complete');
        Route::patch('/{id}/status', [ProductionController::class, 'updateStatus'])->name('production.update-status');

        // Record Materials
        Route::get('/{id}/materials', [ProductionController::class, 'show'])->name('production.materials'); // could reuse show page
        Route::post('/{id}/materials', [ProductionController::class, 'recordMaterials'])->name('production.record-materials');
    });

    // Route::middleware(['role.dept:staff,qc'])->group(function () {
    //     // QC staff routes
    //     Route::get('/staff/qc', [QCStaffController::class, 'index'])->name('qc.index');
    //     Route::get('/staff/qc/{id}', [QCStaffController::class, 'show'])->name('qc.show');
    //     Route::post('/staff/qc/{id}/inspect', [QCStaffController::class, 'inspect'])->name('qc.inspect');
    //     Route::get('/staff/qc/logs', [QCStaffController::class, 'logs'])->name('qc.logs');
    // });

    Route::middleware(['role.dept:staff,qc'])->prefix('qc')->group(function () {
        Route::get('/', [QCController::class, 'index'])->name('qc.index');
        Route::get('/review/{id}', [QCReviewController::class, 'show'])->name('qc.review.show');
        Route::post('/review/{id}', [QCReviewController::class, 'store'])->name('qc.review.store');
        Route::get('/logs', [QCLogController::class, 'index'])->name('qc.logs');
    });

    // Route::middleware(['role.dept:staff,warehouse'])->group(function () {
    //     // Warehouse staff routes
    //     Route::get('/staff/warehouse/', [WarehouseStaffController::class, 'index'])->name('warehouse.index');
    //     Route::get('/staff/warehouse/inventory', [WarehouseStaffController::class, 'inventory'])->name('warehouse.inventory');
    //     Route::post('/staff/warehouse/stock-in', [WarehouseStaffController::class, 'stockIn'])->name('stock.in');
    //     Route::post('/staff/warehouse/stock-out', [WarehouseStaffController::class, 'stockOut'])->name('stock.out');
    //     Route::get('/staff/warehouse/raw/{id}/available', [WarehouseStaffController::class, 'availableForBatch'])->name('raw.available');
    // });

    Route::middleware(['role.dept:staff,warehouse'])->prefix('warehouse')->group(function () {
        Route::get('/stock', [StockController::class, 'index'])->name('warehouse.stock.index');
        Route::post('/stock/in', [StockController::class, 'store'])->name('warehouse.stock.store');
        Route::post('/stock/out', [StockController::class, 'out'])->name('warehouse.stock.out');

        Route::get('/inventory', [InventoryController::class, 'index'])->name('warehouse.inventory.index');

        Route::get('/batches', [BatchController::class, 'index'])->name('warehouse.batches.index');
        Route::get('/batches/{id}', [BatchController::class, 'show'])->name('warehouse.batches.show');

        Route::get('/movements', [MovementController::class, 'index'])->name('warehouse.movements.index');
    });
});