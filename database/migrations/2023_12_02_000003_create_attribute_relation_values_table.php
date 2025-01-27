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
        Schema::create(config('attribute.tables.attribute_relation_value'), function (Blueprint $table) {
            $table->foreignId('attribute_relation_id')->index()->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreignId('attribute_value_id')->index()->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->unique([
                'attribute_relation_id',
                'attribute_value_id',
            ], 'ATTRIBUTE_RELATION_VALUE_UNIQUE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('attribute.tables.attribute_relation_value'));
    }
};
