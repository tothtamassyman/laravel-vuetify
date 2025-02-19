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
        Schema::create('permission_fields', function (Blueprint $table) {
            $table->id();
            $table->string('field')->unique();
            $table->timestamps();
        });

        Schema::create('permission_field_pivot', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->foreignId('field_id')->constrained('permission_fields')->onDelete('cascade');
            $table->timestamps();

            $table->primary(['permission_id', 'field_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_field_pivot');
        Schema::dropIfExists('permission_fields');
    }
};
