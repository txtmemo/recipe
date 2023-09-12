<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;

class RecipeController extends Controller
{
    public function clearSessionAndRedirect(Request $request)
    {
        // セッション `visitedRecipeResult` もクリア
        Session::forget('visitedRecipeResult');
        // セッションをクリア
        Session::forget('selected_recipe');

        // ヘッダーに基づいてリダイレクトをスキップするかどうかを判定
        if ($request->header('Skip-Redirect') === 'true') {
            return response()->json(['success' => true]);
        }

        // recipeconditions.blade.phpにリダイレクト
        return redirect()->route('recipe.conditions');
    }
    


    public function displayRecipe()
    {
        // セッションからレシピを取得
        $randomItem = session('selected_recipe');
        
        // セッションにレシピが存在しない場合の処理を追加（必要に応じて）
        if (!$randomItem) {
            // 例: トップページへリダイレクト
            return redirect()->route('recipe.conditions');
        }
    
        // レシピリザルトページへのアクセス時に、セッションフラグをセット
        session(['visitedRecipeResult' => true]);

        return view('recipedecide', ['items' => [$randomItem]]);

        
    }

    // public function recipeConditions()
    // {
    //     // レシピ条件ページへのアクセス時、セッションフラグを確認
    //     if (session('visitedRecipeResult')) {
    //         return redirect()->route('recipe.decide'); // ルート名は適切なものに変更してください
    //     }

    //     // 実際のレシピ条件ページの処理（ビューの表示など）をここに記述

    //     return view('recipe.conditions'); // ビュー名は実際のものに変更してください
    // }

    public function show($id)
{
    $recipe = Recipe::find($id);

    // 履歴の登録
    RecipeHistory::create([
        'user_id' => auth()->id(),
        'recipe_id' => $id
    ]);

    return view('recipes.show', compact('recipe'));
}


}
