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
        Schema::create('group_project', function (Blueprint $table) {
            $table->foreignId('group_id')->constrained();
            $table->foreignId('project_id')->constrained();
            $table->timestamp('started_at');
            $table->timestamp('finished_at');

            $table->primary(['group_id', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_project');
    }
};
