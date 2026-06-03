# TODO

- [x] Fix Laravel crash: missing `notifications` table by creating migration and running `php artisan migrate`.
- [x] Fix role-based dashboard sidebar: customer should not see admin links.
  - [x] Update `resources/views/layouts/dashboard.blade.php` to show admin sidebar only for admin role.
  - [ ] Ensure customer/vendor dashboards render correct sidebar.
  - [ ] Quick manual test: login as customer and verify left panel links.


