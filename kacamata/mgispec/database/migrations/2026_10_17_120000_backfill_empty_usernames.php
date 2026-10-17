<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * On the dev server the username column was added without its backfill,
     * so admins had no username and could not log in. Fill every empty one
     * from the part of the e-mail before "@".
     */
    public function up(): void
    {
        DB::table('users')
            ->where(fn ($q) => $q->whereNull('username')->orWhere('username', ''))
            ->orderBy('id')
            ->each(function ($user) {
                $base = strtolower(strstr($user->email, '@', true) ?: 'admin');
                $username = $base;
                for ($i = 2; DB::table('users')->where('username', $username)->exists(); $i++) {
                    $username = $base.$i;
                }
                DB::table('users')->where('id', $user->id)->update(['username' => $username]);
            });
    }

    public function down(): void
    {
        // Data-only fix; nothing to undo.
    }
};
