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
        Schema::create('power_setups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g. "Summer Setup"
            $table->decimal('system_voltage', 5, 2)->default(12);
            $table->decimal('inverter_efficiency', 5, 2)->default(85);
            $table->enum('battery_type', ['lead', 'lithium'])->default('lead');
            $table->integer('autonomy_days')->default(2);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_setups');
    }
};
