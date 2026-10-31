<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Feedback 27-10-2026: the product "Teknologi" tab becomes two tabs, Teknologi Frame and Teknologi Lensa.
 * Sample data for now: the existing `technologies` list (blue light, UV400 ...) moves to
 * `frame_technologies`, and `lens_technologies` gets lorem ipsum placeholders until the real
 * lens content arrives. The old column is left in place, just no longer used.
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

        $lens = json_encode(Product::sampleLensTechnologies(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        foreach (DB::table('products')->get(['id', 'technologies', 'frame_technologies', 'lens_technologies']) as $row) {
            $changes = [];
            if (blank($row->frame_technologies) && filled($row->technologies)) {
                $changes['frame_technologies'] = $row->technologies;
            }
            if (blank($row->lens_technologies)) {
                $changes['lens_technologies'] = $lens;
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
