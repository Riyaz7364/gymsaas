<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Gym;
use App\Models\GymSubscription;
use App\Models\Module;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ModuleSeeder::class);

        // Create roles
        $roles = ['super_admin', 'gym_owner', 'manager', 'trainer'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // ── Super Admin ────────────────────────────────────────────────
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@gymhub.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'status'   => 'active',
                'gym_id'   => null,
            ]
        );
        $superAdmin->assignRole('super_admin');

        // ── Subscription Plans (SaaS pricing) ─────────────────────────
        $plans = [
            [
                'name'         => 'basic',
                'display_name' => 'Basic',
                'monthly_price'=> 2999,
                'annual_price' => 29990,
                'max_members'  => 150,
                'max_trainers' => 5,
                'max_classes'  => 10,
                'features'     => ['Member management', 'Attendance tracking', 'Basic reports', 'Email support'],
                'is_active'    => true,
                'sort_order'   => 1,
            ],
            [
                'name'         => 'pro',
                'display_name' => 'Pro',
                'monthly_price'=> 4999,
                'annual_price' => 49990,
                'max_members'  => 500,
                'max_trainers' => 20,
                'max_classes'  => 50,
                'features'     => ['Everything in Basic', 'Diet plans', 'Workout plans', 'WhatsApp campaigns', 'Advanced reports', 'Priority support'],
                'is_active'    => true,
                'sort_order'   => 2,
            ],
            [
                'name'         => 'enterprise',
                'display_name' => 'Enterprise',
                'monthly_price'=> 9999,
                'annual_price' => 99990,
                'max_members'  => -1,  // unlimited
                'max_trainers' => -1,
                'max_classes'  => -1,
                'features'     => ['Everything in Pro', 'Unlimited members', 'Custom branding', 'API access', 'Dedicated support', 'SLA guarantee'],
                'is_active'    => true,
                'sort_order'   => 3,
            ],
        ];

        foreach ($plans as $planData) {
            $plan = SubscriptionPlan::firstOrCreate(['name' => $planData['name']], $planData);
            $defaultModuleKeys = config('gym-modules.plan_defaults.' . $planData['name'], []);

            if ($defaultModuleKeys !== []) {
                $moduleIds = Module::whereIn('key', $defaultModuleKeys)->pluck('id')->all();
                $plan->modules()->syncWithoutDetaching($moduleIds);
            }
        }

        // ── Demo Gym ───────────────────────────────────────────────────
        $gym = Gym::firstOrCreate(
            ['slug' => 'demo-gym'],
            [
                'name'              => 'Demo Gym',
                'slug'              => 'demo-gym',
                'subscription_plan' => 'pro',
                'status'            => 'active',
                'currency'          => 'INR',
                'timezone'          => 'Asia/Kolkata',
                'country'           => 'India',
            ]
        );

        // ── Gym Owner ──────────────────────────────────────────────────
        $owner = User::firstOrCreate(
            ['email' => 'owner@demogym.com'],
            [
                'name'     => 'Gym Owner',
                'password' => Hash::make('password'),
                'gym_id'   => $gym->id,
                'status'   => 'active',
            ]
        );
        $owner->assignRole('gym_owner');

        // Link owner to gym
        $gym->update(['owner_id' => $owner->id]);

        // ── Demo gym subscription ──────────────────────────────────────
        $proPlan = SubscriptionPlan::where('name', 'pro')->first();
        if ($proPlan) {
            GymSubscription::firstOrCreate(
                ['gym_id' => $gym->id, 'plan_id' => $proPlan->id],
                [
                    'status'        => 'active',
                    'billing_cycle' => 'monthly',
                    'amount'        => $proPlan->monthly_price,
                    'started_at'    => now()->subMonths(3),
                    'expires_at'    => now()->addMonth(),
                ]
            );
        }

        // ── Food Categories and Items for Demo Gym ──────────────────────
        $this->call(FoodCategorySeeder::class);
        $this->call(FoodItemSeeder::class);
    }
}

