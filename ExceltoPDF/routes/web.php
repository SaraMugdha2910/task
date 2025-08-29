<?php

use App\Http\Controllers\ExcelImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/excel/upload', function(){
    return view('UploadExcel');
});

Route::post('file/import', [ExcelImportController::class, 'import'])->name('excel.import');

Route::get('/pdf/download',
    [ExcelImportController::class,'PdfDownload']
);
Route::post('/download-pdf', [ExcelImportController::class, 'download'])->name('download.pdf');
Route::post('/zip/download', [ExcelImportController::class, 'zipDownload'])->name('download.zip');