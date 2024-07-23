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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('headerType')->default(1);
            $table->integer('footerType')->default(1);
            $table->string('title');
            $table->string('headerBgColor');
            $table->string('headerTextColor');
            $table->string('footer');
            $table->string('footerBgColor');
            $table->string('footerTextColor');
            $table->string('avaPath');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template');
    }
};
