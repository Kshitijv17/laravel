<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->enum('status', ['waiting', 'active', 'closed', 'resolved'])->default('waiting');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->string('subject')->nullable();
            $table->string('department')->default('general');
            $table->timestamp('last_message_at')->nullable();
            $table->integer('rating')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('agent_id')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('closed_by')->references('id')->on('admins')->onDelete('set null');

            $table->index(['status', 'priority']);
            $table->index(['user_id', 'status']);
            $table->index(['agent_id', 'status']);
            $table->index('department');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_rooms');
    }
};
