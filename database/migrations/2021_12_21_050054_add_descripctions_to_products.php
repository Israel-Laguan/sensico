<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescripctionsToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('technical_specifications');
            $table->text('links');
            $table->text('related_information');
            $table->text('classification');
            $table->text('properties');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('technical_specifications');
            $table->dropColumn('links');
            $table->dropColumn('related_information');
            $table->dropColumn('classification');
            $table->dropColumn('properties');
        });
    }
}
