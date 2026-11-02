<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 30);
            $table->string('institution');
            $table->string('course');
            $table->string('year_of_study');
            $table->string('startup_name');
            $table->string('sector');
            $table->string('stage');
            $table->text('problem');
            $table->text('solution');
            $table->string('team_size');
            $table->string('link')->nullable();
            $table->json('mentor_areas');
            $table->text('goal');
            $table->string('commitment');
            $table->string('heard_about')->nullable();
            $table->boolean('consent')->default(false);
            $table->string('status')->default('new'); // new | reviewing | accepted | declined
            $table->timestamps();

            $table->index(['email', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
