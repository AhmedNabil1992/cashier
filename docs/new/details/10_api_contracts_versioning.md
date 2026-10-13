# API Contracts & Versioning

## Rules
- OpenAPI for REST.
- JSON Schema for event/command payloads.
- no `Bearer <User_ID>`.
- user auth and device auth separate.
- idempotency key on write APIs that may retry.

## Version Headers
- `X-API-Version`
- `X-Edge-Version`
- `X-Schema-Version`

## Compatibility Handshake
Edge reports:
- edge_version
- api_version
- local_schema_version
- edge_profile
- enabled_plugins + versions
- supported_event_versions
- supported_command_versions

Cloud returns:
- compatibility status
- min/max supported versions
- required upgrade actions
- blocked features if incompatible
