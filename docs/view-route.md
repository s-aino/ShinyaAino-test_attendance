###　認証関連（共通）
| 画面名        | URL            | ルート名          | Controller@Method                        | 説明                |
| ---------- | -------------- | ------------- | ---------------------------------------- | ----------------- |
| スタッフログイン画面 | `/login`       | `login`       | `AuthenticatedSessionController@create`  | 一般スタッフログイン画面      |
| 管理者ログイン画面  | `/admin/login` | `admin.login` | `AdminAuthController@showLoginForm`      | 管理者ログイン画面         |
| ログイン処理（共通） | `/login`       | `login.store` | `AuthenticatedSessionController@store`   | スタッフ・管理者共通のログイン処理 |
| ログアウト      | `/logout`      | `logout`      | `AuthenticatedSessionController@destroy` | ログアウト処理           |

**本アプリでは Laravel Fortify を利用して認証処理を実装しています。  
ログイン処理（POST /login）およびログアウト処理は Fortify が提供するAuthenticatedSessionController を共通で使用し、  
ログイン画面（GET）は一般スタッフ用・管理者用で個別に用意しています。**


###　認証関連（管理者）
| 画面名       | URL            | ルート名          | Controller@Method                   | 説明        |
| --------- | -------------- | ------------- | ----------------------------------- | --------- |
| 管理者ログイン画面 | `/admin/login` | `admin.login` | `AdminAuthController@showLoginForm` | 管理者ログイン画面 |
---


###　一般スタッフ：勤怠打刻・確認
| 画面名         | URL                       | ルート名               | Controller@Method            | 説明            |
| ----------- | ------------------------- | ------------------ | ---------------------------- | ------------- |
| 勤怠打刻画面（トップ） | `/attendance`             | `attendance.index` | `AttendanceController@index` | 出勤・退勤・休憩の打刻画面 |
| 出勤処理        | `/attendance/start`       | `attendance.start` | `AttendanceController@start` | 出勤打刻処理        |
| 退勤処理        | `/attendance/end`         | `attendance.end`   | `AttendanceController@end`   | 退勤打刻処理        |
| 休憩開始        | `/attendance/break/start` | `break.start`      | `BreakTimeController@start`  | 休憩開始打刻        |
| 休憩終了        | `/attendance/break/end`   | `break.end`        | `BreakTimeController@end`    | 休憩終了打刻        |
---

###　一般スタッフ：勤怠一覧・詳細
| 画面名       | URL                              | ルート名              | Controller@Method                | 説明            |
| --------- | -------------------------------- | ----------------- | -------------------------------- | ------------- |
| 勤怠一覧（月別）  | `/attendance/list`               | `attendance.list` | `AttendanceListController@index` | 自身の勤怠を月別で一覧表示 |
| 勤怠一覧（月指定） | `/attendance/list?month=YYYY-MM` | `attendance.list` | `AttendanceListController@index` | 月を指定して勤怠一覧を表示 |
| 勤怠詳細      | `/attendance/detail/{id}`        | `attendance.show` | `AttendanceController@show`      | 自身の勤怠詳細を表示    |
---

###　一般スタッフ：勤怠修正申請
| 画面名       | URL                                   | ルート名                          | Controller@Method                             | 説明        |
| --------- | ------------------------------------- | ----------------------------- | --------------------------------------------- | --------- |
| 勤怠修正申請 送信 | `/attendance/{attendance}/correction` | `attendance.correction.store` | `AttendanceCorrectionRequestController@store` | 勤怠修正申請を送信 |
---

###　申請一覧（一般スタッフ・管理者 共通）
| 画面名    | URL                              | ルート名                        | Controller@Method                             | 説明        |
| ------ | -------------------------------- | --------------------------- | --------------------------------------------- | --------- |
| 修正申請一覧 | `/stamp_correction_request/list` | `correction_requests.index` | `AttendanceCorrectionRequestController@index` | 修正申請一覧を表示 |
---

###　管理者：勤怠・スタッフ管理
| 画面名           | URL                                | ルート名                          | Controller@Method                  | 説明         |
| ------------- | ---------------------------------- | ----------------------------- | ---------------------------------- | ---------- |
| 勤怠一覧（管理者）     | `/admin/attendance/list`           | `admin.attendance.list`       | `AdminAttendanceController@index`  | 全スタッフの勤怠一覧 |
| 勤怠詳細・修正       | `/admin/attendance/{id}`           | `admin.attendance.show`       | `AdminAttendanceController@show`   | 勤怠詳細・修正画面  |
| 勤怠修正更新        | `/admin/attendance/{id}`           | `admin.attendance.update`     | `AdminAttendanceController@update` | 勤怠修正の更新処理  |
| スタッフ一覧        | `/admin/staff/list`                | `admin.staff.list`            | `AdminStaffController@index`       | スタッフ一覧     |
| スタッフ別 勤怠一覧    | `/admin/attendance/staff/{id}`     | `admin.attendance.staff.show` | `AdminStaffController@show`        | スタッフ別勤怠一覧  |
| スタッフ別 勤怠CSV出力 | `/admin/attendance/staff/{id}/csv` | `admin.attendance.staff.csv`  | `AdminStaffController@csv`         | 勤怠CSV出力    |
---

###　管理者：勤怠修正申請（承認）
| 画面名       | URL                                                                 | ルート名                                      | Controller@Method                                    | 説明        |
| --------- | ------------------------------------------------------------------- | ----------------------------------------- | ---------------------------------------------------- | --------- |
| 修正申請 承認画面 | `/stamp_correction_request/approve/{attendance_correct_request_id}` | `stamp_correction_request.approve`        | `AdminAttendanceCorrectionRequestController@edit`    | 修正申請の承認画面 |
| 修正申請 承認処理 | `/stamp_correction_request/approve/{attendance_correction_request}` | `stamp_correction_request.approve.update` | `AdminAttendanceCorrectionRequestController@approve` | 修正申請を承認   |

