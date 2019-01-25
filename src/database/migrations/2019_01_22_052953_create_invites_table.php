<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('laravelinvites.table'), function(Blueprint $table) {
            $table->increments('id');
            $table->string('email')->nullable()->unique();
            $table->string('code')->unique();
            $table->integer('allowed_count')->default(0);
            $table->dateTime('valid_upto')->nullable();
            $table->dateTime('valid_from')->nullable();
            $table->integer('used_count')->default(0);
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
        Schema::dropIfExists(config('laravelinvites.table'));
    }
}
