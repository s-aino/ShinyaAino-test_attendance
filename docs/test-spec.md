# テスト仕様一覧（Feature Test）

本アプリでは機能単位で Feature テストを作成しています。  
テスト環境は `.env.testing` を使用します。

---

# 🔐 Auth（認証系）

| No | 機能 | テストファイル |
|----|----------------------|---------------------------|
| 1 | 認証機能（登録） | RegisterTest.php |
| 2 | ログイン（一般ユーザー） | LoginTest.php |
| 3 | ログイン（管理者） | AdminLoginTest.php |
| 16 | メール認証機能 | EmailVerificationTest.php |

---

# 👤 Attendance（一般ユーザー）

| No | 機能 | テストファイル |
|----|------------------------------|------------------------------|
| 4 | 日時取得機能 | DateTimeTest.php |
| 5 | ステータス確認機能 | StatusTest.php |
| 6 | 出勤機能 | ClockInTest.php |
| 7 | 休憩機能 | BreakTest.php |
| 8 | 退勤機能 | ClockOutTest.php |
| 9 | 勤怠一覧情報取得機能（一般） | AttendanceListTest.php |
| 10 | 勤怠詳細情報取得機能（一般） | AttendanceDetailTest.php |
| 11 | 勤怠詳細情報修正申請（一般） | CorrectionRequestTest.php |

---

# 🛠 Admin（管理者）

| No | 機能 | テストファイル |
|----|----------------------------------|-------------------------------|
| 12 | 勤怠一覧情報取得機能（管理者） | AdminAttendanceListTest.php |
| 13 | 勤怠詳細情報取得・修正機能（管理者） | AdminAttendanceEditTest.php |
| 14-1 | ユーザー情報取得機能（管理者／スタッフ一覧） | UserListTest.php |
| 14-2 | ユーザー情報取得機能（管理者／スタッフ別一覧） | StaffAttendanceListTest |
| 15-1 | 勤怠情報修正機能（管理者／一覧表示） | ApprovalListTest.php |
| 15-2 | 勤怠情報修正機能（管理者／詳細表示） | ApprovalDetailTest.php |
| 15-3 | 勤怠情報修正機能（管理者／承認処理） | ApprovalActionTest.php |
---
**テスト14は機能責務ごとに
UserListTest / StaffAttendanceListTest に分割しています**
**テスト15は機能責務ごとに
ApprovalListTest/ ApprovalDetailTest /  ApprovalActionTest に分割しています**
