<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('notes')->nullable();
            $table->json('days'); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('itineraries');
    }
};