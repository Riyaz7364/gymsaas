# Module Access Changes

## What Changed

- Added a central module registry in `config/gym-modules.php`.
- Added `App\Support\GymModuleRegistry` so module access is resolved from one place.
- Updated gym/user module checks to use:
  - assigned modules on the active subscription plan
  - fallback plan defaults from config
  - enabled add-on modules saved in gym settings
- Added a dedicated owner-facing modules page at `/modules`.
- Protected feature routes with `module:<key>` middleware so direct URLs are blocked too.
- Updated the main sidebar and dashboard to only show features the current gym actually has.

## Why This Fixes The Problem

Before this change, only a few premium endpoints were protected. Most menus were always visible, and many feature routes were accessible even when the gym should not have had that module.

Now the app uses the same module decision for:

- menu visibility
- dashboard visibility
- direct route access
- owner module overview

## New Module Structure

Each module is treated independently.

That means:

- `diet_management` controls diet-plan access
- `ai_diet_plans` only controls AI generation for diet plans
- `workout_management` controls workout features
- `ai_workout_plans` only controls AI workout generation
- `advanced_reports` controls reports
- `whatsapp_updates` controls WhatsApp tools

Even if one module reads shared gym/member/workout data, access is still enforced by that module's own key.

## Main Files Added

- `config/gym-modules.php`
- `app/Support/GymModuleRegistry.php`
- `app/Http/Controllers/ModuleController.php`
- `resources/views/modules/index.blade.php`

## Main Files Updated

- `app/Models/User.php`
- `app/Models/Gym.php`
- `app/Http/Middleware/CheckGymModule.php`
- `resources/views/components/layouts/app.blade.php`
- `resources/views/dashboard.blade.php`
- `routes/web.php`
- `database/seeders/ModuleSeeder.php`
- `database/seeders/DatabaseSeeder.php`

## New Owner Page

- URL: `/modules`

This page shows:

- every module as a separate card
- whether it is enabled or locked
- what the module does
- an entry link when the module has a dedicated page

## Plan Defaults

Default plan access is now defined in `config/gym-modules.php`.

This matters because older databases may not yet have every plan-to-module pivot row populated.

The app now safely falls back to config defaults when plan module pivots are missing.

## If You Want Pricing Management To Show The New Core Modules Too

Run:

```bash
php artisan db:seed --class=ModuleSeeder
php artisan db:seed --class=DatabaseSeeder
```

That will upsert the module catalog and sync default plan modules for seeded plans.

## Notes

- Super admins still bypass gym module restrictions.
- Gym owners, managers, and trainers now follow their gym's module access.
- Settings remains available as a common area, but feature sections should continue to respect module keys when real settings screens are added.
