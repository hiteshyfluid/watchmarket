<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->text('confirmation_message')->nullable();

            $table->decimal('initial_payment', 10, 2)->default(0);

            $table->boolean('has_recurring')->default(false);
            $table->decimal('billing_amount', 10, 2)->nullable();
            $table->unsignedInteger('billing_every')->nullable();
            $table->enum('billing_period', ['day', 'week', 'month', 'year'])->nullable();
            $table->unsignedInteger('billing_cycle_limit')->default(0);

            $table->boolean('has_trial')->default(false);
            $table->decimal('trial_amount', 10, 2)->default(0);
            $table->unsignedInteger('trial_cycles')->default(0);

            $table->boolean('has_expiration')->default(false);
            $table->unsignedInteger('expiration_number')->nullable();
            $table->enum('expiration_unit', ['day', 'week', 'month', 'year'])->nullable();

            $table->boolean('allow_signups')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('membership_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('membership_level_id')->constrained('membership_levels')->cascadeOnDelete();
            $table->string('status', 30)->default('active');
            $table->string('fee_label', 120)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('billing_name', 150)->nullable();
            $table->text('billing_address')->nullable();
            $table->string('billing_phone', 50)->nullable();
            $table->timestamps();

            $table->index(['membership_level_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('membership_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('membership_level_id')->nullable()->constrained('membership_levels')->nullOnDelete();
            $table->foreignId('membership_subscription_id')->nullable()->constrained('membership_subscriptions')->nullOnDelete();
            $table->decimal('total', 10, 2)->default(0);
            $table->text('billing_details')->nullable();
            $table->string('gateway', 80)->nullable();
            $table->string('payment_transaction_id', 120)->nullable();
            $table->string('subscription_transaction_id', 120)->nullable();
            $table->string('status', 30)->default('paid');
            $table->timestamp('ordered_at')->nullable();
            $table->timestamps();

            $table->index(['membership_level_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_orders');
        Schema::dropIfExists('membership_subscriptions');
        Schema::dropIfExists('membership_levels');
    }
};

