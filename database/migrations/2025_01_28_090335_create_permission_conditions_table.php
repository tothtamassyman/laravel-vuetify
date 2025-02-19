<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permission_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->index();
            $table->string('operator');
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('permission_condition_pivot', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->foreignId('condition_id')->constrained('permission_conditions')->onDelete('cascade');
            $table->timestamps();

            $table->primary(['permission_id', 'condition_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_condition_pivot');
        Schema::dropIfExists('permission_conditions');
    }
};
