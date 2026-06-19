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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('subdomain')->unique();

            $table->string('db_host');
            $table->integer('db_port')->default(3306);
            $table->string('db_name')->unique();
            $table->string('db_username');
            $table->text('db_password'); // Store encrypted

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->index('subdomain');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
