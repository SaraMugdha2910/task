<?php

use App\Http\Controllers\ExcelImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/excel/upload', function(){
    return view('UploadExcel');
});
<<<<<<< Updated upstream

Route::post('file/import', [ExcelImportController::class, 'import'])->name('excel.import');
=======
Route::post('file/import', [ExcelImportController::class, 'import'])->name('excel.import');

Route::get('/pdf/download',
    [ExcelImportController::class,'PdfDownload']
);
>>>>>>> Stashed changes
