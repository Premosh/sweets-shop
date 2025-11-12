# Sweets Shop — Simple PHP Ecommerce

A small modular ecommerce demo for a sweets shop built with plain PHP, HTML, CSS, and JavaScript (no third-party libraries).

Features
- Product listing loaded from `data/products.json`
- Product detail pages
- Session-based cart (add/update/remove/clear)
- Simple checkout (confirmation page) — no real payments
- Modular includes: `includes/functions.php`, `includes/header.php`, `includes/footer.php`

Run locally (Windows PowerShell):

```powershell
cd d:\ecom\sweets-shop
php -S localhost:8000
# Open http://localhost:8000 in your browser
```

Structure
- `index.php` — product listing
- `product.php` — product details
- `cart.php` — cart management and view
- `checkout.php` — checkout form + confirmation
- `data/products.json` — sample products
- `includes/` — header/footer/functions
- `assets/` — css, js, images

Notes
- No external libraries used.
- To add/edit products, modify `data/products.json`.
- Images use `assets/images/placeholder.svg` by default; replace with real images if desired.

Configuration
- Admin password is stored in `config.php` at the project root. Edit `config.php` to change the admin password (default is `admin123`).
- For security, do NOT commit `config.php` to a public repository. In production, keep secrets outside the webroot or use environment variables.

Next steps (optional)
- Add admin UI to manage products
- Integrate with a database (MySQL/SQLite) for persistence
- Add user auth and order storage
