<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->unique()->after('name');
        });

        // Existing accounts get the part of their e-mail before the "@" (admin@mgispec.com → admin).
        foreach (DB::table('users')->whereNull('username')->get(['id', 'email']) as $user) {
            $base = strtolower(preg_replace('/[^a-z0-9._-]/i', '', strstr($user->email, '@', true) ?: 'user')) ?: 'user';
            $username = $base;
            for ($i = 2; DB::table('users')->where('username', $username)->exists(); $i++) {
                $username = $base.$i;
            }
            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
