# Filament 4 - Common Import Errors & Fixes

## ⚠️ CRITICAL: We are using Filament 4, NOT Filament 3

Filament 4 has different namespace structures compared to Filament 3. This document lists common errors that have occurred and their fixes.

---

## Error 1: Wrong Section Import

### ❌ WRONG (Filament 3)
```php
use Filament\Forms\Components\Section;
```

### ✅ CORRECT (Filament 4)
```php
use Filament\Schemas\Components\Section;
```

### Error Message
```
Class "Filament\Forms\Components\Section" not found
```

### How to Fix
Always use `Filament\Schemas\Components\Section` for form sections in Filament 4.

### Example Files to Learn From
- `app/Filament/Resources/Outlets/Schemas/OutletForm.php`
- `app/Filament/Resources/Suppliers/Schemas/SupplierForm.php`
- `app/Filament/Resources/Categories/Schemas/CategoryForm.php`

---

## Error 2: Wrong Get Import

### ❌ WRONG (Filament 3)
```php
use Filament\Forms\Get;
```

### ✅ CORRECT (Filament 4)
```php
use Filament\Schemas\Components\Utilities\Get;
```

### Error Message
```
Type error in closure parameters
```

### How to Fix
Use the full namespace path for Get utility in Filament 4.

### Example Files to Learn From
- `app/Filament/Resources/PurchaseOrders/Schemas/PurchaseOrderForm.php`

---

## Error 3: Wrong Set Import

### ❌ WRONG (Filament 3)
```php
use Filament\Forms\Set;
```

### ✅ CORRECT (Filament 4)
```php
use Filament\Schemas\Components\Utilities\Set;
```

### Error Message
```
Type error when using afterStateUpdated or other callbacks
```

### How to Fix
Use the full namespace path for Set utility in Filament 4.

### Example Files to Learn From
- `app/Filament/Resources/PurchaseOrders/Schemas/PurchaseOrderForm.php`

---

## Error 6: Type Hints in Closures (CRITICAL!)

### ❌ WRONG (Has type hints)
```php
use Filament\Forms\Get; // Wrong import!

TextInput::make('name')
    ->afterStateUpdated(function (Get $get, $set, $state) {
        // This will cause error!
    })
```

### ✅ CORRECT (No type hints)
```php
// No Get/Set import needed for closure parameters!

TextInput::make('name')
    ->afterStateUpdated(function ($get, $set, $state) {
        // Works perfectly!
    })
```

### Error Message
```
Argument #1 ($get) must be of type Filament\Forms\Get,
Filament\Schemas\Components\Utilities\Get given
```

### How to Fix
**NEVER use type hints for `$get` and `$set` parameters in closures!**
- Remove all type hints from closure parameters
- Remove `use Filament\Forms\Get;` and `use Filament\Forms\Set;` imports
- Just use `function ($get, $set, $state)` without type hints

### Why This Happens
In Filament 4, the actual type passed is `Filament\Schemas\Components\Utilities\Get`, not `Filament\Forms\Get`. But the safest approach is to NOT use type hints at all for these closure parameters.

---

## Error 4: Infolist Component (Not Available)

### ❌ WRONG (Filament 3)
```php
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewSupplier extends ViewRecord
{
    public function infolist(Infolist $infolist): Infolist
    {
        // ...
    }
}
```

### ✅ CORRECT (Filament 4)
Infolist is not available in Filament 4. Use different approaches:
1. Remove ViewRecord pages entirely
2. Use only List, Create, and Edit pages
3. Display details in Edit page instead

### Error Message
```
Class "Filament\Infolists\Infolist" is not available
```

### How to Fix
Don't create ViewRecord pages. Stick to List/Create/Edit pattern.

---

## Error 5: Form Action Buttons (suffixAction)

### ❌ WRONG (Approach that causes errors)
```php
use Filament\Forms\Components\Actions\Action;

TextInput::make('sku')
    ->suffixAction(
        Action::make('generate')
            ->icon('heroicon-m-arrow-path')
            ->action(function ($set) {
                $set('sku', 'PRD-'.strtoupper(substr(uniqid(), -6)));
            })
    )
```

### ✅ CORRECT (Simple default value)
```php
TextInput::make('sku')
    ->default(fn () => 'PRD-'.strtoupper(substr(uniqid(), -6)))
    ->helperText('Kode unik untuk identifikasi produk (akan auto-generate jika kosong)')
```

### Error Message
```
Class "Filament\Forms\Components\Actions\Action" not found
```

### How to Fix
Use `->default()` with closure for auto-generation instead of suffix actions.

---

## Quick Reference: Filament 4 Form Components Namespace

### ✅ CORRECT Imports for Filament 4

```php
// Schema and Section
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

// Utilities (for live updates)
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

// Regular Form Components (still under Filament\Forms\Components)
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
```

---

## Pattern to Follow

### When Creating a New Resource Form:

1. **Always check existing working forms first**
   - Look at `app/Filament/Resources/Outlets/Schemas/OutletForm.php`
   - Look at `app/Filament/Resources/Suppliers/Schemas/SupplierForm.php`

2. **Use correct Section import**
   ```php
   use Filament\Schemas\Components\Section;
   ```

3. **Use correct Get/Set imports if using live updates**
   ```php
   use Filament\Schemas\Components\Utilities\Get;
   use Filament\Schemas\Components\Utilities\Set;
   ```

4. **Don't use Infolist or ViewRecord pages**
   - Only use: ListRecord, CreateRecord, EditRecord

5. **For auto-generation, use default() not suffixAction()**
   ```php
   ->default(fn () => 'PREFIX-'.generateCode())
   ```

---

## Checklist Before Creating New Forms

- [ ] Checked existing form files for correct import patterns
- [ ] Using `Filament\Schemas\Components\Section` for sections
- [ ] Using `Filament\Schemas\Components\Utilities\Get` for Get
- [ ] Using `Filament\Schemas\Components\Utilities\Set` for Set
- [ ] Not using Infolist components
- [ ] Not using suffixAction() for buttons
- [ ] Using `->default()` for auto-generation

---

## Summary of All Errors

| Component | Filament 3 (Wrong) | Filament 4 (Correct) |
|-----------|-------------------|----------------------|
| Section | `Filament\Forms\Components\Section` | `Filament\Schemas\Components\Section` |
| Get (import) | `Filament\Forms\Get` | ❌ Don't import for closures |
| Set (import) | `Filament\Forms\Set` | ❌ Don't import for closures |
| Closure params | `function (Get $get, $set)` | `function ($get, $set)` NO type hints! |
| Infolist | Available in v3 | ❌ Not available in v4 |
| Form Actions | `Filament\Forms\Components\Actions\Action` | ❌ Use `->default()` instead |

---

## When in Doubt

**ALWAYS** check these reference files:
- `app/Filament/Resources/Outlets/Schemas/OutletForm.php` - Simple form with Section
- `app/Filament/Resources/Suppliers/Schemas/SupplierForm.php` - Professional form with multiple sections
- `app/Filament/Resources/PurchaseOrders/Schemas/PurchaseOrderForm.php` - Complex form with Get/Set and Repeater

**DO NOT** guess import paths. **ALWAYS** check working examples first.
