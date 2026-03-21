<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('attribute.tables.attribute_relation'), function (Blueprint $table) {
            $table->id();

            $table->morphs('attributable');
            /**
             * relatable to:
             *
             * ProductInterface
             * Post
             */

            $table->foreignId('attribute_id')->index()->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->boolean('is_variant')->default(false)->index();
            /**
             * note: When active, the attribute can participate in variable / variant logic (e.g. variable product).
             */

            $table->boolean('is_special')->default(false)->index();
            /**
             * note: If this option is active in the attribute table, it is active here by default and cannot be changed
             */

            $table->dateTime('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('attribute.tables.attribute_relation'));
    }
};
