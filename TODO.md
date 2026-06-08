# TODO - Admin returns/refunds fixes

- [ ] Inspect existing admin returns/refund controllers and views (done)
- [x] Add validation/state guards + idempotency to `ReturnsAdminController@refundToWallet()`
- [x] Add validation/state guards for reverse pickup and exchange approval
- [x] Ensure refund completion sets `approved_at` deterministically
- [x] Optionally disable refund-to-wallet UI until refund is approved
- [x] Run quick sanity checks (lint/tests/ route check)



