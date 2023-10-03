<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('layouts.head',['title' => '献立ジェネレーター','description' => 'ディスクリプション'])
    @yield('head')
    @include('layouts.navbar')
    <body class="welcome">
    <h1>今日の献立<br>ジェネレーター</h1>
        <div class="about"> 
            <h2 style="text-align:center;">About</h2>
            <div class="text-container">
                <p>当サイトで表示されるレシピは全て楽天レシピAPIを用いて取得しています。<br></p>
                <p>ログインをしなかった場合、履歴機能やお気に入り機能への登録はできないので予めご了承願います。<br></p>

            </div>
        </div>
        <div class="top-container">
            <a href="{{ route('login') }}" class="welcome-a">ログインをして始める</a>
            <a href="{{ route('recipe.conditions') }}" class="welcome-a">ログインしないで始める</a>
        </div>
        <div class="credit">
            <!-- Rakuten Web Services Attribution Snippet FROM HERE -->
            <a href="https://developers.rakuten.com/" target="_blank">Supported by Rakuten Developers</a>
            <!-- Rakuten Web Services Attribution Snippet TO HERE -->
        </div>
    </body>
</html>
