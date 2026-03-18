<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\TodoSubtaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/home', [HomeController::class, 'index'])->middleware('auth')->name('home');
Route::patch('/home/weekly-goal', [HomeController::class, 'updateWeeklyGoal'])->middleware('auth')->name('home.weekly-goal');


// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Todo routes
    Route::resource('todos', TodoController::class);
    Route::patch('todos/{todo}/toggle', [TodoController::class, 'toggle'])->name('todos.toggle');
    Route::patch('todos/{todo}/snooze', [TodoController::class, 'snooze'])->name('todos.snooze');

    Route::post('todos/{todo}/subtasks', [TodoSubtaskController::class, 'store'])->name('todos.subtasks.store');
    Route::patch('todos/{todo}/subtasks/{subtask}', [TodoSubtaskController::class, 'update'])->name('todos.subtasks.update');
    Route::patch('todos/{todo}/subtasks/{subtask}/toggle', [TodoSubtaskController::class, 'toggle'])->name('todos.subtasks.toggle');
    Route::delete('todos/{todo}/subtasks/{subtask}', [TodoSubtaskController::class, 'destroy'])->name('todos.subtasks.destroy');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/toggle-role', [UserManagementController::class, 'toggleRole'])->name('users.toggleRole');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('logs.index');
    });

require __DIR__.'/auth.php';
