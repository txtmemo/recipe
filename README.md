# 献立ジェネレーター - 自作

## 概要
今日の献立を考えるのに時間がかかる、献立を考えるのがめんどくさい。  
そんな人達に向けたレシピを1つ選んでくれるサイトを作りました。  
楽天レシピAPIと連携しているため、表示されるレシピはしっかりとしたものが多く、便利です。
## アプリURL
https://txtmemo.xsrv.jp
## 機能

### ユーザ
- レシピ表示
- 履歴機能
- お気に入り機能

**アカウント：**  
アカウント新規登録にあたって、メールの送信などは行っていないため、test@test.comなどの架空のアドレスでも追加できます。
お試しアカウントでぜひ機能を体験してみてください。

### ゲストユーザ
- レシピ表示

## 使い方

**レシピ表示の流れ**  
ログイン後、マイメニューにてレシピを探すボタンを押す。または、ログインをしないボタンで選択画面へ繊遷移
3つのジャンルの中から任意のものを押し、条件を適用して検索ボタンを押す。
レシピが一つ選択されるので、このレシピにするを押して詳細を確認することができます。
万が一変えたい場合はもう一度レシピを選ぶボタンを押せばジャンル選択画面へ戻れます。

**マイメニュー操作**  
このレシピにするを押して詳細を確認したレシピのみ、開いた履歴欄に表示されます。
履歴欄のお気に入り追加ボタンを押すことによってレシピをお気に入り欄へ追加することができます。

お気に入り欄へ追加されたレシピはお気に入り削除ボタンで削除することが可能です。
ナビゲーションからお気に入り一覧の表示をすることで、メモを保存することができます。

## 環境
- XAMPP
- MySQL
- PHP Laravel
- 楽天レシピAPI
- Xsever
