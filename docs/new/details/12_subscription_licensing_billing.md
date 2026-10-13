# Subscription, Licensing & Billing

## Commercial Model
النموذج الأساسي:

`Base Platform Fee + Selected Functional Plugins + Scope/Quantity`

Bundles ممكنة كخصم، لكنها لا تقيد المعمارية.

## Price Dimensions
حسب Plugin:
- per tenant
- per branch
- per terminal
- per KDS screen
- per feature
- flat monthly/annual

## Checkout
عند اختيار Plugin:
1. dependency resolver.
2. price calculation.
3. branch scope selection.
4. user sees complete cost.
5. payment/activation.
6. entitlement version increments.
7. license refresh.
8. Edge command.

## License Payload
- tenant_id
- branch_id
- edge_id
- profile
- entitlements
- limits
- license_version
- issued_at
- refresh_before
- offline_allowed_until
- min_edge_version
- policy_version

## Expiry
انتهاء الاشتراك لا يمحو التاريخ. تحدد policy ما إذا كان يمنع new sales بعد grace بينما يسمح upload/backup/history/settlement المحدود.
