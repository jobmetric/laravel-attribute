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
        Schema::create(config('attribute.tables.attribute_relation_value'), function (Blueprint $table) {
            // Pivot: selected attribute values per attribute_relation. No id column — the
            // (attribute_relation_id, attribute_value_id) pair is the logical key; uniqueness below.

            $table->foreignId('attribute_relation_id')
                ->index()
                ->constrained(config('attribute.tables.attribute_relation'))
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('attribute_value_id')
                ->index()
                ->constrained(config('attribute.tables.attribute_value'))
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            /**
             * Must reference a value that belongs to the same attribute as attribute_relation.attribute_id;
             * enforced in application layer when attaching values.
             */

            $table->unique([
                    'attribute_relation_id',
                    'attribute_value_id',
                ], 'ATTRIBUTE_RELATION_VALUE_UNIQUE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('attribute.tables.attribute_relation_value'));
    }
};
