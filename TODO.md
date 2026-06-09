# TODO - AJAXified product loading

- [x] Add AJAX endpoint route(s) for product grid infinite scroll (`/products/ajax`).

- [x] Add controller method `ProductsController@ajaxLoad` (or similar) returning JSON with rendered product-grid HTML.

- [x] Add Blade partial for grid product cards matching the existing shop grid columns.

- [ ] Update `resources/views/front/products.blade.php` to:

  - [x] Render initial product list into a dedicated container
  - [x] Add an IntersectionObserver scroll sentinel

  - [x] Fetch next pages from `/products/ajax` preserving filters & `category` query param


- [x] Append returned HTML and stop when no more pages


- [x] Manual testing:


  - [ ] `/products` loads additional pages on scroll
  - [ ] `/products?category=1` loads only that category on scroll
  - [ ] Filters (sort/search/brand/price_range/category) keep working with infinite scroll

