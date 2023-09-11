<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeFavorite extends Model
{
    protected $table = 'recipe_favorites';
    protected $fillable = ['recipe_id', 'recipe_title', 'recipe_image'];

    public function getFullImageUrlAttribute()
    {
        $baseURL = config('services.recipe.image_base_url');
        return $baseURL . $this->recipe_image;
    }
}