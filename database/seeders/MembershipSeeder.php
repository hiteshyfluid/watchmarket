<?php

namespace Database\Seeders;

use App\Models\MembershipLevel;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'name' => 'Package 1',
                'seller_type' => 'both',
                'description' => 'The first 3 months are free, you will then be charged £49.99 per month thereafter.',
                'initial_payment' => 0,
                'has_recurring' => true,
                'billing_amount' => 49.99,
                'billing_every' => 1,
                'billing_period' => 'month',
                'billing_cycle_limit' => 0,
                'has_trial' => true,
                'trial_amount' => 0,
                'trial_cycles' => 3,
                'allow_signups' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Copper 4 weeks [ £0 - £999 ]',
                'seller_type' => 'private_seller',
                'description' => 'FREE',
                'initial_payment' => 0,
                'has_recurring' => false,
                'has_expiration' => true,
                'expiration_number' => 4,
                'expiration_unit' => 'week',
                'allow_signups' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Bronze 6 Weeks [ £0 - £999 ]',
                'seller_type' => 'private_seller',
                'description' => 'The first 3 months are free, you will then be charged £9.99 per month thereafter.',
                'initial_payment' => 0,
                'has_recurring' => true,
                'billing_amount' => 9.99,
                'billing_every' => 1,
                'billing_period' => 'month',
                'has_trial' => true,
                'trial_amount' => 0,
                'trial_cycles' => 3,
                'has_expiration' => true,
                'expiration_number' => 6,
                'expiration_unit' => 'week',
                'allow_signups' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Silver 10 Week [ £0 - £999 ]',
                'seller_type' => 'trade_seller',
                'description' => 'The first 3 months are free, you will then be charged £14.99 per month thereafter.',
                'initial_payment' => 0,
                'has_recurring' => true,
                'billing_amount' => 14.99,
                'billing_every' => 1,
                'billing_period' => 'month',
                'has_trial' => true,
                'trial_amount' => 0,
                'trial_cycles' => 3,
                'has_expiration' => true,
                'expiration_number' => 10,
                'expiration_unit' => 'week',
                'allow_signups' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Gold [ £0 - £999 ]',
                'seller_type' => 'trade_seller',
                'description' => 'Your listing will remain active for a maximum of 12 months. The price for your package is £19.99.',
                'initial_payment' => 19.99,
                'has_recurring' => false,
                'has_expiration' => true,
                'expiration_number' => 1,
                'expiration_unit' => 'year',
                'allow_signups' => true,
                'is_active' => true,
            ],
        ];

        foreach ($levels as $levelData) {
            MembershipLevel::updateOrCreate(
                ['name' => $levelData['name']],
                $levelData
            );
        }

        // Do not seed membership subscriptions/orders.
        // These should appear only after real customer purchases.
    }
}
