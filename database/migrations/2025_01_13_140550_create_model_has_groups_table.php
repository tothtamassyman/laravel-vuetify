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
        Schema::create('model_has_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id');
            $table->morphs('model'); // Ez létrehozza a 'model_id' és 'model_type' oszlopokat

            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
                ->onDelete('cascade');

            $table->primary(['group_id', 'model_id', 'model_type'], 'mhg_uk_idx');

            $table->index('model_id');
            $table->index('model_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_has_groups');
    }
};
