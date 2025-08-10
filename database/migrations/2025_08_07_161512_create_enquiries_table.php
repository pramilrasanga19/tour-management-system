<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->date('travel_start_date');
            $table->date('travel_end_date');
            $table->integer('number_of_people');
            $table->json('preferred_destinations'); 
            $table->decimal('budget', 10, 2);
            $table->string('status')->default('pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('enquiries');
    }
};