<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorAchievementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctor_achievements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("ach_title");
            $table->text("ach_content");
            $table->text("ach_location");
            $table->datetime("ach_date");

            // for doctors
            $table->bigInteger("doctor_id")->unsigned();
            $table->timestamps();

            $table->foreign("doctor_id")->references("id")->on("doctors")->onDelete("cascade")->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctor_achievements');
    }
}
