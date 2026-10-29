<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Feedback 27-10-2026: the product "Teknologi" tab becomes two tabs, Teknologi Frame and Teknologi Lensa.
 * The existing `technologies` list (blue light, UV400 ...) is lens technology, so it moves to
 * `lens_technologies`; every product gets the standard frame materials in `frame_technologies`.
 * The old column is left in place, just no longer used.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'frame_technologies')) {
                $table->json('frame_technologies')->nullable()->after('technologies');
            }
            if (! Schema::hasColumn('products', 'lens_technologies')) {
                $table->json('lens_technologies')->nullable()->after('frame_technologies');
            }
        });

        $frame = json_encode(Product::frameTechnologyPreset(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        foreach (DB::table('products')->get(['id', 'technologies', 'frame_technologies', 'lens_technologies']) as $row) {
            $changes = [];
            if (blank($row->lens_technologies) && filled($row->technologies)) {
                $changes['lens_technologies'] = $row->technologies;
            }
            if (blank($row->frame_technologies) && $frame !== '[]') {
                $changes['frame_technologies'] = $frame;
            }
            if ($changes) {
                DB::table('products')->where('id', $row->id)->update($changes);
            }
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['frame_technologies', 'lens_technologies']);
        });
    }
};
