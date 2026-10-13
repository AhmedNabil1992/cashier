# Plugin Manifest Specification


```yaml
plugin_key: restaurant-kds
name: Smart KDS
version: 2.3.0
vertical: restaurant
runtime: hybrid
scope: branch
price_model: per_branch
cloud_component: true
edge_component: true
dependencies:
  - restaurant-pos
optional_dependencies:
  - printing-hardware
conflicts: []
min_edge_version: 1.4.0
schema_version: 7
permissions:
  - kds.view
  - kds.bump
events_consumed:
  - order.fired
events_emitted:
  - kitchen.item.ready
commands_supported:
  - ReloadKdsConfig
```

## Required Rules
- dependencies are resolved before checkout.
- pricing shown including missing dependencies.
- migrations are versioned.
- package checksum/signature mandatory.
- runtime entitlement checks tenant/branch scope.


`plugin_key` هو معرف العقد الموحد؛ مسار مجلد المصدر ليس بالضرورة معرف البلجن. المثال hybrid لأن له cloud_component وedge_component. يجب تحديد قيود إصدارات dependencies وتفاصيل التوقيع في Schema المعتمدة؛ المثال الحالي لا يكفي للتحقق الآلي.
