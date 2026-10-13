# قواعد مواصفات الموديولات

كل ملف في هذا المجلد يحدد فقط ما يختلف في الموديول: Runtime، التبعيات، النطاق، ورابط التنفيذ. القواعد التالية تسري على الجميع ولا تتكرر داخل كل ملف:

- لا ينشأ Model أوTable جديد في مجال موجود قبل ERP Gap Analysis وتصنيف Keep/Extend/Wrap/Replace/New.
- كل كتابة يمكن إعادة إرسالها لها Idempotency Strategy، وكل عملية حساسة لها Audit.
- كل مكوّن Edge يعلن Schema والإصدار والأحداث والأوامر في Manifest.
- الاستحقاق ونطاق Tenant/Branch/Device يتحقق منهما في الخدمة، وليس الواجهة فقط.
- التعطيل وإعادة التفعيل لا يحذفان التاريخ، وسلوك Offline موثق عند وجود Edge Runtime.
- المزامنة لا تكرر الأثر المالي أوالمخزني.

هذه الملفات مواصفات نطاق تخطيطية. تفاصيل البيانات والأحداث وقرارات إعادة استخدام ERP تكتمل في Phase 0، والتنفيذ ومعايير القبول التفصيلية في [قائمة العمل](../../plan/EXECUTION_BACKLOG.md).

## الفهرس

| المجموعة | الملفات |
|---|---|
| النواة المشتركة | 01 Platform، 02 Users، 03 Catalog، 04 Shared Sales |
| واجهات البيع | 05 Restaurant POS، 06 Retail POS |
| تشغيل الفرع | 07 Shifts، 08 Tables، 09 KDS، 10 Printing، 11 Waiter |
| ERP والمخزون | 12 Inventory، 13 Recipes، 14 Procurement، 24 ERP Reuse |
| القنوات والتكاملات | 15 QR، 16 Delivery، 17 Tax، 18 Payments، 23 Online/Kiosk |
| التوسع | 19 CRM، 20 Central Kitchen، 21 AI، 22 Enterprise HQ |
