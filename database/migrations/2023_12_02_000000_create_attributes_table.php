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
        Schema::create(config('attribute.tables.attribute'), function (Blueprint $table) {
            $table->id();

            $table->string('type')->index();
            /**
             * Registered attribute type key (slug).
             *
             * - must match a key in config attribute.types / AttributeTypeRegistry
             * - examples: radio, select, color, input
             * - validated in application layer (Rule::in(AttributeTypeRegistry::values()))
             */

            $table->boolean('is_special')->default(false)->index();
            /**
             * When true, this attribute is highlighted in the UI (e.g. top of specification blocks).
             *
             * - mirrored onto attribute_relations.is_special when linking to an entity
             */

            $table->boolean('is_filter')->default(false)->index();
            /**
             * When true, this attribute can be exposed as a filter (e.g. product list filters).
             *
             * - query/faceting behaviour is implemented outside this table
             */

            $table->integer('ordering')->default(0)->index();
            /**
             * Display / sort order among attributes.
             *
             * - lower values typically appear first; exact rules are enforced in the app
             */

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('attribute.tables.attribute'));
    }
};
