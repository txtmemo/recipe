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
         // エラーメッセージをセッションに追加
         session()->flash('error', '1つ選択してください。');
         // フォームページにリダイレクト
         return redirect()->back();
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
                    $categoryUrl = $category['categoryUrl'];
                    preg_match('!/(\d+)-!', $categoryUrl, $matches);
                    $parentCategory = $matches[1] ?? null;
                    //31は定番の肉料理、10は肉カテゴリ、41は中華料理カテゴリ
                    if ($parentCategory === '31' || $parentCategory === '10'
                        || $parentCategory === '41') {
                        $filteredCategories[] = $category;
                    }
                }
                break;
    
            case 'fish':
                foreach ($categories as $category) {
                    $categoryUrl = $category['categoryUrl'];
                    preg_match('!/(\d+)-!', $categoryUrl, $matches);
                    $parentCategory = $matches[1] ?? null;
                    //32は定番の魚料理、11は魚カテゴリ
                    if ($parentCategory === '32' || $parentCategory === '11') {
                        $filteredCategories[] = $category;
                    }
                }
                break;
    
            case 'rice':
                foreach ($categories as $category) {
                    $categoryUrl = $category['categoryUrl'];
                    preg_match('!/(\d+)-!', $categoryUrl, $matches);
                    $parentCategory = $matches[1] ?? null;
                    //14はごはんカテゴリ
                    if ($parentCategory === '14') {
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