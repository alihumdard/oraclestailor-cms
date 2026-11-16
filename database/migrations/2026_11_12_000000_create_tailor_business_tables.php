<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('phone')->index();
            $table->string('email')->nullable();
            $table->json('measurements')->nullable();
            $table->text('measurement_notes')->nullable();
            $table->date('measured_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tailor_orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('garment');
            $table->string('package');
            $table->string('fabric');
            $table->string('standard_size')->nullable();
            $table->string('measurement_unit', 2)->default('in');
            $table->json('measurements');
            $table->text('style_notes')->nullable();
            $table->string('status')->default('new')->index();
            $table->date('due_date')->index();
            $table->unsignedInteger('total');
            $table->unsignedInteger('advance')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tailor_orders');
        Schema::dropIfExists('customers');
    }
};
