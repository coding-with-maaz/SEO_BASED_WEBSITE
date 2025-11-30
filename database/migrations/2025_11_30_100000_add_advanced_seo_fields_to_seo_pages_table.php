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
        Schema::table('seo_pages', function (Blueprint $table) {
            // Additional meta tags
            $table->string('meta_robots')->nullable()->after('meta_keywords'); // index, noindex, follow, nofollow, etc.
            $table->string('meta_author')->nullable()->after('meta_robots');
            $table->string('meta_language')->nullable()->after('meta_author'); // en, en-US, etc.
            $table->string('meta_geo_region')->nullable()->after('meta_language'); // US-NY, US-CA, etc.
            $table->string('meta_geo_placename')->nullable()->after('meta_geo_region');
            $table->decimal('meta_geo_position_lat', 10, 8)->nullable()->after('meta_geo_placename');
            $table->decimal('meta_geo_position_lon', 11, 8)->nullable()->after('meta_geo_position_lat');
            $table->string('meta_revisit_after')->nullable()->after('meta_geo_position_lon'); // 7 days, 1 month, etc.
            
            // Enhanced Open Graph
            $table->string('og_type')->nullable()->after('og_url'); // website, article, video.movie, video.tv_show, etc.
            $table->string('og_locale')->nullable()->after('og_type'); // en_US, en_GB, etc.
            $table->string('og_site_name')->nullable()->after('og_locale');
            $table->string('og_video_url')->nullable()->after('og_site_name');
            $table->integer('og_video_duration')->nullable()->after('og_video_url'); // in seconds
            $table->string('og_video_type')->nullable()->after('og_video_duration'); // video/mp4, etc.
            
            // Enhanced Twitter Card
            $table->string('twitter_site')->nullable()->after('twitter_image'); // @username
            $table->string('twitter_creator')->nullable()->after('twitter_site'); // @username
            
            // Additional SEO fields
            $table->string('hreflang_tags')->nullable()->after('canonical_url'); // JSON array of alternate language URLs
            $table->text('additional_meta_tags')->nullable()->after('hreflang_tags'); // Custom meta tags JSON
            $table->text('breadcrumb_schema')->nullable()->after('schema_markup'); // BreadcrumbList schema JSON
            
            // Performance and indexing hints
            $table->string('preconnect_domains')->nullable()->after('breadcrumb_schema'); // Comma-separated domains
            $table->string('dns_prefetch_domains')->nullable()->after('preconnect_domains');
            $table->boolean('enable_amp')->default(false)->after('dns_prefetch_domains');
            $table->string('amp_url')->nullable()->after('enable_amp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_pages', function (Blueprint $table) {
            $table->dropColumn([
                'meta_robots',
                'meta_author',
                'meta_language',
                'meta_geo_region',
                'meta_geo_placename',
                'meta_geo_position_lat',
                'meta_geo_position_lon',
                'meta_revisit_after',
                'og_type',
                'og_locale',
                'og_site_name',
                'og_video_url',
                'og_video_duration',
                'og_video_type',
                'twitter_site',
                'twitter_creator',
                'hreflang_tags',
                'additional_meta_tags',
                'breadcrumb_schema',
                'preconnect_domains',
                'dns_prefetch_domains',
                'enable_amp',
                'amp_url',
            ]);
        });
    }
};

