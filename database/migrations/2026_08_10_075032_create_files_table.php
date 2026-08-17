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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            
            // 1. The missing columns that match your Model!
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); 
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable(); 
            $table->integer('size_in_kb')->nullable();
            
            // 2. The magic morph columns
            $table->morphs('fileable'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};