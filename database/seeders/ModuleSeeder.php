<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            // ── Plan-included features (price = null = free/included) ──
            // AI & Automation
            ['key' => 'ai_diet_plans',         'label' => 'AI Diet Plans',            'description' => 'Auto-generate personalized diet plans using AI',          'group_name' => 'AI & Automation', 'sort_order' => 1,  'price' => null, 'billing_type' => null, 'icon' => '🤖'],
            ['key' => 'ai_workout_plans',      'label' => 'AI Workout Plans',         'description' => 'Auto-generate personalized workout plans using AI',       'group_name' => 'AI & Automation', 'sort_order' => 2,  'price' => null, 'billing_type' => null, 'icon' => '🏋️'],
            ['key' => 'ai_body_analysis',      'label' => 'AI Body Progress',         'description' => 'AI-powered body composition & progress analysis',         'group_name' => 'AI & Automation', 'sort_order' => 3,  'price' => null, 'billing_type' => null, 'icon' => '📊'],
            // Notifications
            ['key' => 'whatsapp_updates',      'label' => 'WhatsApp Notifications',   'description' => 'Automated WhatsApp messages for renewals & updates',      'group_name' => 'Notifications',   'sort_order' => 4,  'price' => null, 'billing_type' => null, 'icon' => '💬'],
            ['key' => 'sms_notifications',     'label' => 'SMS Notifications',        'description' => 'SMS alerts for payments, renewals & events',              'group_name' => 'Notifications',   'sort_order' => 5,  'price' => null, 'billing_type' => null, 'icon' => '📱'],
            ['key' => 'email_marketing',       'label' => 'Email Marketing',          'description' => 'Send newsletters & promotions to members',                'group_name' => 'Notifications',   'sort_order' => 6,  'price' => null, 'billing_type' => null, 'icon' => '📧'],
            // Automation
            ['key' => 'inactive_reminders',    'label' => 'Inactive Reminders',       'description' => 'Auto-alert members who stop visiting',                   'group_name' => 'Automation',      'sort_order' => 7,  'price' => null, 'billing_type' => null, 'icon' => '⏰'],
            ['key' => 'birthday_automation',   'label' => 'Birthday Automation',      'description' => 'Auto birthday wishes with discount offers',              'group_name' => 'Automation',      'sort_order' => 8,  'price' => null, 'billing_type' => null, 'icon' => '🎂'],
            ['key' => 'renewal_reminders',     'label' => 'Renewal Reminders',        'description' => 'Automated membership expiry reminders',                  'group_name' => 'Automation',      'sort_order' => 9,  'price' => null, 'billing_type' => null, 'icon' => '🔔'],
            // Business
            ['key' => 'multi_branch',          'label' => 'Multi-Branch Manager',     'description' => 'Manage multiple gym locations under one account',         'group_name' => 'Business',        'sort_order' => 10, 'price' => null, 'billing_type' => null, 'icon' => '🏢'],
            ['key' => 'online_payments',       'label' => 'Online Payment Gateway',   'description' => 'Accept payments online (Razorpay / Stripe)',             'group_name' => 'Business',        'sort_order' => 11, 'price' => null, 'billing_type' => null, 'icon' => '💳'],
            ['key' => 'custom_branding',       'label' => 'Custom Branding',          'description' => 'White-label with your gym logo & colors',                'group_name' => 'Business',        'sort_order' => 12, 'price' => null, 'billing_type' => null, 'icon' => '🎨'],
            // Advanced
            ['key' => 'advanced_reports',      'label' => 'Advanced Analytics',       'description' => 'In-depth revenue trends & custom report builder',        'group_name' => 'Advanced',        'sort_order' => 13, 'price' => null, 'billing_type' => null, 'icon' => '📈'],
            ['key' => 'api_access',            'label' => 'API Access',               'description' => 'Developer API for third-party integrations',             'group_name' => 'Advanced',        'sort_order' => 14, 'price' => null, 'billing_type' => null, 'icon' => '🔌'],

            // ── Paid Add-ons (price > 0) ───────────────────────────────
            ['key' => 'biometric_device',      'label' => 'Biometric Device Integration', 'description' => 'Connect fingerprint & face scanners for attendance', 'group_name' => 'Add-ons', 'sort_order' => 20, 'price' => 199,   'billing_type' => 'monthly',  'icon' => '👆'],
            ['key' => 'custom_website',        'label' => 'Custom Website',           'description' => 'Professional website built for your gym brand',           'group_name' => 'Add-ons', 'sort_order' => 21, 'price' => 9999,  'billing_type' => 'one_time', 'icon' => '🌐'],
            ['key' => 'diet_workout_addon',    'label' => 'Diet & Workout Plans Add-on','description' => 'Advanced diet & workout features for all members',     'group_name' => 'Add-ons', 'sort_order' => 22, 'price' => 199,   'billing_type' => 'monthly',  'icon' => '🥗'],
            ['key' => 'expense_tracking_addon','label' => 'Expense Tracking Add-on',  'description' => 'Track gym expenses, salaries & profitability',           'group_name' => 'Add-ons', 'sort_order' => 23, 'price' => 99,    'billing_type' => 'monthly',  'icon' => '💰'],
            ['key' => 'product_management',    'label' => 'Product Management',       'description' => 'Manage and sell gym products, supplements & merchandise', 'group_name' => 'Add-ons', 'sort_order' => 24, 'price' => 199,   'billing_type' => 'monthly',  'icon' => '📦'],
            ['key' => 'upi_qr_collection',     'label' => 'UPI QR Collection',        'description' => 'Accept UPI payments with auto-generated QR codes',       'group_name' => 'Add-ons', 'sort_order' => 25, 'price' => 199,   'billing_type' => 'monthly',  'icon' => '📲'],
        ];

        foreach ($modules as $mod) {
            Module::firstOrCreate(['key' => $mod['key']], $mod);
        }
    }
}
