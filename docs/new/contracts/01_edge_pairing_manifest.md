# Edge Pairing Contract


## Request
```json
{
  "cloud_base_url": "https://customer-domain.com",
  "pairing_token": "one-time-token",
  "device": {
    "fingerprint": "...",
    "hostname": "...",
    "edge_version": "..."
  }
}
```

## Response
```json
{
  "edge_id": "...",
  "tenant_id": "...",
  "branch_id": "...",
  "edge_profile": "RETAIL",
  "device_credential": "...",
  "license": "signed-payload",
  "manifest_version": 12,
  "snapshot": {"id":"...","cursor":"..."}
}
```

Pairing token one-time فقط ولا يستخدم في التشغيل اليومي.
