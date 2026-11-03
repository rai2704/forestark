<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $t) {
            $t->string('token', 64)->nullable()->unique();
            $t->timestamp('confirmed_at')->nullable();
        });

        // Quem já estava na lista segue ativo e ganha link de cancelamento.
        DB::table('subscribers')->orderBy('id')->each(function ($s) {
            DB::table('subscribers')->where('id', $s->id)->update(['token' => Str::random(64), 'confirmed_at' => now()]);
        });

        Schema::create('newsletters', function (Blueprint $t) {
            $t->id();
            $t->jsonb('data'); // subject e body por idioma
            $t->timestamp('sent_at')->nullable();
            $t->unsignedInteger('sent_count')->default(0);
            $t->unsignedInteger('failed_count')->default(0);
            $t->timestamps();
        });

        // Corrige o título do rodapé, só se ainda estiver com o texto original.
        $setting = SiteSetting::query()->first();
        if ($setting && ($setting->data['footer_subscribe']['pt'] ?? null) === 'Inscreva-nos') {
            $data = $setting->data;
            $data['footer_subscribe']['pt'] = 'Inscreva-se';
            $setting->update(['data' => $data]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletters');
        Schema::table('subscribers', fn (Blueprint $t) => $t->dropColumn(['token', 'confirmed_at']));
    }
};
