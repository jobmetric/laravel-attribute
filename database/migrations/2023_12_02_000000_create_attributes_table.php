<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JobMetric\Attribute\Enums\AttributeTypeEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('attribute.tables.attribute'), function (Blueprint $table) {
            $table->id();

            $table->string('type');
            /**
             * value: radio, checkbox, select, color, card, image, input
             * use: @extends AttributeTypeEnum
             */

            $table->boolean('is_gallery')->default(false)->index();
            /**
             * note: If the gallery is active, a variable will be added to add the gallery to the product interface
             */

            $table->boolean('is_special')->default(false)->index();
            /**
             * note: If this option is enabled, the specifications will be displayed on the top of the page in a special way
             */

            $table->boolean('is_filter')->default(false)->index();
            /**
             * note: If this option is enabled, this attribute will be displayed as a filter in the product list
             */

            $table->integer('ordering')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('attribute.tables.attribute'));
    }
};
