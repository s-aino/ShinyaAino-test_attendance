# 認証・ログイン設計について

本アプリでは、Laravel Fortify をベースにしつつ  
**ユーザーの role（admin / staff）によってログイン導線と遷移先を分離**しています。

---

## 使用技術
- Laravel Fortify
- AuthenticatedSessionController（共通ログイン処理）
- AdminAuthController（管理者ログイン画面表示）
- users テーブルの role カラム

---

## ユーザー区分
| role | 説明 |
|----|----|
| staff | 一般スタッフ |
| admin | 管理者 |

role は **データベース上でのみ設定**します。

---

## ログイン画面一覧

| URL | 対象 | 備考 |
|---|---|---|
| `/login` | staff / admin | 共通ログイン画面 |
| `/admin/login` | admin 専用 | URL直打ち |

---

## 管理者ログイン画面の挙動

- `/admin/login` からログインした場合
- **role = staff のユーザーはログイン不可**
- エラーメッセージ  
  > 管理者として登録されていません。

これにより  
**「管理者専用ログイン画面」という意味を明確にしています。**

---

## 一般ログイン画面の挙動

- `/login` からログインした場合
- staff → 一般スタッフ用ページへ
- admin → 管理者用ページへ遷移

管理者が通常ログイン画面から入れる仕様は  
**実務でも自然な挙動と判断しています。**

---

## ログイン後の遷移先

| role | 遷移先 |
|---|---|
| staff | 勤怠登録 / 勤怠一覧 |
| admin | 管理者勤怠一覧 |

---

## 実装上のポイント

- ログイン画面ごとに hidden input で `login_type` を送信
- AuthenticatedSessionController で分岐処理
- 不正ログイン時は必ず logout + session invalidate を実行

---

## 設計意図

- URLレベルでログイン導線を分離
- 画面の見た目だけでなく「権限としての意味」を持たせる
- 管理者誤操作・誤ログインを防止
