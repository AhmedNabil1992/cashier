# Multi-Tenancy / Database-per-Tenant

## Model
- Central DB: platform-wide metadata only.
- Tenant DB: one database per subscribed organization.
- Company/Branch remain business entities داخل Tenant.

## Central Data
- tenants
- tenant_db_catalog
- domains/custom_domains
- plans/prices
- subscriptions
- plugin_catalog
- entitlements
- edge_devices
- pairing_tokens
- licenses
- releases
- commands/fleet status

## Isolation Tests
- same numeric IDs in two tenants.
- queued jobs switching tenants.
- cache keys.
- file storage.
- session/guard.
- permissions.
- plugin enablement.
- broadcasts.
- explicit connections/raw SQL.

## Plugin Manager Risk
Tenant entitlement لا يجوز أن يغير global route cache أو service-provider registration بطريقة تؤثر على Tenant آخر.

## Provisioning State
`PENDING → DB_CREATING → MIGRATING → SEEDING → OWNER_CREATING → READY`

Provisioning must be idempotent and resumable.
