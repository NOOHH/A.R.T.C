# Session Environment Setup Guide

## Fast Fixes Applied ✅

1. **Updated TrustProxies Middleware**: Removed AWS ELB specific header that can cause issues on some platforms
2. **Fixed Session Domain Binding**: Removed hardcoded domain that was causing 419 errors
3. **Cleared All Runtime Caches**: Config, application, route, view, and compiled caches

## Environment Variables for Sevalla

Set these environment variables in your Sevalla deployment:

```bash
# Application
APP_URL=https://laravel-zfurp.sevalla.app
APP_KEY=base64:your-existing-app-key-here

# Session Configuration (CRITICAL for fixing 419 errors)
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_DOMAIN=        # Leave this BLANK (or remove it entirely)

# Optional: Database sessions (if you want to switch later)
# SESSION_DRIVER=database
```

## Key Points:

1. **SESSION_DOMAIN must be blank** - This lets Laravel default to the current host
2. **Don't rotate APP_KEY** - Keep your existing base64:... key
3. **Use HTTPS URLs** - Ensure APP_URL starts with https://

## Verification Steps:

1. **Check Cookies in DevTools**: After loading /login, you should see:
   - `laravel_session` (Secure, SameSite=Lax)
   - `XSRF-TOKEN` (if using JavaScript)

2. **Network Tab**: On POST /login, confirm cookies are being sent

3. **Environment Check**: Run this command to verify settings:
   ```bash
   php -r 'foreach(["APP_URL","SESSION_DRIVER","SESSION_DOMAIN","SESSION_SECURE_COOKIE","APP_KEY"] as $k){echo $k."=".$_ENV[$k].PHP_EOL;}'
   ```

## If 419 Still Occurs:

1. Ensure you're visiting https:// (not http://)
2. Remove any CDN cache on /login and / pages
3. Check that your login form has `@csrf` directive
4. Verify cookies are being set and sent properly

## Optional: Database Sessions

If you want to use database-backed sessions later:

```bash
php artisan session:table
php artisan migrate --force
```

Then set `SESSION_DRIVER=database` and clear caches again.
