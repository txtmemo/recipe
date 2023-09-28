@include('layouts.head',['title' => 'カテゴリ選択','description' => 'ディスクリプション'])
@yield('head')
@include('layouts.navbar')
<html>
    <body>
        <div class="container">
            <h2>レシピ条件選択</h2>
            @if(session('error'))
                <div style="color:red;">
                    {{ session('error') }}
                </div>
            @endif
            <form action="{{ route('recipe.decide') }}" method="POST">
                @csrf
                <div class="flex-container">
                    <div>
                        <input type="radio" id="meat" name="category" value="meat" class="hidden-input">
                        <label for="meat" class="image-container">
                            <img src="/img/meat-default.png" alt="Meat" class="inactive-image">
                            <img src="/img/meat-cooked.png" alt="Meat Active" class="active-image">
                        </label>
                    </div>
                    <div style="height:118px;">
                        <!-- 魚カテゴリーの選択項目 -->
                        <input type="radio" id="fish" name="category" value="fish" class="hidden-input">
                        <label for="fish" class="image-container">
                            <img src="/img/fish-default.png" alt="Fish" class="inactive-image">
                            <img src="/img/fish-cooked.png" alt="Fish Active" class="active-image">
                        </label>
                    </div>
                    <div>
                        <!-- 魚カテゴリーの選択項目 -->
                        <input type="radio" id="rice" name="category" value="rice" class="hidden-input">
                        <label for="rice" class="image-container">
                            <img src="/img/rice-default.png" alt="Rice" class="inactive-image">
                            <img src="/img/rice-cooked.png" alt="Rice Active" class="active-image">
                        </label>
                    </div>
                <button type="submit"class="button">条件を適用して検索する</button>
            </form>
        </div>
        <script type="text/javascript">
            //送信ボタンを押した際に送信ボタンを無効化する（連打による多数送信回避）
            $(function(){
                $('[type="submit"]').click(function(){
                    $(this).prop('disabled',true);//ボタンを無効化する
                    $(this).closest('form').submit();//フォームを送信する
                });
            });
</script>
    </body>
</html>