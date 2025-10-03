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
        Schema::create('auction_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_handler_id')->constrained('auction_handlers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('userauths')->onDelete('cascade');
            $table->decimal('bid_amount', 10, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->index('bid_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auction_bids');
    }
};
