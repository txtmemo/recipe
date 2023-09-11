<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('layouts.head',['title' => '献立ジェネレーター','description' => 'ディスクリプション'])
    @yield('head')
    @include('layouts.navbar')
    <body class="welcome">
    <h1>今日の献立ジェネレーター</h1>

        <div class="container">
     
            <a href="{{ route('login') }}" class="welcome-a">ログインをして始める</a>
            <a href="{{ route('recipe.conditions') }}" class="welcome-a">ログインしないで始める</a>
        </div>
    </body>
</html>
