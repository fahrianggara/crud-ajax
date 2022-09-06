<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('layouts.app');
});

// Employee
Route::get('/employee', [App\Http\Controllers\EmployeeController::class, 'index'])->name('employee');
Route::post('/employee', [App\Http\Controllers\EmployeeController::class, 'store'])->name('employee.store');
Route::get('/employee/table', [App\Http\Controllers\EmployeeController::class, 'employeeTable'])->name('employee.table');
Route::get('/employee/{id}', [App\Http\Controllers\EmployeeController::class, 'edit'])->name('employee.edit');
Route::put('/update-employee/{id}', [App\Http\Controllers\EmployeeController::class, 'update'])->name('employee.update');
Route::delete('/delete-employee/{id}', [App\Http\Controllers\EmployeeController::class, 'delete'])->name('employee.delete');

// teacher
Route::get('/teacher/table', [App\Http\Controllers\TeacherController::class, 'dataTable'])->name('teacher.table');
Route::resource('teacher', App\Http\Controllers\TeacherController::class);
