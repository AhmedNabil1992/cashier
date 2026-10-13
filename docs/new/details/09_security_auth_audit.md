# Security, Authentication & Audit

## Cloud Users
ERP authentication + authorization with tenant context.

## Edge Users
- users/roles/permissions synced from Cloud.
- PIN or other permitted local factor.
- hashes only.
- local session tokens with expiry.
- permission version stored locally.

## Device Trust
- one-time pairing token.
- permanent edge identity after pairing.
- mTLS or signed device credentials design preferred.
- key rotation/revocation.
- signed license.
- signed release/plugin packages.

## Sensitive Actions Audit
- void/refund.
- discount override.
- price override.
- drawer kick.
- shift reopen/adjustments.
- plugin install/disable.
- remote support action.
- backup/restore.

## Support Access
Explicit customer consent + time limit + reason + audit trail.
