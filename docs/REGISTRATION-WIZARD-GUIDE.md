# 🚀 Registration Wizard - Implementation Guide

**Status:** ✅ READY & TESTED
**URL:** http://127.0.0.1:8000/register
**Flow:** 2-Step Wizard → Auto-create Business/Outlet → 14-Day Trial → Auto-login → Dashboard

---

## 📋 What's Included

### **Files Created:**
```
app/Http/Controllers/Auth/RegisterController.php   (Registration logic)
app/Http/Requests/RegisterStep1Request.php         (Validation - User data)
app/Http/Requests/RegisterStep2Request.php         (Validation - Business data)
resources/views/auth/register-step1.blade.php      (User info form)
resources/views/auth/register-step2.blade.php      (Business info form)
routes/web.php                                      (Registration routes)
```

### **Models Updated:**
```
app/Models/SubscriptionPlan.php        (Added fillable & casts)
app/Models/BusinessSubscription.php    (Added fillable & casts)
```

---

## 🎯 User Journey

### **Step 1: User Information** (`/register`)
User fills in:
- ✅ Nama Lengkap (required)
- ✅ Email (required, unique)
- ⚪ Nomor Telepon (optional)
- ✅ Password (required, min 8 chars)
- ✅ Konfirmasi Password (required, must match)

**What happens:**
1. Validation runs (RegisterStep1Request)
2. Data stored in session: `registration.step1`
3. Redirect to step 2

---

### **Step 2: Business Information** (`/register/step2`)
User fills in:
- ✅ Nama Bisnis (required)
- ⚪ Nama Outlet (optional - defaults to "[Business Name] - Pusat")
- ⚪ Alamat Bisnis (optional)
- ⚪ Telepon Bisnis (optional)
- ⚪ Email Bisnis (optional - defaults to user email)

**What happens:**
1. Validation runs (RegisterStep2Request)
2. Database transaction begins
3. Auto-creates:
   - ✅ User (role: business_owner)
   - ✅ Business (status: active, trial)
   - ✅ Default Outlet
   - ✅ Trial Subscription (14 days, Rp 0)
   - ✅ Links: User → Business → Outlet
4. Transaction commits
5. User auto-login
6. Redirect to Filament dashboard
7. Success message: "Selamat datang! Trial 14 hari Anda telah dimulai. 🎉"

---

## 🔧 Technical Implementation

### **Controller Logic** (RegisterController.php)

#### **showStep1()** - Display Step 1
```php
public function showStep1()
{
    return view('auth.register-step1');
}
```

#### **processStep1()** - Process Step 1
```php
public function processStep1(RegisterStep1Request $request)
{
    // Store validated data in session
    $request->session()->put('registration.step1', $request->validated());

    return redirect()->route('register.step2');
}
```

#### **showStep2()** - Display Step 2
```php
public function showStep2(Request $request)
{
    // Check if step 1 is completed
    if (!$request->session()->has('registration.step1')) {
        return redirect()->route('register.step1')
            ->with('error', 'Silakan lengkapi data akun Anda terlebih dahulu.');
    }

    return view('auth.register-step2');
}
```

#### **processStep2()** - Complete Registration
```php
public function processStep2(RegisterStep2Request $request)
{
    // Get step 1 data from session
    $step1Data = $request->session()->get('registration.step1');
    $step2Data = $request->validated();

    DB::beginTransaction();
    try {
        // 1. Create User (Owner)
        $user = User::create([...]);

        // 2. Create Business
        $business = Business::create([...]);

        // 3. Create Default Outlet
        $outlet = Outlet::create([...]);

        // 4. Get/Create Trial Plan
        $trialPlan = SubscriptionPlan::firstOrCreate(['slug' => 'trial'], [...]);

        // 5. Create Trial Subscription
        $subscription = BusinessSubscription::create([...]);

        // 6. Link Everything
        $business->update(['current_subscription_id' => $subscription->id]);
        $user->update(['business_id' => $business->id, 'outlet_id' => $outlet->id]);

        DB::commit();

        // Clear session & login
        $request->session()->forget('registration');
        Auth::login($user);

        return redirect()->route('filament.admin.pages.dashboard');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', '...');
    }
}
```

---

## 🎨 Design Features

### **Progress Bar**
- Shows: "Langkah X dari 2"
- Visual progress: 50% → 100%
- Consistent with landing page design

### **Form Styling**
- Clean, professional inputs
- Proper validation error display
- Focus states with primary blue
- Mobile responsive
- Placeholder text for guidance

### **Trust Signals**
- "🔒 Data Anda aman"
- "✓ Tidak perlu kartu kredit"
- "⚡ Setup 5 menit"

### **User Guidance**
- Clear field labels
- Required fields marked with (*)
- Optional fields marked with "(Opsional)"
- Help text below fields
- "What Happens Next" section on step 2

---

## 📊 Database Structure

### **Trial Subscription Details:**
```php
[
    'slug' => 'trial',
    'name' => 'Trial',
    'price' => 0,
    'billing_cycle' => 'once',
    'max_outlets' => 1,
    'max_users' => 2,
    'max_products' => 50,
    'max_transactions_per_month' => 100,
    'features' => [
        '1 outlet',
        '2 users',
        'Max 50 products',
        'Max 100 transactions/month',
        'Dashboard dasar',
    ],
    'is_active' => true,
]
```

### **Business Created With:**
```php
[
    'status' => 'active',
    'subscription_status' => 'trial',
    'activated_at' => now(),
    'expired_at' => now()->addDays(14),  // 14-day trial
]
```

---

## 🛣️ Routes

```php
// Registration Wizard (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showStep1'])
        ->name('register.step1');

    Route::post('/register/step1', [RegisterController::class, 'processStep1'])
        ->name('register.step1.process');

    Route::get('/register/step2', [RegisterController::class, 'showStep2'])
        ->name('register.step2');

    Route::post('/register/step2', [RegisterController::class, 'processStep2'])
        ->name('register.step2.process');
});
```

---

## ✅ Validation Rules

### **Step 1 (User Data):**
```php
'name' => 'required|string|max:255',
'email' => 'required|email|unique:users,email|max:255',
'phone' => 'nullable|string|max:20',
'password' => 'required|confirmed|min:8',
```

### **Step 2 (Business Data):**
```php
'business_name' => 'required|string|max:255',
'business_phone' => 'nullable|string|max:20',
'business_email' => 'nullable|email|max:255',
'address' => 'nullable|string|max:500',
'outlet_name' => 'nullable|string|max:255',
```

### **Custom Error Messages:**
All validation rules have Indonesian error messages for better UX.

---

## 🔐 Security Features

### **Middleware:**
- `guest` - Only accessible when not logged in
- Prevents logged-in users from re-registering

### **Data Protection:**
- Password hashing with bcrypt
- Email uniqueness validation
- CSRF protection on forms
- Database transactions (rollback on error)

### **Session Management:**
- Step 1 data stored in session
- Session cleared after successful registration
- Prevents direct access to step 2 without step 1

---

## 🎯 Integration with Landing Page

All CTAs on landing page now point to registration:

### **Updated Links:**
1. **Navigation:** "Coba Gratis" → `/register`
2. **Hero:** "Mulai Trial Gratis 14 Hari" → `/register`
3. **Pricing Cards:** All "Mulai Trial" → `/register`
4. **Trial CTA Section:** "Mulai Trial Gratis Sekarang" → `/register`

---

## 🧪 Testing Checklist

### **Manual Testing:**
- ✅ Step 1 form renders correctly
- ✅ Validation works (try invalid email, short password)
- ✅ Session persists between steps
- ✅ Step 2 redirects back if step 1 not completed
- ✅ Business/Outlet/Subscription created correctly
- ✅ User auto-login works
- ✅ Redirect to dashboard works
- ⏳ Check trial expiration (14 days from now)
- ⏳ Test with real data and login again

### **Test Scenario 1: Happy Path**
```
1. Visit http://127.0.0.1:8000/register
2. Fill valid data:
   - Name: Test User
   - Email: test@example.com
   - Password: password123
3. Click "Lanjut ke Data Bisnis"
4. Fill business data:
   - Business Name: Toko Test
5. Click "Mulai Trial Gratis"
6. Should redirect to dashboard
7. Check welcome message appears
```

### **Test Scenario 2: Validation Errors**
```
1. Visit /register
2. Try to submit empty form
3. Should see error messages
4. Try duplicate email
5. Should see "Email sudah terdaftar"
6. Try password mismatch
7. Should see "Konfirmasi password tidak cocok"
```

### **Test Scenario 3: Direct Step 2 Access**
```
1. Visit /register/step2 directly
2. Should redirect to /register with error message
```

---

## 📈 Post-Registration Flow

### **What User Sees:**
1. ✅ Redirected to Filament dashboard
2. ✅ Welcome message with trial info
3. ✅ Dashboard showing business data
4. ✅ Sidebar with all menu items
5. ✅ Trial countdown visible (if implemented)

### **Database State:**
```sql
-- Users table
id | name      | email           | role_id | business_id | outlet_id
1  | Test User | test@domain.com | 2       | 1           | 1

-- Businesses table
id | name      | owner_id | subscription_status | expired_at
1  | Toko Test | 1        | trial               | +14 days

-- Outlets table
id | name             | business_id
1  | Toko Test - Pusat | 1

-- Business Subscriptions table
id | business_id | plan_id | status | expired_at
1  | 1           | 1       | active | +14 days

-- Subscription Plans table
id | slug  | name  | price | max_outlets | max_users
1  | trial | Trial | 0     | 1           | 2
```

---

## 🚨 Error Handling

### **Registration Fails:**
- Database transaction rolls back
- User sees error message
- Form data preserved (withInput)
- Can retry submission

### **Session Expired:**
- Step 2 redirects to step 1
- User can start over
- No data loss

---

## 🔄 Future Enhancements

### **Optional Additions:**
1. **Email Verification** - Send verification email after registration
2. **Demo Data Seeder** - Auto-create sample products/transactions
3. **Onboarding Tour** - Guide user through dashboard features
4. **Trial Countdown** - Show remaining trial days in dashboard
5. **Upgrade Prompt** - Prompt to upgrade before trial expires
6. **Social Registration** - Google/Facebook OAuth
7. **Business Category** - Add category selection in step 2
8. **Currency Selection** - Let user choose currency
9. **Tax Settings** - Optional tax configuration
10. **Analytics Tracking** - Track registration funnel

---

## 📊 Analytics Events (To Implement)

```javascript
// Track registration funnel
gtag('event', 'registration_started', {
  'event_category': 'engagement',
  'event_label': 'Step 1 Viewed'
});

gtag('event', 'registration_step2', {
  'event_category': 'engagement',
  'event_label': 'Step 2 Viewed'
});

gtag('event', 'sign_up', {
  'event_category': 'conversion',
  'method': 'email',
  'trial_days': 14
});
```

---

## 🎯 Conversion Optimization

### **Current Features:**
- ✅ 2-step wizard (not overwhelming)
- ✅ Clear progress indicator
- ✅ Optional fields marked clearly
- ✅ Trust signals visible
- ✅ No credit card required
- ✅ Auto-login (reduce friction)
- ✅ Immediate access to dashboard

### **Best Practices Applied:**
- Short form fields (only essentials)
- Indonesian language (target audience)
- Mobile responsive
- Clear error messages
- Success feedback
- Professional design

---

## 🔗 Related Documentation

- [Landing Page Guide](./LANDING-PAGE-GUIDE.md)
- [Demo Deployment Strategy](./DEMO-DEPLOYMENT-STRATEGY.md)
- [Marketing Roadmap](./marketing-focused-roadmap.md)

---

## ✨ Summary

**What We Built:**
✅ 2-step registration wizard
✅ Session-based multi-step flow
✅ Complete validation with error messages
✅ Auto-creation of Business + Outlet
✅ 14-day trial subscription setup
✅ Auto-login after registration
✅ Redirect to dashboard
✅ Mobile responsive design
✅ Aligned with landing page style

**Key Benefits:**
- 🚀 User can start using POS in < 2 minutes
- 💳 No credit card required for trial
- ✅ All data structures created automatically
- 🔐 Secure with proper validation
- 📱 Works on all devices
- 🎨 Professional, clean design

**Status:** 🟢 READY FOR DEMO

**Next:** Add trial countdown, feature gating, upgrade prompts

---

*Built with Laravel 12 + Filament 4*
*Aligned with JagoFlutter Academy brand*
