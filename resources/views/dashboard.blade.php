@include('layouts.head',['title' => '献立ジェネレーター','description' => 'ディスクリプション'])
@yield('head')
<!DOCTYPE html>
@php
    // ベースURLの取得
    $baseUrl = config('services.rakuten.base_url');
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<script>
$(document).ready(function() {
    $("#favoriteButton").click(function(event) {
        event.preventDefault();

        var recipeId = $('input[name="recipe_id"]').val();
        var recipeTitle = $('input[name="recipe_title"]').val();
        var recipeImage = $('input[name="recipe_image"]').val();

        $.ajax({
            type: "POST",
            url: "{{ route('favorite.store') }}",
            data: {
                _token: '{{ csrf_token() }}',
                recipe_id: recipeId,
                recipe_title: recipeTitle,
                recipe_image: recipeImage
            },
            success: function(response) {
                if(response.message) {
                    $("#favoriteMessage").text(response.message);
                    var currentText = $("#favoriteButton").text();
                    $("#favoriteButton").text(currentText == 'お気に入り' ? 'お気に入り解除' : 'お気に入り');
                }
            },
            error: function(xhr) {
                try {
                    var jsonResponse = JSON.parse(xhr.responseText);
                    if(jsonResponse && jsonResponse.error) {
                        $("#favoriteMessage").text(jsonResponse.error);
                    } else {
                        console.error('エラーが発生しました：', xhr.responseText);
                    }
                } catch(e) {
                    console.error('エラーが発生しました：', xhr.responseText);
                }
            }
        });
    });
});

$(document).ready(function(){
    $('.favorite-button').on('click', function(){
        $('#memo-field-container').show();
    });

    $('#submit-memo').on('click', function(){
        let memoContent = $('#memo-field').val();
        
        // サーバーにメモ内容を送信
        $.ajax({
            type: 'POST',
            url: '/recipes/add_memo',
            data: {
                memo: memoContent,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success) {
                    alert('メモが保存されました!');
                } else {
                    alert('エラーが発生しました。');
                }
                $('#memo-field-container').hide();
            }
        });
    });
});

</script>

<body style="">
    @include('layouts.navbar')

    <h1>マイメニュー</h1>
    <div class="container">
        <!-- Search Recipe Button -->
        <div class="flex-container">
            <a class="button" href="{{ route('recipe.conditions') }}" style="margin-top: 2rem;">レシピを探す</a>
        </div>
        
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

        <!-- 履歴表示のセクション -->
        <div class="section">
            <div>
                <h3 class="title">開いた履歴</h3>
                @if($histories->isEmpty())
                    <p>履歴はまだありません。</p>
                @else
                <!-- 履歴の数が3以上の場合、scroll-container クラスを適用 -->
                <ul class="list-none {{ count($histories) >= 3 ? 'scroll-container' : '' }}">
                @foreach($histories as $history)
                    <li class="recipe-card">
                        <a href="{{ $baseUrl . $history->recipe_id }}" target="_blank">
                            <img src="{{ $history->full_image_url }}" alt="{{ $history->recipe_title }}" class="recipe-image">
                        </a>
                        <div class="recipe-text-content">
                        <a href="{{ $baseUrl . $history->recipe_id }}" target="_blank">
                            <h2>{{ $history->recipe_title }}</h2>
                            </a>
                        </div>
                        <form method="POST" action="{{ route('favorite.store') }}">
                            @csrf
                            <input type="hidden" name="recipe_id" value="{{ $history->recipe_id }}">
                            <input type="hidden" name="recipe_title" value="{{ $history->recipe_title }}">
                            <input type="hidden" name="recipe_image" value="{{ $history->full_image_url }}">
                            <button type="submit" class="favorite-button">
                                {{ $history->isFavorited ? 'お気に入り解除' : 'お気に入り追加' }}
                            </button>
                        </form>
                    </li>
                @endforeach
                </ul>
                @endif
            </div>
        </div>

        <!-- お気に入りのレシピ セクション -->
        <div class="section">
            <div>
            <h3 class="title">お気に入りのレシピ</h3>
            @if($favorites->isEmpty())
                <p>お気に入りのレシピはまだありません。</p>
            @else
            <ul class="list-none {{ count($favorites) >= 3 ? 'scroll-container' : '' }}">
            @foreach($favorites as $favorite)
                <li class="recipe-card">
                    <a href="{{ $baseUrl . $favorite->recipe_id }}" target="_blank">
                        <img src="{{ $favorite->full_image_url }}" alt="{{ $favorite->recipe_title }}" class="recipe-image">
                    </a>
                        <div class="recipe-text-content">
                            <a href="{{ $baseUrl . $favorite->recipe_id }}" target="_blank">
                                <h2>{{ $favorite->recipe_title }}</h2>
                            </a>
                            @if(!empty($favorite->memo))
                                <p class="memo">メモ：{{ $favorite->memo }}</p>
                            @endif
                        </div>
                        <!-- お気に入りを削除するボタン -->
                        <form action="{{ route('favorite.delete', $favorite->recipe_id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-button" onclick="return confirm('お気に入りを削除しますか？');">
                                お気に入り削除
                            </button>
                        </form>
                </li>
            @endforeach
            </ul>
            @endif
        </div>
    </div>

    <!--履歴一覧 モーダルウィンドウの内容 -->
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyModalLabel">閲覧履歴</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-none">
                    @foreach($histories as $history)
                    <li class="recipe-card">
                        <a href="{{ $baseUrl . $history->recipe_id }}" target="_blank">
                            <img src="{{ $history->full_image_url }}" alt="{{ $history->recipe_title }}" class="recipe-image">
                        </a>
                        <div class="recipe-text-content">
                        <a href="{{ $baseUrl . $history->recipe_id }}" target="_blank">
                                <h2>{{ $history->recipe_title }}</h2>
                                </a>
                            </div>
                            <form method="POST" action="{{ route('favorite.store') }}">
                                @csrf
                                <input type="hidden" name="recipe_id" value="{{ $history->recipe_id }}">
                                <input type="hidden" name="recipe_title" value="{{ $history->recipe_title }}">
                                <input type="hidden" name="recipe_image" value="{{ $history->full_image_url }}">
                                <button type="submit" class="favorite-button">
                                    {{ $history->isFavorited ? 'お気に入り解除' : 'お気に入り' }}
                                </button>
                            </form>
                            <div id="favoriteMessage"></div>
                    </li>
                @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- お気に入りモーダルウィンドウの内容 -->
    <div class="modal fade" id="favoritesModal" tabindex="-1" aria-labelledby="favoritesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="favoritesModalLabel">お気に入り一覧</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-none">
                    @foreach($favorites as $favorite)
                    <li class="recipe-card">
                        <a href="{{ $baseUrl . $favorite->recipe_id }}" target="_blank">
                            <img src="{{ $favorite->full_image_url }}" alt="{{ $favorite->recipe_title }}" class="recipe-image">
                        </a>
                            <div class="recipe-text-content">
                            <a href="{{ $baseUrl . $favorite->recipe_id }}" target="_blank" class="d-inline-block">
                                    <h2>{{ $favorite->recipe_title }}</h2>
                            </a>
                            </div>
                            <form action="{{ route('favorites.addMemo', $favorite->id) }}" method="POST">
                                @csrf
                                <textarea name="memo" placeholder="メモを追加">{{ $favorite->memo }}</textarea>
                                <button type="submit">保存</button>
                            </form>
                        </li>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>