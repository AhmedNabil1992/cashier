# ERP Reuse & Gap Analysis

## الهدف
منع بناء نظام موازٍ لما هو موجود في Aureus ERP.

## لكل Domain نفذ الجدول التالي

| Domain | Existing ERP Asset | Decision | Gap | New Work |
|---|---|---|---|---|
| Items | Models/Resources/Tables | Keep/Extend | TBD | TBD |
| Inventory | Existing stock modules | Keep/Extend/Wrap | TBD | Edge projection only if needed |
| Purchasing | Existing purchasing | Keep/Extend | TBD | Sync/events only |
| Accounting | Existing GL/COA | Keep/Wrap | Posting contract | Restaurant posting adapter |
| Customers | Existing CRM/customer | Keep/Extend | Loyalty gaps | Loyalty plugin |
| Users | Existing auth/permissions | Extend | Offline auth material | Edge sync adapter |
| Branches/Companies | Existing models | Extend | Tenant boundary | Tenancy integration |

## قواعد القرار

### Keep
الموجود يحقق المسؤولية بدون تعديلات جوهرية.

### Extend
الموجود صحيح لكن يحتاج fields/services/events/permissions.

### Wrap
لا نغيره كثيرًا، ونضع Adapter/Facade حوله للمزامنة أو الـPlugins.

### Replace
يستخدم فقط عند إثبات أن الموجود لا يصلح أو سيخلق مخاطر أعلى من الاستبدال.

### New
وظيفة جديدة فعلًا مثل Edge Fleet Registry أو Pairing Token.

## مخرجات Phase 0 الإلزامية
- `ERP_DOMAIN_MAP.md`
- `TABLE_CLASSIFICATION.csv`
- `MODEL_CONNECTION_AUDIT.md`
- `PLUGIN_REUSE_MATRIX.md`
- `RAW_QUERY_AUDIT.md`
- `TENANCY_RISK_REGISTER.md`
