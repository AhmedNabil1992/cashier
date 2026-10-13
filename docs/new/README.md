# eCashier SaaS — Final Architecture Pack V2

**الإصدار:** 2.1 Multi-Vertical / ERP-First  
**التاريخ:** 2026-09-13  
**المرجع الحاكم:** `00_MASTER_PLAN.md`

هذه الحزمة هي النسخة المحدثة بعد تثبيت القرارات التالية:

- الـCloud الرئيسي هو مشروع الـERP الحالي المبني على Aureus، ولا يتم بناء ERP جديد من الصفر.
- المنصة لم تعد مقصورة على المطاعم والكافيهات؛ هي **ERP SaaS متعدد الأنشطة**.
- الـEdge يعمل على **Ubuntu فقط** في النسخة المستهدفة، ويخدم الكاشير والتشغيل المحلي.
- الـEdge ليس Cache؛ هو **Branch Runtime مستقل Offline**.
- يوجد أكثر من POS Vertical فوق نفس الـERP ونفس Edge Core:
  - Restaurant POS.
  - Retail POS.
  - ويمكن إضافة Verticals أخرى لاحقًا.
- مكونات المطعم مثل Tables وKDS وDelivery وQR Ordering مستقلة كـPlugins؛ لا يجبر العميل الصغير على شرائها.
- كل Functional Plugin له سعر مستقل ويمكن شراؤه منفردًا، مع فرض Dependencies عند الحاجة.
- الـAI **Cloud ERP Only** ويعمل حاليًا كتوصيات وتحليلات فقط، بدون تنفيذ قرارات تلقائيًا.

## ترتيب القراءة

1. [00_MASTER_PLAN.md](00_MASTER_PLAN.md)
2. [CHANGELOG_V2.md](CHANGELOG_V2.md)
3. [details/03_target_architecture.md](details/03_target_architecture.md)
4. [details/05_plugin_engine_entitlements.md](details/05_plugin_engine_entitlements.md)
5. [details/06_edge_lifecycle_provisioning.md](details/06_edge_lifecycle_provisioning.md)
6. [details/17_vertical_profiles.md](details/17_vertical_profiles.md)
7. [details/14_implementation_roadmap.md](details/14_implementation_roadmap.md)

## الملفات التفصيلية

| الملف | الغرض |
|---|---|
| `details/01_source_reconciliation.md` | توحيد المصادر القديمة والجديدة |
| `details/02_erp_reuse_gap_analysis.md` | قاعدة Keep/Extend/Wrap/Replace/New |
| `details/03_target_architecture.md` | المعمارية النهائية Cloud/Edge/Clients |
| `details/04_tenancy_multi_database.md` | Multi-Database Tenancy |
| `details/05_plugin_engine_entitlements.md` | Plugin Engine + Dependencies + Entitlements |
| `details/06_edge_lifecycle_provisioning.md` | Pairing/Provisioning/Offline/Updates |
| `details/07_sync_events_commands.md` | Sync + Events + Commands |
| `details/08_database_strategy_migration.md` | استراتيجية DB بدون إعادة بناء ERP |
| `details/09_security_auth_audit.md` | Security/Auth/Audit |
| `details/10_api_contracts_versioning.md` | APIs وVersioning |
| `details/11_operations_fleet_backup_updates.md` | Fleet/Backup/Restore/Updates |
| `details/12_subscription_licensing_billing.md` | التسعير والترخيص Plugin-by-Plugin |
| `details/13_testing_acceptance.md` | الاختبارات ومعايير القبول |
| `details/14_implementation_roadmap.md` | خطة التنفيذ المرحلية |
| `details/15_feature_catalog.md` | كتالوج القدرات وتصنيفها |
| `details/16_project_structure.md` | هيكل المشاريع والمجلدات |
| `details/17_vertical_profiles.md` | Restaurant/Retail Profiles |
| `details/18_ai_strategy.md` | AI Cloud-only Advisory |

## مواصفات الموديولات

ابدأ من [قواعد وفهرس الموديولات](modules/README.md). كل ملف Module يحتوي فقط ما يخص مجاله، بينما القواعد المشتركة مكتوبة مرة واحدة في الفهرس.

## عقود الربط

ابدأ من [قواعد وفهرس العقود](contracts/README.md)، ثم افتح العقد المتخصص المطلوب.

## قاعدة الأولوية عند التعارض

توجيه صاحب المشروع الأحدث → قرارات [الخطة الرئيسية](00_MASTER_PLAN.md) → التفاصيل والعقود والوحدات التابعة → قائمة المهام → الأرشيف التاريخي.

أي SQL قديم أو أسماء جداول أو نسخ تقنية في الملفات القديمة تعتبر **Reference فقط** حتى يتم Phase 0 Mapping على الكود والـDB الفعلية للـERP.


## التنفيذ والتاريخ

- [المهام](../plan/EXECUTION_BACKLOG.md): القائمة التنفيذية الوحيدة، وتشمل التفاصيل ومعايير AC-01–16.
- [سجل التنظيم](../plan/DOCUMENTATION_RECONCILIATION.md): ما تغير ولماذا.

حزمة V2.1 خط أساس تخطيطي، وليست شهادة جاهزية. تبقى أسماء الجداول والإصدارات وتفاصيل Schemas والتقديرات مشروطة بـPhase 0. الهيكل المقترح للمجلدات ليس تغييرًا منفذًا.
