# PulseMonitor Domain Model

The initial domain model supports organization-owned monitors, historical checks, and incidents. Monitoring execution and authorization rules will be added in the next roadmap steps.

```mermaid
erDiagram
    USERS ||--o{ ORGANIZATION_USER : belongs_to
    ORGANIZATIONS ||--o{ ORGANIZATION_USER : has
    ORGANIZATIONS ||--o{ MONITORS : owns
    MONITORS ||--o{ MONITOR_CHECKS : records
    MONITORS ||--o{ INCIDENTS : opens

    ORGANIZATIONS {
        bigint id PK
        string name
        string slug UK
    }

    ORGANIZATION_USER {
        bigint organization_id PK, FK
        bigint user_id PK, FK
        string role
    }

    MONITORS {
        bigint id PK
        bigint organization_id FK
        string name
        string target_url
        string method
        integer interval_seconds
        integer expected_status
        integer timeout_seconds
        boolean is_enabled
        timestamp last_checked_at
        timestamp next_check_at
    }

    MONITOR_CHECKS {
        bigint id PK
        bigint monitor_id FK
        string status
        integer http_status
        integer response_time_ms
        text error_message
        timestamp checked_at
    }

    INCIDENTS {
        bigint id PK
        bigint monitor_id FK
        string status
        integer failure_count
        text last_error
        timestamp started_at
        timestamp resolved_at
    }
```
