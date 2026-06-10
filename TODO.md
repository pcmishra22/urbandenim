# TODO - PayU integration (replace Razorpay)

- [ ] Step 1: Add PayU config keys to `config/services.php` (merchant_key, secret/salt, base_url)
- [ ] Step 2: Update `app/Http/Controllers/PaymentController.php` to create PayU hosted checkout redirect request
- [ ] Step 3: Update `app/Http/Controllers/PaymentController.php` to verify PayU callback and mark order paid
- [x] Step 4: Update `resources/views/front/checkout.blade.php` to remove Razorpay JS and use PayU redirect flow
- [x] Step 5: Update UI labels/text Razorpay → PayU
- [x] Step 6: Ensure order id/payment reference stored (reuse existing `razorpay_*` columns for now)
- [ ] Step 7: Manual test: COD commented/disabled; card/upi works end-to-end in sandbox


