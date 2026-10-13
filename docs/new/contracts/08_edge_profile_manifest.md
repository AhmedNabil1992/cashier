# Edge Profile Manifest


```json
{
  "profile": "RETAIL",
  "required_core": ["edge-core","local-auth","sync","licensing"],
  "default_ui": "retail-pos",
  "enabled_plugins": ["retail-pos","shifts-cash","printing-hardware"],
  "profile_config_version": 4
}
```

Profile يحدد default experience فقط؛ entitlement هو الحاكم النهائي للـPlugins.
