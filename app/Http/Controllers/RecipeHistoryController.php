<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\RecipeHistory;

class RecipeHistoryController extends Controller
{
    public function store(Request $request)
    {
        $recipeHistory = new RecipeHistory;
        $recipeHistory->user_id = auth()->id();
        $recipeHistory->recipe_id = $request->recipe_id;
        $recipeHistory->recipe_title = $request->recipe_title;  // 追加
        
        // ここでベースURLを取り除いた画像のパスのみを保存
        $fullImageUrl = $request->input('recipe_image');
        $baseURL = config('services.recipe.image_base_url');
        $imagePath = str_replace($baseURL, '', $fullImageUrl);
        $recipeHistory->recipe_image = $imagePath;
    
        $recipeHistory->save();

        return response()->json(['success' => true]);
    }

    public function getBrowsingHistory()
    {
        $histories = RecipeHistory::where('user_id', auth()->id())
            ->orderBy('recipe_histories.created_at', 'desc')
            ->get();

        foreach($histories as $history) {
            $history->full_image_url = $this->getRecipeImageUrl($history->recipe_image);
        }

        return view('dashboard', ['histories' => $histories]);
    }

    private function getRecipeImageUrl($imageName)
    {
        $baseURL = config('services.recipe.image_base_url');
        return $baseURL . $imageName;
    }
}