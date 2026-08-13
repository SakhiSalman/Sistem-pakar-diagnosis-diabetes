<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tes_lab', function (Blueprint $table) {
            $table->boolean('sudah_tes_ttgo')->default(false)->after('hasil_cpeptide');
            $table->decimal('ttgo_puasa', 5, 1)->nullable()->comment('mg/dL')->after('sudah_tes_ttgo');
            $table->decimal('ttgo_1jam', 5, 1)->nullable()->comment('mg/dL')->after('ttgo_puasa');
            $table->decimal('ttgo_2jam', 5, 1)->nullable()->comment('mg/dL')->after('ttgo_1jam');
        });
    }

    public function down(): void
    {
        Schema::table('tes_lab', function (Blueprint $table) {
            $table->dropColumn(['sudah_tes_ttgo', 'ttgo_puasa', 'ttgo_1jam', 'ttgo_2jam']);
        });
    }
};
