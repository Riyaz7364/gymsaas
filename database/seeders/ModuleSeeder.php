<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['key' => 'members_management', 'label' => 'Members', 'description' => 'Manage member profiles, plans, and assignments', 'group_name' => 'Core Operations', 'sort_order' => 1, 'price' => null, 'billing_type' => null, 'icon' => 'users'],
            ['key' => 'trainers_management', 'label' => 'Trainers', 'description' => 'Manage trainers, schedules, and trainer assignments', 'group_name' => 'Core Operations', 'sort_order' => 2, 'price' => null, 'billing_type' => null, 'icon' => 'trainer'],
            ['key' => 'membership_management', 'label' => 'Membership', 'description' => 'Manage gym membership plans and plan assignment flows', 'group_name' => 'Core Operations', 'sort_order' => 3, 'price' => null, 'billing_type' => null, 'icon' => 'membership'],
            ['key' => 'attendance_management', 'label' => 'Attendance', 'description' => 'Track member check-ins and check-outs', 'group_name' => 'Core Operations', 'sort_order' => 4, 'price' => null, 'billing_type' => null, 'icon' => 'attendance'],
            ['key' => 'workout_management', 'label' => 'Workouts', 'description' => 'Manage workout plans, sequences, activities, and logs', 'group_name' => 'Training', 'sort_order' => 5, 'price' => null, 'billing_type' => null, 'icon' => 'workout'],
            ['key' => 'diet_management', 'label' => 'Diet Plans', 'description' => 'Manage custom and default diet plans', 'group_name' => 'Training', 'sort_order' => 6, 'price' => null, 'billing_type' => null, 'icon' => 'diet'],
            ['key' => 'body_progress', 'label' => 'Body Progress', 'description' => 'Track body stats and progress photos', 'group_name' => 'Training', 'sort_order' => 7, 'price' => null, 'billing_type' => null, 'icon' => 'progress'],
            ['key' => 'finance_management', 'label' => 'Finance', 'description' => 'Manage invoices, expenses, and payments', 'group_name' => 'Business', 'sort_order' => 8, 'price' => null, 'billing_type' => null, 'icon' => 'finance'],
            ['key' => 'locker_management', 'label' => 'Lockers', 'description' => 'Manage locker inventory and assignments', 'group_name' => 'Business', 'sort_order' => 9, 'price' => null, 'billing_type' => null, 'icon' => 'locker'],
            ['key' => 'event_management', 'label' => 'Events', 'description' => 'Manage events, event types, and class calendar flows', 'group_name' => 'Engagement', 'sort_order' => 10, 'price' => null, 'billing_type' => null, 'icon' => 'event'],
            ['key' => 'notice_board', 'label' => 'Notice Board', 'description' => 'Manage notices and public updates for members', 'group_name' => 'Engagement', 'sort_order' => 11, 'price' => null, 'billing_type' => null, 'icon' => 'notice'],
            ['key' => 'contact_diary', 'label' => 'Contact Diary', 'description' => 'Track prospect contacts and follow-up records', 'group_name' => 'Engagement', 'sort_order' => 12, 'price' => null, 'billing_type' => null, 'icon' => 'contact'],
            ['key' => 'ai_diet_plans', 'label' => 'AI Diet Plans', 'description' => 'Auto-generate personalized diet plans using AI', 'group_name' => 'AI & Automation', 'sort_order' => 20, 'price' => null, 'billing_type' => null, 'icon' => 'ai-diet'],
            ['key' => 'ai_workout_plans', 'label' => 'AI Workout Plans', 'description' => 'Auto-generate personalized workout plans using AI', 'group_name' => 'AI & Automation', 'sort_order' => 21, 'price' => null, 'billing_type' => null, 'icon' => 'ai-workout'],
            ['key' => 'ai_body_analysis', 'label' => 'AI Body Progress', 'description' => 'AI-powered body composition and progress analysis', 'group_name' => 'AI & Automation', 'sort_order' => 22, 'price' => null, 'billing_type' => null, 'icon' => 'ai-body'],
            ['key' => 'whatsapp_updates', 'label' => 'WhatsApp Notifications', 'description' => 'Automated WhatsApp messages for renewals and updates', 'group_name' => 'Notifications', 'sort_order' => 23, 'price' => null, 'billing_type' => null, 'icon' => 'whatsapp'],
            ['key' => 'sms_notifications', 'label' => 'SMS Notifications', 'description' => 'SMS alerts for payments, renewals, and events', 'group_name' => 'Notifications', 'sort_order' => 24, 'price' => null, 'billing_type' => null, 'icon' => 'sms'],
            ['key' => 'email_marketing', 'label' => 'Email Marketing', 'description' => 'Send newsletters and promotions to members', 'group_name' => 'Notifications', 'sort_order' => 25, 'price' => null, 'billing_type' => null, 'icon' => 'email'],
            ['key' => 'inactive_reminders', 'label' => 'Inactive Reminders', 'description' => 'Auto-alert members who stop visiting', 'group_name' => 'Automation', 'sort_order' => 26, 'price' => null, 'billing_type' => null, 'icon' => 'inactive'],
            ['key' => 'birthday_automation', 'label' => 'Birthday Automation', 'description' => 'Auto birthday wishes with discount offers', 'group_name' => 'Automation', 'sort_order' => 27, 'price' => null, 'billing_type' => null, 'icon' => 'birthday'],
            ['key' => 'renewal_reminders', 'label' => 'Renewal Reminders', 'description' => 'Automated membership expiry reminders', 'group_name' => 'Automation', 'sort_order' => 28, 'price' => null, 'billing_type' => null, 'icon' => 'renewal'],
            ['key' => 'multi_branch', 'label' => 'Multi-Branch Manager', 'description' => 'Manage multiple gym locations under one account', 'group_name' => 'Business', 'sort_order' => 29, 'price' => null, 'billing_type' => null, 'icon' => 'branch'],
            ['key' => 'online_payments', 'label' => 'Online Payment Gateway', 'description' => 'Accept payments online with Razorpay or Stripe', 'group_name' => 'Business', 'sort_order' => 30, 'price' => null, 'billing_type' => null, 'icon' => 'online-payments'],
            ['key' => 'custom_branding', 'label' => 'Custom Branding', 'description' => 'White-label with your gym logo and colors', 'group_name' => 'Business', 'sort_order' => 31, 'price' => null, 'billing_type' => null, 'icon' => 'branding'],
            ['key' => 'advanced_reports', 'label' => 'Advanced Analytics', 'description' => 'In-depth revenue trends and custom report builder', 'group_name' => 'Advanced', 'sort_order' => 32, 'price' => null, 'billing_type' => null, 'icon' => 'reports'],
            ['key' => 'api_access', 'label' => 'API Access', 'description' => 'Developer API for third-party integrations', 'group_name' => 'Advanced', 'sort_order' => 33, 'price' => null, 'billing_type' => null, 'icon' => 'api'],
            ['key' => 'biometric_device', 'label' => 'Biometric Device Integration', 'description' => 'Connect fingerprint and face scanners for attendance', 'group_name' => 'Add-ons', 'sort_order' => 40, 'price' => 199, 'billing_type' => 'monthly', 'icon' => 'biometric'],
            ['key' => 'custom_website', 'label' => 'Custom Website', 'description' => 'Professional website built for your gym brand', 'group_name' => 'Add-ons', 'sort_order' => 41, 'price' => 9999, 'billing_type' => 'one_time', 'icon' => 'website'],
            ['key' => 'diet_workout_addon', 'label' => 'Diet and Workout Plans Add-on', 'description' => 'Advanced diet and workout features for all members', 'group_name' => 'Add-ons', 'sort_order' => 42, 'price' => 199, 'billing_type' => 'monthly', 'icon' => 'diet-workout'],
            ['key' => 'expense_tracking_addon', 'label' => 'Expense Tracking Add-on', 'description' => 'Track gym expenses, salaries, and profitability', 'group_name' => 'Add-ons', 'sort_order' => 43, 'price' => 99, 'billing_type' => 'monthly', 'icon' => 'expense'],
            ['key' => 'product_management', 'label' => 'Product Management', 'description' => 'Manage and sell gym products, supplements, and merchandise', 'group_name' => 'Add-ons', 'sort_order' => 44, 'price' => 199, 'billing_type' => 'monthly', 'icon' => 'products'],
            ['key' => 'upi_qr_collection', 'label' => 'UPI QR Collection', 'description' => 'Accept UPI payments with auto-generated QR codes', 'group_name' => 'Add-ons', 'sort_order' => 45, 'price' => 199, 'billing_type' => 'monthly', 'icon' => 'upi'],
        ];

        foreach ($modules as $mod) {
            Module::updateOrCreate(['key' => $mod['key']], $mod);
        }
    }
}
