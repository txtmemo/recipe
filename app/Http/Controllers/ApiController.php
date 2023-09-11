<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use RakutenRws_Client;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function getRakutenRecipeRanking()
{
    // セッションにレシピが保存されている場合、API呼び出しをスキップ
    if (session()->has('selected_recipe')) {
        $items = [session('selected_recipe')];  // セッションからレシピを取得
        return view('recipedecide', ['items' => $items]);
    }


    $client = new Client();
    $applicationId = config('app.rakuten_id'); // .envからアプリケーションIDを取得

    $selectedCategory = request()->input('category');

    //getCategoryIdメソッドから選択されたIDを追加する
    $categoryId = $this->getCategoryId($client, $applicationId, $selectedCategory);
    if (!$categoryId) {
        return 'Error: Could not fetch category ID for meat.';
    }

    // 楽天レシピカテゴリー別ランキングAPIでランキングを取得する
    $response = $client->get('https://app.rakuten.co.jp/services/api/Recipe/CategoryRanking/20170426', [
        'query' => [
            'applicationId' => $applicationId,
            'categoryId' => $categoryId
        ]
    ]);

    $data = json_decode($response->getBody(), true);

    if (isset($data['error'])) {
        return 'Error: ' . $data['error_description'];
    }

    $items = $data['result'];
    session(['visitedRecipeResult' => true]);
    return view('recipedecide', ['items' => $items]);
}

//APIを全て呼び出し、その中からsmallカテゴリの文字列を検索
    protected function getCategoryId($client, $applicationId, $selectedCategory)
    {
        $response = $client->get('https://app.rakuten.co.jp/services/api/Recipe/CategoryList/20170426', [
            'query' => [
                'format' => 'json',
                'applicationId' => $applicationId
            ]
        ]);
        
        $categories = json_decode($response->getBody(), true)['result']['small'];
        
        $filteredCategories = []; // 選択されたカテゴリに一致する項目を保存するための配列

        switch ($selectedCategory) {
            case 'meat':
                foreach ($categories as $category) {
                    if (mb_strpos($category['categoryName'], '使わない') === false 
                        && (mb_strpos($category['categoryName'], '肉') !== false 
                            || mb_strpos($category['categoryName'], 'にく') !== false
                            || mb_strpos($category['categoryName'], '豚') !== false
                            || mb_strpos($category['categoryName'], '牛') !== false
                            || mb_strpos($category['categoryName'], '鶏') !== false)) {
                        $filteredCategories[] = $category;
                    }
                }
                break;
    
            case 'fish':
                foreach ($categories as $category) {
                    if (mb_strpos($category['categoryName'], '使わない') === false 
                        && (mb_strpos($category['categoryName'], 'さかな') !== false
                        || mb_strpos($category['categoryName'], '刺身') !== false
                        || mb_strpos($category['categoryName'], 'まぐろ') !== false
                        || mb_strpos($category['categoryName'], 'サーモン') !== false
                        || mb_strpos($category['categoryName'], 'いか') !== false
                        || mb_strpos($category['categoryName'], 'たこ') !== false
                        || mb_strpos($category['categoryName'], 'アジ') !== false
                        || mb_strpos($category['categoryName'], '魚') !== false)) {
                        $filteredCategories[] = $category;
                    }
                }
                break;
    
            case 'rice':
                foreach ($categories as $category) {
                    if (mb_strpos($category['categoryName'], '使わない') === false 
                        && mb_strpos($category['categoryName'], 'ケーキ') === false 
                        && mb_strpos($category['categoryName'], '米粉') === false 
                        && (mb_strpos($category['categoryName'], '米') !== false 
                        || mb_strpos($category['categoryName'], 'ご飯') !== false
                        || mb_strpos($category['categoryName'], '飯') !== false
                        || mb_strpos($category['categoryName'], 'めし') !== false
                        || mb_strpos($category['categoryName'], 'チャーハン') !== false)) {
                        $filteredCategories[] = $category;
                    }
                }
                break;
    
            default:
                return null;
        }
    
        if (!empty($filteredCategories)) {
            // ランダムに1つのランキングを選ぶ
            $index = mt_rand(0, count($filteredCategories) - 1);
            $chosenCategory = $filteredCategories[$index];
            // Log::info('Filtered categories based on selected category', ['filteredCategories' => $filteredCategories]);
            // Log::info('Filtered categories based on selected category', ['filteredCategories' => $chosenCategory]);
            // categoryUrlからcategoryIdを抽出
            $parts = explode("/", rtrim($chosenCategory['categoryUrl'], '/'));
            $categoryId = end($parts);
            return $categoryId;
        }

        return null; 
    }
}