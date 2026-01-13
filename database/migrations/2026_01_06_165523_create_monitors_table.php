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
        Schema::create('monitors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->enum('type', ['http', 'ping', 'port'])->default('http');
            $table->integer('interval')->default(3); // সেকেন্ডে
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_checked_at')->nullable();
            $table->boolean('last_status')->nullable(); // true = UP, false = DOWN
            $table->timestamp('last_up_at')->nullable();
            $table->timestamp('last_down_at')->nullable();
            $table->decimal('uptime_percentage', 5, 2)->default(100);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
