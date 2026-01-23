## ER図

```mermaid
erDiagram

users {
  bigint id PK
  string name
  string email
  timestamp email_verified_at
  string password
  string role
  timestamp created_at
  timestamp updated_at
}

attendances {
  bigint id PK
  bigint user_id FK
  date date
  datetime clock_in
  datetime clock_out
  text reason
  timestamp created_at
  timestamp updated_at
}

breaks {
  bigint id PK
  bigint attendance_id FK
  datetime break_start
  datetime break_end
  timestamp created_at
  timestamp updated_at
}

attendance_correction_requests {
  bigint id PK
  bigint attendance_id FK
  bigint user_id FK
  json requested_data
  string status
  timestamp created_at
  timestamp updated_at
}

users ||--o{ attendances : has
attendances ||--o{ breaks : has
attendances ||--o{ attendance_correction_requests : requested
users ||--o{ attendance_correction_requests : submits
```