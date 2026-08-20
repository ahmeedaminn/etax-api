<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('USER')->index()->after('password');
            $table->string('institution_request_status')
                ->default('NONE')
                ->index()
                ->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['institution_request_status']);
            $table->dropColumn(['role', 'institution_request_status']);
        });
    }
};
