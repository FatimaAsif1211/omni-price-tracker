use App\Http\Controllers\NaheedController;

Route::get('/fetch-products', [NaheedController::class, 'fetchProducts']);

Route::get('/search/dawaai', [MedicineApiController::class, 'searchDawaai']);