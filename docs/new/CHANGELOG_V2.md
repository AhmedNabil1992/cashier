# Changelog — V2.1

## القرارات الجديدة المثبتة

1. **Platform Scope**: المنصة ERP SaaS متعدد الأنشطة، وليس Restaurant-only.
2. **Edge OS**: Ubuntu هو الـbaseline الرسمي للـEdge.
3. **AI Placement**: AI داخل الـMain ERP/Cloud فقط، وليس Edge.
4. **AI Authority**: Advisory only؛ لا Purchase Order تلقائي، لا تغيير سعر تلقائي، لا قرار تشغيلي تلقائي.
5. **POS Strategy**: Sales Engine مشترك + POS Verticals متعددة.
6. **Restaurant Plugins**: Restaurant POS لا يفرض Tables/KDS/Delivery/QR Ordering.
7. **Retail POS**: Vertical مستقل بواجهة بيع مباشر أبسط، لكنه يستخدم نفس ERP inventory/accounting/catalog.
8. **Plugin Pricing**: كل Functional Plugin له سعر مستقل؛ Bundles اختيارية وليست قيدًا معماريًا.
9. **Dependencies**: أي Plugin يعتمد على Plugin آخر يفرضه النظام آليًا ويعرض السعر الإجمالي قبل التفعيل.
10. **Edge Provisioning**: Cloud URL + one-time pairing token → Device Identity → License → Edge Profile → Plugins/Migrations/Data Snapshot.
11. **ERP Reuse**: لا إعادة إنشاء Accounting/Purchasing/Inventory/Customers إذا كانت موجودة بصورة مناسبة داخل Aureus؛ يتم Gap Analysis أولًا.
12. **Database**: لا تحويل شامل لكل IDs إلى Snowflake؛ Global Sync IDs فقط حيث يلزم.


## مواءمة التوثيق — 2026-09-14

اعتماد الحزمة كمرجع رئيسي، وإضافة Shared Sales/Retail/Waiter للـBacklog، وتصحيح تبعيات KDS وحالة العقود وبوابات Pilot، وأرشفة الخطط والتتبع المكررين. التفاصيل في [سجل التنظيم](../plan/DOCUMENTATION_RECONCILIATION.md).
