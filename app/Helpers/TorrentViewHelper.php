<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://choosealicense.com/licenses/gpl-3.0/  GNU General Public License v3.0
 * @author     Mr.G
 */

namespace App\Helpers;

use \App\Services\MovieScrapper;
use \App\UserFreeleech;
use \App\Group;
use \App\User;

use Illuminate\Support\Facades\Auth;

class TorrentViewHelper
{
    public static function view($results)
    {
        $user = Auth::user();
        $personal_freeleech = UserFreeleech::where('user_id', '=', $user->id)->first();

        $data = [];

        foreach ($results as $list) {
            if ($list->sticky == 1) {
                $sticky = "<tr class='info'>";
            } else {
                $sticky = "<tr>";
            }

            $client = new MovieScrapper(config('api-keys.tmdb'), config('api-keys.tvdb'), config('api-keys.omdb'));

            if ($list->category_id == 2) {
                $movie = $client->scrape('tv', 'tt' . $list->imdb);
            } else {
                $movie = $client->scrape('movie', 'tt' . $list->imdb);
            }

            if ($user->show_poster == 1) {
                $poster = "<div class='torrent-poster pull-left'><img src='{$movie->poster}' data-poster-mid='{$movie->poster}' class='img-tor-poster torrent-poster-img-small' alt='Poster' data-original-title='' title=''></div>";
            } else {
                $poster = "";
            }

            $category_link = route('category', ['slug' => $list->category->slug, 'id' => $list->category->id]);

            if ($list->category_id == '1') {
                $category = "<i class='fa fa-film torrent-icon' data-toggle='tooltip' title='' data-original-title='Movie Torrent'></i>";
            } elseif ($list->category_id == '2') {
                $category = "<i class='fa fa-tv torrent-icon' data-toggle='tooltip' title='' data-original-title='TV-Show Torrent'></i>";
            } else {
                $category = "<i class='fa fa-video-camera torrent-icon' data-toggle='tooltip' title='' data-original-title='FANRES Torrent'></i>";
            }

            $torrent_link = route('torrent', ['slug' => $list->slug, 'id' => $list->id]);
            $download_check_link = route('download_check', ['slug' => $list->slug, 'id' => $list->id]);
            $user_link = route('profil', ['username' => $list->user->username, 'id' => $list->user->id]);

            if ($list->anon == 1) {
                if ($user->id == $list->user->id || $user->group->is_modo) {
                    $staff_anon = "<a href='{$user_link}'>({$list->user->username})</a>";
                } else {
                    $staff_anon = "";
                }

                $anon = "ANONYMOUS {$staff_anon}";
            } else {
                $anon = "<a href='{$user_link}'>{$list->user->username}</a>";
            }

            if ($user->ratings == 1) {
                $link = "https://anon.to?http://www.imdb.com/title/tt" . $list->imdb;
                $rating = $movie->imdbRating;
                $votes = $movie->imdbVotes;
            } else {
                $rating = $movie->tmdbRating;
                $votes = $movie->tmdbVotes;
                if ($list->category_id == '2') {
                    $link = "https://www.themoviedb.org/tv/" . $movie->tmdb;
                } else {
                    $link = "https://www.themoviedb.org/movie/" . $movie->tmdb;
                }
            }

            $thank_count = $list->thanks()->count();

            $icons = "";

            if ($list->stream == "1") {
                $icons .= "<span class='badge-extra text-bold'><i class='fa fa-play text-red' data-toggle='tooltip' title='' data-original-title='Stream Optimized'></i> Stream Optimized</span>";
            }

            if ($list->featured == "0") {
                if ($list->doubleup == "1") {
                    $icons .= "<span class='badge-extra text-bold'><i class='fa fa-diamond text-green' data-toggle='tooltip' title='' data-original-title='Double upload'></i> Double Upload</span>";
                }

                if ($list->free == "1") {
                    $icons .= "<span class='badge-extra text-bold'><i class='fa fa-star text-gold' data-toggle='tooltip' title='' data-original-title='100% Free'></i> 100% Free</span>";
                }
            }

            if ($personal_freeleech) {
                $icons .= "<span class='badge-extra text-bold'><i class='fa fa-id-badge text-orange' data-toggle='tooltip' title='' data-original-title='Personal FL'></i> Personal FL</span>";
            }

            if ($list->featured == "1") {
                $icons .= "<span class='badge-extra text-bold' style='background-image:url(https://i.imgur.com/F0UCb7A.gif);'><i class='fa fa-certificate text-pink' data-toggle='tooltip' title='' data-original-title='Featured Torrent'></i> Featured</span>";
            }

            if ($user->group->is_freeleech == 1) {
                $icons .= "<span class='badge-extra text-bold'><i class='fa fa-trophy text-purple' data-toggle='tooltip' title='' data-original-title='Special FL'></i> Special FL</span>";
            }

            if (config('other.freeleech') == true) {
                $icons .= "<span class='badge-extra text-bold'><i class='fa fa-globe text-blue' data-toggle='tooltip' title='' data-original-title='Global FreeLeech'></i> Global FreeLeech</span>";
            }

            if (config('other.doubleup') == true) {
                $icons .= "<span class='badge-extra text-bold'><i class='fa fa-globe text-green' data-toggle='tooltip' title='' data-original-title='Double Upload'></i> Global Double Upload</span>";
            }

            if ($list->leechers >= "5") {
                $icons .= "<span class='badge-extra text-bold'><i class='fa fa-fire text-orange' data-toggle='tooltip' title='' data-original-title='Hot!'></i> Hot</span>";
            }

            if ($list->sticky == 1) {
                $icons .= "<span class='badge-extra text-bold'><i class='fa fa-thumb-tack text-black' data-toggle='tooltip' title='' data-original-title='Sticky!''></i> Sticky</span>";
            }

            if ($user->updated_at->getTimestamp() < $list->created_at->getTimestamp()) {
                $icons .= "<span class='badge-extra text-bold'><i class='fa fa-magic text-black' data-toggle='tooltip' title='' data-original-title='NEW!'></i> NEW</span>";
            }

            if ($list->highspeed == 1) {
                $icons .= "<span class='badge-extra text-bold'><i class='fa fa-tachometer text-red' data-toggle='tooltip' title='' data-original-title='High Speeds!'></i> High Speeds</span>";
            }

            if ($list->sd == 1) {
                $icons .= "<span class='badge-extra text-bold'><i class='fa fa-ticket text-orange' data-toggle='tooltip' title='' data-original-title='SD Content!'></i> SD Content</span>";
            }

            $datetime = date('Y-m-d H:m:s', strtotime($list->created_at));
            $datetime_inner = $list->created_at->diffForHumans();

            $common_times = trans('common.times');


            $data[] = $sticky .
                "<td>" . $poster . "</td>
            <td>
              <center>
              <a href='{$category_link}'>{$category}</a>
              <br>
              <br>
              <span class='label label-success'>{$list->type}</span>
              </center>
            </td>
            <td><a class='view-torrent' data-id='{$list->id}' data-slug='{$list->slug}' href='{$torrent_link}' data-toggle='tooltip' title='' data-original-title='{$list->name}'>{$list->name}</a>
                <a href='{$download_check_link}'><button class='btn btn-primary btn-circle' type='button' data-toggle='tooltip' title='' data-original-title='DOWNLOAD!'><i class='fa fa-download'></i></button></a>
                <br>
                <strong>
                <span class='badge-extra text-bold'>
                <i class='fa fa-upload'></i> By {$anon}
                </span>

                <a rel='nofollow' href='{$link}'>
                <span class='badge-extra text-bold'>
                  <span class='text-gold movie-rating-stars'>
                    <i class='fa fa-star' data-toggle='tooltip' title='' data-original-title='View More'></i>
                  </span>
                  {$rating}/10 ({$votes} votes)
                </span>
                </a>

                <span class='badge-extra text-bold text-pink'><i class='fa fa-heart' data-toggle='tooltip' title='' data-original-title='Thanks Given'></i>{$thank_count}</span>

                {$icons}
                </strong>
            </td>

            <td><time datetime='{$datetime}'>{$datetime_inner}</time></td>
            <td><span class='badge-extra text-blue text-bold'>" . $list->getSize() . "</span></td>
            <td><span class='badge-extra text-orange text-bold'>{$list->times_completed} {$common_times}</span></td>
            <td><span class='badge-extra text-green text-bold'>{$list->seeders}</span></td>
            <td><span class='badge-extra text-red text-bold'>{$list->leechers}</span></td>
            </tr>
            ";
        }
        return $data;
    }
}
