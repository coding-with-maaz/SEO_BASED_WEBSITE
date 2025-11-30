<?php

namespace App\Helpers;

class SchemaHelper
{
    /**
     * Generate Organization schema
     */
    public static function organization(array $data = []): array
    {
        $defaults = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $data['name'] ?? 'Nazaarabox',
            'url' => $data['url'] ?? url('/'),
            'logo' => $data['logo'] ?? url('/images/logo.png'),
            'sameAs' => $data['social_links'] ?? [],
        ];

        if (isset($data['contact_point'])) {
            $defaults['contactPoint'] = [
                '@type' => 'ContactPoint',
                'telephone' => $data['contact_point']['phone'] ?? '',
                'contactType' => $data['contact_point']['type'] ?? 'customer service',
            ];
        }

        return array_merge($defaults, $data);
    }

    /**
     * Generate Website schema
     */
    public static function website(array $data = []): array
    {
        return array_merge([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $data['name'] ?? 'Nazaarabox',
            'url' => $data['url'] ?? url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $data['search_url'] ?? url('/search?q={search_term_string}'),
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ], $data);
    }

    /**
     * Generate BreadcrumbList schema
     */
    public static function breadcrumbList(array $items): array
    {
        $listItems = [];
        foreach ($items as $index => $item) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'] ?? '',
                'item' => $item['url'] ?? '',
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];
    }

    /**
     * Generate Movie schema
     */
    public static function movie(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Movie',
            'name' => $data['name'] ?? '',
            'image' => $data['image'] ?? '',
            'description' => $data['description'] ?? '',
            'url' => $data['url'] ?? '',
        ];

        if (isset($data['director'])) {
            $schema['director'] = [
                '@type' => 'Person',
                'name' => $data['director'],
            ];
        }

        if (isset($data['date_published'])) {
            $schema['datePublished'] = $data['date_published'];
        }

        if (isset($data['duration'])) {
            $schema['duration'] = 'PT' . $data['duration'] . 'M';
        }

        if (isset($data['aggregate_rating'])) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $data['aggregate_rating']['value'] ?? 0,
                'ratingCount' => $data['aggregate_rating']['count'] ?? 0,
            ];
        }

        if (isset($data['genre'])) {
            $schema['genre'] = is_array($data['genre']) ? $data['genre'] : [$data['genre']];
        }

        if (isset($data['actor'])) {
            $actors = is_array($data['actor']) ? $data['actor'] : [$data['actor']];
            $schema['actor'] = array_map(function($actor) {
                return [
                    '@type' => 'Person',
                    'name' => is_array($actor) ? $actor['name'] : $actor,
                ];
            }, $actors);
        }

        return $schema;
    }

    /**
     * Generate TVSeries schema
     */
    public static function tvSeries(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'TVSeries',
            'name' => $data['name'] ?? '',
            'image' => $data['image'] ?? '',
            'description' => $data['description'] ?? '',
            'url' => $data['url'] ?? '',
        ];

        if (isset($data['number_of_seasons'])) {
            $schema['numberOfSeasons'] = $data['number_of_seasons'];
        }

        if (isset($data['number_of_episodes'])) {
            $schema['numberOfEpisodes'] = $data['number_of_episodes'];
        }

        if (isset($data['start_date'])) {
            $schema['startDate'] = $data['start_date'];
        }

        if (isset($data['end_date'])) {
            $schema['endDate'] = $data['end_date'];
        }

        if (isset($data['actor'])) {
            $actors = is_array($data['actor']) ? $data['actor'] : [$data['actor']];
            $schema['actor'] = array_map(function($actor) {
                return [
                    '@type' => 'Person',
                    'name' => is_array($actor) ? $actor['name'] : $actor,
                ];
            }, $actors);
        }

        return $schema;
    }

    /**
     * Generate VideoObject schema
     */
    public static function videoObject(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
            'thumbnailUrl' => $data['thumbnail'] ?? '',
            'uploadDate' => $data['upload_date'] ?? date('c'),
            'contentUrl' => $data['content_url'] ?? '',
        ];

        if (isset($data['duration'])) {
            $schema['duration'] = 'PT' . $data['duration'] . 'S';
        }

        return $schema;
    }

    /**
     * Generate Person schema
     */
    public static function person(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $data['name'] ?? '',
            'image' => $data['image'] ?? '',
            'description' => $data['description'] ?? '',
        ];

        if (isset($data['birth_date'])) {
            $schema['birthDate'] = $data['birth_date'];
        }

        if (isset($data['birth_place'])) {
            $schema['birthPlace'] = [
                '@type' => 'Place',
                'name' => $data['birth_place'],
            ];
        }

        if (isset($data['job_title'])) {
            $schema['jobTitle'] = $data['job_title'];
        }

        return $schema;
    }

    /**
     * Generate CollectionPage schema
     */
    public static function collectionPage(array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $data['name'] ?? '',
            'url' => $data['url'] ?? '',
            'description' => $data['description'] ?? '',
        ];
    }
}

