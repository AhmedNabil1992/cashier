# Implementation Roadmap

## Phase 0 — ERP Audit
**Goal:** نفهم الموجود قبل أي بناء.

Deliverables:
- domain map.
- DB/table classification.
- plugin manager risks.
- tenancy risks.
- reuse matrix.

Gate: approved Keep/Extend/Wrap/Replace/New matrix.

## Phase 1 — Multi-Tenant Foundation
- Stancl proof.
- central/tenant separation.
- custom domains.
- queue/cache/files/session isolation.

Implementation status (2026-09-14): Stancl Tenancy v3.10.1 is installed, central tenant/domain migrations and database-per-tenant provisioning are wired, the Filament admin and customer panels resolve tenants by domain, and automated SQLite tests prove database and domain isolation. ERP plugin migration classification plus queue/cache/files/session/permission isolation remain open before the Phase 1 gate can pass.

Gate: two tenants pass isolation tests.

## Phase 2 — SaaS Plugin Commercial Layer
- catalog.
- dependencies.
- pricing.
- entitlements.
- branch scope.
- licenses.

Gate: same plugin enabled for one tenant/branch and disabled for another safely.

## Phase 3 — Edge Core
- Ubuntu package.
- pairing.
- device identity.
- local DB.
- local auth.
- sync.
- update/backup/health.

Gate: offline transaction + reconnect with at-least-once delivery and one idempotent business effect.

## Phase 4 — Shared Transaction Contracts
- item/price snapshot.
- order/payment/shifts.
- print jobs.
- common event model.

## Phase 5A — Retail Pilot
- retail UX.
- barcode/search.
- direct sale.
- returns.
- cash/printing.

## Phase 5B — Restaurant Pilot
- restaurant cart/order flow.
- takeaway first.
- optional Tables.
- optional KDS.

## Phase 6 — Restaurant Add-ons
Tables → KDS → Waiter → QR Ordering → Delivery.

## Phase 7 — ERP Deep Integrations
Inventory → Recipes → Procurement → Accounting postings.

## Phase 8 — Production Hardening
Fleet, security, updates, backup, recovery, monitoring.

## Phase 9 — AI Advisory
Cloud reporting + recommendations only.

## Phase 10 — Enterprise
HQ, Central Kitchen, franchise, advanced providers.


## بوابات التشغيل التي لا تؤجل

قبل Pilot حي في Phase 5A أو5B: ربط ERP المالي الأساسي E10، واختبار backup/restore ونقل سلطة الجهاز E26، وامتثال البلد المطلوب. Phase 7 و8 توسع هذه القدرات ولا تبدأها من الصفر بعد Pilot. يكتمل عقد المعاملة المشتركة قبل الاعتماد على نتائج تجربة Edge المبكرة.

AI اختياري لاحق في Phase 9، وليس شرط Pilot. Tables/KDS/Printing fallback ليست سلسلة اعتماد تثبيت إلزامية؛ الأسهم في Phase 6 ترتيب تطوير مقترح فقط.

[قائمة المهام](../../plan/EXECUTION_BACKLOG.md) تتبع هذه المراحل وتحتوي بوابات القبول. يعاد تقدير المدة بعد جرد Phase 0 وإضافة Retail، ولا يعتمد الجدول الأسبوعي القديم.
