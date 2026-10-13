# Command Protocol


```json
{
  "command_id": "...",
  "command_type": "CheckForUpdates",
  "tenant_id": "...",
  "branch_id": "...",
  "edge_id": "...",
  "created_at": "...",
  "expires_at": "...",
  "requested_by": "...",
  "payload": {}
}
```

States:
`REQUESTED → DELIVERED → ACCEPTED → RUNNING → APPLIED|FAILED|REJECTED|EXPIRED`

Commands لا تعتبر Events ولا يجوز اعتبار إرسالها نجاحًا قبل result state.


الاسم الموحد `command_type` يطابق متطلبات info؛ `type` في المسودة السابقة اسم قديم وليس حقلًا ثانيًا. توثق aliases لحالات info البشرية مثل Received عند اعتماد Schema؛ لا يعتبر DELIVERED إتمامًا.
