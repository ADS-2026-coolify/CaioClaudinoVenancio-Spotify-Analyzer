<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tornar campos padrão opcionais (Spotify pode não fornecer)
            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();

            // Campos do Spotify
            $table->string('spotify_id')->unique()->after('id');
            $table->string('display_name')->nullable()->after('name');
            $table->string('avatar_url')->nullable()->after('email');
            $table->string('country', 2)->nullable()->after('avatar_url');
            $table->string('product')->nullable()->after('country');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['spotify_id']);
            $table->dropColumn([
                'spotify_id',
                'display_name',
                'avatar_url',
                'country',
                'product',
                'last_login_at',
            ]);

            // Restaurar campos obrigatórios
            $table->string('name')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
