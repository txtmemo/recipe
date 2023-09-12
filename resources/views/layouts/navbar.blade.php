{{-- resouces/views/navbar.blade.php --}}
 
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="nav-container">
 
    <!-- スマホやタブレットで表示した時のメニューボタン -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
 
    <!-- メニュー -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
     <!-- 左寄せメニュー -->
     <ul class="navbar-nav mr-auto">
        @auth
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard') }}">マイメニューへ戻る</a>
            </li>

            @if(Route::currentRouteName() != 'welcome')
                <li class="nav-item">
                    <a class="nav-link" href="" data-toggle="modal" data-target="#historyModal">{{ __('履歴一覧') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="" data-toggle="modal" data-target="#favoritesModal">{{ __('お気に入り一覧') }}</a>
                </li>
            @endif
        @endauth
    </ul>
 
      <!-- 右寄せメニュー -->
      <ul class="navbar-nav">
      @if(auth()->check())
      <!-- ログイン時の表示 -->
      <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              {{ auth()->user()->name }} <span class="caret"></span>
          </a>
          
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
              <!-- プロフィール情報リンクを追加 -->
              <a class="dropdown-item" href="{{ route('profile.edit') }}">
                  {{ __('プロフィール情報') }}
              </a>

              <form method="POST" action="{{ route('logout') }}" id="logout-form">
                  @csrf
              </form>
              <a class="dropdown-item" href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                  {{ __('Log Out') }}
              </a>
          </div>
      </li>
      @else
              <!-- 未ログイン時の表示 -->
              <li class="nav-item">
                  <a class="nav-link" href="{{ route('login') }}">{{ __('ログイン') }}</a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="{{ route('register') }}">{{ __('新規登録') }}</a>
              </li>
          @endif
      </ul>
    </div>
    </div>
</nav>