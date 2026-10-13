# أمثلة الاتصال بـPlatform وCloud وEdge APIs

> **الغرض:** مرجع عملي للاستدعاءات المنفذة حاليًا. استخدم الدومين المركزي `cashier.test` مع Platform API، واستخدم دومين التينت مثل `cairo-cafe.cashier.test` مع Cloud وEdge APIs.

## 1. أنواع المصادقة

| نوع الاتصال | الدومين | المصادقة |
|---|---|---|
| موقع المنصة → Cashier | `https://cashier.test` | توقيع HMAC في `X-Platform-Signature` |
| لوحة التينت → Cloud API | `https://cairo-cafe.cashier.test` | `Authorization: Bearer <SANCTUM_TOKEN>` |
| Edge → Cloud | `https://cairo-cafe.cashier.test` | حاليًا Sanctum Bearer token؛ ستستبدل بمصادقة جهاز محدد عند تنفيذ Edge |

مسارات البلجنز لا تعمل على الدومين المركزي، وتتطلب أن يكون البلجن مثبتًا ومفعلًا داخل التينت.

## 2. دالة PowerShell لاستدعاء Platform API

يوقع Cashier النص التالي باستخدام `HMAC-SHA256`:

```text
<unix_timestamp>\n<UPPERCASE_METHOD>\n<REQUEST_PATH>\n<RAW_JSON_BODY>
```

يجب حساب التوقيع من نفس نص JSON المرسل دون إعادة تنسيق بعد التوقيع. المسار لا يحتوي على الدومين أو query string.

```powershell
$PlatformBaseUrl = 'https://cashier.test'
$PlatformApiSecret = '<PLATFORM_API_SECRET>'

function Invoke-CashierPlatformApi {
    param (
        [Parameter(Mandatory)] [string] $Method,
        [Parameter(Mandatory)] [string] $Path,
        [object] $Payload = $null,
        [string] $IdempotencyKey = ''
    )

    $timestamp = [DateTimeOffset]::UtcNow.ToUnixTimeSeconds().ToString()
    $body = if ($null -eq $Payload) {
        ''
    } else {
        $Payload | ConvertTo-Json -Depth 20 -Compress
    }

    $signedPayload = "$timestamp`n$($Method.ToUpperInvariant())`n$Path`n$body"
    $secretBytes = [Text.Encoding]::UTF8.GetBytes($PlatformApiSecret)
    $hmac = [Security.Cryptography.HMACSHA256]::new($secretBytes)

    try {
        $signatureBytes = $hmac.ComputeHash([Text.Encoding]::UTF8.GetBytes($signedPayload))
        $signature = [Convert]::ToHexString($signatureBytes).ToLowerInvariant()
    } finally {
        $hmac.Dispose()
    }

    $headers = @{
        Accept                 = 'application/json'
        'X-Platform-Timestamp' = $timestamp
        'X-Platform-Signature' = $signature
    }

    if ($IdempotencyKey -ne '') {
        $headers['Idempotency-Key'] = $IdempotencyKey
    }

    $request = @{
        Uri     = "$PlatformBaseUrl$Path"
        Method  = $Method
        Headers = $headers
    }

    if ($body -ne '') {
        $request.ContentType = 'application/json'
        $request.Body = $body
    }

    Invoke-RestMethod @request
}
```

اضبط `PLATFORM_API_SECRET` في موقع المنصة على نفس قيمة `PLATFORM_API_SECRET` الموجودة في بيئة Cashier. لا تحفظ السر داخل Git.

## 3. إنشاء تينت جديد

```powershell
$createTenantPayload = @{
    platform_customer_id = 'customer_10025'
    subdomain = 'cairo-cafe'
    subscription = @{
        plan_key = 'restaurant-pro'
        billing_cycle = 'yearly'
        starts_at = '2026-09-23T00:00:00Z'
        ends_at = '2027-09-22T23:59:59Z'
    }
    plugins = @()
}

Invoke-CashierPlatformApi `
    -Method 'POST' `
    -Path '/api/platform/v1/tenants' `
    -Payload $createTenantPayload `
    -IdempotencyKey ([guid]::NewGuid().ToString())
```

عدم إرسال `plugins` أو إرسالها فارغة ينشئ التينت بالبلجنز الأساسية فقط. لا ترسل `tenant_id`؛ Cashier يولده ويرجعه مع الدومين النهائي.

استجابة متوقعة:

```json
{
  "data": {
    "tenant_id": "01K5R91NJWBC2T8Y3F7Q6MXZPA",
    "status": "provisioning",
    "domain": "cairo-cafe.cashier.test",
    "status_url": "/api/platform/v1/tenants/01K5R91NJWBC2T8Y3F7Q6MXZPA/provisioning"
  }
}
```

## 4. متابعة تجهيز التينت

```powershell
$tenantId = '01K5R91NJWBC2T8Y3F7Q6MXZPA'

Invoke-CashierPlatformApi `
    -Method 'GET' `
    -Path "/api/platform/v1/tenants/$tenantId/provisioning"
```

## 5. تجديد الاشتراك

```powershell
$renewalPayload = @{
    payment_reference = 'pay_784512'
    plan_key = 'restaurant-pro'
    billing_cycle = 'yearly'
    starts_at = '2027-09-23T00:00:00Z'
    ends_at = '2028-09-22T23:59:59Z'
    plugins = @(
        @{
            name = 'restaurant-pos'
            latest_version = '1.1.0'
            sort = 10
        }
    )
}

Invoke-CashierPlatformApi `
    -Method 'POST' `
    -Path "/api/platform/v1/tenants/$tenantId/renewals" `
    -Payload $renewalPayload `
    -IdempotencyKey ([guid]::NewGuid().ToString())
```

## 6. مزامنة البلجنز المتاحة للتينت

```powershell
$pluginSyncPayload = @{
    plugins = @(
        @{
            name = 'restaurant-tables'
            description = 'Restaurant tables management'
            latest_version = '1.0.0'
            author = 'Webkul'
            sort = 20
        }
        @{
            name = 'delivery'
            description = 'Delivery order management'
            latest_version = '1.0.0'
            author = 'Webkul'
            sort = 30
        }
    )
}

Invoke-CashierPlatformApi `
    -Method 'POST' `
    -Path "/api/platform/v1/tenants/$tenantId/plugins" `
    -Payload $pluginSyncPayload
```

المزامنة تضيف أو تحدث metadata فقط، وتحافظ على `is_installed` و`is_active`. الحقول المقبولة للبلجن هي `name`, `author`, `summary`, `description`, `latest_version`, `license`, و`sort`.

## 7. تسجيل شراء بلجن

```powershell
$purchasePayload = @{
    purchase_id = 'purchase_45812'
    payment_reference = 'pay_784900'
    purchased_at = '2026-09-23T11:30:00Z'
    entitlement = @{
        starts_at = '2026-09-23T11:30:00Z'
        ends_at = '2027-09-22T23:59:59Z'
    }
    plugin = @{
        name = 'delivery'
        description = 'Delivery order management'
        latest_version = '1.0.0'
        author = 'Webkul'
        sort = 30
    }
}

Invoke-CashierPlatformApi `
    -Method 'POST' `
    -Path "/api/platform/v1/tenants/$tenantId/plugin-purchases" `
    -Payload $purchasePayload `
    -IdempotencyKey ([guid]::NewGuid().ToString())
```

## 8. إعداد استدعاءات Cloud وEdge

الأمثلة التالية تستخدم `curl.exe` حتى لا يحدث تعارض مع alias الخاص بـPowerShell.

```powershell
$TenantBaseUrl = 'https://cairo-cafe.cashier.test'
$SanctumToken = '<SANCTUM_TOKEN>'
```

احصل على التوكن من مسار تسجيل الدخول الحالي `POST /admin/api/v1/login`، ثم أرسله في `Authorization`.

## 9. Restaurant POS Profiles

إنشاء Profile:

```powershell
curl.exe --request POST "$TenantBaseUrl/admin/api/v1/restaurant-pos/profiles" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json' `
    --header 'Content-Type: application/json' `
    --data-raw '{"company_id":1,"code":"cairo-main","name":"Cairo Main POS","default_order_type":"dine_in","enabled_order_types":["takeaway","dine_in","delivery"],"settings":{"receipt_language":"ar"},"is_active":true}'
```

عرض القائمة أو Profile واحد:

```powershell
curl.exe "$TenantBaseUrl/admin/api/v1/restaurant-pos/profiles" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json'

curl.exe "$TenantBaseUrl/admin/api/v1/restaurant-pos/profiles/1" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json'
```

تحديث Profile:

```powershell
curl.exe --request PATCH "$TenantBaseUrl/admin/api/v1/restaurant-pos/profiles/1" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json' `
    --header 'Content-Type: application/json' `
    --data-raw '{"company_id":1,"code":"cairo-main","name":"Cairo Main POS","default_order_type":"takeaway","enabled_order_types":["takeaway","dine_in","delivery"],"is_active":true}'
```

القيم المتاحة لنوع الطلب: `takeaway`, `dine_in`, و`delivery`. يجب أن توجد قيمة `default_order_type` داخل `enabled_order_types`.

## 10. إدارة منيو Restaurant POS

المنيو تشير إلى منتجات ERP الحالية عن طريق `product_id`. يمكن تخصيص اسم العرض والصورة والتوفر والترتيب والسعر داخل المنيو دون نسخ المنتج نفسه.

أنشئ مجموعات الإضافات أولًا:

```powershell
curl.exe --request POST "$TenantBaseUrl/admin/api/v1/restaurant-pos/profiles/1/modifier-groups" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json' `
    --header 'Content-Type: application/json' `
    --data-raw '{"code":"milk-choice","name":"Milk Choice","minimum_selections":1,"maximum_selections":1,"free_allowance":1,"is_required":true,"options":[{"name":"Whole Milk","price_delta":0},{"name":"Oat Milk","price_delta":8}]}'
```

مسارات مجموعات الإضافات:

```text
GET    /admin/api/v1/restaurant-pos/profiles/{profile}/modifier-groups
POST   /admin/api/v1/restaurant-pos/profiles/{profile}/modifier-groups
GET    /admin/api/v1/restaurant-pos/profiles/{profile}/modifier-groups/{modifier_group}
PATCH  /admin/api/v1/restaurant-pos/profiles/{profile}/modifier-groups/{modifier_group}
DELETE /admin/api/v1/restaurant-pos/profiles/{profile}/modifier-groups/{modifier_group}
```

يمكن للخيار الإشارة إلى مجموعة أخرى بواسطة `nested_modifier_group_id`. يمنع النظام الدورات ويتوقف عند ثلاث طبقات كحد أقصى.

```powershell
curl.exe --request POST "$TenantBaseUrl/admin/api/v1/restaurant-pos/profiles/1/menus" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json' `
    --header 'Content-Type: application/json' `
    --data-raw '{"code":"all-day","name":"All Day Menu","channels":["pos","qr","delivery"],"availability":{"days":["sun","mon","tue","wed","thu"],"from":"08:00","to":"23:00"},"is_active":true,"sort":10,"categories":[{"name":"Hot Drinks","sort":10,"items":[{"product_id":15,"display_name":"Cairo Signature Coffee","price_override":75,"availability":{"from":"08:00","to":"22:00"},"modifier_groups":[{"id":3,"sort":10}],"prices":[{"channel":"delivery","order_type":"delivery","amount":85,"starts_at":"2026-10-01T00:00:00Z","ends_at":"2026-12-31T23:59:59Z","priority":10}],"combo_groups":[{"code":"drink-side","name":"Choose a Side","minimum_selections":1,"maximum_selections":1,"choices":[{"product_id":20,"display_name":"Water","price_delta":5,"is_default":true}]}],"sort":10}]}]}'
```

المسارات المتاحة:

```text
GET    /admin/api/v1/restaurant-pos/profiles/{profile}/menus
POST   /admin/api/v1/restaurant-pos/profiles/{profile}/menus
GET    /admin/api/v1/restaurant-pos/profiles/{profile}/menus/{menu}
PATCH  /admin/api/v1/restaurant-pos/profiles/{profile}/menus/{menu}
DELETE /admin/api/v1/restaurant-pos/profiles/{profile}/menus/{menu}
```

القنوات المدعومة حاليًا: `pos`, `qr`, `delivery`, و`kiosk`. عند التحديث أرسل snapshot كاملًا للأقسام والعناصر؛ الأقسام أو العناصر المحذوفة من الطلب تُحذف من المنيو.

يمكن تخصيص السعر حسب `channel` و`order_type` وفترة `starts_at`/`ends_at`. لا يقبل النظام فترتين متداخلتين لهما نفس القناة ونوع الطلب والأولوية. الـCombo يحتفظ بمجموعاته واختياراته وفروق الأسعار داخل Catalog snapshot المنشورة.

الأحجام والـvariants تستخدم منتجات ERP الحالية؛ يحتوي كل منتج منشور على `parent_id` و`is_configurable` لكي يجمع Edge المنتج الأساسي وأحجامه دون إنشاء مصدر أصناف موازٍ.

## 11. نشر Catalog وقراءة آخر نسخة

عملية النشر لا تحتاج body؛ تنشئ Snapshot جديدًا من بيانات الكتالوج الحالية:

```powershell
curl.exe --request POST "$TenantBaseUrl/admin/api/v1/restaurant-pos/profiles/1/catalog-publications" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json'

curl.exe "$TenantBaseUrl/admin/api/v1/restaurant-pos/profiles/1/catalog-publications/latest" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json'
```

## 12. Restaurant KDS stations and live tickets

Create a kitchen station and route either a product or a product category to it. A routing rule must contain exactly one of `product_id` and `product_category_id`.

```powershell
curl.exe --request POST "$TenantBaseUrl/admin/api/v1/restaurant-kds/stations" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json' `
    --header 'Content-Type: application/json' `
    --data-raw '{"profile_id":1,"code":"hot-kitchen","name":"Hot Kitchen","display_mode":"station","target_seconds":600,"settings":{"sound":true},"is_active":true,"sort":10,"routing_rules":[{"product_category_id":4,"order_type":"dine_in","priority":100,"is_active":true}]}'
```

Station management endpoints:

```text
GET    /admin/api/v1/restaurant-kds/stations
POST   /admin/api/v1/restaurant-kds/stations
GET    /admin/api/v1/restaurant-kds/stations/{station}
PATCH  /admin/api/v1/restaurant-kds/stations/{station}
DELETE /admin/api/v1/restaurant-kds/stations/{station}
```

Send the current kitchen ticket state from Edge to Cloud:

```powershell
curl.exe --request POST "$TenantBaseUrl/api/v1/edge/restaurant-kds/ticket-projections" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json' `
    --header 'Content-Type: application/json' `
    --data-raw '{"tickets":[{"edge_ticket_id":"018f47f0-92a2-7bc1-b37a-57dc00f72c14","edge_order_id":"4f332e88-79aa-4e61-88f7-aa852fa63696","station_id":1,"status":"preparing","priority":10,"version":4,"payload":{"number":"K-104","items":[{"name":"Burger","quantity":2}]},"source_created_at":"2026-09-23T18:20:00Z","source_updated_at":"2026-09-23T18:30:00Z"}]}'
```

Supported states are `queued`, `preparing`, `ready`, `served`, `recalled`, and `cancelled`. Each request accepts up to 500 tickets. Cloud applies only a higher version; equal or older versions are returned as `stale`.

Read the live Cloud projection:

```powershell
curl.exe "$TenantBaseUrl/admin/api/v1/restaurant-kds/ticket-projections?page=1" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json'
```

## 13. QR Ordering Cloud API

Create a public QR channel linked to a Restaurant POS profile:

```powershell
curl.exe --request POST "$TenantBaseUrl/admin/api/v1/qr-ordering/channels" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json' `
    --header 'Content-Type: application/json' `
    --data-raw '{"profile_id":1,"slug":"main-menu","name":"Main QR Menu","mode":"browse_and_order","requires_table_token":true,"is_active":true,"settings":{"languages":["ar","en"]}}'
```

Create a short-lived guest session for a table. The returned plaintext `token` is shown once and should be encoded in, or obtained after scanning, the table QR flow:

```powershell
curl.exe --request POST "$TenantBaseUrl/admin/api/v1/qr-ordering/channels/1/guest-sessions" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json' `
    --header 'Content-Type: application/json' `
    --data-raw '{"table_reference":"table-12","expires_in_minutes":120}'
```

Read the public menu using the channel slug and session token:

```powershell
$QrSessionToken = '<QR_SESSION_TOKEN>'
curl.exe "$TenantBaseUrl/api/v1/public/qr/main-menu/menu" `
    --header "X-QR-Session-Token: $QrSessionToken" `
    --header 'Accept: application/json'
```

Submit an idempotent order intent. Cloud recalculates prices from the requested current catalog version and keeps the order in `pending_edge`:

```powershell
curl.exe --request POST "$TenantBaseUrl/api/v1/public/qr/main-menu/orders" `
    --header "X-QR-Session-Token: $QrSessionToken" `
    --header 'Accept: application/json' `
    --header 'Content-Type: application/json' `
    --data-raw '{"idempotency_key":"guest-order-104","catalog_version":7,"order_type":"dine_in","customer":{"name":"Ahmed"},"items":[{"menu_item_id":11,"quantity":2,"modifier_option_ids":[41]}],"notes":"No onions"}'
```

Edge reads pending intents and acknowledges each one. The customer must not be shown an accepted order until this acknowledgement succeeds:

```powershell
curl.exe "$TenantBaseUrl/api/v1/edge/qr-ordering/order-intents?page=1" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json'

curl.exe --request PATCH "$TenantBaseUrl/api/v1/edge/qr-ordering/order-intents/<ORDER_INTENT_ID>" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json' `
    --header 'Content-Type: application/json' `
    --data-raw '{"status":"accepted","edge_order_id":"018f47f0-92a2-7bc1-b37a-57dc00f72c20","version":2}'
```

The public status endpoint is `GET /api/v1/public/qr/{channel}/orders/{order_intent}` and requires the same session token when the intent belongs to a protected guest session. Edge can reply with `accepted` or `rejected`; stale or repeated acknowledgement versions do not change the stored outcome.

## 14. إرسال حالة الترابيزات من Edge إلى Cloud

```powershell
curl.exe --request POST "$TenantBaseUrl/api/v1/edge/restaurant-tables/session-projections" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json' `
    --header 'Content-Type: application/json' `
    --data-raw '{"sessions":[{"edge_session_id":"35b27f34-bf8d-4f4c-b0c1-a1896da79d95","table_id":1,"edge_order_id":"4f332e88-79aa-4e61-88f7-aa852fa63696","waiter_reference":"waiter-12","guest_count":4,"status":"ordering","version":7,"payload":{"total":425.50},"source_updated_at":"2026-09-23T18:30:00Z"}]}'
```

استجابة نموذجية:

```json
{
  "data": {
    "applied": 1,
    "stale": 0
  }
}
```

الحد الأقصى 500 جلسة في الطلب. الحالات المتاحة: `seated`, `ordering`, `served`, `departed`, `cleaning`, و`closed`. يطبق Cloud النسخة الأعلى فقط؛ النسخة المساوية أو الأقدم تحتسب `stale`.

قراءة المصدر الحي من لوحة الإدارة:

```powershell
curl.exe "$TenantBaseUrl/admin/api/v1/restaurant-tables/session-projections?page=1" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json'
```

## 15. إرسال حالة التوصيل من Edge إلى Cloud

```powershell
curl.exe --request POST "$TenantBaseUrl/api/v1/edge/delivery/task-projections" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json' `
    --header 'Content-Type: application/json' `
    --data-raw '{"tasks":[{"edge_task_id":"53c79a89-cfa5-4f93-968a-c6fb88286348","edge_order_id":"4f332e88-79aa-4e61-88f7-aa852fa63696","driver_id":12,"status":"out_for_delivery","version":9,"latitude":30.0444,"longitude":31.2357,"payload":{"eta_minutes":18},"source_updated_at":"2026-09-23T18:35:00Z"}]}'
```

الحد الأقصى 500 مهمة في الطلب. الحالات المتاحة: `unassigned`, `assigned`, `accepted`, `picked_up`, `out_for_delivery`, `delivered`, `failed`, و`cancelled`. يطبق Cloud النسخة الأعلى فقط.

قراءة المصدر الحي من لوحة الإدارة:

```powershell
curl.exe "$TenantBaseUrl/admin/api/v1/delivery/task-projections?page=1" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json'
```

## 16. Restaurant operational reports

The reporting plugin depends on `restaurant-pos` and reads optional plugin tables only when they are installed. Missing sources are returned with `available: false`.

```powershell
curl.exe "$TenantBaseUrl/admin/api/v1/restaurant-reports/overview?from=2026-09-01&to=2026-09-30" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json'

curl.exe "$TenantBaseUrl/admin/api/v1/restaurant-reports/daily?from=2026-09-01&to=2026-09-30" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json'
```

`overview` summarizes QR orders, KDS tickets, table sessions, reservations, delivery tasks, and external delivery orders. `daily` currently provides accepted/rejected QR order totals. The maximum requested period is 366 days.

Tables administration endpoints:

```text
/api/v1/admin is not used; all staff endpoints start with /admin/api/v1.
GET|POST       /admin/api/v1/restaurant-tables/floors
GET|PATCH|DELETE /admin/api/v1/restaurant-tables/floors/{floor}
GET|POST       /admin/api/v1/restaurant-tables/floors/{floor}/tables
GET|PATCH|DELETE /admin/api/v1/restaurant-tables/floors/{floor}/tables/{table}
GET|POST       /admin/api/v1/restaurant-tables/reservations
GET|PATCH|DELETE /admin/api/v1/restaurant-tables/reservations/{reservation}
```

Delivery administration and external order bridge endpoints:

```text
/admin/api/v1/delivery/zones
/admin/api/v1/delivery/drivers
/admin/api/v1/delivery/channels
POST  /api/v1/integrations/delivery/channels/{channel}/orders
GET   /api/v1/edge/delivery/external-orders
PATCH /api/v1/edge/delivery/external-orders/{external_order}
```

Channel configuration is encrypted at rest. Integration authentication currently uses a scoped Sanctum token; provider-specific webhook signature adapters are implemented when each provider is selected.

## 17. تشخيص الأخطاء الشائعة

| الحالة | السبب المحتمل |
|---|---|
| `401 Invalid platform signature` | السر مختلف، أو timestamp منتهي، أو method/path/body المستخدم في التوقيع لا يطابق الطلب المرسل |
| `401 Unauthenticated` | Sanctum token مفقود أو غير صالح |
| `404` على مسار بلجن | الطلب أرسل إلى الدومين المركزي، أو البلجن غير مثبت ومفعل، أو السجل المطلوب غير موجود |
| `409` | إعادة استخدام `Idempotency-Key` مع body مختلف، أو تعارض purchase/domain |
| `422` | payload لا يطابق قواعد التحقق، مثل status غير مدعوم أو `table_id`/`driver_id` غير موجود |

## 18. حدود التنفيذ الحالي

- QR Ordering Cloud now manages public channels, short-lived hashed guest sessions, server-priced idempotent order intents, and Edge accept/reject acknowledgement. Payment and the local ordering runtime remain separate phases.
- The KDS implementation in this Cloud phase manages stations, routing rules, and live ticket projections. The local KDS screen, timers, routing execution, recall flow, and print fallback belong to the Edge phase.

- أمثلة هذا الملف تخص المسارات الموجودة في الكود الآن فقط.
- توجد REST APIs لإدارة Restaurant POS Profiles والمنيو. لا توجد حاليًا REST APIs لإدارة الترابيزات أو الحجوزات أو مناطق التوصيل أو السائقين؛ الموجود لهذه البلجنز هو تخزين الإعدادات عبر migrations ومزامنة الـprojections الحية الموضحة أعلاه.
- مصادقة Edge الحالية بـSanctum مناسبة للربط الأولي. قبل تشغيل أجهزة المطعم في الإنتاج يجب تنفيذ هوية جهاز مستقلة، صلاحيات محدودة، تدوير secrets، وإبطال الجهاز دون التأثير على مستخدمي لوحة الإدارة.
- تفاصيل عقد إنشاء التينت والتجديد والشراء موجودة في [عقد API إنشاء التينت والاشتراك والبلجنز](10_tenant_subscription_plugin_api.md).


## 19. ????? ???? Edge ??????? ???????

???? Default Admin ?? ?????? ???? ?????? `manage_edge_devices` ??????. ???? `plain_token` ??? ????? ???? ???? ?????? ?? secret store ??? ???? Edge:

```powershell
curl.exe --request POST "$TenantBaseUrl/admin/api/v1/edge-devices" `
    --header "Authorization: Bearer $SanctumToken" `
    --header 'Accept: application/json' `
    --header 'Content-Type: application/json' `
    --data-raw '{"company_id":1,"name":"Main Branch Server","type":"server","abilities":["edge:sync","restaurant-tables:sync","restaurant-kds:sync","delivery:sync","qr-ordering:sync"]}'
```

?????? ?????? ?????? ?????? ???? ?????. ???? ?????? ??????? ?? ??????? ????? ?????? ??????? ?? ??????? ???? ?? ???? Edge ???? `tenant_id` ?? `source_edge_device_id` ???? ?????:

```powershell
$EdgeToken = '<EDGE_DEVICE_TOKEN>'
curl.exe --request POST "$TenantBaseUrl/api/v1/edge/sync/events" `
    --header "Authorization: Bearer $EdgeToken" `
    --header 'Accept: application/json' `
    --header 'Content-Type: application/json' `
    --data-raw '{"events":[{"event_id":"019dd35e-c3f4-7000-8000-000000000001","aggregate_type":"sale_order","aggregate_id":"order-1001","aggregate_version":1,"event_type":"sale_order.closed","schema_version":1,"occurred_at":"2026-09-25T12:00:00Z","payload":{"total":125.50,"currency":"EGP"}}]}'
```

????? ??? `event_id` ???? ???????? ?????? ??duplicate ??? ?????? ?????. ??????? ??? ?????? ??????? ?????? ???? `409`. ????? ??????:

```text
GET  /admin/api/v1/edge-devices
POST /admin/api/v1/edge-devices/{edgeDevice}/rotate-token
POST /admin/api/v1/edge-devices/{edgeDevice}/revoke
```
