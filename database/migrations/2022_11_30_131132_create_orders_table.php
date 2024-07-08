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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoiceno')->nullable();
            $table->string('salestype')->nullable();
            $table->string('orderdate')->nullable();
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->string('qn_no')->nullable();
            $table->string('dn_no')->nullable();
            $table->double('discount_amount',8,2)->nullable();
            $table->string('ref')->nullable();
            $table->integer('vatpercentage')->nullable();
            $table->double('vatamount',10,2)->nullable();
            $table->integer('offerpercentage')->nullable();
            $table->double('offeramount')->nullable();
            $table->float('pointpercentage')->nullable();
            $table->float('point')->nullable();
            $table->double('grand_total',10,2)->nullable();
            $table->double('customer_paid',10,2)->nullable();
            $table->double('net_total',10,2)->nullable();
            $table->double('due',10,2)->nullable();
            $table->double('due_omitted',10,2)->nullable();
            $table->double('return_amount',10,2)->nullable();
            $table->boolean('condition_sale')->nullable();
            $table->string('due_date')->nullable();
            $table->boolean('partnoshow')->nullable();
            $table->boolean('quotation')->default("0")->nullable();
            $table->boolean('delivery_note')->default("0")->nullable();
            $table->boolean('sales_status')->default("0")->nullable();
            $table->boolean('status')->default("1");
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
