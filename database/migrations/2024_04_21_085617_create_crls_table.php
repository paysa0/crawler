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
        Schema::create('crls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('EPS')->nullable();
            $table->string('P/E')->nullable();
            $table->string('P/EG')->nullable();
            $table->string('P/S')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crls');
    }
};
