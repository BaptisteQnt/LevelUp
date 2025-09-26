<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\IGDBService;
use App\Models\Game;
use Illuminate\Support\Str;

class ImportGamesFromIGDB extends Command
{
    protected $signature = 'games:import {search}';
    protected $description = 'Import games from IGDB by keyword';

    public function handle(IGDBService $igdb): int
    {
        $games = $igdb->fetchGames($this->argument('search'));

        foreach ($games as $g) {
            Game::updateOrCreate(
                ['slug' => Str::slug($g['name'])],
                [
                    'title' => $g['name'],
                    'twitch_id' => $g['id'],
                    'cover_url' => $g['cover']['url'] ?? null,
                    'description' => $g['summary'] ?? null,
                ]
            );
        }

        $this->info('Games imported successfully.');
        return Command::SUCCESS;
    }
}
