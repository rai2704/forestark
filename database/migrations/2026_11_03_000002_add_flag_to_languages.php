<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('languages', function (Blueprint $t) {
            $t->string('flag', 2)->nullable();
        });

        foreach (['pt' => 'br', 'en' => 'us', 'es' => 'es'] as $code => $flag) {
            DB::table('languages')->where('code', $code)->update(['flag' => $flag]);
        }
    }

    public function down(): void
    {
        Schema::table('languages', fn (Blueprint $t) => $t->dropColumn('flag'));
    }
};
