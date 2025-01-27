<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('attribute.tables.gallery_variation_attribute_value'), function (Blueprint $table) {
            $table->foreignId('gallery_variation_id')->index()->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreignId('attribute_relation_id')->index()->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreignId('attribute_value_id')->index()->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->unique([
                'gallery_variation_id',
                'attribute_relation_id'
            ], 'GALLERY_VARIATION_ATTRIBUTE_RELATION_UNIQUE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('attribute.tables.gallery_variation_attribute_value'));
    }
};
