# Operations, Fleet, Backup & Updates

## Fleet Dashboard
لكل Edge:
- status/last_seen
- profile
- version
- plugin versions
- local schema
- CPU/RAM/disk
- MySQL health
- clock drift
- sync lag/pending/oldest
- license remaining
- backup age
- printers/hardware status

## Release Channels
- internal
- pilot
- canary
- stable

## Update Flow
1. compatibility check.
2. signed artifact download.
3. checksum/signature verify.
4. backup.
5. maintenance readiness.
6. migrations.
7. service reload.
8. health checks.
9. success ACK or rollback/recovery.

## Backups
- local backup.
- off-device backup.
- retention policy.
- periodic restore drill.
- RPO/RTO defined before production.

## Failure Tests
power loss, disk full, MySQL restart, Node crash, cert expiry, clock rollback, network flapping.
