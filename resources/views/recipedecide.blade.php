@include('layouts.head',['title' => '今日のレシピ','description' => 'ディスクリプション'])
@yield('head')
<script>
        var clearSessionUrl = "{{ route('clear_session_and_redirect') }}";
</script>
@if(isset($items))
    @php

        if (session()->has('selected_recipe')) {
            $randomItem = session('selected_recipe');
        } else {
            $randomItem = collect($items)->random();
            session(['selected_recipe' => $randomItem]);
        }
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('save-recipe-history-btn');

        if(btn) {
            btn.addEventListener('click', function(event) {
                event.preventDefault();

                // 既にボタンのテキストが「マイページへ戻る」または「トップへ戻る」なら、対応するページに遷移する
                if (btn.innerText === "マイページへ戻る") {
                    fetch(clearSessionUrl, {
                        method: 'GET',
                        headers: {
                            'Skip-Redirect': 'true'
                        }
                    }).then(response => {
                        // セッションがクリアされた後、ダッシュボードにリダイレクト
                        window.location.href = "{{ route('dashboard') }}";
                    });
                    return;
                } else if (btn.innerText === "トップへ戻る") {
                    fetch(clearSessionUrl, {
                        method: 'GET',
                        headers: {
                            'Skip-Redirect': 'true'
                        }
                    }).then(response => {
                        // セッションがクリアされた後、ダッシュボードにリダイレクト
                        window.location.href = "{{ route('welcome') }}";
                    });
                    return;
                }

                @if(Auth::check())
                // ログインしている場合の処理
                fetch('/api/recipe-history', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        recipe_id: "{{ $randomItem['recipeId'] }}",
                        recipe_title: "{{ $randomItem['recipeTitle'] }}",
                        recipe_image: "{{ $randomItem['foodImageUrl'] }}"
                    })
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 新しいタブでレシピURLを開く
                        window.open("{{ $randomItem['recipeUrl'] }}", '_blank');

                        // ボタンのテキストを変更する
                        btn.innerText = "マイページへ戻る";

                        // 新しいタブで開かないようにtarget属性を変更
                        btn.removeAttribute('target');
                    }
                });
                @else
                // ログインしていない場合の処理
                window.open("{{ $randomItem['recipeUrl'] }}", '_blank');
                btn.innerText = "トップへ戻る";
                btn.removeAttribute('target');
                @endif
            });
        }
    });
</script>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <body style="">
    @include('layouts.navbar')



<div class="container">
    <h1>本日のレシピ</h1>
    <h3 class="text-center">{{ $randomItem['recipeTitle'] }}</h3>
    <img src="{{ $randomItem['foodImageUrl'] }}" alt="レシピの画像" class="responsive-image">
    <h3 class="text-center">必要材料</h3>
        <div class="material-container">
            <ul>
                @foreach ($randomItem['recipeMaterial'] as $material)
                    <li>{{ $material }}</li>
                @endforeach
            </ul>
        </div>
    <div class="flex-container">
        <a id="save-recipe-history-btn" class="button" href="{{ $randomItem['recipeUrl'] }}" target="_blank" rel=”noopener” class="centered-link">このレシピにする</a>
        <a class="button" href="{{ route('clear_session_and_redirect') }}">もう一度レシピを選ぶ</a>
    </div>
</div>
@endif
</body>
<html>
