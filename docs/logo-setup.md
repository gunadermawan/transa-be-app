# Logo & Branding Setup Guide

## Overview
This guide explains how to add your custom logo and favicon to the POS SaaS JagoFlutter Academy application.

---

## Quick Setup

### 1. Prepare Your Images

**Logo Requirements:**
- **Format:** PNG (recommended) or SVG
- **Size:** 200x50px to 400x100px (width × height)
- **Background:** Transparent PNG preferred
- **File name:** `logo.png` or `logo.svg`

**Favicon Requirements:**
- **Format:** PNG or ICO
- **Size:** 32×32px or 64×64px
- **File name:** `favicon.png` or `favicon.ico`

---

### 2. Upload Images

Place your logo files in:
```
public/images/
├── logo.png          # Main logo
└── favicon.png       # Browser favicon
```

**Using Terminal:**
```bash
# From project root
cp /path/to/your/logo.png public/images/logo.png
cp /path/to/your/favicon.png public/images/favicon.png
```

**Using Finder/File Manager:**
1. Navigate to `laravel_jago_pos_backend/public/images/`
2. Copy your logo and favicon files there
3. Ensure filenames match exactly: `logo.png` and `favicon.png`

---

### 3. Enable Logo in Filament

Edit `app/Providers/Filament/AdminPanelProvider.php`:

**Uncomment these lines:**
```php
->brandLogo(asset('images/logo.png'))
->brandLogoHeight('2rem')
->favicon(asset('images/favicon.png'))
```

**Full example:**
```php
return $panel
    ->default()
    ->id('admin')
    ->path('admin')
    ->login()
    ->brandName('POS SaaS JagoFlutter Academy')
    ->brandLogo(asset('images/logo.png'))        // ← Uncomment
    ->brandLogoHeight('2rem')                    // ← Uncomment
    ->favicon(asset('images/favicon.png'))       // ← Uncomment
    ->colors([
        'primary' => Color::Blue,
    ])
```

---

### 4. Verify Setup

1. Clear cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. Refresh browser (Ctrl+F5 or Cmd+Shift+R)

3. Check:
   - ✅ Logo appears in sidebar
   - ✅ Favicon shows in browser tab

---

## Free Logo Resources

### Option 1: AI Logo Generators
- **Canva:** https://www.canva.com/create/logos/
- **Looka:** https://looka.com/
- **Tailor Brands:** https://www.tailorbrands.com/

### Option 2: Icon Libraries (Make Simple Logo)
- **Heroicons:** https://heroicons.com/
- **Font Awesome:** https://fontawesome.com/
- **Flaticon:** https://www.flaticon.com/

### Option 3: Design Tools
- **Figma:** Create custom logo (free)
- **Adobe Express:** Logo maker (free tier)
- **GIMP:** Free photoshop alternative

---

## Logo Design Tips

### For POS SaaS Application:
1. **Simple & Clean:** Easy to recognize at small sizes
2. **Professional:** Reflects business credibility
3. **Relevant Icons:**
   - 🛒 Shopping cart
   - 💳 Point of sale terminal
   - 📊 Analytics/graphs
   - 🏪 Store/shop
   - 💰 Money/cash register

### Color Palette Suggestions:
- **Primary Blue:** #3B82F6 (matches Filament default)
- **Accent Colors:** Green (#10B981), Orange (#F59E0B)
- **Professional:** Navy (#1E3A8A), Gray (#6B7280)

---

## Quick DIY Logo (Text-based)

If you want a simple text logo quickly:

1. Use **Canva:**
   - Go to canva.com
   - Create custom size (400×100px)
   - Add text: "JagoFlutter POS"
   - Choose professional font
   - Add small icon (cart/shop)
   - Download as PNG (transparent)

2. **GIMP Method:**
   - New image: 400×100px
   - Add text layer
   - Export as PNG

3. **Online Tool:**
   - Use https://www.namecheap.com/logo-maker/
   - Generate free logo
   - Download PNG

---

## Temporary Solution

If you don't have a logo yet:

### Option 1: Text-Only (Current Setup)
Logo lines are commented out, showing text only:
```php
->brandName('POS SaaS JagoFlutter Academy')
// Logo commented out - shows text only
```

### Option 2: Use Emoji/Unicode
```php
->brandName('🏪 POS SaaS JagoFlutter Academy')
```

### Option 3: Use Initials
```php
->brandName('PJA POS SaaS')  // POS JagoFlutter Academy
```

---

## Advanced Customization

### Different Logos for Light/Dark Mode
```php
->brandLogo(fn () => auth()->user()?->isDarkMode()
    ? asset('images/logo-dark.png')
    : asset('images/logo-light.png')
)
```

### Custom Logo Height
```php
->brandLogoHeight('3rem')  // Larger logo
->brandLogoHeight('1.5rem')  // Smaller logo
```

### SVG Logo (Scalable)
```php
->brandLogo(asset('images/logo.svg'))
```

---

## Troubleshooting

### Logo Not Showing
1. **Check file exists:**
   ```bash
   ls -la public/images/logo.png
   ```

2. **Check permissions:**
   ```bash
   chmod 644 public/images/logo.png
   ```

3. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   ```

4. **Check browser console** for 404 errors

### Logo Too Large/Small
Adjust height in AdminPanelProvider:
```php
->brandLogoHeight('2rem')   // Default
->brandLogoHeight('2.5rem') // Larger
->brandLogoHeight('1.5rem') // Smaller
```

### Wrong Aspect Ratio
- Crop/resize your image before uploading
- Recommended aspect: 4:1 (width:height)
- Example: 400×100px, 200×50px

---

## Example File Structure

```
public/
└── images/
    ├── .gitkeep
    ├── logo.png              # Main logo (200x50px)
    ├── logo-dark.png         # Dark mode variant (optional)
    ├── logo-light.png        # Light mode variant (optional)
    ├── favicon.png           # Browser favicon (32x32px)
    ├── favicon.ico           # IE favicon (optional)
    └── default-business-logo.png  # For businesses without logo
```

---

## Current Status

- ✅ Directory created: `public/images/`
- ⏸️ Logo references: **Commented out** (text-only mode)
- 📝 Ready for logo upload

**Next Steps:**
1. Get/create your logo file
2. Upload to `public/images/`
3. Uncomment logo lines in AdminPanelProvider
4. Refresh browser

---

**Document Version:** 1.0
**Last Updated:** November 29, 2025
**Status:** Ready for logo upload
