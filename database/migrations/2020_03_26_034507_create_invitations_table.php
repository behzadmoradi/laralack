<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->foreignId('channel_id')->constrained()->onDelete('cascade');
            $table->index(['channel_id']);
            $table->string('email', 100);
            $table->string('link', 255)->nullable();
            $table->integer('already_registered')->default(0);
            $table->integer('is_sent')->default(0);
            $table->integer('status');
            $table->index(['status']);
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
        Schema::dropIfExists('invitations');
    }
}
