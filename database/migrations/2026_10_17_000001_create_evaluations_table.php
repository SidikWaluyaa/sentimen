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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('judul_dataset');
            $table->string('file_name')->nullable();
            $table->integer('total_ulasan')->default(0);
            $table->integer('total_positif')->default(0);
            $table->integer('total_negatif')->default(0);
            $table->float('akurasi')->default(0);
            $table->float('presisi')->default(0);
            $table->float('recall')->default(0);
            $table->float('f1_score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
