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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoiceno')->nullable();
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->bigInteger('vendor_id')->unsigned()->nullable();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->string('date')->nullable();
            $table->string('purchase_type')->nullable();
            $table->string('ref')->nullable();
            $table->string('vat_reg')->nullable();
            $table->longText('remarks')->nullable();
            $table->double('total_amount',10,2)->nullable();
            $table->double('discount',10,2)->nullable();
            $table->double('total_vat_amount',10,2)->nullable();
            $table->double('net_amount',10,2)->nullable();
            $table->double('paid_amount',10,2)->nullable();
            $table->double('due_amount',10,2)->nullable();
            $table->string('exp_date')->nullable();
            $table->boolean('status')->default(1);
            $table->string('updated_by')->nullable();
            $table->string('created_by')->nullable();
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
        Schema::dropIfExists('purchases');
    }
};
