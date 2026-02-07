<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('watermark_settings', function (Blueprint $table) {
            $table->id();
            $table->string('text')->default('ArimbiStore');
            $table->string('font_family', 50)->default('Arial');
            $table->integer('font_size')->default(40);
            $table->enum('font_size_unit', ['px', 'percent'])->default('px');
            $table->enum('position', [
                'center',
                'top-left',
                'top-right',
                'bottom-left',
                'bottom-right',
                'top-center',
                'bottom-center'
            ])->default('center');
            $table->integer('opacity')->default(40);
            $table->string('color', 7)->default('#FFFFFF');
            $table->boolean('shadow')->default(true);
            $table->integer('angle')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watermark_settings');
    }
};
