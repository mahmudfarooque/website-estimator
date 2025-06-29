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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            // This links the project to a specific user. If the user is deleted, their projects are also deleted.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->default('New Website Estimate');
            // This will track where the user is in the process (e.g., draft, design, content, invoiced)
            $table->string('status')->default('draft');
            // This will help us return the user to the correct step in the form
            $table->integer('current_step')->default(1);
            $table->timestamps(); // This automatically adds 'created_at' and 'updated_at' columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
