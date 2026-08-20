<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->renameColumn('user_id', 'institution_id');
            $table->renameColumn('content', 'description');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('institution_id')->references('id')->on('users')->cascadeOnDelete();
            // Existing generic posts remain valid as Announcements after migration.
            $table->string('type')->default('ANNOUNCEMENT')->index()->after('category_id');
            // Event fields stay nullable because Announcements do not use them.
            $table->timestamp('start_at')->nullable()->after('description');
            $table->timestamp('end_at')->nullable()->after('start_at');
            $table->string('location')->nullable()->after('end_at');
            $table->unsignedInteger('capacity')->nullable()->after('location');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);
            $table->dropIndex(['type']);
            $table->dropColumn(['type', 'start_at', 'end_at', 'location', 'capacity']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->renameColumn('institution_id', 'user_id');
            $table->renameColumn('description', 'content');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
