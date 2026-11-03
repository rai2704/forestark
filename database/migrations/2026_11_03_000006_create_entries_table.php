<?php

use App\Support\CollectionMigrator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entries', function (Blueprint $t) {
            $t->id();
            $t->string('collection');
            $t->string('slug')->nullable();
            $t->jsonb('data');
            $t->boolean('is_published')->default(true);
            $t->timestamp('published_at')->nullable();
            $t->unsignedInteger('position')->default(0);
            $t->timestamps();
            $t->index(['collection', 'position']);
            $t->unique(['collection', 'slug']);
        });

        // Leva os itens que estavam dentro dos blocos para os novos módulos.
        CollectionMigrator::run();
    }

    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
