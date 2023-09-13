<?php

namespace App\Http\Controllers;
use App\Models\RecipeFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function store(Request $request) {
    // すでにお気に入りに追加されているか確認
    $existingFavorite = RecipeFavorite::where('user_id', auth()->id())
    ->where('recipe_id', $request->recipe_id)
    ->first();

    if ($existingFavorite) {
    $message = 'すでにお気に入りに追加されています！';
    if ($request->ajax()) {
    return response()->json(['message' => $message], 400);
    } else {
    return redirect()->back()->with('error', $message);
    }
    }
    // データベースに情報を保存
    $favorite = new RecipeFavorite();
    $favorite->user_id = auth()->id();
    $favorite->recipe_id = $request->recipe_id;
    $favorite->recipe_title = $request->recipe_title;

    // 画像のURLからベースURLを取り除く
    $fullImageUrl = $request->input('recipe_image');
    $baseURL = config('services.recipe.image_base_url');
    $imagePath = str_replace($baseURL, '', $fullImageUrl);
    $favorite->recipe_image = $imagePath;

    $favorite->save();
    
    $message = 'お気に入りに追加しました！';
    if ($request->ajax()) {
    return response()->json(['message' => $message]);
    } else {
    return redirect()->route('dashboard')->with('success', $message);
    }

    
}

    public function destroy($recipeId)
    {
        // ログインユーザーからお気に入りを取得
        $user = auth()->user();

        // ユーザーのお気に入りから指定されたレシピを削除
        $user->favorites()->where('recipe_id', $recipeId)->delete();

        return redirect()->back()->with('success', 'お気に入りからレシピを削除しました。');
    }

    public function getFavorites()
    {
        $favorites = RecipeFavorite::where('user_id', auth()->id())
            ->orderBy('recipe_favorites.created_at', 'desc')
            ->get();

        foreach($favorites as $favorite) {
            $favorite->full_image_url = $this->getRecipeImageUrl($favorite->recipe_image);
        }
        return view('dashboard', ['favorites' => $favorites]);
    }

    private function getRecipeImageUrl($imageName)
    {
        
        $baseURL = config('services.recipe.image_base_url');
        return $baseURL . $imageName;
    }

    public function addMemo(Request $request, $favoriteId)
    {
        $favorite = RecipeFavorite::findOrFail($favoriteId);
        $favorite->memo = $request->input('memo');
        $favorite->save();

        $message = 'メモを追加しました！';
        return redirect()->route('dashboard')->with('success', $message);
    }
}
