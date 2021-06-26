<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('line_id')->unique();
            $table->string('name');
            $table->string('picture');
            $table->string('gender')->default('');
            $table->timestamp('is_open_chat')->nullable();

            $table->timestamps();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('a_line_id');
            $table->string('b_line_id');
            $table->timestamp('leave_at')->nullable();
            $table->integer('total_reminder')->default(0);

            $table->timestamps();
        });

        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->integer('room_id');
            $table->string('line_id');
            $table->string('reply_token');
            $table->string('message_type');
            $table->string('message');

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
        Schema::dropIfExists('users');
    }
}
