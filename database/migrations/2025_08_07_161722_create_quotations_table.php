<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->decimal('price_per_person', 10, 2);
            $table->string('currency')->default('USD');
            $table->text('notes')->nullable();
            $table->boolean('is_final')->default(false);
            $table->uuid('unique_id')->unique(); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotations');
    }
};