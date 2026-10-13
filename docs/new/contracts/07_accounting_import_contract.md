# Accounting Posting / Import Contract


هدف العقد: تشغيل Edge لا يكتب مباشرة في General Ledger.

Edge/Sync يرسل business facts مثل:
- paid sale
- refund
- cash movement
- shift close
- payment settlement

Cloud adapter يحولها إلى ERP posting وفق قواعد الحسابات الحالية.

Requirements:
- idempotent posting by source_event_id.
- traceable source branch/shift/order/payment.
- reversals instead of silent update.
- posting status and error queue.
