<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers_details', function (Blueprint $table) {
            $table->unsignedBigInteger('web_db_supplier_id');
            $table->unsignedBigInteger('web_db_category_id');
            $table->unsignedBigInteger('web_db_product_id');
            $table->unsignedBigInteger('mark_up');
            $table->unsignedBigInteger('expenses');
            $table->unsignedBigInteger('warrant_id')->nullable();
            $table->unsignedBigInteger('delivery_deadline_id')->nullable();

            $table->foreign('warrant_id')->references('id')->on('warrants');
            $table
                ->foreign('delivery_deadline_id')
                ->references('id')
                ->on('delivery_deadlines');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers_details');
    }
};
