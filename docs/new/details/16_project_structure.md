# Project Structure

## Cloud — existing `cashier`
لا يتم إنشاء `ecashier-cloud` جديد.

مقترح Extensions:
```text
cashier/
  app/SaaS/
    Tenancy/
    Billing/
    Licensing/
    Fleet/
    Sync/
    Commands/
  plugins/
    ... existing plugin system ...
  docs/
```

## Edge — new repository/application
```text
ecashier-edge/
  src/
    core/
      auth/
      licensing/
      pairing/
      plugins/
      migrations/
      sync/
      commands/
      updates/
      backup/
      health/
    verticals/
      restaurant-pos/
      retail-pos/
    plugins/
      shifts-cash/
      printing-hardware/
      tables/
      kds/
      waiter/
      delivery/
      qr-ordering/
      inventory-edge/
  database/
  tests/
  deploy/
```

## Frontends
يمكن أن تكون frontend packages داخل Edge repo أو workspace مشترك، لكن مصدر البيانات المحلي دائمًا Edge API.
