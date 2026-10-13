# Target Architecture

## Cloud / Main ERP
مشروع `cashier` الحالي هو الـMain ERP لكل الأنشطة.

### Cloud Responsibilities
- Tenant provisioning.
- Companies/branches.
- Domain/custom-domain mapping.
- ERP administration.
- Plugin catalog/entitlements.
- Subscription/licensing.
- Reference data ownership.
- Central reporting/accounting.
- Fleet management.
- AI advisory.
- Integration adapters.

## Edge Core
Ubuntu branch runtime واحد لكل فرع في V1.

### Edge Responsibilities
- Local authentication/session.
- POS runtime.
- Local transaction processing.
- Local plugin runtime.
- Shifts/cash.
- Hardware/printing.
- KDS/Tables/Delivery فقط عند التفعيل.
- Local database.
- Sync/update/backup/health.

## Client Responsibilities
POS/Waiter/KDS browser clients لا تتصل بـTenant DB مباشرة، بل بـEdge API على LAN.

## Multi-Vertical Rule
Edge Core لا يعرف أنه "مطعم" أو "محل" كافتراض ثابت. Profile يحدد الـUX/flows/plugins.

### Profiles
- `RESTAURANT`
- `RETAIL`
- future: `PHARMACY`, `SERVICES`, etc. بعد دراسة المجال.

## AI Placement
لا AI inference runtime على Edge في V1. كل AI في Cloud ERP، ويستخدم بيانات متزامنة فقط.
