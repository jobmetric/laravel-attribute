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
        Schema::create(config('attribute.tables.attribute_value'), function (Blueprint $table) {
            $table->id();

            $table->foreignId('attribute_id')
                ->index()
                ->constrained(config('attribute.tables.attribute'))
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            /**
             * Parent attribute this value belongs to.
             *
             * - deleting the attribute removes all its values (CASCADE)
             */

            $table->integer('ordering')->default(0)->index();
            /**
             * Display / sort order among values of the same attribute.
             */

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('attribute.tables.attribute_value'));
    }
};
