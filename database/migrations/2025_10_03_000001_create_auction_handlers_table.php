<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('auction_handlers')) {
            Schema::create('auction_handlers', function (Blueprint $table) {
                $table->id();
                $table->string('lot_number', 30)->nullable()->unique();
                $table->foreignId('ad_id')->constrained('ads')->onDelete('cascade');
                $table->decimal('starting_price', 10, 2);
                $table->decimal('mini_bid_increment', 8, 2);
                $table->integer('auction_duration_hours');
                $table->decimal('current_highest_bid', 10, 2)->default(0.00);
                $table->timestamp('start_time')->nullable();
                $table->timestamp('end_time')->nullable();
                $table->enum('status', ['pending','approved','rejected','active','ended'])->default('pending');
                $table->foreignId('winner_user_id')->nullable()->constrained('userauths')->onDelete('set null');
                $table->timestamps();

                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auction_handlers');
    }
};
