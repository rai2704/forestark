<?php

use App\Support\CollectionMigrator;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        CollectionMigrator::ensureMediaPages();
    }

    public function down(): void
    {
        //
    }
};
