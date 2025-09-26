<?php
namespace App\Jobs;

use App\Models\Game;
use App\Models\GameTranslation;
use App\Services\Translator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranslateGameTexts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $gameId) {}

    public function handle(Translator $translator): void
    {
        $game = Game::findOrFail($this->gameId);

        $srcSummary   = (string) ($game->summary   ?? '');
        $srcStoryline = (string) ($game->storyline ?? '');

        $src = trim($srcStoryline . "\n\n" . $srcSummary);
        if ($src === '') return;

        $hash = hash('sha256', $src);
        $existing = GameTranslation::where('game_id', $game->id)
            ->where('lang', 'fr')
            ->first();

        if ($existing && $existing->source_hash === $hash) {
            return; // déjà à jour
        }

        // Sécurité : DeepL accepte de longs textes, mais on segmente à ~4000 chars
        $chunks = $this->chunkText($src, 4000);
        $translated = collect($chunks)->map(
            fn($c) => $translator->translate($c, 'fr', 'en')
        )->implode("\n");

        GameTranslation::updateOrCreate(
            ['game_id' => $game->id, 'lang' => 'fr'],
            [
                // choix simple : tout dans summary FR, ou split si tu veux
                'summary'     => $translated,
                'storyline'   => null,
                'provider'    => class_basename($translator),
                'source_hash' => $hash,
            ]
        );
    }

    private function chunkText(string $text, int $limit): array
    {
        // coupe proprement aux sauts de ligne quand possible
        $out = [];
        $current = '';
        foreach (preg_split("/(\r?\n){1,}/", $text) as $para) {
            $candidate = $current === '' ? $para : $current . "\n\n" . $para;
            if (mb_strlen($candidate) <= $limit) {
                $current = $candidate;
            } else {
                if ($current !== '') $out[] = $current;
                $current = $para;
                if (mb_strlen($current) > $limit) {
                    // hard split si un paragraphe est géant
                    $out = array_merge($out, str_split($current, $limit));
                    $current = '';
                }
            }
        }
        if ($current !== '') $out[] = $current;
        return $out;
    }
}
