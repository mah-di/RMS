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
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('residence_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('apartment_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('occupant_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('service_charge_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('type', ['Rent', 'Service Charge', 'Other']);
            $table->string('reference')->nullable();
            $table->date('rcv_date')->nullable();
            $table->string('for_month_year', 7);
            $table->unsignedInteger('amount');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenues');
    }
};
