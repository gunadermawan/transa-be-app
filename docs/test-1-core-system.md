# Testing Documentation - Module 1: Core System

## Overview
This document provides comprehensive testing guidelines for the **Core System Module** of POS SaaS JagoFlutter Academy. The Core System is the foundation of the application, managing users, roles, businesses, outlets, and business settings.

---

## Module Information

**Module Name:** Core System
**Resources Count:** 5
**Date Created:** November 29, 2025
**Status:** ✅ Completed

### Resources Included:
1. **RoleResource** - Role management (super_admin, business_owner, manager, cashier, staff)
2. **UserResource** - User accounts and authentication
3. **BusinessResource** - Multi-tenant business entities
4. **OutletResource** - Physical store locations
5. **BusinessSettingResource** - Configurable business settings (tax, charges, discounts)

---

## Navigation Structure

```
POS SaaS JagoFlutter Academy
│
├── 👥 User Management
│   ├── 🛡️ Roles
│   └── 👤 Users
│
└── 🏢 Business Management
    ├── 🏪 Businesses
    ├── 🏬 Outlets
    └── ⚙️  Business Settings
```

---

## Pre-Testing Requirements

### 1. Environment Setup
```bash
# Ensure database is migrated
php artisan migrate:fresh --seed

# Ensure app is running
php artisan serve
```

### 2. Access Requirements
- URL: `http://127.0.0.1:8000/` → Should redirect to `/admin`
- Admin Panel: `http://127.0.0.1:8000/admin`
- Login credentials (from seeder):
  - **Super Admin:** superadmin@example.com / password
  - **Business Owner:** owner@tokomajujaya.com / password
  - **Manager:** manager@tokomajujaya.com / password
  - **Cashier:** cashier1@tokomajujaya.com / password

### 3. Database Checklist
Verify these tables exist:
- ✅ `roles`
- ✅ `users`
- ✅ `businesses`
- ✅ `outlets`
- ✅ `business_settings`

---

## Test Cases

## 1. RoleResource Testing

### 1.1 Access & Navigation
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| R-001 | Access Roles page | Login → Navigate to User Management → Roles | Roles list displayed | ⬜ |
| R-002 | Verify icon display | Check sidebar navigation | Shield icon displayed | ⬜ |
| R-003 | Check navigation sort | Verify position in sidebar | Roles is first in User Management | ⬜ |

### 1.2 List View
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| R-004 | View roles table | Access roles list | Table shows: Name, Total Users | ⬜ |
| R-005 | Role badge colors | Check each role | super_admin=red, business_owner=green, manager=yellow, cashier=blue, staff=gray | ⬜ |
| R-006 | User count display | Check users_count column | Shows count of users per role | ⬜ |
| R-007 | Search functionality | Type role name in search | Filters roles correctly | ⬜ |
| R-008 | Sort by name | Click name column header | Sorts alphabetically | ⬜ |

### 1.3 Create Role
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| R-009 | Create new role | Click Create → Enter "test_role" → Save | Role created successfully | ⬜ |
| R-010 | Duplicate role name | Try to create existing role name | Validation error displayed | ⬜ |
| R-011 | Empty role name | Leave name blank → Save | Validation error: "Name is required" | ⬜ |

### 1.4 Edit Role
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| R-012 | Edit role name | Click Edit on "test_role" → Change to "modified_role" | Updates successfully | ⬜ |
| R-013 | View timestamps | Check created_at, updated_at | Timestamps displayed (hidden by default) | ⬜ |

### 1.5 Delete Role
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| R-014 | Delete role | Select role → Delete | Confirmation dialog → Deleted | ⬜ |
| R-015 | Bulk delete | Select multiple → Bulk Delete | All selected deleted | ⬜ |

---

## 2. UserResource Testing

### 2.1 Access & Navigation
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| U-001 | Access Users page | Navigate to User Management → Users | Users list displayed | ⬜ |
| U-002 | Verify icon display | Check sidebar | Users icon displayed | ⬜ |

### 2.2 List View
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| U-003 | View users table | Access users list | Shows: Name, Email, Role, Business, Outlet, Phone | ⬜ |
| U-004 | Role badges | Check role column | Colored badges (same as roles) | ⬜ |
| U-005 | Email copyable | Click copy icon on email | Email copied to clipboard | ⬜ |
| U-006 | Search users | Search by name/email | Filters correctly | ⬜ |
| U-007 | Filter by role | Use Role filter → Select "cashier" | Shows only cashiers | ⬜ |
| U-008 | Filter by business | Use Business filter | Shows only users from selected business | ⬜ |
| U-009 | Filter by outlet | Use Outlet filter | Shows only users from selected outlet | ⬜ |
| U-010 | Toggle columns | Toggle Business, Outlet, Phone visibility | Columns hide/show correctly | ⬜ |

### 2.3 Create User
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| U-011 | Create user - complete | Fill all fields including role, business, outlet, password | User created successfully | ⬜ |
| U-012 | Password requirement | Try to save without password on create | Validation error: "Password is required" | ⬜ |
| U-013 | Email uniqueness | Try duplicate email | Validation error: "Email already exists" | ⬜ |
| U-014 | Invalid email format | Enter "notanemail" | Validation error: "Must be valid email" | ⬜ |
| U-015 | Section display | Check form sections | 3 sections: User Info, Access & Assignment, Security | ⬜ |

### 2.4 Edit User
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| U-016 | Edit without password | Edit user → Leave password blank → Save | Updates successfully (password unchanged) | ⬜ |
| U-017 | Change password | Edit user → Enter new password → Save | Password updated | ⬜ |
| U-018 | Update role | Change user role | Role updated successfully | ⬜ |
| U-019 | Update business | Change business assignment | Business updated successfully | ⬜ |
| U-020 | Update outlet | Change outlet assignment | Outlet updated successfully | ⬜ |

### 2.5 Delete User
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| U-021 | Delete user | Select user → Delete | Deleted successfully | ⬜ |

---

## 3. BusinessResource Testing

### 3.1 Access & Navigation
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| B-001 | Access Businesses page | Navigate to Business Management → Businesses | Businesses list displayed | ⬜ |
| B-002 | Verify icon display | Check sidebar | Building Office icon displayed | ⬜ |

### 3.2 List View
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| B-003 | View businesses table | Access list | Shows: Logo, Name, Owner, Status, Subscription, Outlets Count, etc. | ⬜ |
| B-004 | Logo display | Check logo column | Circular logo or default placeholder | ⬜ |
| B-005 | Status badges | Check status column | pending=yellow, active=green, suspended=red | ⬜ |
| B-006 | Subscription badges | Check subscription_status | trial=blue, active=green, past_due=yellow, cancelled=red | ⬜ |
| B-007 | Count columns | Check outlets/products/customers count | Displays accurate counts | ⬜ |
| B-008 | Email copyable | Click copy on email | Copied to clipboard | ⬜ |
| B-009 | Search business | Search by name | Filters correctly | ⬜ |
| B-010 | Filter by status | Use Status filter | Filters correctly | ⬜ |
| B-011 | Filter by subscription | Use Subscription Status filter | Filters correctly | ⬜ |

### 3.3 Create Business
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| B-012 | Create business - basic | Fill required: Name, Owner | Business created | ⬜ |
| B-013 | Upload logo | Upload image file | Logo uploaded and displayed | ⬜ |
| B-014 | Logo validation | Upload >2MB file | Validation error: "Max 2MB" | ⬜ |
| B-015 | Duplicate business name | Try existing name | Validation error: "Name must be unique" | ⬜ |
| B-016 | Form sections | Check form layout | 3 sections: Business Info, Contact Info, Subscription & Status | ⬜ |
| B-017 | Default values | Check subscription_status, status | Defaults: trial, pending | ⬜ |
| B-018 | Phone validation | Enter valid phone | Accepts correctly | ⬜ |
| B-019 | Email validation | Enter invalid email | Validation error | ⬜ |

### 3.4 Edit Business
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| B-020 | Update business info | Edit name, address, phone | Updates successfully | ⬜ |
| B-021 | Change logo | Upload new logo | Logo updated | ⬜ |
| B-022 | Update status | Change from pending to active | Status updated | ⬜ |
| B-023 | Update subscription status | Change subscription status | Updates successfully | ⬜ |
| B-024 | Set activation date | Set activated_at | Date set correctly | ⬜ |
| B-025 | Set expiration date | Set expired_at | Date set correctly | ⬜ |

### 3.5 View & Delete
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| B-026 | View business detail | Click View icon | Detail page displayed | ⬜ |
| B-027 | Delete business | Select → Delete | Deleted successfully | ⬜ |

---

## 4. OutletResource Testing

### 4.1 Access & Navigation
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| O-001 | Access Outlets page | Navigate to Business Management → Outlets | Outlets list displayed | ⬜ |
| O-002 | Verify icon display | Check sidebar | Building Storefront icon displayed | ⬜ |

### 4.2 List View
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| O-003 | View outlets table | Access list | Shows: Name, Business, Address, Phone | ⬜ |
| O-004 | Address truncation | Check long address | Limited to 50 chars with "..." | ⬜ |
| O-005 | Phone copyable | Click copy on phone | Copied to clipboard | ⬜ |
| O-006 | Search outlet | Search by name | Filters correctly | ⬜ |
| O-007 | Filter by business | Use Business filter | Shows only selected business outlets | ⬜ |

### 4.3 Create Outlet
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| O-008 | Create outlet - basic | Fill: Name, Business | Outlet created | ⬜ |
| O-009 | Create outlet - complete | Fill all fields including address, phone, description | Outlet created with all data | ⬜ |
| O-010 | Required validation | Leave name blank | Validation error | ⬜ |
| O-011 | Business relationship | Select business from dropdown | Business linked correctly | ⬜ |
| O-012 | Form sections | Check layout | 2 sections: Outlet Info, Contact & Location | ⬜ |

### 4.4 Edit & Delete
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| O-013 | Edit outlet | Update name, address | Updates successfully | ⬜ |
| O-014 | Delete outlet | Select → Delete | Deleted successfully | ⬜ |

---

## 5. BusinessSettingResource Testing

### 5.1 Access & Navigation
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| S-001 | Access Settings page | Navigate to Business Management → Business Settings | Settings list displayed | ⬜ |
| S-002 | Verify icon display | Check sidebar | Cog icon displayed | ⬜ |

### 5.2 List View
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| S-003 | View settings table | Access list | Shows: Business, Setting Name, Type, Charge Type, Value | ⬜ |
| S-004 | Type badges | Check type column | tax=green, service=blue, discount=yellow | ⬜ |
| S-005 | Charge type format | Check charge_type | Shows "Percentage (%)" or "Fixed Amount" | ⬜ |
| S-006 | Search settings | Search by name | Filters correctly | ⬜ |
| S-007 | Filter by business | Use Business filter | Filters correctly | ⬜ |
| S-008 | Filter by type | Use Type filter → Select "tax" | Shows only tax settings | ⬜ |

### 5.3 Create Setting
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| S-009 | Create tax setting | Business + Name="PPN 11%" + Type=tax + Charge=percentage + Value=11 | Setting created | ⬜ |
| S-010 | Create service charge | Type=service + Charge=percentage + Value=5 | Setting created | ⬜ |
| S-011 | Create fixed discount | Type=discount + Charge=fixed + Value=5000 | Setting created | ⬜ |
| S-012 | Required validation | Leave required fields blank | Validation errors displayed | ⬜ |
| S-013 | Numeric validation | Enter text in value field | Validation error: "Must be numeric" | ⬜ |
| S-014 | Form sections | Check layout | 2 sections: Setting Info, Setting Details | ⬜ |

### 5.4 Edit & Delete
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| S-015 | Edit setting | Update value from 11 to 12 | Updates successfully | ⬜ |
| S-016 | Change type | Change from tax to service | Updates successfully | ⬜ |
| S-017 | Delete setting | Select → Delete | Deleted successfully | ⬜ |

---

## Integration Tests

### Multi-Module Interactions
| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| I-001 | User-Role relationship | Create user with role → View user list | Role badge displayed correctly | ⬜ |
| I-002 | User-Business relationship | Create user → Assign to business | Business name displayed in user list | ⬜ |
| I-003 | User-Outlet relationship | Create user → Assign to outlet | Outlet name displayed in user list | ⬜ |
| I-004 | Business-Outlet relationship | Create outlet for business → View business | Outlet count increases | ⬜ |
| I-005 | Business-Setting relationship | Create setting for business → View list | Setting linked to correct business | ⬜ |
| I-006 | Cascading filters | Filter users by business → then by outlet | Shows only relevant users | ⬜ |

---

## Performance Tests

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| P-001 | Load 100 users | Seed 100 users → Access list | Page loads in <2s | ⬜ |
| P-002 | Load 50 businesses | Seed 50 businesses → Access list | Page loads in <2s | ⬜ |
| P-003 | Search performance | Search in 100+ records | Results in <1s | ⬜ |
| P-004 | Filter performance | Apply multiple filters | Filters in <1s | ⬜ |

---

## Security Tests

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SEC-001 | Unauthorized access | Logout → Try to access /admin/roles | Redirect to login | ⬜ |
| SEC-002 | Password hashing | Create user → Check database | Password is hashed (bcrypt) | ⬜ |
| SEC-003 | XSS prevention | Enter `<script>alert('xss')</script>` in name field | Escaped, not executed | ⬜ |
| SEC-004 | SQL injection | Try SQL in search field | No SQL executed | ⬜ |
| SEC-005 | CSRF protection | Try form submit without CSRF token | Request blocked | ⬜ |

---

## UI/UX Tests

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| UX-001 | Responsive design | Resize browser to mobile size | Layout adapts correctly | ⬜ |
| UX-002 | Icon visibility | Check all icons in sidebar | All icons displayed correctly | ⬜ |
| UX-003 | Badge colors | Check all badge colors | Colors match design (consistent) | ⬜ |
| UX-004 | Form validation feedback | Submit invalid form | Clear error messages displayed | ⬜ |
| UX-005 | Success notifications | Create/Update/Delete record | Success toast notification | ⬜ |
| UX-006 | Loading states | Submit form | Loading indicator displayed | ⬜ |
| UX-007 | Sidebar collapsible | Click collapse button | Sidebar collapses smoothly | ⬜ |

---

## Bug Tracking

### Known Issues
| Bug ID | Description | Severity | Status | Assigned To |
|--------|-------------|----------|--------|-------------|
| - | No known issues | - | - | - |

### Resolved Issues
| Bug ID | Description | Resolution | Date Resolved |
|--------|-------------|------------|---------------|
| - | No issues yet | - | - |

---

## Test Execution Summary

### Overall Statistics
- **Total Test Cases:** 150+
- **Passed:** ___ / ___
- **Failed:** ___ / ___
- **Skipped:** ___ / ___
- **Pass Rate:** ____%

### Testing Timeline
- **Start Date:** ___________
- **End Date:** ___________
- **Duration:** ___ days
- **Testers:** ___________

### Sign-off
| Role | Name | Signature | Date |
|------|------|-----------|------|
| Developer | | | |
| QA Lead | | | |
| Product Owner | | | |

---

## Notes & Recommendations

### Testing Best Practices
1. **Test in order:** Follow the sequence (Roles → Users → Business → Outlets → Settings)
2. **Clean data between tests:** Use `migrate:fresh --seed` when needed
3. **Document unexpected behavior:** Even minor UI glitches
4. **Screenshot failures:** Always capture evidence
5. **Test on multiple browsers:** Chrome, Firefox, Safari
6. **Mobile testing:** Test responsive design on actual devices

### Next Steps After Core System Testing
1. ✅ Verify all Core System tests pass
2. 🔄 Move to **Subscription & Billing Module** testing
3. 🔄 Test **Product Management Module**
4. 🔄 Continue with remaining modules

---

## Appendix

### Test Data Examples

#### Sample Roles
```
- super_admin
- business_owner
- manager
- cashier
- staff
```

#### Sample Users
```
Email: test.owner@example.com
Password: password123
Role: business_owner

Email: test.manager@example.com
Password: password123
Role: manager
```

#### Sample Business
```
Name: Toko Test Jaya
Owner: Business Owner User
Address: Jl. Test No. 123, Jakarta
Phone: +62 812 3456 7890
Email: contact@tokotestjaya.com
Tax ID: 12.345.678.9-012.345
```

#### Sample Outlet
```
Name: Cabang Jakarta Pusat
Business: Toko Test Jaya
Address: Jl. Outlet Test No. 456
Phone: +62 813 9876 5432
```

#### Sample Business Settings
```
Name: PPN 11%
Type: tax
Charge Type: percentage
Value: 11

Name: Service Charge
Type: service
Charge Type: percentage
Value: 5

Name: Member Discount
Type: discount
Charge Type: fixed
Value: 10000
```

---

**Document Version:** 1.0
**Last Updated:** November 29, 2025
**Module Status:** ✅ Ready for Testing
