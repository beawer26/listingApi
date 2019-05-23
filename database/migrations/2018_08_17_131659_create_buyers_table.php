<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuyersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buyers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->integer('min_price');
            $table->integer('max_price');
            $table->integer('beds_min');
            $table->integer('beds_max');
            $table->integer('baths');
            $table->string('property_type');
            $table->string('status')->nullable();
            $table->string('time_on_redfin')->nullable();
            $table->integer('open_houses')->nullable();
            $table->integer('price_reduced')->nullable();
            $table->integer('exclude_short_sales')->nullable();
            $table->integer('fixer_uppers_only')->nullable();
            $table->integer('mls_listings')->nullable();
            $table->integer('new_construction')->nullable();
            $table->integer('agent_listed_homes')->nullable();
            $table->integer('mls_listed_foreclosures')->nullable();
            $table->integer('for_sale_by_owner')->nullable();
            $table->integer('foreclosures')->nullable();
            $table->string('square_feet_min');
            $table->string('square_feet_max');
            $table->string('lot_size_min');
            $table->string('lot_size_max');
            $table->string('year_built_min');
            $table->string('year_built_max');
            $table->string('max_hoa_fees');
            $table->string('parking_spaces');
            $table->integer('waterfront_only')->nullable();
            $table->integer('must_have_pool')->nullable();
            $table->integer('accessible_homes_only')->nullable();
            $table->integer('green_homes_only')->nullable();
            $table->integer('must_have_garage')->nullable();
            $table->integer('must_have_view')->nullable();
            $table->integer('single_story_only')->nullable();
            $table->integer('must_have_basement')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buyers');
    }
}
