<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            // invited team members and email-link customers have no password until they set one
            $table->string('password')->nullable();
            $table->enum('role', ['OWNER', 'ADMIN', 'STAFF', 'CUSTOMER'])->default('CUSTOMER');
            // TOTP secrets are stored encrypted with APP_KEY (see the User model's casts)
            $table->text('totp_secret')->nullable();
            $table->text('totp_pending_secret')->nullable();
            $table->boolean('totp_enabled')->default(false);
            $table->bigInteger('totp_last_step')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
