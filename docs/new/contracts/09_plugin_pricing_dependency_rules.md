# Plugin Pricing & Dependency Rules


## Checkout Calculation
`Total = Base Platform + Requested Plugins + Missing Required Dependencies + Quantity/Scope Charges - Discounts`

## Example
Customer requests `KDS` on branch 2:
- if Restaurant POS already owned: charge KDS only.
- if not owned: add Restaurant POS as required dependency.
- Printing fallback is optional, so لا يضاف إجباريًا.

## Rules
- no hidden dependency charge.
- all required additions displayed before payment.
- annual/monthly cycles versioned.
- price changes do not rewrite historical invoices.
- entitlement version increments after successful commercial action.
