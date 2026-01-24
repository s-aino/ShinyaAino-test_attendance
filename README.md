# 勤怠管理アプリ

Docker 上で動作する Laravel 製の勤怠管理アプリです。


## 🔧 機能一覧

### 認証について
- role による STAFF / ADMIN 制御
- 管理者専用ログイン画面あり  
→ 詳細: [docs/auth.md](docs/auth.md)
---
### 勤怠データの仕様
- 自動退勤なし
- 修正申請ベース設計  
→ 詳細: [docs/attendance.md](docs/attendance.md)
---
### カレンダー機能(コーチ了承済みの追加)
- 管理者勤怠一覧にカレンダー実装  
→ 詳細: [docs/calendar.md](docs/calendar.md)
---
## 🏗 環境構築

 ### Docker ビルド

1. git clone https://github.com/s-aino/ShinyaAino-test_attendance.git
2. DockerDesktopアプリを立ち上げる
3. docker compose up -d --build


### 🛠 Laravel環境構築

#### 1. コンテナに入る
```bash
docker compose exec php bash
```
---
#### 2. Composer インストール
```bash
composer install
```
---
#### 3. .env 作成  
.env.example をコピー、
```bash
cp .env.example .env
```
以下の DB 設定に変更：
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
---
#### 4. アプリケーションキーの作成
```bash
php artisan key:generate
```
---
#### 5. マイグレーション & シーディング
##### 初回構築・仕様更新時
```bash
php artisan migrate:fresh --seed
```
##### （データ保持が必要な場合のみ）
```bash
php artisan migrate
php artisan db:seed
```
---
#### 6.権限の修正が必要なとき
```bash
chmod -R 777 storage
chmod -R 777 bootstrap/cache
```
---
##### Laravel のキャッシュ削除
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---
### 🎉 以上で環境構築は完了です。
ブラウザでアプリを利用できる状態になりました。

- アプリURL: http://localhost
---
## 🧾 PHPUnit テスト
####   テスト環境（env.testing）

phpunit / php artisan test 実行時は、本番 DB とは別の テスト用データベース を使用します。

#####   env.testing を作成
プロジェクト直下で以下を実行します。
```bash
cp .env.example .env.testing
```
##### 　env.testingに以下の内容を記述してください。

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_test_db
DB_USERNAME=root
DB_PASSWORD=root
```
##### APP_KEYの生成（初回のみ）
テスト環境専用のアプリケーションキーを生成します。
```bash
php artisan key:generate --env=testing
```

##### テーブルの作成（初回のみ）
```bash
php artisan migrate --env=testing
```

##### 注意：初回は以下のような質問が表示されます
```bash
The database 'laravel_test_db' does not exist. Create it? (yes/no)
```
 **👉 yes  と入力してください。**
（yes を選ぶことで、テスト用 DB が自動作成されます）

####   テストの実行

本アプリには 16 個の自動テストが含まれています。
以下のコマンドで すべてのテストを一括実行できます。
```bash
php artisan test
```
## 🌐 開発環境 
- **アプリ**：http://localhost  
- **phpMyAdmin**：http://localhost:8080  

## 🧰 使用技術（実行環境）

- PHP 8.1
- Laravel 10.x
- MySQL 8.x
- Nginx（php-fpm 経由）
  
## 🗂 ER 図 / 仕様書

- **ER 図（Mermaid 元ファイル）** : [docs/ER.md](docs/ER.md)
- **テーブル仕様書** : [docs/DB_SPEC.md](docs/DB_SPEC.md)
  
  （Google スプレッドシート版「テーブル仕様書」を Markdown へ書き起こしたもの）

