=== Archive Post Order Plus ===
Contributors: nbk45
Tags: latest posts order,categories post order,tags post order,custom taxonomy post order
Requires at least: 4.9
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.2.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A plugin that sets the display order of posts.
投稿の表示順を設定するプラグイン。

== Description ==
This plugin is a plugin that customizes the posting order below.
- Your latest posts / Settings - Reading Settings
- Search results
- Category
- Tag
- Custom Taxonomy
- Custom Posts Archive

このプラグインは、下記の投稿表示順をカスタマイズするプラグインです。
・［設定］－［表示設定］の「最新の投稿」
・検索結果
・カテゴリー
・タグ
・カスタム分類
・カスタム投稿アーカイブ

= Specification =
Select either 1) standard + custom field sort or 2) drag sort for the display order.

［設定］－［表示設定］の「最新の投稿」、検索結果、カテゴリー、タグ、カスタム分類毎に投稿表示順を設定可能にします。
表示順は 1）標準＋カスタムフィールドソート、2）ドラッグソートのどちらを選択します。

1）Standard + custom field sort (標準＋カスタムフィールドソート)
In addition to the post update date, ID, title, and registration date, 4 custom fields (*) can be registered.
Select the post you want to enable and drag to set the order.

*) Custom fields can be selected from existing custom fields or added for this plugin.
*) When this plug-in is deleted, the added custom field will also be deleted.

投稿の更新日、ID、タイトル、登録日に加え、4つのカスタムフィールド（※）が登録可能です。
有効にしたい項目を選択しドラッグで順番を設定します。

※）カスタムフィールドは、既存のカスタムフィールドから選択、もしくは本プラグイン用に追加可能です。
（追加の場合は各投稿の専用入力フォームから登録します）
※）本プラグインの削除時は、追加したカスタムフィールドも削除されます

2）Drag sort (ドラッグソート)
In the list of posts displayed in the list, drag the posts to set the display order.

リスト表示されてた投稿一覧で、投稿をドラッグし表示順を設定します。

== Installation ==

= Automatic installation (自動インストール) =
1. Enter "Archive Post Order Plus" in the plugin search field and click "Search for Plugins".
2. If you find this plugin, click "Install Now" to install it and activate the plugin.

1)プラグインの検索フィールドより「Archive Post Order Plus」と入力し、"プラグインの検索"をクリックします。
2)当プラグインを見つけたら、"今すぐインストール"をクリックしてインストールし、プラグインを有効化してください。

= Manual installation (手動インストール) =
1. Download this plugin.
2. Please upload it in the plugin folder and activate the plugin from the management screen.

1)プラグインをダウンロードします。
2)プラグインフォルダ内にアップロードし、管理画面よりプラグインを有効化してください。

== Frequently Asked Questions ==

== Screenshots ==
screenshot-1.png
screenshot-2.png
screenshot-3.png
screenshot-4.png
screenshot-5.png
screenshot-6.png
screenshot-7.png
screenshot-8.png

== Changelog ==
= 1.2.3 =
confirm WordPress6.5

= 1.2.2 =
confirm WordPress6.3
confirm WordPress6.1

WordPress6.1動作確認

= 1.2.1 =
confirm WordPress6.0
Fix template typos.
Add method return type specification.
Delete unnecessary variable assignments.
Add condition to file include.

WordPress6.0動作確認
テンプレートのミスタイプの修正
メソッドの戻り型指定の追加
不要な変数代入の削除
ファイルインクルードの条件追加

= 1.2.0 =
confirm WordPress5.9
Fixed Notice error in custom post archive.

WordPress5.9動作確認
カスタム投稿アーカイブのNoticeエラー修正

= 1.1.9 =
Changed the layout of the sort menu.
Modified JavaScript variable declaration and stored element specification in variable.

並べ替えのレイアウトを変更
JavaScriptで要素を変数に格納

= 1.1.8 =
Supports sort settings for custom post archives.
Bug fixed: "Follow global settings" of category, tag, and custom classification does not work properly when all items are selected in global settings.

カスタム投稿アーカイブのソート設定対応
全体設定で全件を選択している時にカテゴリー、タグ、カスタム分類の「全体設定に従う」が正常機能しないバグの修正

= 1.1.7 =
Supports translation.
Bug fixed: custom field selection by Search

翻訳対応
検索のカスタムフィールド選択バグの修正

= 1.1.6 =
Fixed the part where the notice error occurred when the initial data was not set.
初期データ未設定時にnoticeエラーになっていた箇所を修正

= 1.1.5 =
Smartphone support for drag sorting of posted articles.
投稿記事のドラッグ並べ替えのスマートフォン対応

= 1.1.4 =
Explicitly include jquery-ui-autocomplete.
jquery-ui-autocompleteを明示的にインクルード

= 1.1.3 =
Change selected custom field to autocomplete.
選択カスタムフィールドをオートコンプリートに変更

= 1.1.2 =
Fixed selected custom fields to be read-only.
選択カスタムフィールドを読み取り専用表示するよう修正

= 1.1.1 =
Bug fixed: Fixed missing addition of selected attribute of custom field.
バグ修正：カスタムフィールドのselected属性の追加漏れ修正

= 1.1.0 =
Fixed mandatory check omissions for custom fields.
Fixed existing custom field selection.
Set background color in sort list.
Fixed refresh button position on sort settings page.
Fixed the order of radio buttons for sorting by category, tag, and custom taxonomies to be the same as standard and search.

カスタムフィールドの必須チェック漏れの修正
既存カスタムフィールド選択も可能なよう修正
ソートのリストに背景色を設定
並べ替え設定ページの更新ボタン位置を修正
カテゴリー、タグ、カスタム分類のソート対象選択のラジオボタン並び順を標準、検索と同一になるよう修正

= 1.0.1 =
Add custom posts to search sort.
検索のソート対象にカスタム投稿を追加

= 1.0.0 =
First lease.
初回リリース

== Upgrade Notice ==
No information