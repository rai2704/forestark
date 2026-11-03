<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $t) {
            $t->id();
            $t->string('code', 10)->unique();
            $t->string('name');
            $t->boolean('is_default')->default(false);
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('position')->default(0);
            $t->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $t) {
            $t->id();
            $t->jsonb('data');
            $t->timestamps();
        });

        Schema::create('pages', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique(); // string vazia = página inicial
            $t->jsonb('data'); // title, meta_description (por idioma)
            $t->boolean('is_published')->default(true);
            $t->timestamps();
        });

        Schema::create('blocks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('page_id')->constrained()->cascadeOnDelete();
            $t->string('type');
            $t->jsonb('data');
            $t->unsignedInteger('position')->default(0);
            $t->boolean('is_visible')->default(true);
            $t->timestamps();
            $t->index(['page_id', 'position']);
        });

        Schema::create('menu_items', function (Blueprint $t) {
            $t->id();
            $t->string('location'); // header | footer
            $t->jsonb('data'); // label (por idioma), url
            $t->unsignedInteger('position')->default(0);
            $t->boolean('is_visible')->default(true);
            $t->timestamps();
            $t->index(['location', 'position']);
        });

        Schema::create('contact_messages', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email');
            $t->string('phone')->nullable();
            $t->string('subject')->nullable();
            $t->text('message');
            $t->string('locale', 10)->nullable();
            $t->timestamp('read_at')->nullable();
            $t->timestamps();
        });

        Schema::create('subscribers', function (Blueprint $t) {
            $t->id();
            $t->string('email')->unique();
            $t->string('locale', 10)->nullable();
            $t->timestamps();
        });

        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->boolean('full_access')->default(false);
            $t->jsonb('permissions')->default('[]');
            $t->timestamps();
        });

        Schema::table('users', function (Blueprint $t) {
            $t->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
            $t->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropConstrainedForeignId('role_id');
            $t->dropColumn('is_active');
        });
        foreach (['roles', 'subscribers', 'contact_messages', 'menu_items', 'blocks', 'pages', 'site_settings', 'languages'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
