<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conversion_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 50)->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('from_currency', ['paypal', 'skrill']);
            $table->decimal('from_amount', 10, 2);
            $table->foreignId('to_method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('fee_percentage', 5, 4)->default(0);
            $table->decimal('fee_amount', 15, 2)->default(0);
            $table->decimal('admin_fee', 15, 2)->default(0);
            $table->decimal('gross_idr', 15, 2)->default(0); // Add default value
            $table->decimal('total_idr', 15, 2)->default(0);
            
            // Recipient details
            $table->string('recipient_name');
            $table->string('recipient_account', 100);
            $table->string('recipient_email')->nullable();
            
            // Payment details
            $table->string('sender_email');
            $table->text('payment_proof')->nullable();
            
            // Status and notes
            $table->enum('status', ['pending', 'paid', 'processing', 'completed', 'cancelled', 'failed', 'refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Processing info
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('order_code');
            $table->index('from_currency');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversion_orders');
    }
};
