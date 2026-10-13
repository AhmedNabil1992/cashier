# Sync, Events & Commands

## Events = Facts
أمثلة:
- `order.paid`
- `shift.closed`
- `inventory.movement.created`
- `user.permissions.version_changed`

## Commands = Requests
أمثلة:
- `SyncNow`
- `CheckForUpdates`
- `RefreshLicense`
- `ApplyConfiguration`
- `InstallPlugin`
- `RunHealthCheck`
- `CreateBackup`

## Event Envelope
راجع [غلاف الحدث](../contracts/02_sync_event_envelope.md).

## Command Lifecycle
`REQUESTED → DELIVERED → ACCEPTED → RUNNING → APPLIED|REJECTED|FAILED|EXPIRED`

## Guarantees
- Outbox داخل نفس transaction مع business write.
- Inbox unique by event_id.
- ACK بعد durable write.
- retries exponential/backoff.
- duplicate-safe handlers.
- no Last-Write-Wins for payments/stock ledger/closed shifts.

## Initial Snapshot
- consistent reference cursor.
- chunking/checksums.
- apply then pull events after snapshot cursor.

## WebSocket
Wake-up/notification only. Durable data transfer عبر HTTPS.


تمييز الحالة: DELIVERED/ACCEPTED ليست APPLIED. ACK نقل حدث محفوظ لا يخفي فشل ترحيله المحاسبي؛ تعرض received/applied/posting status بصورة منفصلة بحسب العقد. لا يعد cursor متقدمًا إلا للبيانات المحفوظة/المطبقة حسب تعريف stream.
