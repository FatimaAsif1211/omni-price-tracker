use App\Http\Controllers\NaheedController;

Route::get('/fetch-products', [NaheedController::class, 'fetchProducts']);

