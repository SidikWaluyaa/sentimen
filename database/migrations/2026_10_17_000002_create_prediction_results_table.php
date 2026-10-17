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
        Schema::create('prediction_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->string('id_review')->nullable()->index();
            $table->string('nama_pengguna')->nullable();
            $table->text('ulasan_asli');
            $table->text('ulasan_bersih');
            $table->integer('rating')->nullable();
            $table->string('sentimen_aktual', 30)->nullable();
            $table->string('sentimen_prediksi', 30);
            $table->boolean('is_correct')->default(true);
            $table->boolean('relevan_shopeefood')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prediction_results');
    }
};
