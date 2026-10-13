# Plugin Engine, Entitlements & Dependencies

## الهدف
تحويل نظام البلجنز الحالي من "الكود موجود/مفعل" إلى نظام تجاري Tenant-aware وBranch-aware.

## أربع حالات مستقلة
- **Available**: الكود موجود في release.
- **Entitled**: العميل دفع/حصل على حق الاستخدام.
- **Provisioned**: schema/config جاهز.
- **Enabled**: مفعّل فعليًا لهذا Tenant/Branch.

## أنواع الـPlugins

### Technical Core
غير مسعّرة منفردًا وغير قابلة للإزالة: sync/license/edge runtime/local auth/update/backup.

### Functional
مسعّرة وقابلة للاختيار مثل Restaurant POS, Retail POS, Tables, KDS, Delivery, Loyalty.

### Cloud-only
AI Advisory, HQ BI، وبعض integrations.

## Dependencies
Manifest يحدد dependencies إلزامية واختيارية.

مثال:
```yaml
plugin_key: restaurant-kds
vertical: restaurant
runtime: edge
scope: branch
dependencies:
  - restaurant-pos
optional_dependencies:
  - printing-hardware
```

## قواعد الشراء
1. المستخدم يختار Plugin.
2. النظام يحسب missing dependencies.
3. يعرض السعر لكل عنصر والسعر الإجمالي.
4. يمنع الشراء إذا dependency incompatible.
5. بعد الدفع يحدث entitlement.
6. يرسل command للـEdge لفحص manifest.
7. Edge ينزل package/migrations/config.
8. يرسل Applied ACK.

## تعطيل Plugin
- لا DROP tables تلقائيًا.
- feature يصبح غير متاح.
- البيانات تحفظ حسب retention policy.
- re-enable يعيد التشغيل من schema الحالية أو migration لاحقة.
