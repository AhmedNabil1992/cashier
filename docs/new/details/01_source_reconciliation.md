# توحيد المصادر القديمة مع الخطة النهائية

## المرجع الحاكم
[00_MASTER_PLAN.md](../00_MASTER_PLAN.md) مرجع V2.1. الخطة والمتطلبات السابقة محفوظة في [أرشيف 14 سبتمبر](../../archive/docs-before-v2.1-reconciliation-2026-09-14.zip).

## ما يبقى من الوثائق القديمة
- Requirements الوظيفية.
- Edge cases.
- Acceptance criteria.
- Product ideas.
- Domain terminology.
- Plugin dependency ideas.

## ما لا يبقى كقرار معماري
- بناء ERP جديد من الصفر.
- Shared DB tenancy كخيار أساسي.
- Pure Cloud/IndexedDB POS كمسار رئيسي.
- Windows/SQL Server Edge.
- Bearer<User_ID>.
- fixed inbound webhook to branch IP.
- 76-table greenfield schema.
- كل ID = Snowflake.
- Restaurant-only scope.
- AI على Edge أو AI autonomous actions.
- Tables/KDS/Delivery كجزء إجباري من Restaurant POS.

## التعديلات الجديدة على تصنيف المصادر

### `CORE-03_pos-order-billing.md`
يصبح مصدر Requirements لمحرك مبيعات مشترك + Restaurant POS، وليس تعريفًا بأن كل POS في المنصة Restaurant-only.

### `PLUGIN-02_floor-table-management.md`
Plugin Restaurant اختياري، وليس dependency إلزامية للـRestaurant POS الأساسي.

### `PLUGIN-04_kitchen-display-kds.md`
Plugin مستقل بسعر واستحقاق منفصل.

### `PLUGIN-11_delivery-hub-aggregators.md`
Plugin مستقل؛ لا يدخل في Restaurant POS افتراضيًا.

### `PLUGIN-15_ai-menu-engineering-pricing.md`
Cloud-only Advisory. أي Dynamic Pricing تلقائي أو تنفيذ قرارات مؤجل ومرفوض في المرحلة الحالية.

### `04_DATABASE_DESIGN.md`
Domain reference فقط. أي table DDL يحتاج Mapping على Aureus أولًا.


راجع [سجل التنظيم](../../plan/DOCUMENTATION_RECONCILIATION.md). أسماء الملفات القديمة أدناه مصادر داخل أرشيف 10 سبتمبر، وليست ملفات نشطة مطلوب إعادة إنشائها.
