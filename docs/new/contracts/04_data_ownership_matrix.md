# Data Ownership Matrix


| Data | Primary Owner | Other Side |
|---|---|---|
| subscription/license | Cloud | validate/apply |
| plugin entitlement | Cloud | apply runtime |
| users/roles/permissions | Cloud | local operational copy |
| catalog/pricing policy | Cloud | versioned copy |
| local order in progress | Edge | read/command only |
| payments/shifts | Edge | import/report/accounting |
| tables/KDS state | Edge | observe |
| accounting ledger | Cloud ERP | receive postings |
| purchase orders | Cloud ERP | Edge reference only if needed |
| stock movements local | Edge operation / ERP aggregation | movement sync, no balance overwrite |
| AI recommendations | Cloud | user reviews only |
