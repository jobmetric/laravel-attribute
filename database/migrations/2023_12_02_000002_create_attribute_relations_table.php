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

            $table->boolean('is_coding')->default(false)->index();
            /**
             * note: If the coding is active, it can be used to make a variable product
             */

            $table->boolean('is_gallery')->default(false)->index();
            /**
             * note: If the gallery is active, it can be used to make gallery in product interface
             */

            $table->boolean('is_special')->default(false)->index();
            /**
             * note: If this option is active in the attribute table, it is active here by default and cannot be changed
             */

            $table->boolean('is_filter')->default(false)->index();
            /**
             * note: Its value is taken directly from the attribute table and is used only for better query execution
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
