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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users', 'id');
            $table->string('cover');
            $table->string('title');
            $table->text('description');
            $table->text('goals');
            $table->longText('activities');
            $table->longText('conditions');
            $table->timestamp('started_at');
            $table->timestamp('finished_at');
            $table->enum('status', ['active', 'archived']);
            $table->auditFields();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
