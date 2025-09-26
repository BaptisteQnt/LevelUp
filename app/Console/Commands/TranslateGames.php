<?php
namespace App\Console\Commands;

use App\Jobs\TranslateGameTexts;
use App\Models\Game;
use Illuminate\Console\Command;

class TranslateGames extends Command
{
    protected $signature = 'games:translate 
        {gameId? : ID du jeu (optionnel si --slug)} 
        {--slug= : Slug du jeu}
        {--all : Traduire tous les jeux ayant un summary/storyline}';

    protected $description = 'Traduit les textes IGDB des jeux (summary/storyline) en FR via DeepL';

    public function handle(): int
    {
        // --all : dispatch sur tous
        if ($this->option('all')) {
            Game::query()
                ->where(function($q) {
                    $q->whereNotNull('summary')->orWhereNotNull('storyline');
                })
                ->pluck('id')
                ->each(fn ($gid) => TranslateGameTexts::dispatch($gid));

            $this->info('Jobs de traduction dispatchés pour tous les jeux.');
            return self::SUCCESS;
        }

        // --slug=... prioritaire si fourni
        if ($slug = $this->option('slug')) {
            $game = Game::where('slug', $slug)->first();
            if (!$game) {
                $this->error("Aucun jeu avec le slug '{$slug}'.");
                return self::FAILURE;
            }
            TranslateGameTexts::dispatchSync($game->id);
            $this->info("Jeu '{$slug}' (ID {$game->id}) traduit (sync).");
            return self::SUCCESS;
        }

        // Sinon on prend l’argument ID
        $id = (int) $this->argument('gameId');
        if ($id <= 0) {
            $this->error('Fournis un {gameId} ou --slug=...');
            return self::INVALID;
        }

        // On évite le findOrFail qui jette une exception CLI
        $game = Game::find($id);
        if (!$game) {
            $this->error("Aucun jeu avec l'ID {$id}.");
            return self::FAILURE;
        }

        TranslateGameTexts::dispatchSync($game->id);
        $this->info("Jeu {$id} traduit (sync).");
        return self::SUCCESS;
    }
}
