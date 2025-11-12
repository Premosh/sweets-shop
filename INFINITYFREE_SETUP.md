# InfinityFree Deployment Verification

**Site URL:** https://sweets-shop.infinityfreeapp.com  
**Deployment Date:** November 12, 2025

---

## ✅ Next Steps to Complete

### 1. Upload `includes/env.php` to InfinityFree

**Via InfinityFree File Manager (easiest):**
1. Log in to your InfinityFree control panel
2. Click "File Manager"
3. Navigate to `public_html/includes/`
4. Upload the `includes/env.php` file from your local machine

**Via FTP (alternative):**
1. Use FileZilla or another FTP client
2. Connect to: `ftp.YOUR_ACCOUNT.ftp.infinityfree.net`
3. Username/Password: from InfinityFree control panel
4. Upload to: `/htdocs/includes/env.php`

---

### 2. Configure eSewa Callback URL

1. Log in to your eSewa Merchant Dashboard: https://dashboard.esewa.com.np
2. Find settings/configuration section
3. Set **Callback/Redirect URL** to:
   ```
   https://sweets-shop.infinityfreeapp.com/checkout.php
   ```
4. Make sure you're using **RC (Test) environment**

---

### 3. Test Your Live Site

1. Visit: https://sweets-shop.infinityfreeapp.com
2. Browse products
3. Add an item to cart
4. Go to checkout
5. Try a test payment with eSewa

**Test eSewa Credentials:**
- Merchant ID: `JB0BBQ4aD0UqIThFJwAKBgAXEUkEGQUBBAwdOgABHD4DChwUAB0R`
- Secret Key: `8gBm/:&EnhH.1/q`
- Gateway: RC (test) environment

---

### 4. Troubleshooting

#### "Page not found" or blank page
- Check that all files uploaded correctly
- Verify `includes/env.php` exists on the server
- Check InfinityFree error logs in control panel

#### eSewa payment fails
- Verify callback URL is set correctly in eSewa dashboard
- Check that `includes/env.php` has correct SITE_URL
- Review InfinityFree error logs for cURL/HTTPS issues

#### "Cannot access cart" or sessions not working
- Sessions should work by default on InfinityFree
- If issues persist, uncomment session config lines in `includes/env.php`:
  ```php
  ini_set('session.save_path', '/tmp');
  ini_set('session.gc_probability', 1);
  ini_set('session.gc_divisor', 1000);
  ```

---

## 🔐 Security Checklist

- [ ] `includes/env.php` is NOT committed to Git (it's in `.gitignore`)
- [ ] Admin password changed from default `admin123` (optional but recommended)
- [ ] HTTPS enabled (InfinityFree provides free SSL)
- [ ] eSewa credentials kept private (not shared in code)

---

## 📊 Admin Panel

Access your admin area at:
```
https://sweets-shop.infinityfreeapp.com/admin/
```

**Default password:** `admin123`

**To change password:**
1. Edit `includes/env.php` on the server
2. Update: `define('ADMIN_PASSWORD', 'your_new_password');`
3. Save and reload

---

## 🚀 Going Live (When Ready)

To switch from test to live eSewa payments:

1. Request live credentials from eSewa: https://developer.esewa.com.np
2. Update `includes/env.php`:
   ```php
   define('ESEWA_MODE', 'live');
   define('ESEWA_MERCHANT_ID', 'your_live_merchant_id');
   define('ESEWA_SECRET_KEY', 'your_live_secret_key');
   ```
3. Update eSewa dashboard callback URL to your production domain
4. Upload updated `includes/env.php`
5. Test with real payment

---

## 📞 Support

- **eSewa Developer Docs:** https://developer.esewa.com.np
- **InfinityFree Support:** https://www.infinityfree.com/support
- **PHP Documentation:** https://www.php.net/manual

---

**Site is now live!** 🎉 Test purchases and report any issues.
