<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeHistoryController;
use App\Http\Controllers\FavoriteController;
use App\Models\RecipeHistory;
use App\Models\RecipeFavorite;

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
})->name('welcome');

Route::get('/dashboard', function () {
    // 現在認証されているユーザーのレシピ履歴を取得
    $histories = RecipeHistory::where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get();

    // 現在認証されているユーザーのお気に入りのレシピを取得
    $favorites = RecipeFavorite::where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get();

    return view('dashboard', [
        'histories' => $histories,
        'favorites' => $favorites
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::post('/dashboard', [ApiController::class, 'getRakutenRecipeRanking'])->name('getRecipe');
require __DIR__.'/auth.php';

Route::get('/recipeconditions', function() {
    if (session('visitedRecipeResult')) {
        return redirect()->route('recipedecide.get');
    }
    return view('recipeconditions');
})->name('recipe.conditions')->middleware('prevent-back-history');

Route::post('/recipedecide', [ApiController::class, 'getRakutenRecipeRanking'])->name('recipe.decide');

Route::get('/clear-session-and-redirect', [RecipeController::class, 'clearSessionAndRedirect'])->name('clear_session_and_redirect');
Route::get('/recipedecide', [RecipeController::class, 'displayRecipe'])->name('recipedecide.get');

Route::get('recipes/{id}', [RecipeController::class, 'show'])->name('recipes.show');

Route::post('/api/recipe-history', [RecipeHistoryController::class, 'store']);
Route::post('/favorite', [FavoriteController::class,'store'])->name('favorite.store');
Route::delete('/favorites/{recipe}', [FavoriteController::class, 'destroy'])->name('favorite.delete');

Route::post('/favorites/{recipe_id}/memo', [FavoriteController::class, 'storeMemo'])->name('favorite.memo');
Route::put('/favorites/{recipe_id}/memo', [FavoriteController::class, 'updateMemo'])->name('favorite.memo.update');
Route::delete('/favorites/{recipe_id}/memo', [FavoriteController::class, 'deleteMemo'])->name('favorite.memo.delete');
