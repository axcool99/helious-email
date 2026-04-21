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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_type', 32)->index();
            $table->string('company_name');
            $table->text('email');
            $table->string('email_hash', 64)->index();
            $table->date('incorporation_date');
            $table->unsignedTinyInteger('reminder_month');
            $table->unsignedTinyInteger('reminder_day');
            $table->date('next_reminder_on')->index();
            $table->date('last_reminder_sent_on')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
