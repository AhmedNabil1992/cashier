# Database Strategy & Migration

## قاعدة أساسية
لا ننفذ الـgreenfield schemas القديمة حرفيًا.

## Cloud Tenant DB
يبدأ من ERP schema الحالية، ثم:
- migrations صغيرة قابلة للمراجعة.
- restaurant/retail extensions فقط حيث توجد فجوة.
- plugin-specific tables عندما تكون الوظيفة جديدة فعلًا.

## Local DB
Schema مصممة لتشغيل الفرع، وليست نسخة لكل ERP.

### يجب أن تحتوي
- local reference data.
- local users/permissions.
- POS/order/payment/shifts.
- enabled plugin data.
- outbox/inbox.
- command log.
- version tables.
- audit/print jobs.

## IDs
- existing PK remains.
- synced entities get public/sync ID if needed.
- events/commands always globally unique.

## Migration Safety
- backup before risky migrations.
- expand/contract pattern.
- compatibility window.
- no blind destructive migration on Edge.
- migration status tracked per plugin.
