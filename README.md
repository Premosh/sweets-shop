# Sweets Shop — Simple PHP Ecommerce# Sweets Shop — Simple PHP Ecommerce



A small modular ecommerce demo for a sweets shop built with plain PHP, HTML, CSS, and JavaScript (no third-party libraries).A small modular ecommerce demo for a sweets shop built with plain PHP, HTML, CSS, and JavaScript (no third-party libraries).



## FeaturesFeatures

- Product listing loaded from `data/products.json`- Product listing loaded from `data/products.json`

- Product detail pages- Product detail pages

- Session-based cart (add/update/remove/clear)- Session-based cart (add/update/remove/clear)

- Wishlist with AJAX- Simple checkout (confirmation page) — no real payments

- Checkout with eSewa payment integration (v2 API)- Modular includes: `includes/functions.php`, `includes/header.php`, `includes/footer.php`

- Google Analytics tracking

- Modular includes: `includes/functions.php`, `includes/header.php`, `includes/footer.php`Run locally (Windows PowerShell):



## Quick Start (Local Development)```powershell

cd d:\ecom\sweets-shop

### Windows PowerShellphp -S localhost:8000

```powershell# Open http://localhost:8000 in your browser

cd d:\xampp\htdocs\sweets-shop```

php -S localhost:8000

# Open http://localhost:8000 in your browserStructure

```- `index.php` — product listing

- `product.php` — product details

### Mac/Linux- `cart.php` — cart management and view

```bash- `checkout.php` — checkout form + confirmation

cd ~/path/to/sweets-shop- `data/products.json` — sample products

php -S localhost:8000- `includes/` — header/footer/functions

```- `assets/` — css, js, images



## Project StructureNotes

```- No external libraries used.

sweets-shop/- To add/edit products, modify `data/products.json`.

├── index.php              # Product listing page- Images use `assets/images/placeholder.svg` by default; replace with real images if desired.

├── product.php            # Product detail page

├── cart.php               # Cart management & viewConfiguration

├── checkout.php           # Checkout & payment processing- Admin password is stored in `config.php` at the project root. Edit `config.php` to change the admin password (default is `admin123`).

├── wishlist.php           # Wishlist page- For security, do NOT commit `config.php` to a public repository. In production, keep secrets outside the webroot or use environment variables.

├── wishlist-api.php       # AJAX wishlist API

├── config.php             # Configuration (local defaults)Next steps (optional)

├── data/- Add admin UI to manage products

│   └── products.json      # Product catalog- Integrate with a database (MySQL/SQLite) for persistence

├── includes/- Add user auth and order storage

│   ├── env.php           # Production config (NOT in Git)
│   ├── functions.php     # Helper functions (cart, eSewa, etc.)
│   ├── header.php        # Page header/navigation
│   └── footer.php        # Page footer
├── assets/
│   ├── css/style.css     # Styles
│   ├── js/app.js         # Cart/checkout JS
│   ├── js/wishlist.js    # Wishlist JS
│   └── images/           # Product images
└── admin/
    ├── index.php         # Admin dashboard (password protected)
    ├── login.php         # Admin login
    ├── edit.php          # Edit products
    └── logout.php        # Admin logout
```

## Configuration

### Local Development (config.php defaults)
No setup needed. Run `php -S localhost:8000` and visit http://localhost:8000.

### Production Deployment
Create `includes/env.php` (NOT committed to Git) with your actual values:

```php
<?php
define('SITE_URL', 'https://your-domain.epizy.com');
define('ESEWA_MODE', 'RC'); // or 'live'
define('ESEWA_MERCHANT_ID', 'your_merchant_id');
define('ESEWA_SECRET_KEY', 'your_secret_key');
define('ADMIN_PASSWORD', 'your_strong_password');
?>
```

See `includes/env.php` for a full template with comments.

## Deployment Guide

### ✅ Option 1: InfinityFree (Recommended - Free)

**Best for:** Traditional PHP apps with sessions and file storage.

**Steps:**

1. Sign up at https://infinityfree.net
2. Create a new hosting account and note your FTP credentials
3. Upload files via FTP or file manager:
   - Connect with FTP: `ftp.YOUR_ACCOUNT.ftp.infinityfree.net`
   - Upload all files to `/htdocs/`
   - **Enable "Show hidden files"** in FTP client to upload `.htaccess`
4. Create `includes/env.php` on the server:
   - Use file manager or FTP
   - Copy the template from local `includes/env.php`
   - Replace placeholders with your domain, eSewa credentials
5. Configure eSewa in merchant dashboard:
   - Callback URL: `https://your-subdomain.epizy.com/checkout.php`
   - Use RC (test) environment first
6. Test:
   - Visit your domain
   - Try adding to cart and checkout
   - Check InfinityFree error logs if issues arise

**Pros:** Free, simple, file storage persists  
**Cons:** Shared server (slower), limited support

---

### ✅ Option 2: Railway.app (Free tier available)

**Best for:** Easy deployment with auto-scaling.

**Steps:**

1. Sign up at https://railway.app (GitHub login recommended)
2. Create new project → select this GitHub repo
3. Set environment variables in Railway dashboard:
   ```
   SITE_URL=https://your-railway-app.up.railway.app
   ESEWA_MODE=RC
   ESEWA_MERCHANT_ID=your_merchant_id
   ESEWA_SECRET_KEY=your_secret_key
   ```
4. Deploy: Railway auto-deploys on git push
5. Set eSewa callback URL to your Railway app domain

**Pros:** Easy, auto-deploys on push, good uptime  
**Cons:** Free tier limited (5,000 compute hours/month)

---

### ✅ Option 3: Render.com (Free tier available)

**Best for:** Free hosting with persistent filesystem.

**Steps:**

1. Sign up at https://render.com
2. Create "New Web Service" → connect GitHub repo
3. Select **PHP** environment
4. Set build & start commands in Render dashboard:
   - Build Command: (leave empty)
   - Start Command: `php -S 0.0.0.0:3000`
5. Add environment variables (same as Railway)
6. Deploy and set eSewa callbacks

**Pros:** Free, reliable, persistent filesystem  
**Cons:** Slower startup time

---

### ⚠️ Option 4: Netlify (Not Recommended)

Netlify is optimized for static sites, not traditional PHP. See `NETLIFY_DEPLOYMENT.md` for details.

---

## eSewa Payment Integration

### Test Credentials (RC Environment - Use for Testing)
- **Merchant ID**: `JB0BBQ4aD0UqIThFJwAKBgAXEUkEGQUBBAwdOgABHD4DChwUAB0R`
- **Secret Key**: `8gBm/:&EnhH.1/q`
- **Gateway URL**: `https://rc-epay.esewa.com.np`

**How to test:**
1. Ensure `includes/env.php` has `ESEWA_MODE='RC'` (default)
2. Go to checkout and enter any amount
3. eSewa will show test payment UI
4. Complete test payment to verify integration

### Production (Live) Setup
1. Request live credentials from eSewa: https://developer.esewa.com.np
2. Update `includes/env.php`:
   ```php
   define('ESEWA_MODE', 'live');
   define('ESEWA_MERCHANT_ID', 'your_live_merchant_id');
   define('ESEWA_SECRET_KEY', 'your_live_secret_key');
   ```
3. Redeploy and update eSewa callback URL to production domain

---

## Editing Products

Edit `data/products.json` to add/update/remove products:

```json
{
  "products": [
    {
      "id": 1,
      "name": "Chocolate Cake",
      "price": 400,
      "image": "assets/images/cake.jpg",
      "description": "Rich chocolate cake",
      "badge": "Best Seller"
    }
  ]
}
```

Then redeploy (or edit directly on server via file manager).

---

## Admin Panel

Access at `/admin/` (password protected):
- **Default password**: `admin123`
- **Change password**: Update `ADMIN_PASSWORD` in `includes/env.php`
- Manage products, view sales (when database support is added)

---

## Security Best Practices

1. **Never commit secrets to Git**:
   - `includes/env.php` is in `.gitignore`
   - Keep eSewa credentials private
   - Use strong admin passwords

2. **Always use HTTPS** in production (required for payments)

3. **Change default admin password** immediately in production

4. **Use environment variables** for sensitive data instead of hardcoding in code

5. **Keep dependencies updated** (none currently, but plan for future)

---

## Troubleshooting

### "Page not found" error
- Check that `.htaccess` was uploaded
- Verify mod_rewrite is enabled on your hosting
- Test with direct file access: `domain.com/index.php`

### eSewa payment fails
- Check `includes/env.php` exists and has correct credentials
- Verify callback URL is set in eSewa dashboard
- Check hosting error logs for detailed messages
- Ensure hosting supports outgoing HTTPS (cURL)

### Cart not working
- Sessions must be enabled (default on most hosts)
- Check `/tmp` or session directory has write permissions
- Review `includes/functions.php` session setup

---

## Future Enhancements

- [ ] Database (MySQL) for order persistence
- [ ] Admin panel for order management
- [ ] User registration and login
- [ ] Email notifications for orders
- [ ] Inventory management
- [ ] Product reviews/ratings
- [ ] Multiple payment gateways

---

## Support & Resources

- **eSewa Developer Docs**: https://developer.esewa.com.np
- **PHP Documentation**: https://www.php.net/manual
- **InfinityFree Support**: https://www.infinityfree.com/support
- **Railway Documentation**: https://docs.railway.app
- **Render Documentation**: https://render.com/docs

---

## License

Created as a learning project. Feel free to use and modify freely.

---

**Last Updated**: November 2025  
**PHP Version Required**: 8.0+  
**No external dependencies** (plain PHP + vanilla JavaScript)
