# Shifts & Cash Control

## Runtime
`Edge + Cloud reporting`

## Dependencies
لا توجد dependencies وظيفية إلزامية مبدئيًا؛ يظل Technical Edge Core مطلوبًا لأي Edge module.

## Scope
فتح/إغلاق ورديات، drawers/vaults، float، cash in/out، blind count، X/Z، overnight shifts، immutable cash movements.

## الربط بالتنفيذ

ملخص نطاق تابع لـV2.1، وليس Schema أوتقرير إنجاز. تفاصيل العمل والقبول: **E11** في [قائمة التنفيذ](../../plan/EXECUTION_BACKLOG.md). بيانات وأحداث المجال تفصل بعد ERP Mapping.

البيع النقدي لا يتجاوز قواعد الوردية إذا كانت وظيفة Shifts/Cash غير مستحقة؛ يحسم resolver متطلبات التدفق ويعرض تكلفتها قبل التفعيل.
