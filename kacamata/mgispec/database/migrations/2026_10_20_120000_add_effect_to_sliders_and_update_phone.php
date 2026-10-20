<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Feedback 20-10-2026: every banner gets its own transition effect (chosen in the CMS), starting with a
 * different one per banner; the footer "Layanan Cepat" number becomes the Indonesian office number.
 */
return new class extends Migration
{
    /** Initial effect per banner, in slider order. */
    private const INITIAL = ['fade', 'slide', 'wipe', 'kenburns', 'cube'];

    public function up(): void
    {
        if (! Schema::hasColumn('sliders', 'effect')) {
            Schema::table('sliders', function (Blueprint $table) {
                $table->string('effect', 20)->nullable()->after('overlay');
            });
        }

        DB::table('sliders')->orderBy('sort_order')->orderBy('id')->pluck('id')
            ->each(fn ($id, $i) => DB::table('sliders')->where('id', $id)
                ->update(['effect' => self::INITIAL[$i % count(self::INITIAL)]]));

        Setting::put([
            'slider_effect' => 'fade',
            'phone' => '+62 821-2881-5099',
            'whatsapp' => '6282128815099',
        ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('sliders', 'effect')) {
            Schema::table('sliders', function (Blueprint $table) {
                $table->dropColumn('effect');
            });
        }
    }
};
