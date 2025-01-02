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
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('group_id')->constrained();
            $table->unsignedTinyInteger('score')->nullable();
            $table->string('folder');
            $table->string('cover')->nullable();
            $table->json('images')->nullable();
            $table->enum('visibility', ['public', 'private', 'group'])->default('private');
            $table->auditFields();
            $table->timestamps();

            $table->unique(['project_id', 'user_id', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};
