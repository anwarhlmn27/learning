<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('lms_sidebar_color')->nullable()->after('font_family');
            $table->string('lms_sidebar_font_color')->nullable()->after('lms_sidebar_color');
            $table->string('lms_navbar_color')->nullable()->after('lms_sidebar_font_color');
            $table->string('lms_navbar_font_color')->nullable()->after('lms_navbar_color');
            $table->string('lms_content_color')->nullable()->after('lms_navbar_font_color');
            $table->string('lms_content_font_color')->nullable()->after('lms_content_color');
            $table->string('lms_font_family')->nullable()->after('lms_content_font_color');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'lms_sidebar_color',
                'lms_sidebar_font_color',
                'lms_navbar_color',
                'lms_navbar_font_color',
                'lms_content_color',
                'lms_content_font_color',
                'lms_font_family',
            ]);
        });
    }
};
