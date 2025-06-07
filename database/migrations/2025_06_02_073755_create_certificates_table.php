<?php

use App\Models\Target;
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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Target::class)->constrained()->cascadeOnDelete();
            $table->date('ssl_expiry_date')->nullable();
            $table->date('domain_expiry_date')->nullable();
            $table->integer('days_to_ssl_expiry')->nullable();
            $table->integer('days_to_domain_expiry')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
