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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users', 'id');
            $table->integer('year');
            $table->string('cycle', 1);
            $table->string('period')->virtualAs('CONCAT(year, cycle)');
            $table->string('title');
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->auditFields();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
