<?php

use App\Http\Controllers\MengelolaBuku;
use App\Http\Controllers\Otentikasi;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

Route::middleware('guest')->group(function() {
    Route::get('/login', [Otentikasi::class, 'formLogin'])->name('login');
    Route::post('/login', [Otentikasi::class, 'login']);
});

Route::middleware('auth')->group(function() {
    Route::get('/', [MengelolaBuku::class, 'tampilkanBuku']);
    Route::get('/create', [MengelolaBuku::class, 'formTambahBuku']);
    Route::post('/', [MengelolaBuku::class, 'simpanBuku']);
    Route::get('/trace/{isbn}', [MengelolaBuku::class, 'lacakBuku']);

    Route::post('/logout', [Otentikasi::class, 'logout']);

    Route::get('/proxy-image', function(Request $request) {
        if (!$request->url) {
            return response(['error' => 'URL is required'], 400);
        }

        try {
            $imageContent = file_get_contents($request->url);
            $contentType = get_headers($request->url, 1)["Content-Type"];

            if (!str_starts_with($contentType, 'image')) {
                return response(['error' => 'URL does not point to an image'], 400);
            }

            return response($imageContent, 200)
                ->header('Content-Type', $contentType)
                ->header('Content-Disposition', 'attachment; filename="downloaded_image.jpg"');
        } catch (\Exception $e) {
            return response(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    });
});

Route::get('test', function() {
    dump(Http::get('https://opac.perpusnas.go.id/ResultListOpac.aspx?pDataItem=9786230010507&pType=Isbn&pLembarkerja=-1&pPilihan=Isbn')->body());
    dump(Http::get('https://isbn.perpusnas.go.id/Account/GetBuku?kd1=ISBN&kd2=9786230010507&limit=10&offset=0')->json());
});
