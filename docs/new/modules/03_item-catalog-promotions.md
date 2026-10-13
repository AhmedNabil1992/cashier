# Item Catalog & Promotions

## Runtime
`Cloud + Edge projection`

## Dependencies
لا توجد dependencies وظيفية إلزامية مبدئيًا؛ يظل Technical Edge Core مطلوبًا لأي Edge module.

## Scope
يعاد استخدام Item Master/price structures من ERP حيث يصلح. Edge يحصل على snapshot/version. Promotions engine مستقل منطقيًا عن Restaurant-only flows.

## الربط بالتنفيذ

ملخص نطاق تابع لـV2.1، وليس Schema أوتقرير إنجاز. تفاصيل العمل والقبول: **E08/E16** في [قائمة التنفيذ](../../plan/EXECUTION_BACKLOG.md). بيانات وأحداث المجال تفصل بعد ERP Mapping.

جمع Catalog وPromotions في ملف واحد لا يجعل محرك العروض شرطًا لعرض الأصناف أوالبيع الأساسي؛ حسم حدود الحزم التجارية في E03 مع ERP Mapping.
