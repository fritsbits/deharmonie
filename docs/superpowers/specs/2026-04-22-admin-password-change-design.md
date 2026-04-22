# Admin password change page

## Problem

The Filament admin account is seeded from env with a fallback password of `secret`. There is currently no way for the admin to rotate their password without shell access, so in practice the default password tends to stay in place. That is dangerous.

## Goal

Give the admin a self-service page to change their own password, reachable from the top-right user menu in the Filament panel. Follow Filament 4 defaults.

## Non-goals

- Editing the admin's name or email. The `User::canAccessPanel()` check gates access on `email === config('auth.admin_email')`, so an email change would immediately lock the admin out. Out of scope for this spec.
- Multi-user admin, roles, or password reset by email. Single admin, manual rotation only.
- Admin panel translations. The app locale is `nl`; Filament 4 ships Dutch translations and handles the profile page labels automatically.

## Approach

Use Filament's built-in panel-level `->profile()` feature with a custom `EditProfile` page that overrides the default form to show password fields only. This is the Filament-canonical path: routing, user-menu link, authentication, and page chrome come from the base class.

## Components

### 1. `App\Filament\Pages\EditProfile`

Extends `Filament\Auth\Pages\EditProfile`. Overrides `form(Schema $schema): Schema` to define three fields:

- **Current password** — `currentPassword` field (camelCase is required by Filament's base class), `->password()`, `->required()`, `->currentPassword()` (verifies against the authenticated user's hash), `->dehydrated(false)` (not persisted).
- **New password** — `password` field, `->password()`, `->required()`, `->rule(Password::default())`, `->same('passwordConfirmation')`, `->revealable()`.
- **New password confirmation** — `passwordConfirmation` field (camelCase mandated by parent's `same()` rule), `->password()`, `->required()`, `->dehydrated(false)`, `->revealable()`.

Hashing is handled automatically by the `password => hashed` cast on `User`; no manual `Hash::make` needed.

### 2. `AdminPanelProvider`

Add `->profile(EditProfile::class)` to the panel chain. That registers the route (`/admin/profile`) and wires the "Admin" entry in the top-right user menu to link to the page.

### 3. No changes to

- `User` model (existing `canAccessPanel` check continues to gate the profile page like any other Filament page).
- `AdminUserSeeder` or env configuration.

## Behaviour

- User menu (top-right): "Admin" label becomes a link to `/admin/profile`; "Uitloggen" unchanged.
- Submitting the form with a valid current password and a strong matching new password updates the hash and shows Filament's default success notification.
- Wrong current password → field-level validation error.
- New password failing `Password::default()` (Laravel's default: min 8 chars) → field-level validation error.
- Mismatched confirmation → field-level validation error.
- Unauthenticated access to `/admin/profile` → redirects to Filament login.
- A user whose email no longer matches `config('auth.admin_email')` → blocked by existing `canAccessPanel`.

## Testing

Feature test at `tests/Feature/Filament/EditProfileTest.php` using Livewire testing helpers, covering:

1. **Happy path** — authenticated admin submits valid current password + strong new password (matching confirmation); assert the user's hash changes and the new password verifies.
2. **Wrong current password** — assert validation error on `current_password`, hash unchanged.
3. **Weak new password** — assert validation error on `password`, hash unchanged.
4. **Mismatched confirmation** — assert validation error on `password`, hash unchanged.
5. **Unauthenticated GET `/admin/profile`** — asserts redirect to login.
6. **Non-admin user (email ≠ configured admin email)** — asserts 403 / blocked by `canAccessPanel`.

## Open questions

None.
