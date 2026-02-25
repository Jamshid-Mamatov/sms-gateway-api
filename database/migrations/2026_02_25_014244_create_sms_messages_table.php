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
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string("phone",13);
            $table->text("message");
            $table->enum("status",['pending','sent','deliverd','failed'])->default('pending');
            $table->string("provider_response")->nullable();
            $table->string("provider_message_id")->nullable();
            $table->timestamp("sent_at")->nullable();
            $table->timestamps();
            $table->index(['project_id','status']);
            $table->index(['project_id','phone']);
            $table->index(['project_id','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
