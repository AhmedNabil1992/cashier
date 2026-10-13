# Edge Lifecycle & Provisioning

## Baseline
Ubuntu only.

## First Boot State Machine
`UNPAIRED → PAIRING → VALIDATING → LICENSED → FETCHING_MANIFEST → PREPARING_DB → INSTALLING_PLUGINS → MIGRATING → DOWNLOADING_SNAPSHOT → APPLYING → HEALTH_CHECK → READY`

## Setup UI
يطلب:
- Cloud Base URL.
- One-Time Pairing Token.

لا يفترض Subdomain؛ Custom Domain مدعوم.

## Cloud Pairing Validation
- token valid/unexpired/unused.
- tenant active.
- branch valid.
- device limit.
- no conflicting active authority.
- edge version compatibility.

## بعد Pairing
Cloud يصدر:
- edge_id.
- device credentials/certificate.
- tenant/branch binding.
- signed license.
- edge profile.
- plugin manifest.
- sync policy.
- initial snapshot descriptor.

## Startup Flow
`Check DB → Check License → Load Core → Load Plugins → Allow Local Operation → Connect Cloud → Check Commands/Updates → Upload Outbox → Pull Changes`

Cloud availability ليست شرطًا لبدء POS بعد provisioning إذا license يسمح.

## Update Checks
- Scheduled حسب إعداد الفرع.
- Startup.
- `CheckForUpdates` command.
- Manual admin action.

## Replacement Server
لا يسمح لسيرفرين بإنشاء transactions لنفس branch دون controlled authority transfer.
