# 勤怠管理アプリ

Docker 上で動作する Laravel 製の勤怠管理アプリです。


## 🔧 機能一覧

### 認証について
- メール認証機能
- role による STAFF / ADMIN 制御
- 管理者専用ログイン画面あり  
→ 詳細: [docs/auth.md](docs/auth.md)
---
###  MailHog（メール確認）

メール認証は MailHog を使用しています。
- MailHog UI : http://localhost:8025
会員登録後、上記URLで認証メールを確認できます。
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
.env.example をコピー
```bash
cp .env.example .env
```
.envを以下の DB 設定に変更：
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=test@example.com
MAIL_FROM_NAME="Attendance App"
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

- アプリ: http://localhost
- 管理者用: http://localhost/admin/login
- MailHog : http://localhost:8025
---
#### サンプルユーザー（Seeder）

本アプリには、動作確認用のユーザーがシーディングされています。

**一般スタッフ**
- 氏名：高橋花子
- メールアドレス：takahashi@mail
- パスワード：00000000（8文字）

**管理者**
- メールアドレス：admin@mail
- パスワード：11111111（8文字）

※ これらのアカウントはローカル環境・確認用です。

## 🧾 PHPUnit テスト
####   テスト環境（env.testing）

phpunit / php artisan test 実行時は、本番 DB とは別の テスト用データベース を使用します。

#####   env.testing を作成
PHPコンテナ内で以下を実行してください。
```bash
docker compose exec php bash
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
```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=test@example.com
MAIL_FROM_NAME="Attendance App"
```

##### テスト用データベースの作成（初回のみ）
Docker 環境では、テスト用データベースは MySQL 側で事前に作成する必要があります。
```bash
docker compose exec mysql bash
mysql -u root -p
```
```sql
CREATE DATABASE laravel_test_db;
SHOW DATABASES;
```
```md
MySQL から exit で抜けたあと、PHP コンテナに戻ります。
```

##### APP_KEY の生成（初回のみ）
```bash
docker compose exec php bash
php artisan key:generate --env=testing
```

##### テーブルの作成（初回のみ）
```bash
php artisan config:clear
php artisan migrate --env=testing
```

##### tests/Unit フォルダの確認（重要）

PHPUnitの設定上、`tests/Unit` フォルダが存在しない場合、テストが正常に開始されないことがあります。

PHPコンテナ内で以下を実行し、`Unit` フォルダが存在するか確認してください。

```bash
docker compose exec php bash
ls tests
```
`Unit` フォルダが存在しない場合は、以下のコマンドで作成してください：
```bash
mkdir tests/Unit
```

※ フォルダは空で問題ありません。


---

####   テストの実行

本アプリには 19 個の自動テストが含まれています。
以下のコマンドで すべてのテストを一括実行できます。
※ php artisan test 実行時は、自動的に testing 環境（.env.testing）が使用されます。

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
  
## 🗂 ER 図 / 仕様書　/ 対応表

- **ER図（画像）** : [docs/er.png](docs/er.png)
- **Mermaid元ファイル（ER図ソース）** : [docs/er.mmd](docs/er.mmd)
- **テーブル仕様書** : [docs/DB_SPEC.md](docs/DB_SPEC.md)
- **画面・ルート対応表** : [docs/view-route.md](docs/view-route.md)
- **PHP Unitテスト対応表** : [docs/test-spec.md](docs/test-spec.md)


