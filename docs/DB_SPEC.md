# 📘 テーブル仕様書（DB_SPEC）

このドキュメントは、本アプリで使用している データベース仕様 をまとめたものです。
Google スプレッドシートの「テーブル仕様書」を Markdown 形式に書き起こした構成です。
---
# 🔗 users テーブル
| カラム名 | 型 | PK | UK | NN | FK | 説明 |
|---|---|---|---|---|---|---|
| id | bigint | ○ | | ○ | | ユーザーID |
| name | varchar(255) | | | ○ | | 氏名 |
| email | varchar(255) | | ○ | ○ | | メールアドレス |
| email_verified_at | timestamp | | | | | メール認証日時 |
| password | varchar(255) | | | ○ | | パスワード |
| role | varchar(50) | | | ○ | | 権限（staff / admin） |
| created_at | timestamp | | | ○ | | 作成日時 |
| updated_at | timestamp | | | ○ | | 更新日時 |
---
# 🔗 attendances テーブル
| カラム名       | 型               | PK | UK | NN | FK        | 説明                             |
| ---------- | --------------- | -- | -- | -- | --------- | ------------------------------ |
| id         | unsigned bigint | ○  |    | ○  |           | 勤怠ID                           |
| user_id    | unsigned bigint |    |    | ○  | users(id) | ユーザーID                         |
| date       | date            |    |    | ○  |           | 勤務日                            |
| clock_in   | time            |    |    |    |           | 出勤時刻                           |
| clock_out  | time            |    |    |    |           | 退勤時刻                           |
| status     | string          |    |    | ○  |           | 勤務状態（working / resting / left） |
| created_at | timestamp       |    |    | ○  |           | レコード作成日時                       |
| updated_at | timestamp       |    |    | ○  |           | レコード更新日時                       |

---

# 🔗 breaks テーブル
| カラム名          | 型               | PK | UK | NN | FK              | 説明     |
| ------------- | --------------- | -- | -- | -- | --------------- | ------ |
| id            | unsigned bigint | ○  |    | ○  |                 | 休憩ID   |
| attendance_id | unsigned bigint |    |    | ○  | attendances(id) | 勤怠ID   |
| break_start   | time            |    |    | ○  |                 | 休憩開始時刻 |
| break_end     | time            |    |    |    |                 | 休憩終了時刻 |
| created_at    | timestamp       |    |    | ○  |                 | 作成日時   |
| updated_at    | timestamp       |    |    | ○  |                 | 更新日時   |

---

# 🔗 attendance_correction_requests テーブル
| カラム名                | 型               | PK | UK | NN | FK              | 説明                     |
| ------------------- | --------------- | -- | -- | -- | --------------- | ---------------------- |
| id                  | unsigned bigint | ○  |    | ○  |                 | 修正申請ID                 |
| attendance_id       | unsigned bigint |    |    | ○  | attendances(id) | 対象勤怠                   |
| user_id             | unsigned bigint |    |    | ○  | users(id)       | 申請者                    |
| requested_clock_in  | time            |    |    | ○  |                 | 修正後の出勤時刻               |
| requested_clock_out | time            |    |    | ○  |                 | 修正後の退勤時刻               |
| requested_breaks    | json            |    |    | ○  |                 | 修正後の休憩情報               |
| reason              | text            |    |    | ○  |                 | 修正理由                   |
| status              | string          |    |    | ○  |                 | 状態（pending / approved） |
| created_at          | timestamp       |    |    | ○  |                 | 申請日時                   |
| updated_at          | timestamp       |    |    | ○  |                 | 承認日時                   |
