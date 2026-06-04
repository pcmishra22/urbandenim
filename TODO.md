# TODO - Fix product image storage + DB logic

- [x] Update frontend product image rendering with filesystem existence check and default fallback
  - [x] resources/views/front/partials/product-card.blade.php
  - [x] resources/views/front/product-detail.blade.php
- [x] Update admin product image upload + delete to use storage/products/{product_id}/images/{filename}
  - [x] app/Http/Controllers/Admin/ProductController.php
- [x] Update migration semantics: `product_images.image` stores only filename (keep column type string)
  - [x] database/migrations/2026_05_28_113002_create_product_images_table.php
- [x] Add one-time data migration command/script
  - [x] Convert existing product_images.image values that currently contain paths -> basename
- [x] Test: run `php artisan migrate` (and run the data migration if included)
- [ ] Smoke test: create/upload product images and verify default.jpeg fallback when missing

