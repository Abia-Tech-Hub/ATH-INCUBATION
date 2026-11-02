<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_tokens', function (Blueprint $table) {
            // SHA-256 of the emailed token; the raw token never touches the database
            $table->string('token_hash')->primary();
            $table->string('purpose'); // "signin" | "invite"
            $table->string('email')->index();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_tokens');
    }
};
