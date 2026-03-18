<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = config('achievements.definitions', []);

        $rows = collect($definitions)
            ->map(fn (array $item): array => [
                'slug' => $item['slug'],
                'title' => $item['title'],
                'description' => $item['description'],
                'condition_type' => $item['condition_type'],
                'threshold' => (int) $item['threshold'],
                'metric_key' => $item['metric_key'],
                'badge_icon' => $item['badge_icon'] ?? null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->all();

        Achievement::upsert(
            $rows,
            ['slug'],
            ['title', 'description', 'condition_type', 'threshold', 'metric_key', 'badge_icon', 'is_active', 'updated_at']
        );
    }
}
