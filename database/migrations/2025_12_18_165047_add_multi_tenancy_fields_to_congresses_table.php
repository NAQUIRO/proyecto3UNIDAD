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
        Schema::table('congresses', function (Blueprint $table) {
            // Configuración de dominio/slug para multi-tenancy
            $table->string('custom_domain')->nullable()->unique()->after('url');
            $table->boolean('use_custom_domain')->default(false)->after('custom_domain');
            $table->string('subdomain')->nullable()->unique()->after('use_custom_domain');
            
            // Configuración visual (colores y branding)
            $table->string('primary_color', 7)->default('#667eea')->after('banner');
            $table->string('secondary_color', 7)->default('#764ba2')->after('primary_color');
            $table->string('accent_color', 7)->nullable()->after('secondary_color');
            $table->string('font_family')->default('Inter')->after('accent_color');
            $table->string('favicon')->nullable()->after('logo');
            
            // Configuración de multi-tenancy
            $table->boolean('is_active')->default(true)->after('status');
            $table->json('settings')->nullable()->after('is_active'); // Configuración adicional en JSON
            $table->string('timezone')->default('UTC')->after('settings');
            $table->string('locale', 10)->default('es')->after('timezone');
            
            // Configuración de ubicación
            $table->string('location')->nullable()->after('end_date');
            $table->text('address')->nullable()->after('location');
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('congresses', function (Blueprint $table) {
            $table->dropColumn([
                'custom_domain',
                'use_custom_domain',
                'subdomain',
                'primary_color',
                'secondary_color',
                'accent_color',
                'font_family',
                'favicon',
                'is_active',
                'settings',
                'timezone',
                'locale',
                'location',
                'address',
                'latitude',
                'longitude',
            ]);
        });
    }
};
