# Admin password change Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a Filament-canonical profile page that lets the admin change only their password, linked from the top-right user menu.

**Architecture:** Extend Filament 4's built-in `Filament\Auth\Pages\EditProfile` and override `form()` to expose only current-password, new-password, and new-password-confirmation fields. Enable via `->profile(EditProfile::class)` on `AdminPanelProvider`. Reuse the base class's validation, hashing, rate-limiting, and success notification — no new logic needed beyond the form definition.

**Tech Stack:** Laravel 13, Filament 4, Livewire 3, PHPUnit 12.

**Spec:** `docs/superpowers/specs/2026-04-22-admin-password-change-design.md`

---

## Files

- Create: `app/Filament/Pages/EditProfile.php` — custom password-only profile page
- Modify: `app/Providers/Filament/AdminPanelProvider.php` — register `->profile(EditProfile::class)`
- Create: `tests/Feature/Filament/EditProfileTest.php` — feature tests covering happy path + validation + access

---

## Task 1: Create the custom EditProfile page

**Files:**
- Create: `app/Filament/Pages/EditProfile.php`

**Background for the implementer:**
- Filament 4's base `Filament\Auth\Pages\EditProfile` class already defines `getPasswordFormComponent()`, `getPasswordConfirmationFormComponent()`, and `getCurrentPasswordFormComponent()` helpers. They include all the right validation rules (`Password::default()`, `->currentPassword(guard: ...)`, `->same('passwordConfirmation')`) and the password hashing dehydrate callback.
- Two quirks we must override: the parent's `currentPassword` field is only `->visible()` when the password or email changes, and the `passwordConfirmation` field is only `->visible()` when `password` is filled. For a dedicated password-change page we want all three fields visible up front.
- The parent's `getPasswordFormComponent()` also has `->dehydrated(fn ($state): bool => filled($state))` (allows blank → skip), which is wrong for a page whose sole purpose is changing the password. We add `->required()` to force a value.
- Form state paths are `data.currentPassword`, `data.password`, `data.passwordConfirmation` (camelCase — matches the parent's field names).
- The `User` model has a `password => hashed` cast. The parent's `getPasswordFormComponent()` calls `Hash::make($state)` in `dehydrateStateUsing`. Laravel's `hashed` cast detects already-hashed values via `Hash::isHashed()` and skips re-hashing, so there's no double-hash bug.

- [ ] **Step 1: Create the file with the skeleton class**

Create `app/Filament/Pages/EditProfile.php`:

```php
<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getCurrentPasswordFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getCurrentPasswordFormComponent(): Component
    {
        return parent::getCurrentPasswordFormComponent()->visible(true);
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()->required();
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return parent::getPasswordConfirmationFormComponent()->visible(true);
    }
}
```

- [ ] **Step 2: Run Pint**

Run: `vendor/bin/pint --dirty --format agent`
Expected: no style errors (or fixes applied).

- [ ] **Step 3: Commit**

```bash
git add app/Filament/Pages/EditProfile.php
git commit -m "feat(admin): add password-only EditProfile page"
```

---

## Task 2: Register the profile page on the admin panel

**Files:**
- Modify: `app/Providers/Filament/AdminPanelProvider.php`

- [ ] **Step 1: Add the `->profile(EditProfile::class)` call**

Open `app/Providers/Filament/AdminPanelProvider.php`. Find the chain after `->login()`:

```php
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
```

Change to:

```php
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(\App\Filament\Pages\EditProfile::class)
```

(Use a `use App\Filament\Pages\EditProfile;` import at the top of the file instead of the fully qualified name if the existing style prefers imports — it does; there's already `use App\Filament\Pages\Dashboard;`.)

Final imports block should include:

```php
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\EditProfile;
```

And the chain becomes:

```php
            ->login()
            ->profile(EditProfile::class)
```

- [ ] **Step 2: Clear Filament route/view cache**

Run: `php artisan view:clear && php artisan route:clear`
Expected: commands succeed without errors.

- [ ] **Step 3: Smoke-check the route is registered**

Run: `php artisan route:list --path=admin/profile`
Expected: one row showing `GET|HEAD admin/profile ... filament.admin.auth.profile`.

- [ ] **Step 4: Run Pint**

Run: `vendor/bin/pint --dirty --format agent`

- [ ] **Step 5: Commit**

```bash
git add app/Providers/Filament/AdminPanelProvider.php
git commit -m "feat(admin): enable profile page in admin panel"
```

---

## Task 3: Feature test — authenticated admin can open the profile page

**Files:**
- Create: `tests/Feature/Filament/EditProfileTest.php`

**Background for the implementer:**
- The project uses PHPUnit (not Pest). Existing Filament test pattern: `tests/Feature/TeamLidResourceTest.php` — seeds `AdminUserSeeder`, then `actingAs($adminUser)->get('/admin/...')`.
- The `AdminUserSeeder` reads `ADMIN_LOGIN_EMAIL` (default `admin@deharmonie.be`) and `ADMIN_LOGIN_PASSWORD` (default `secret`). `canAccessPanel` gates by `config('auth.admin_email')` which reads `ADMIN_LOGIN_EMAIL`.
- All tests use `RefreshDatabase`.
- For Livewire form-submit testing, use `Livewire::test(\App\Filament\Pages\EditProfile::class)` and chain `->fillForm([...])->call('save')`. Filament 4 forms use `statePath('data')` so when using Livewire directly, field names are `data.currentPassword`, `data.password`, `data.passwordConfirmation`. But with `->fillForm([...])` from `Filament\Testing\Concerns\HasForms` you pass the raw keys (`currentPassword`, etc.) and Filament handles the state path.

- [ ] **Step 1: Create the test file with one passing smoke test**

Create directory if needed, then create `tests/Feature/Filament/EditProfileTest.php`:

```php
<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditProfileTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::where('email', config('auth.admin_email'))->firstOrFail();
    }

    public function test_profile_page_renders_for_authenticated_admin(): void
    {
        $this->seed(AdminUserSeeder::class);

        $response = $this->actingAs($this->adminUser())->get('/admin/profile');

        $response->assertStatus(200);
    }
}
```

- [ ] **Step 2: Run the test and verify it passes**

Run: `php artisan test --compact --filter=test_profile_page_renders_for_authenticated_admin`
Expected: 1 passed.

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/Filament/EditProfileTest.php
git commit -m "test(admin): profile page renders for authenticated admin"
```

---

## Task 4: Test — unauthenticated access redirects to login

**Files:**
- Modify: `tests/Feature/Filament/EditProfileTest.php`

- [ ] **Step 1: Add the failing test**

In `tests/Feature/Filament/EditProfileTest.php`, add after the existing test:

```php
    public function test_profile_page_redirects_guest_to_login(): void
    {
        $response = $this->get('/admin/profile');

        $response->assertRedirect('/admin/login');
    }
```

- [ ] **Step 2: Run the test and verify it passes**

Run: `php artisan test --compact --filter=test_profile_page_redirects_guest_to_login`
Expected: 1 passed. (No implementation change needed — Filament's auth middleware does this.)

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/Filament/EditProfileTest.php
git commit -m "test(admin): profile page redirects guest"
```

---

## Task 5: Test — happy path password change

**Files:**
- Modify: `tests/Feature/Filament/EditProfileTest.php`

- [ ] **Step 1: Add imports**

At the top of `tests/Feature/Filament/EditProfileTest.php`, add these imports below the existing ones:

```php
use App\Filament\Pages\EditProfile;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
```

- [ ] **Step 2: Add the failing test**

Append to the class body:

```php
    public function test_admin_can_change_password_with_valid_current_password(): void
    {
        $this->seed(AdminUserSeeder::class);
        $admin = $this->adminUser();
        $admin->password = Hash::make('old-password-123');
        $admin->save();

        Livewire::actingAs($admin)
            ->test(EditProfile::class)
            ->fillForm([
                'currentPassword' => 'old-password-123',
                'password' => 'NewStrongPass!99',
                'passwordConfirmation' => 'NewStrongPass!99',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $admin->refresh();
        $this->assertTrue(Hash::check('NewStrongPass!99', $admin->password));
    }
```

- [ ] **Step 3: Run the test and verify it passes**

Run: `php artisan test --compact --filter=test_admin_can_change_password_with_valid_current_password`
Expected: 1 passed.

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/Filament/EditProfileTest.php
git commit -m "test(admin): happy-path password change"
```

---

## Task 6: Test — wrong current password rejected

**Files:**
- Modify: `tests/Feature/Filament/EditProfileTest.php`

- [ ] **Step 1: Add the failing test**

Append to the class body:

```php
    public function test_wrong_current_password_blocks_change(): void
    {
        $this->seed(AdminUserSeeder::class);
        $admin = $this->adminUser();
        $admin->password = Hash::make('old-password-123');
        $admin->save();
        $originalHash = $admin->password;

        Livewire::actingAs($admin)
            ->test(EditProfile::class)
            ->fillForm([
                'currentPassword' => 'wrong-password',
                'password' => 'NewStrongPass!99',
                'passwordConfirmation' => 'NewStrongPass!99',
            ])
            ->call('save')
            ->assertHasFormErrors(['currentPassword']);

        $this->assertSame($originalHash, $admin->refresh()->password);
    }
```

- [ ] **Step 2: Run the test and verify it passes**

Run: `php artisan test --compact --filter=test_wrong_current_password_blocks_change`
Expected: 1 passed.

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/Filament/EditProfileTest.php
git commit -m "test(admin): wrong current password blocks change"
```

---

## Task 7: Test — weak new password rejected

**Files:**
- Modify: `tests/Feature/Filament/EditProfileTest.php`

- [ ] **Step 1: Add the failing test**

Append to the class body:

```php
    public function test_weak_new_password_blocks_change(): void
    {
        $this->seed(AdminUserSeeder::class);
        $admin = $this->adminUser();
        $admin->password = Hash::make('old-password-123');
        $admin->save();
        $originalHash = $admin->password;

        Livewire::actingAs($admin)
            ->test(EditProfile::class)
            ->fillForm([
                'currentPassword' => 'old-password-123',
                'password' => 'short',
                'passwordConfirmation' => 'short',
            ])
            ->call('save')
            ->assertHasFormErrors(['password']);

        $this->assertSame($originalHash, $admin->refresh()->password);
    }
```

- [ ] **Step 2: Run the test and verify it passes**

Run: `php artisan test --compact --filter=test_weak_new_password_blocks_change`
Expected: 1 passed.

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/Filament/EditProfileTest.php
git commit -m "test(admin): weak new password blocks change"
```

---

## Task 8: Test — mismatched confirmation rejected

**Files:**
- Modify: `tests/Feature/Filament/EditProfileTest.php`

- [ ] **Step 1: Add the failing test**

Append to the class body:

```php
    public function test_mismatched_confirmation_blocks_change(): void
    {
        $this->seed(AdminUserSeeder::class);
        $admin = $this->adminUser();
        $admin->password = Hash::make('old-password-123');
        $admin->save();
        $originalHash = $admin->password;

        Livewire::actingAs($admin)
            ->test(EditProfile::class)
            ->fillForm([
                'currentPassword' => 'old-password-123',
                'password' => 'NewStrongPass!99',
                'passwordConfirmation' => 'DifferentPass!99',
            ])
            ->call('save')
            ->assertHasFormErrors(['password']);

        $this->assertSame($originalHash, $admin->refresh()->password);
    }
```

- [ ] **Step 2: Run the test and verify it passes**

Run: `php artisan test --compact --filter=test_mismatched_confirmation_blocks_change`
Expected: 1 passed.

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/Filament/EditProfileTest.php
git commit -m "test(admin): mismatched confirmation blocks change"
```

---

## Task 9: Test — non-admin user blocked from profile page

**Files:**
- Modify: `tests/Feature/Filament/EditProfileTest.php`

**Background:** `User::canAccessPanel()` returns `false` for any user whose email doesn't match `config('auth.admin_email')`. Filament returns a 403 in that case.

- [ ] **Step 1: Add `UserFactory` import**

Verify the top of the file has `use App\Models\User;` (already added earlier). No new import needed — `User::factory()` is a static method on the model.

- [ ] **Step 2: Add the failing test**

Append to the class body:

```php
    public function test_non_admin_user_cannot_access_profile_page(): void
    {
        $nonAdmin = User::factory()->create([
            'email' => 'someone-else@example.test',
        ]);

        $response = $this->actingAs($nonAdmin)->get('/admin/profile');

        $response->assertStatus(403);
    }
```

- [ ] **Step 3: Run the test and verify it passes**

Run: `php artisan test --compact --filter=test_non_admin_user_cannot_access_profile_page`
Expected: 1 passed.

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/Filament/EditProfileTest.php
git commit -m "test(admin): non-admin blocked from profile page"
```

---

## Task 10: Run the full test file and the full suite

- [ ] **Step 1: Run the full EditProfile test file**

Run: `php artisan test --compact tests/Feature/Filament/EditProfileTest.php`
Expected: 7 passed (task 3 + tasks 4–9).

- [ ] **Step 2: Run the full test suite to catch any regression**

Run: `php artisan test --compact`
Expected: all tests pass — no prior test should be affected because we only added new files and one panel config line.

- [ ] **Step 3: If anything fails, stop and debug**

Do not proceed until the full suite is green.

---

## Task 11: Manual verification in the browser

- [ ] **Step 1: Log in at `https://deharmonie.test/admin/login`**

Use the current admin credentials.

- [ ] **Step 2: Confirm the user menu (top-right) now has a clickable "Admin" entry**

Clicking it should navigate to `https://deharmonie.test/admin/profile`.

- [ ] **Step 3: Confirm the form shows exactly three fields**

- Huidig wachtwoord (current password)
- Nieuw wachtwoord (new password)
- Bevestig nieuw wachtwoord (password confirmation)

No "Naam" or "E-mailadres" fields should appear.

- [ ] **Step 4: Change the password to a new strong value**

Enter current password + new password (matching confirmation). Submit. Expect a success notification.

- [ ] **Step 5: Log out and log in again with the new password**

Confirm login works with the new password.

- [ ] **Step 6: Restore a known password if needed**

If this is a shared environment, reset to whatever the team expects — or document the new password in the team's password manager.

---

## Done when

- All 7 feature tests pass.
- Full test suite is green.
- Manual verification in the browser confirms the user-menu link, the three-field form, and a successful password change with subsequent login using the new password.
