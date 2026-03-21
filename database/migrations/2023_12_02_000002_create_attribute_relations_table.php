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
        Schema::create(config('attribute.tables.attribute_relation'), function (Blueprint $table) {
            $table->id();

            $table->morphs('attributable');
            /**
             * Entity this attribute is attached to (polymorphic parent).
             *
             * - attributable_type: fully qualified model class name
             * - attributable_id: primary key of that model
             * - examples: product interfaces, posts, or any model that uses the attribute system
             */

            $table->foreignId('attribute_id')
                ->index()
                ->constrained(config('attribute.tables.attribute'))
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            /**
             * Which attribute definition is linked to the attributable entity.
             */

            $table->boolean('is_variant')->default(false)->index();
            /**
             * When true, this link participates in variant / variable logic (e.g. variable products).
             *
             * - business rules live in the domain layer; this flag is for storage and queries
             */

            $table->boolean('is_special')->default(false)->index();
            /**
             * Mirrors the attribute’s special flag for this relation.
             *
             * - typically copied from attributes.is_special when the relation is created
             * - kept on the relation for faster reads without joining attributes
             */

            $table->timestamp('created_at')->nullable()->useCurrent();
            /**
             * When the relation row was created (no updated_at on this model).
             *
             * - useCurrent() keeps behaviour portable across drivers (avoids raw SQL)
             */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('attribute.tables.attribute_relation'));
    }
};
