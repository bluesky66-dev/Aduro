<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TorrentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type'          => 'torrent',
            'id'            => (string) $this->id,
            'attributes'    => [
                'name'            => $this->name,
                'release_year'    => $this->release_year,
                'category'        => $this->category->name,
                'type'            => $this->type,
                'size'            => $this->getSize(),
                'seeders'         => $this->seeders,
                'leechers'        => $this->leechers,
                'times_completed' => $this->times_completed,
                'tmdb_id'         => $this->tmdb,
                'imdb_id'         => $this->imdb,
                'tvdb_id'         => $this->tvdb,
                'mal_id'          => $this->mal,
                'igdb_id'         => $this->igdb,
                'created_at'      => $this->created_at->toDayDateTimeString(),
                'download_link'   => route('torrent.download.rsskey', ['id' => $this->id, 'rsskey' => auth('api')->user()->rsskey]),
            ],
        ];
    }
}
