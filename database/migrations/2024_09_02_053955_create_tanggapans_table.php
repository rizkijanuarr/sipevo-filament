<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('tanggapans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengaduan_id')->nullable()->constrained();
            $table->text('comment');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('tanggapans');
    }
};
