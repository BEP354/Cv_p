<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['buy', 'sell', 'convert']);
            $table->enum('from_currency', ['paypal', 'skrill', 'usd']);
            $table->enum('to_currency', ['paypal', 'skrill', 'usd']);
            $table->decimal('amount', 10, 2);
            $table->decimal('rate', 8, 4);
            $table->decimal('fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pending', 'completed', 'cancelled', 'failed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
