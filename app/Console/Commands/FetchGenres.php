<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Console\Commands;

use App\Models\Torrent;
use App\Models\TagTorrent;
use Illuminate\Console\Command;

class FetchGenres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:genres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Genres For Torrents In DB';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new \App\Services\MovieScrapper(config('api-keys.tmdb'), config('api-keys.tvdb'), config('api-keys.omdb'));

        $torrents = Torrent::withAnyStatus()
            ->select(['id', 'category_id', 'imdb', 'tmdb'])
            ->get();

        foreach ($torrents as $torrent) {
            if ($torrent->category_id == 2) {
                if ($torrent->tmdb && $torrent->tmdb != 0) {
                    $movie = $client->scrape('tv', null, $torrent->tmdb);
                } else {
                    $movie = $client->scrape('tv', 'tt'.$torrent->imdb);
                }
            } else {
                if ($torrent->tmdb && $torrent->tmdb != 0) {
                    $movie = $client->scrape('movie', null, $torrent->tmdb);
                } else {
                    $movie = $client->scrape('movie', 'tt'.$torrent->imdb);
                }
            }

            if ($movie->genres) {
                foreach ($movie->genres as $genre) {
                    $tag = new TagTorrent();
                    $tag->torrent_id = $torrent->id;
                    $tag->tag_name = $genre;
                    $tag->save();
                }
            }

            // sleep for 2 seconds
            sleep(2);
        }
    }
}
