# API / Compatibility Contract


## Edge Hello
```json
{
  "edge_version":"1.5.0",
  "api_version":"2",
  "local_schema_version":"41",
  "edge_profile":"RESTAURANT",
  "plugins":{"restaurant-pos":"2.0.0","restaurant-kds":"1.4.0"},
  "supported_event_versions":[1,2],
  "supported_command_versions":[1]
}
```

Cloud returns compatible/degraded/upgrade_required/blocked plus min/max versions.
