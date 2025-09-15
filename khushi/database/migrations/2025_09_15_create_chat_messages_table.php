<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_room_id');
            $table->unsignedBigInteger('sender_id');
            $table->enum('sender_type', ['user', 'admin', 'system'])->default('user');
            $table->text('message');
            $table->enum('message_type', ['text', 'image', 'file', 'system', 'emoji'])->default('text');
            $table->string('attachment_url')->nullable();
            $table->string('attachment_type')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('chat_room_id')->references('id')->on('chat_rooms')->onDelete('cascade');

            $table->index(['chat_room_id', 'created_at']);
            $table->index(['sender_id', 'sender_type']);
            $table->index('is_read');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
};
