# Testing & Acceptance

## Tenancy
- no cross-tenant DB/cache/file/job leaks.

## Plugin Engine
- plugin purchased alone.
- missing dependencies and full price displayed and approved before entitlement/install; no hidden charge.
- incompatible plugin rejected.
- branch-scoped enablement.
- disable/re-enable data preserved.

## Edge
- first pairing.
- invalid token.
- custom domain.
- startup offline.
- expired license policy.
- device replacement.

## Sync
- duplicate event.
- lost ACK.
- reordered events.
- partial batch.
- long outage.

## Financial
- double checkout click.
- payment commit then process crash.
- partial refund.
- closed shift conflicts.

## Vertical Isolation
- Retail branch installs no Tables/KDS code paths unless entitled.
- Restaurant branch can operate without Tables/KDS/Delivery.

## AI
- recommendation generated.
- no direct write/automatic action endpoint from AI service in V1.


التفاصيل في [AC-01–16 وقائمة المهام](../../plan/EXECUTION_BACKLOG.md). اختبارات AI تخص Phase 9؛ اختبارات المال والاستعادة أساسية قبل Pilot حي. هذه خطة اختبارات وليست نتائج تشغيل.
