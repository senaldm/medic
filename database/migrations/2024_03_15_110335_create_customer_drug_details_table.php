<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_drug_details', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->string('customer_name');
            $table->string('drug_no');
            $table->string('drug_name');
            $table->integer('quantity');
            $table->date('purchase_date')->now();
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
        Schema::dropIfExists('customer_drug_details');
    }
};
