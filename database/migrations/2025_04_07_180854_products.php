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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('ClientName');
            $table->string('ProductName');
            $table->string('ProductPrice');
            $table->integer('Store');
            $table->enum('Status', ['Yes', 'No'])->default('Yes');
            $table->integer('AddedBy')->nullable();
            $table->integer('UpdatedBy')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
