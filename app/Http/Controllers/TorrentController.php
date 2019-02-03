<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 *
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Http\Controllers;

use App\Peer;
use App\Type;
use App\User;
use App\History;
use App\Torrent;
use App\Warning;
use App\Category;
use Carbon\Carbon;
use App\TagTorrent;
use App\TorrentFile;
use App\FreeleechToken;
use App\PrivateMessage;
use App\TorrentRequest;
use App\BonTransactions;
use App\FeaturedTorrent;
use App\Services\Bencode;
use App\Helpers\MediaInfo;
use App\PersonalFreeleech;
use App\Bots\IRCAnnounceBot;
use Brian2694\Toastr\Toastr;
use Illuminate\Http\Request;
use App\Helpers\TorrentHelper;
use App\Services\TorrentTools;
use Illuminate\Support\Facades\DB;
use App\Repositories\ChatRepository;
use App\Notifications\NewReseedRequest;
use App\Repositories\TorrentFacetedRepository;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class TorrentController extends Controller
{
    /**
     * @var TorrentFacetedRepository
     */
    private $faceted;

    /**
     * @var ChatRepository
     */
    private $chat;

    /**
     * @var Toastr
     */
    private $toastr;

    /**
     * RequestController Constructor.
     *
     * @param TorrentFacetedRepository $faceted
     * @param ChatRepository           $chat
     * @param Toastr                   $toastr
     */
    public function __construct(TorrentFacetedRepository $faceted, ChatRepository $chat, Toastr $toastr)
    {
        $this->faceted = $faceted;
        $this->chat = $chat;
        $this->toastr = $toastr;
    }

    /**
     * Displays Torrent List View.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function torrents()
    {
        $user = auth()->user();
        $personal_freeleech = PersonalFreeleech::where('user_id', '=', $user->id)->first();
        $torrents = Torrent::with(['user', 'category'])->withCount(['thanks', 'comments'])->orderBy('sticky', 'desc')->orderBy('created_at', 'desc')->paginate(25);
        $repository = $this->faceted;

        return view('torrent.torrents', [
            'personal_freeleech' => $personal_freeleech,
            'repository'         => $repository,
            'torrents'           => $torrents,
            'user'               => $user,
            'sorting'            => '',
            'direction'          => 1,
            'links'              => null,
        ]);
    }

    /**
     * Torrent Similar Results.
     *
     * @param $imdb
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function similar($imdb)
    {
        $user = auth()->user();
        $personal_freeleech = PersonalFreeleech::where('user_id', '=', $user->id)->first();
        $torrents = Torrent::with(['user', 'category'])
            ->withCount(['thanks', 'comments'])
            ->where('imdb', '=', $imdb)
            ->latest()
            ->get();

        if (! $torrents || $torrents->count() < 1) {
            abort(404);
        }

        return view('torrent.similar', [
            'user' => $user,
            'personal_freeleech' => $personal_freeleech,
            'torrents' => $torrents,
            'imdb' => $imdb,
        ]);
    }

    /**
     * Displays Torrent Cards View.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cardLayout()
    {
        $user = auth()->user();
        $torrents = Torrent::with(['user', 'category'])->latest()->paginate(33);
        $repository = $this->faceted;

        $client = new \App\Services\MovieScrapper(config('api-keys.tmdb'), config('api-keys.tvdb'), config('api-keys.omdb'));
        foreach ($torrents as $torrent) {
            $movie = null;
            if ($torrent->category_id == 2) {
                if ($torrent->tmdb || $torrent->tmdb != 0) {
                    $movie = $client->scrape('tv', null, $torrent->tmdb);
                } elseif ($torrent->imdb && $torrent->imdb != 0) {
                    $movie = $client->scrape('tv', 'tt'.$torrent->imdb);
                }
            } else {
                if ($torrent->tmdb || $torrent->tmdb != 0) {
                    $movie = $client->scrape('movie', null, $torrent->tmdb);
                } elseif ($torrent->imdb && $torrent->imdb != 0) {
                    $movie = $client->scrape('movie', 'tt'.$torrent->imdb);
                }
            }
            if ($movie) {
                $torrent->movie = $movie;
            }
        }

        return view('torrent.cards', [
            'user' => $user,
            'torrents' => $torrents,
            'repository' => $repository,
        ]);
    }

    /**
     * Torrent Filter Remember Setting.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function filtered(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            if ($request->has('force')) {
                if ($request->input('force') == 1) {
                    $user->torrent_filters = 0;
                    $user->save();
                } elseif ($request->input('force') == 2) {
                    $user->torrent_filters = 1;
                    $user->save();
                }
            }
        }
    }

    /**
     * Torrent Grouping.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function groupingLayout()
    {
        $user = auth()->user();
        $repository = $this->faceted;
        $personal_freeleech = PersonalFreeleech::where('user_id', '=', $user->id)->first();

        $page = 0;
        $sorting = 'created_at';
        $direction = 2;
        $order = 'desc';
        $qty = 25;
        $logger = null;
        $cache = [];
        $attributes = [];

        $torrent = DB::table('torrents')->selectRaw('distinct(torrents.imdb),max(torrents.created_at) as screated_at,max(torrents.seeders) as sseeders,max(torrents.leechers) as sleechers,max(torrents.times_completed) as stimes_completed,max(torrents.name) as sname')->leftJoin('torrents as torrentsl', 'torrents.id', '=', 'torrentsl.id')->groupBy('torrents.imdb')->whereRaw('torrents.status = ? AND torrents.imdb != ?', [1, 0]);

        $prelauncher = $torrent->orderBy('s'.$sorting, $order)->pluck('imdb')->toArray();

        if (! is_array($prelauncher)) {
            $prelauncher = [];
        }
        $links = new Paginator($prelauncher, floor(count($prelauncher) / $qty) * $qty, $qty);

        $hungry = array_chunk($prelauncher, $qty);
        $fed = [];
        if (is_array($hungry) && array_key_exists($page, $hungry)) {
            $fed = $hungry[$page];
        }
        $totals = [];
        $counts = [];
        $launcher = Torrent::with(['user', 'category'])->withCount(['thanks', 'comments'])->whereIn('imdb', $fed)->orderBy($sorting, $order);
        foreach ($launcher->cursor() as $chunk) {
            if ($chunk->imdb) {
                if (! array_key_exists($chunk->imdb, $totals)) {
                    $totals[$chunk->imdb] = 1;
                } else {
                    $totals[$chunk->imdb] = $totals[$chunk->imdb] + 1;
                }
                if (! array_key_exists('imdb'.$chunk->imdb, $cache)) {
                    $cache['imdb'.$chunk->imdb] = [];
                }
                if (! array_key_exists('imdb'.$chunk->imdb, $counts)) {
                    $counts['imdb'.$chunk->imdb] = 0;
                }
                if (! array_key_exists('imdb'.$chunk->imdb, $attributes)) {
                    $attributes['imdb'.$chunk->imdb]['seeders'] = 0;
                    $attributes['imdb'.$chunk->imdb]['leechers'] = 0;
                    $attributes['imdb'.$chunk->imdb]['times_completed'] = 0;
                    $attributes['imdb'.$chunk->imdb]['types'] = [];
                    $attributes['imdb'.$chunk->imdb]['categories'] = [];
                    $attributes['imdb'.$chunk->imdb]['genres'] = [];
                }
                $attributes['imdb'.$chunk->imdb]['times_completed'] += $chunk->times_completed;
                $attributes['imdb'.$chunk->imdb]['seeders'] += $chunk->seeders;
                $attributes['imdb'.$chunk->imdb]['leechers'] += $chunk->leechers;
                if (! array_key_exists($chunk->type, $attributes['imdb'.$chunk->imdb])) {
                    $attributes['imdb'.$chunk->imdb]['types'][$chunk->type] = $chunk->type;
                }
                if (! array_key_exists($chunk->category_id, $attributes['imdb'.$chunk->imdb])) {
                    $attributes['imdb'.$chunk->imdb]['categories'][$chunk->category_id] = $chunk->category_id;
                }
                $cache['imdb'.$chunk->imdb]['torrent'.$counts['imdb'.$chunk->imdb]] = [
                    'created_at' => $chunk->created_at,
                    'seeders' => $chunk->seeders,
                    'leechers' => $chunk->leechers,
                    'name' => $chunk->name,
                    'times_completed' => $chunk->times_completed,
                    'size' => $chunk->size,
                    'chunk' => $chunk,
                ];
                $counts['imdb'.$chunk->imdb]++;
            }
        }
        if (count($cache) > 0) {
            $torrents = $cache;
        } else {
            $torrents = null;
        }

        if (is_array($torrents)) {
            $client = new \App\Services\MovieScrapper(config('api-keys.tmdb'), config('api-keys.tvdb'), config('api-keys.omdb'));
            foreach ($torrents as $k1 => $c) {
                foreach ($c as $k2 => $d) {
                    $movie = null;
                    if ($d['chunk']->category_id == 2) {
                        if ($d['chunk']->tmdb || $d['chunk']->tmdb != 0) {
                            $movie = $client->scrape('tv', null, $d['chunk']->tmdb);
                        } elseif ($d['chunk']->imdb && $d['chunk']->imdb != 0) {
                            $movie = $client->scrape('tv', 'tt'.$d['chunk']->imdb);
                        }
                    } else {
                        if ($d['chunk']->tmdb || $d['chunk']->tmdb != 0) {
                            $movie = $client->scrape('movie', null, $d['chunk']->tmdb);
                        } elseif ($d['chunk']->imdb && $d['chunk']->imdb != 0) {
                            $movie = $client->scrape('movie', 'tt'.$d['chunk']->imdb);
                        }
                    }
                    if ($movie) {
                        $d['chunk']->movie = $movie;
                    }
                }
            }
        }

        return view('torrent.groupings', [
            'torrents'           => $torrents,
            'user'               => $user,
            'sorting'            => $sorting,
            'direction'          => $direction,
            'links'              => $links,
            'totals'             => $totals,
            'personal_freeleech' => $personal_freeleech,
            'repository'         => $repository,
            'attributes'         => $attributes,
        ])->render();
    }

    /**
     * Uses Input's To Put Together A Search.
     *
     * @param \Illuminate\Http\Request $request
     * @param $torrent Torrent
     *
     * @return array
     */
    public function faceted(Request $request, Torrent $torrent)
    {
        $user = auth()->user();
        $repository = $this->faceted;
        $personal_freeleech = PersonalFreeleech::where('user_id', '=', $user->id)->first();
        $collection = null;
        $history = null;
        $nohistory = null;
        $seedling = null;
        $notdownloaded = null;
        $downloaded = null;
        $leeching = null;
        $idling = null;

        if ($request->has('view') && $request->input('view') == 'group') {
            $collection = 1;
        }
        if ($request->has('notdownloaded') && $request->input('notdownloaded') != null) {
            $notdownloaded = 1;
            $nohistory = 1;
        }
        if ($request->has('seeding') && $request->input('seeding') != null) {
            $seedling = 1;
            $history = 1;
        }
        if ($request->has('downloaded') && $request->input('downloaded') != null) {
            $downloaded = 1;
            $history = 1;
        }
        if ($request->has('leeching') && $request->input('leeching') != null) {
            $leeching = 1;
            $history = 1;
        }
        if ($request->has('idling') && $request->input('idling') != null) {
            $idling = 1;
            $history = 1;
        }

        $search = $request->input('search');
        $description = $request->input('description');
        $uploader = $request->input('uploader');
        $imdb = $request->input('imdb');
        $tvdb = $request->input('tvdb');
        $tmdb = $request->input('tmdb');
        $mal = $request->input('mal');
        $categories = $request->input('categories');
        $types = $request->input('types');
        $genres = $request->input('genres');
        $freeleech = $request->input('freeleech');
        $doubleupload = $request->input('doubleupload');
        $featured = $request->input('featured');
        $stream = $request->input('stream');
        $highspeed = $request->input('highspeed');
        $sd = $request->input('sd');
        $internal = $request->input('internal');
        $alive = $request->input('alive');
        $dying = $request->input('dying');
        $dead = $request->input('dead');
        $page = (int) $request->input('page');

        $totals = null;
        $links = null;
        $order = null;
        $sorting = null;

        $terms = explode(' ', $search);
        $search = '';
        foreach ($terms as $term) {
            $search .= '%'.$term.'%';
        }

        $usernames = explode(' ', $uploader);
        $uploader = null;
        foreach ($usernames as $username) {
            $uploader .= $username.'%';
        }

        $keywords = explode(' ', $description);
        $description = '';
        foreach ($keywords as $keyword) {
            $description .= '%'.$keyword.'%';
        }

        if ($request->has('sorting') && $request->input('sorting') != null) {
            $sorting = $request->input('sorting');
        }
        if ($request->has('direction') && $request->input('direction') != null) {
            $order = $request->input('direction');
        }
        if (! $sorting || $sorting == null || ! $order || $order == null) {
            $sorting = 'created_at';
            $order = 'desc';
            // $order = 'asc';
        }

        if ($order == 'asc') {
            $direction = 1;
        } else {
            $direction = 2;
        }

        if ($request->has('qty')) {
            $qty = $request->input('qty');
        } else {
            $qty = 25;
        }

        if ($collection == 1) {
            $torrent = DB::table('torrents')->selectRaw('distinct(torrents.imdb),max(torrents.created_at) as screated_at,max(torrents.seeders) as sseeders,max(torrents.leechers) as sleechers,max(torrents.times_completed) as stimes_completed,max(torrents.name) as sname')->leftJoin('torrents as torrentsl', 'torrents.id', '=', 'torrentsl.id')->groupBy('torrents.imdb')->whereRaw('torrents.status = ? AND torrents.imdb != ?', [1, 0]);

            if ($request->has('search') && $request->input('search') != null) {
                $torrent->where(function ($query) use ($search) {
                    $query->where('torrentsl.name', 'like', $search);
                });
            }
            if ($request->has('description') && $request->input('description') != null) {
                $torrent->where(function ($query) use ($description) {
                    $query->where('torrentsl.description', 'like', $description)->orwhere('torrentsl.mediainfo', 'like', $description);
                });
            }

            if ($request->has('uploader') && $request->input('uploader') != null) {
                $match = User::whereRaw('(username like ?)', [$uploader])->orderBy('username', 'ASC')->first();
                if (null === $match) {
                    return ['result' => [], 'count' => 0];
                }
                $torrent->where('torrentsl.user_id', '=', $match->id)->where('torrentsl.anon', '=', 0);
            }

            if ($request->has('imdb') && $request->input('imdb') != null) {
                $torrent->where('torrentsl.imdb', '=', $imdb);
            }

            if ($request->has('tvdb') && $request->input('tvdb') != null) {
                $torrent->where('torrentsl.tvdb', '=', $tvdb);
            }

            if ($request->has('tmdb') && $request->input('tmdb') != null) {
                $torrent->where('torrentsl.tmdb', '=', $tmdb);
            }

            if ($request->has('mal') && $request->input('mal') != null) {
                $torrent->where('torrentsl.mal', '=', $mal);
            }

            if ($request->has('categories') && $request->input('categories') != null) {
                $torrent->whereIn('torrentsl.category_id', $categories);
            }

            if ($request->has('types') && $request->input('types') != null) {
                $torrent->whereIn('torrentsl.type', $types);
            }

            if ($request->has('genres') && $request->input('genres') != null) {
                $genreID = TagTorrent::distinct()->select('torrent_id')->whereIn('tag_name', $genres)->get();
                $torrent->whereIn('torrentsl.id', $genreID);
            }

            if ($request->has('freeleech') && $request->input('freeleech') != null) {
                $torrent->where('torrentsl.free', '=', $freeleech);
            }

            if ($request->has('doubleupload') && $request->input('doubleupload') != null) {
                $torrent->where('torrentsl.doubleup', '=', $doubleupload);
            }

            if ($request->has('featured') && $request->input('featured') != null) {
                $torrent->where('torrentsl.featured', '=', $featured);
            }

            if ($request->has('stream') && $request->input('stream') != null) {
                $torrent->where('torrentsl.stream', '=', $stream);
            }

            if ($request->has('highspeed') && $request->input('highspeed') != null) {
                $torrent->where('torrentsl.highspeed', '=', $highspeed);
            }

            if ($request->has('sd') && $request->input('sd') != null) {
                $torrent->where('torrentsl.sd', '=', $sd);
            }

            if ($request->has('internal') && $request->input('internal') != null) {
                $torrent->where('torrentsl.internal', '=', $internal);
            }

            if ($request->has('alive') && $request->input('alive') != null) {
                $torrent->where('torrentsl.seeders', '>=', $alive);
            }

            if ($request->has('dying') && $request->input('dying') != null) {
                $torrent->where('torrentsl.seeders', '=', $dying)->where('torrentsl.times_completed', '>=', 3);
            }

            if ($request->has('dead') && $request->input('dead') != null) {
                $torrent->where('torrentsl.seeders', '=', $dead);
            }
        } elseif ($nohistory == 1) {
            $history = History::select('torrents.id')->leftJoin('torrents', 'torrents.info_hash', '=', 'history.info_hash')->where('history.user_id', '=', $user->id)->get()->toArray();
            if (! $history || ! is_array($history)) {
                $history = [];
            }
            $torrent = $torrent->with(['user', 'category'])->withCount(['thanks', 'comments'])->whereNotIn('torrents.id', $history);
        } elseif ($history == 1) {
            $torrent = History::where('history.user_id', '=', $user->id);
            $torrent->where(function ($query) use ($user, $seedling, $downloaded, $leeching, $idling) {
                if ($seedling == 1) {
                    $query->orWhere(function ($query) use ($user) {
                        $query->whereRaw('history.active = ? AND history.seeder = ?', [1, 1]);
                    });
                }
                if ($downloaded == 1) {
                    $query->orWhere(function ($query) use ($user) {
                        $query->whereRaw('history.completed_at is not null');
                    });
                }
                if ($leeching == 1) {
                    $query->orWhere(function ($query) use ($user) {
                        $query->whereRaw('history.active = ? AND history.seeder = ? AND history.completed_at is null', [1, 0]);
                    });
                }
                if ($idling == 1) {
                    $query->orWhere(function ($query) use ($user) {
                        $query->whereRaw('history.active = ? AND history.seeder = ? AND history.completed_at is null', [0, 0]);
                    });
                }
            });
            $torrent = $torrent->selectRaw('distinct(torrents.id),max(torrents.sticky),max(torrents.created_at),max(torrents.seeders),max(torrents.leechers),max(torrents.name),max(torrents.size),max(torrents.times_completed)')->leftJoin('torrents', function ($join) use ($user) {
                $join->on('history.info_hash', '=', 'torrents.info_hash');
            })->groupBy('torrents.id');
        } else {
            $torrent = $torrent->with(['user', 'category'])->withCount(['thanks', 'comments']);
        }
        if ($collection != 1) {
            if ($request->has('search') && $request->input('search') != null) {
                $torrent->where(function ($query) use ($search) {
                    $query->where('torrents.name', 'like', $search);
                });
            }

            if ($request->has('description') && $request->input('description') != null) {
                $torrent->where(function ($query) use ($description) {
                    $query->where('torrents.description', 'like', $description)->orWhere('mediainfo', 'like', $description);
                });
            }

            if ($request->has('uploader') && $request->input('uploader') != null) {
                $match = User::whereRaw('(username like ?)', [$uploader])->orderBy('username', 'ASC')->first();
                if (null === $match) {
                    return ['result' => [], 'count' => 0];
                }
                $torrent->where('torrents.user_id', '=', $match->id)->where('anon', '=', 0);
            }

            if ($request->has('imdb') && $request->input('imdb') != null) {
                $torrent->where('torrents.imdb', '=', $imdb);
            }

            if ($request->has('tvdb') && $request->input('tvdb') != null) {
                $torrent->where('torrents.tvdb', '=', $tvdb);
            }

            if ($request->has('tmdb') && $request->input('tmdb') != null) {
                $torrent->where('torrents.tmdb', '=', $tmdb);
            }

            if ($request->has('mal') && $request->input('mal') != null) {
                $torrent->where('torrents.mal', '=', $mal);
            }

            if ($request->has('categories') && $request->input('categories') != null) {
                $torrent->whereIn('torrents.category_id', $categories);
            }

            if ($request->has('types') && $request->input('types') != null) {
                $torrent->whereIn('torrents.type', $types);
            }

            if ($request->has('genres') && $request->input('genres') != null) {
                $genreID = TagTorrent::distinct()->select('torrent_id')->whereIn('tag_name', $genres)->get();
                $torrent->whereIn('torrents.id', $genreID);
            }

            if ($request->has('freeleech') && $request->input('freeleech') != null) {
                $torrent->where('torrents.free', '=', $freeleech);
            }

            if ($request->has('doubleupload') && $request->input('doubleupload') != null) {
                $torrent->where('torrents.doubleup', '=', $doubleupload);
            }

            if ($request->has('featured') && $request->input('featured') != null) {
                $torrent->where('torrents.featured', '=', $featured);
            }

            if ($request->has('stream') && $request->input('stream') != null) {
                $torrent->where('torrents.stream', '=', $stream);
            }

            if ($request->has('highspeed') && $request->input('highspeed') != null) {
                $torrent->where('torrents.highspeed', '=', $highspeed);
            }

            if ($request->has('sd') && $request->input('sd') != null) {
                $torrent->where('torrents.sd', '=', $sd);
            }

            if ($request->has('internal') && $request->input('internal') != null) {
                $torrent->where('torrents.internal', '=', $internal);
            }

            if ($request->has('alive') && $request->input('alive') != null) {
                $torrent->where('torrents.seeders', '>=', $alive);
            }

            if ($request->has('dying') && $request->input('dying') != null) {
                $torrent->where('torrents.seeders', '=', $dying)->where('times_completed', '>=', 3);
            }

            if ($request->has('dead') && $request->input('dead') != null) {
                $torrent->where('torrents.seeders', '=', $dead);
            }
        }

        $logger = null;
        $cache = [];
        $attributes = [];
        $links = null;
        if ($collection == 1) {
            if ($logger == null) {
                $logger = 'torrent.results_groupings';
            }

            $prelauncher = $torrent->orderBy('s'.$sorting, $order)->pluck('imdb')->toArray();

            if (! is_array($prelauncher)) {
                $prelauncher = [];
            }
            $links = new Paginator($prelauncher, floor(count($prelauncher) / $qty) * $qty, $qty);

            $hungry = array_chunk($prelauncher, $qty);
            $fed = [];
            if (is_array($hungry) && array_key_exists($page, $hungry)) {
                $fed = $hungry[$page];
            }
            $totals = [];
            $counts = [];
            $launcher = Torrent::with(['user', 'category'])->withCount(['thanks', 'comments'])->whereIn('imdb', $fed)->orderBy($sorting, $order);
            foreach ($launcher->cursor() as $chunk) {
                if ($chunk->imdb) {
                    if (! array_key_exists($chunk->imdb, $totals)) {
                        $totals[$chunk->imdb] = 1;
                    } else {
                        $totals[$chunk->imdb] = $totals[$chunk->imdb] + 1;
                    }
                    if (! array_key_exists('imdb'.$chunk->imdb, $cache)) {
                        $cache['imdb'.$chunk->imdb] = [];
                    }
                    if (! array_key_exists('imdb'.$chunk->imdb, $counts)) {
                        $counts['imdb'.$chunk->imdb] = 0;
                    }
                    if (! array_key_exists('imdb'.$chunk->imdb, $attributes)) {
                        $attributes['imdb'.$chunk->imdb]['seeders'] = 0;
                        $attributes['imdb'.$chunk->imdb]['leechers'] = 0;
                        $attributes['imdb'.$chunk->imdb]['times_completed'] = 0;
                        $attributes['imdb'.$chunk->imdb]['types'] = [];
                        $attributes['imdb'.$chunk->imdb]['categories'] = [];
                        $attributes['imdb'.$chunk->imdb]['genres'] = [];
                    }
                    $attributes['imdb'.$chunk->imdb]['times_completed'] += $chunk->times_completed;
                    $attributes['imdb'.$chunk->imdb]['seeders'] += $chunk->seeders;
                    $attributes['imdb'.$chunk->imdb]['leechers'] += $chunk->leechers;
                    if (! array_key_exists($chunk->type, $attributes['imdb'.$chunk->imdb])) {
                        $attributes['imdb'.$chunk->imdb]['types'][$chunk->type] = $chunk->type;
                    }
                    if (! array_key_exists($chunk->category_id, $attributes['imdb'.$chunk->imdb])) {
                        $attributes['imdb'.$chunk->imdb]['categories'][$chunk->category_id] = $chunk->category_id;
                    }
                    $cache['imdb'.$chunk->imdb]['torrent'.$counts['imdb'.$chunk->imdb]] = [
                        'created_at' => $chunk->created_at,
                        'seeders' => $chunk->seeders,
                        'leechers' => $chunk->leechers,
                        'name' => $chunk->name,
                        'times_completed' => $chunk->times_completed,
                        'size' => $chunk->size,
                        'chunk' => $chunk,
                    ];
                    $counts['imdb'.$chunk->imdb]++;
                }
            }
            if (count($cache) > 0) {
                $torrents = $cache;
            } else {
                $torrents = null;
            }
        } elseif ($history == 1) {
            $prelauncher = $torrent->orderBy('torrents.sticky', 'desc')->orderBy('torrents.'.$sorting, $order)->pluck('id')->toArray();

            if (! is_array($prelauncher)) {
                $prelauncher = [];
            }

            $links = new Paginator($prelauncher, floor(count($prelauncher) / $qty) * $qty, $qty);

            $hungry = array_chunk($prelauncher, $qty);
            $fed = [];
            if (is_array($hungry) && array_key_exists($page, $hungry)) {
                $fed = $hungry[$page];
            }
            $torrents = Torrent::with(['user', 'category'])->withCount(['thanks', 'comments'])->whereIn('id', $fed)->orderBy($sorting, $order)->get();
        } else {
            $torrents = $torrent->orderBy('sticky', 'desc')->orderBy($sorting, $order)->paginate($qty);
        }
        if ($collection == 1 && is_array($torrents)) {
            $client = new \App\Services\MovieScrapper(config('api-keys.tmdb'), config('api-keys.tvdb'), config('api-keys.omdb'));
            foreach ($torrents as $k1 => $c) {
                foreach ($c as $k2 => $d) {
                    $movie = null;
                    if ($d['chunk']->category_id == 2) {
                        if ($d['chunk']->tmdb || $d['chunk']->tmdb != 0) {
                            $movie = $client->scrape('tv', null, $d['chunk']->tmdb);
                        } elseif ($d['chunk']->imdb && $d['chunk']->imdb != 0) {
                            $movie = $client->scrape('tv', 'tt'.$d['chunk']->imdb);
                        }
                    } else {
                        if ($d['chunk']->tmdb || $d['chunk']->tmdb != 0) {
                            $movie = $client->scrape('movie', null, $d['chunk']->tmdb);
                        } elseif ($d['chunk']->imdb && $d['chunk']->imdb != 0) {
                            $movie = $client->scrape('movie', 'tt'.$d['chunk']->imdb);
                        }
                    }
                    if ($movie) {
                        $d['chunk']->movie = $movie;
                    }
                }
            }
        }
        if ($request->has('view') && $request->input('view') == 'card') {
            if ($logger == null) {
                $logger = 'torrent.results_cards';
            }
            $client = new \App\Services\MovieScrapper(config('api-keys.tmdb'), config('api-keys.tvdb'), config('api-keys.omdb'));
            foreach ($torrents as $torrent) {
                $movie = null;
                if ($torrent->category_id == 2) {
                    if ($torrent->tmdb || $torrent->tmdb != 0) {
                        $movie = $client->scrape('tv', null, $torrent->tmdb);
                    } elseif ($torrent->imdb && $torrent->imdb != 0) {
                        $movie = $client->scrape('tv', 'tt'.$torrent->imdb);
                    }
                } else {
                    if ($torrent->tmdb || $torrent->tmdb != 0) {
                        $movie = $client->scrape('movie', null, $torrent->tmdb);
                    } elseif ($torrent->imdb && $torrent->imdb != 0) {
                        $movie = $client->scrape('movie', 'tt'.$torrent->imdb);
                    }
                }
                if ($movie) {
                    $torrent->movie = $movie;
                }
            }
        }
        if ($logger == null) {
            $logger = 'torrent.results';
        }

        return view($logger, [
            'torrents'           => $torrents,
            'user'               => $user,
            'personal_freeleech' => $personal_freeleech,
            'sorting'            => $sorting,
            'direction'          => $direction,
            'links'              => $links,
            'totals'             => $totals,
            'repository'         => $repository,
            'attributes'         => $attributes,
        ])->render();
    }

    /**
     * Anonymize A Torrent Media Info.
     *
     * @param $mediainfo
     *
     * @return array
     */
    private static function anonymizeMediainfo($mediainfo)
    {
        if ($mediainfo === null) {
            return;
        }
        $complete_name_i = strpos($mediainfo, 'Complete name');
        if ($complete_name_i !== false) {
            $path_i = strpos($mediainfo, ': ', $complete_name_i);
            if ($path_i !== false) {
                $path_i += 2;
                $end_i = strpos($mediainfo, "\n", $path_i);
                $path = substr($mediainfo, $path_i, $end_i - $path_i);
                $new_path = MediaInfo::stripPath($path);

                return substr_replace($mediainfo, $new_path, $path_i, strlen($path));
            }
        }

        return $mediainfo;
    }

    /**
     * Display The Torrent.
     *
     * @param $slug
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function torrent($slug, $id)
    {
        $torrent = Torrent::withAnyStatus()->with('comments')->findOrFail($id);
        $uploader = $torrent->user;
        $user = auth()->user();
        $freeleech_token = FreeleechToken::where('user_id', '=', $user->id)->where('torrent_id', '=', $torrent->id)->first();
        $personal_freeleech = PersonalFreeleech::where('user_id', '=', $user->id)->first();
        $comments = $torrent->comments()->latest()->paginate(6);
        $total_tips = BonTransactions::where('torrent_id', '=', $id)->sum('cost');
        $user_tips = BonTransactions::where('torrent_id', '=', $id)->where('sender', '=', auth()->user()->id)->sum('cost');
        $last_seed_activity = History::where('info_hash', '=', $torrent->info_hash)->where('seeder', '=', 1)->latest('updated_at')->first();

        $client = new \App\Services\MovieScrapper(config('api-keys.tmdb'), config('api-keys.tvdb'), config('api-keys.omdb'));
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

        if ($torrent->featured == 1) {
            $featured = FeaturedTorrent::where('torrent_id', '=', $id)->first();
        } else {
            $featured = null;
        }

        $general = null;
        $video = null;
        $settings = null;
        $audio = null;
        $general_crumbs = null;
        $text_crumbs = null;
        $subtitle = null;
        $view_crumbs = null;
        $video_crumbs = null;
        $settings = null;
        $audio_crumbs = null;
        $subtitle = null;
        $subtitle_crumbs = null;
        if ($torrent->mediainfo != null) {
            $parser = new MediaInfo();
            $parsed = $parser->parse($torrent->mediainfo);
            $view_crumbs = $parser->prepareViewCrumbs($parsed);
            $general = $parsed['general'];
            $general_crumbs = $view_crumbs['general'];
            $video = $parsed['video'];
            $video_crumbs = $view_crumbs['video'];
            $settings = ($parsed['video'] !== null && isset($parsed['video'][0]) && isset($parsed['video'][0]['encoding_settings'])) ? $parsed['video'][0]['encoding_settings'] : null;
            $audio = $parsed['audio'];
            $audio_crumbs = $view_crumbs['audio'];
            $subtitle = $parsed['text'];
            $text_crumbs = $view_crumbs['text'];
        }

        return view('torrent.torrent', [
            'torrent'            => $torrent,
            'comments'           => $comments,
            'user'               => $user,
            'personal_freeleech' => $personal_freeleech,
            'freeleech_token'    => $freeleech_token,
            'movie'              => $movie,
            'total_tips'         => $total_tips,
            'user_tips'          => $user_tips,
            'client'             => $client,
            'featured'           => $featured,
            'general'            => $general,
            'general_crumbs'     => $general_crumbs,
            'video_crumbs'       => $video_crumbs,
            'audio_crumbs'       => $audio_crumbs,
            'text_crumbs'        => $text_crumbs,
            'video'              => $video,
            'audio'              => $audio,
            'subtitle'           => $subtitle,
            'settings'           => $settings,
            'uploader'           => $uploader,
            'last_seed_activity' => $last_seed_activity,
        ]);
    }

    /**
     * Torrent Edit Form.
     *
     * @param $slug
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editForm($slug, $id)
    {
        $user = auth()->user();
        $torrent = Torrent::withAnyStatus()->findOrFail($id);

        abort_unless($user->group->is_modo || $user->id == $torrent->user_id, 403);

        return view('torrent.edit_torrent', [
            'categories' => Category::all()->sortBy('position'),
            'types'      => Type::all()->sortBy('position'),
            'torrent'    => $torrent,
        ]);
    }

    /**
     * Edit A Torrent.
     *
     * @param \Illuminate\Http\Request $request
     * @param $slug
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, $slug, $id)
    {
        $user = auth()->user();
        $torrent = Torrent::withAnyStatus()->findOrFail($id);

        abort_unless($user->group->is_modo || $user->id == $torrent->user_id, 403);
        $torrent->name = $request->input('name');
        $torrent->slug = str_slug($torrent->name);
        $torrent->description = $request->input('description');
        $torrent->category_id = $request->input('category_id');
        $torrent->imdb = $request->input('imdb');
        $torrent->tvdb = $request->input('tvdb');
        $torrent->tmdb = $request->input('tmdb');
        $torrent->mal = $request->input('mal');
        $torrent->type = $request->input('type');
        $torrent->mediainfo = $request->input('mediainfo');
        $torrent->anon = $request->input('anonymous');
        $torrent->stream = $request->input('stream');
        $torrent->sd = $request->input('sd');
        $torrent->internal = $request->input('internal');

        $v = validator($torrent->toArray(), [
            'name'        => 'required',
            'slug'        => 'required',
            'description' => 'required',
            'category_id' => 'required',
            'imdb'        => 'required|numeric',
            'tvdb'        => 'required|numeric',
            'tmdb'        => 'required|numeric',
            'mal'         => 'required|numeric',
            'type'        => 'required',
            'anon'        => 'required',
            'stream'      => 'required',
            'sd'          => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {
            $torrent->save();

            if ($user->group->is_modo) {
                // Activity Log
                \LogActivity::addToLog("Staff Member {$user->username} has edited torrent, ID: {$torrent->id} NAME: {$torrent->name} .");
            } else {
                // Activity Log
                \LogActivity::addToLog("Member {$user->username} has edited torrent, ID: {$torrent->id} NAME: {$torrent->name} .");
            }

            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->success('Successfully Edited!!!', 'Yay!', ['options']));
        }
    }

    /**
     * Delete A Torrent.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function deleteTorrent(Request $request)
    {
        $v = validator($request->all(), [
            'id'      => 'required|exists:torrents',
            'slug'    => 'required|exists:torrents',
            'message' => 'required|alpha_dash|min:1',
        ]);

        if ($v) {
            $user = auth()->user();
            $id = $request->id;
            $torrent = Torrent::withAnyStatus()->findOrFail($id);

            if ($user->group->is_modo || ($user->id == $torrent->user_id && Carbon::now()->lt($torrent->created_at->addDay()))) {
                $users = History::where('info_hash', '=', $torrent->info_hash)->get();
                foreach ($users as $pm) {
                    $pmuser = new PrivateMessage();
                    $pmuser->sender_id = 1;
                    $pmuser->receiver_id = $pm->user_id;
                    $pmuser->subject = 'Torrent Deleted!';
                    $pmuser->message = "[b]Attention:[/b] Torrent {$torrent->name} has been removed from our site. Our system shows that you were either the uploader, a seeder or a leecher on said torrent. We just wanted to let you know you can safley remove it from your client.
                                        [b]Removal Reason:[/b] {$request->message}
                                        [color=red][b]THIS IS AN AUTOMATED SYSTEM MESSAGE, PLEASE DO NOT REPLY![/b][/color]";
                    $pmuser->save();
                }

                if ($user->group->is_modo) {
                    // Activity Log
                    \LogActivity::addToLog("Staff Member {$user->username} has deleted torrent, ID: {$torrent->id} NAME: {$torrent->name} .");
                } else {
                    // Activity Log
                    \LogActivity::addToLog("Member {$user->username} has deleted torrent, ID: {$torrent->id} NAME: {$torrent->name} .");
                }

                // Reset Requests
                $torrentRequest = TorrentRequest::where('filled_hash', '=', $torrent->info_hash)->get();
                foreach ($torrentRequest as $req) {
                    if ($req) {
                        $req->filled_by = null;
                        $req->filled_when = null;
                        $req->filled_hash = null;
                        $req->approved_by = null;
                        $req->approved_when = null;
                        $req->save();
                    }
                }

                //Remove Torrent related info
                Peer::where('torrent_id', '=', $id)->delete();
                History::where('info_hash', '=', $torrent->info_hash)->delete();
                Warning::where('id', '=', $id)->delete();
                TorrentFile::where('torrent_id', '=', $id)->delete();
                if ($torrent->featured == 1) {
                    FeaturedTorrent::where('torrent_id', '=', $id)->delete();
                }
                Torrent::withAnyStatus()->where('id', '=', $id)->delete();

                return redirect('/torrents')
                    ->with($this->toastr->success('Torrent Has Been Deleted!', 'Yay!', ['options']));
            }
        } else {
            $errors = '';
            foreach ($v->errors()->all() as $error) {
                $errors .= $error."\n";
            }
            \Log::notice("Deletion of torrent failed due to: \n\n{$errors}");

            return redirect()->back()
                ->with($this->toastr->error('Unable to delete Torrent', 'Error', ['options']));
        }
    }

    /**
     * Display Peers Of A Torrent.
     *
     * @param $slug
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function peers($slug, $id)
    {
        $torrent = Torrent::withAnyStatus()->findOrFail($id);
        $peers = Peer::where('torrent_id', '=', $id)->latest('seeder')->paginate(25);

        return view('torrent.peers', ['torrent' => $torrent, 'peers' => $peers]);
    }

    /**
     * Display History Of A Torrent.
     *
     * @param $slug
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function history($slug, $id)
    {
        $torrent = Torrent::withAnyStatus()->findOrFail($id);
        $history = History::where('info_hash', '=', $torrent->info_hash)->latest()->paginate(25);

        return view('torrent.history', ['torrent' => $torrent, 'history' => $history]);
    }

    /**
     * Torrent Upload Form.
     *
     * @param $title
     * @param $imdb
     * @param $tmdb
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function uploadForm($title = '', $imdb = 0, $tmdb = 0)
    {
        $user = auth()->user();

        return view('torrent.upload', [
            'categories' => Category::all()->sortBy('position'),
            'types'      => Type::all()->sortBy('position'),
            'user'       => $user,
            'title'      => $title,
            'imdb'       => str_replace('tt', '', $imdb),
            'tmdb'       => $tmdb,
        ]);
    }

    /**
     * Upload A Torrent.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function upload(Request $request)
    {
        $user = auth()->user();
        $client = new \App\Services\MovieScrapper(config('api-keys.tmdb'), config('api-keys.tvdb'), config('api-keys.omdb'));
        $requestFile = $request->file('torrent');

        if ($request->hasFile('torrent') == false) {
            return view('torrent.upload', [
                'categories' => Category::all()->sortBy('position'),
                'types'      => Type::all()->sortBy('position'),
                'user'       => $user, ])
                ->with($this->toastr->error('You Must Provide A Torrent File For Upload!', 'Whoops!', ['options']));
        } elseif ($requestFile->getError() != 0 && $requestFile->getClientOriginalExtension() != 'torrent') {
            return view('torrent.upload', [
                'categories' => Category::all()->sortBy('position'),
                'types'      => Type::all()->sortBy('position'),
                'user'       => $user, ])
                ->with($this->toastr->error('A Error Has Occurred!', 'Whoops!', ['options']));
        }

        // Deplace and decode the torrent temporarily
        TorrentTools::moveAndDecode($requestFile);
        // Array decoded from torrent
        $decodedTorrent = TorrentTools::$decodedTorrent;
        // Tmp filename
        $fileName = TorrentTools::$fileName;
        // Torrent Info
        $info = Bencode::bdecode_getinfo(getcwd().'/files/torrents/'.$fileName, true);
        // Find the right category
        $category = Category::withCount('torrents')->findOrFail($request->input('category_id'));

        // Create the torrent (DB)
        $torrent = new Torrent();
        $torrent->name = $request->input('name');
        $torrent->slug = str_slug($torrent->name);
        $torrent->description = $request->input('description');
        $torrent->mediainfo = self::anonymizeMediainfo($request->input('mediainfo'));
        $torrent->info_hash = $info['info_hash'];
        $torrent->file_name = $fileName;
        $torrent->num_file = $info['info']['filecount'];
        $torrent->announce = $decodedTorrent['announce'];
        $torrent->size = $info['info']['size'];
        $torrent->nfo = ($request->hasFile('nfo')) ? TorrentTools::getNfo($request->file('nfo')) : '';
        $torrent->category_id = $category->id;
        $torrent->user_id = $user->id;
        $torrent->imdb = $request->input('imdb');
        $torrent->tvdb = $request->input('tvdb');
        $torrent->tmdb = $request->input('tmdb');
        $torrent->mal = $request->input('mal');
        $torrent->type = $request->input('type');
        $torrent->anon = $request->input('anonymous');
        $torrent->stream = $request->input('stream');
        $torrent->sd = $request->input('sd');
        $torrent->internal = $request->input('internal');
        $torrent->moderated_at = Carbon::now();
        $torrent->moderated_by = 1; //System ID

        // Validation
        $v = validator($torrent->toArray(), [
            'name'        => 'required',
            'slug'        => 'required',
            'description' => 'required',
            'info_hash'   => 'required|unique:torrents',
            'file_name'   => 'required',
            'num_file'    => 'required|numeric',
            'announce'    => 'required',
            'size'        => 'required',
            'category_id' => 'required',
            'user_id'     => 'required',
            'imdb'        => 'required|numeric',
            'tvdb'        => 'required|numeric',
            'tmdb'        => 'required|numeric',
            'mal'         => 'required|numeric',
            'type'        => 'required',
            'anon'        => 'required',
            'stream'      => 'required',
            'sd'          => 'required',
        ]);

        if ($v->fails()) {
            if (file_exists(getcwd().'/files/torrents/'.$fileName)) {
                unlink(getcwd().'/files/torrents/'.$fileName);
            }

            return redirect()->route('upload_form')
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']))->withInput();
        } else {
            // Save The Torrent
            $torrent->save();

            // Count and save the torrent number in this category
            $category->num_torrent = $category->torrents_count;
            $category->save();

            // Backup the files contained in the torrent
            $fileList = TorrentTools::getTorrentFiles($decodedTorrent);
            foreach ($fileList as $file) {
                $f = new TorrentFile();
                $f->name = $file['name'];
                $f->size = $file['size'];
                $f->torrent_id = $torrent->id;
                $f->save();
                unset($f);
            }

            // Torrent Tags System
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

            // check for trusted user and update torrent
            if ($user->group->is_trusted) {
                $appurl = config('app.url');
                $user = $torrent->user;
                $user_id = $user->id;
                $username = $user->username;
                $anon = $torrent->anon;

                // Announce To Shoutbox
                if ($anon == 0) {
                    $this->chat->systemMessage(
                        ":robot: [b][color=#fb9776]System[/color][/b] : User [url={$appurl}/".$username.'.'.$user_id.']'.$username."[/url] has uploaded [url={$appurl}/torrents/".$torrent->slug.'.'.$torrent->id.']'.$torrent->name.'[/url] grab it now! :slight_smile:'
                    );
                } else {
                    $this->chat->systemMessage(
                        ":robot: [b][color=#fb9776]System[/color][/b] : An anonymous user has uploaded [url={$appurl}/torrents/".$torrent->slug.'.'.$torrent->id.']'.$torrent->name.'[/url] grab it now! :slight_smile:'
                    );
                }

                TorrentHelper::approveHelper($torrent->slug, $torrent->id);

                \LogActivity::addToLog("Member {$user->username} has uploaded torrent, ID: {$torrent->id} NAME: {$torrent->name} . \nThis torrent has been auto approved by the System.");
            } else {
                \LogActivity::addToLog("Member {$user->username} has uploaded torrent, ID: {$torrent->id} NAME: {$torrent->name} . \nThis torrent is pending approval from satff.");
            }

            return redirect()->route('download_check', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->success('Your torrent file is ready to be downloaded and seeded!', 'Yay!', ['options']));
        }
    }

    /**
     * Download Check.
     *
     * @param $slug
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function downloadCheck($slug, $id)
    {
        $torrent = Torrent::withAnyStatus()->findOrFail($id);
        $user = auth()->user();

        return view('torrent.download_check', ['torrent' => $torrent, 'user' => $user]);
    }

    /**
     * Download A Torrent.
     *
     * @param $slug
     * @param $id
     * @param $rsskey
     *
     * @return TorrentFile
     */
    public function download($slug, $id, $rsskey = null)
    {
        $user = auth()->user();
        if (! $user && $rsskey) {
            $user = User::where('rsskey', '=', $rsskey)->firstOrFail();
        }

        $torrent = Torrent::withAnyStatus()->findOrFail($id);

        // User's ratio is too low
        if ($user->getRatio() < config('other.ratio')) {
            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->error('Your Ratio Is To Low To Download!!!', 'Whoops!', ['options']));
        }

        // User's download rights are revoked
        if ($user->can_download == 0 && $torrent->user_id != $user->id) {
            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->error('Your Download Rights Have Been Revoked!!!', 'Whoops!', ['options']));
        }

        // Torrent Status Is Rejected
        if ($torrent->isRejected()) {
            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->error('This Torrent Has Been Rejected By Staff', 'Whoops!', ['options']));
        }

        // Define the filename for the download
        $tmpFileName = $torrent->slug.'.torrent';

        // The torrent file exist ?
        if (! file_exists(getcwd().'/files/torrents/'.$torrent->file_name)) {
            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->error('Torrent File Not Found! Please Report This Torrent!', 'Error!', ['options']));
        } else {
            // Delete the last torrent tmp file
            if (file_exists(getcwd().'/files/tmp/'.$tmpFileName)) {
                unlink(getcwd().'/files/tmp/'.$tmpFileName);
            }
        }
        // Get the content of the torrent
        $dict = Bencode::bdecode(file_get_contents(getcwd().'/files/torrents/'.$torrent->file_name));
        if (auth()->check() || ($rsskey && $user)) {
            // Set the announce key and add the user passkey
            $dict['announce'] = route('announce', ['passkey' => $user->passkey]);
            // Remove Other announce url
            unset($dict['announce-list']);
        } else {
            return redirect('/login');
        }

        $fileToDownload = Bencode::bencode($dict);
        file_put_contents(getcwd().'/files/tmp/'.$tmpFileName, $fileToDownload);

        return response()->download(getcwd().'/files/tmp/'.$tmpFileName)->deleteFileAfterSend(true);
    }

    /**
     * Bump A Torrent.
     *
     * @param $slug
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function bumpTorrent($slug, $id)
    {
        $user = auth()->user();

        abort_unless($user->group->is_modo || $user->group->is_internal, 403);
        $torrent = Torrent::withAnyStatus()->findOrFail($id);
        $torrent->created_at = Carbon::now();
        $torrent->save();

        // Activity Log
        \LogActivity::addToLog('Staff Member '.$user->username." has bumped torrent, ID: {$torrent->id} NAME: {$torrent->name} .");

        // Announce To Chat
        $torrent_url = hrefTorrent($torrent);
        $profile_url = hrefProfile($user);

        $this->chat->systemMessage(
            ":robot: [b][color=#fb9776]System[/color][/b] : Attention, [url={$torrent_url}]{$torrent->name}[/url] has been bumped to top by [url={$profile_url}]{$user->username}[/url]! It could use more seeds!"
        );

        // Announce To IRC
        if (config('irc-bot.enabled') == true) {
            $appname = config('app.name');
            $bot = new IRCAnnounceBot();
            $bot->message('#announce', '['.$appname.'] User '.$user->username.' has bumped '.$torrent->name.' , it could use more seeds!');
            $bot->message('#announce', '[Category: '.$torrent->category->name.'] [Type: '.$torrent->type.'] [Size:'.$torrent->getSize().']');
            $bot->message('#announce', "[Link: $torrent_url]");
        }

        return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
            ->with($this->toastr->success('Torrent Has Been Bumped To Top Successfully!', 'Yay!', ['options']));
    }

    /**
     * Bookmark A Torrent.
     *
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function bookmark($id)
    {
        $torrent = Torrent::withAnyStatus()->findOrFail($id);

        if (auth()->user()->isBookmarked($torrent->id)) {
            return redirect()->back()
                ->with($this->toastr->error('Torrent has already been bookmarked.', 'Whoops!', ['options']));
        } else {
            auth()->user()->bookmarks()->attach($torrent->id);

            return redirect()->back()
                ->with($this->toastr->success('Torrent Has Been Bookmarked Successfully!', 'Yay!', ['options']));
        }
    }

    /**
     * Unbookmark A Torrent.
     *
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function unBookmark($id)
    {
        $torrent = Torrent::withAnyStatus()->findOrFail($id);
        auth()->user()->bookmarks()->detach($torrent->id);

        return redirect()->back()
            ->with($this->toastr->success('Torrent Has Been Unbookmarked Successfully!', 'Yay!', ['options']));
    }

    /**
     * Sticky A Torrent.
     *
     * @param $slug
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function sticky($slug, $id)
    {
        $user = auth()->user();

        abort_unless($user->group->is_modo || $user->group->is_internal, 403);
        $torrent = Torrent::withAnyStatus()->findOrFail($id);
        if ($torrent->sticky == 0) {
            $torrent->sticky = '1';
        } else {
            $torrent->sticky = '0';
        }
        $torrent->save();

        // Activity Log
        \LogActivity::addToLog('Staff Member '.auth()->user()->username." has stickied torrent, ID: {$torrent->id} NAME: {$torrent->name} .");

        return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
            ->with($this->toastr->success('Torrent Sticky Status Has Been Adjusted!', 'Yay!', ['options']));
    }

    /**
     * 100% Freeleech A Torrent.
     *
     * @param $slug
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function grantFL($slug, $id)
    {
        $user = auth()->user();

        abort_unless($user->group->is_modo || $user->group->is_internal, 403);
        $torrent = Torrent::withAnyStatus()->findOrFail($id);
        $torrent_url = hrefTorrent($torrent);

        if ($torrent->free == 0) {
            $torrent->free = '1';

            $this->chat->systemMessage(
                ":robot: [b][color=#fb9776]System[/color][/b] : Ladies and Gents, [url={$torrent_url}]{$torrent->name}[/url] has been granted 100% FreeLeech! Grab It While You Can! :fire:"
            );
        } else {
            $torrent->free = '0';

            $this->chat->systemMessage(
                ":robot: [b][color=#fb9776]System[/color][/b] : Ladies and Gents, [url={$torrent_url}]{$torrent->name}[/url] has been revoked of its 100% FreeLeech! :poop:"
            );
        }

        $torrent->save();

        // Activity Log
        \LogActivity::addToLog('Staff Member '.$user->username." has granted freeleech on torrent, ID: {$torrent->id} NAME: {$torrent->name} .");

        return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
            ->with($this->toastr->success('Torrent FL Has Been Adjusted!', 'Yay!', ['options']));
    }

    /**
     * Feature A Torrent.
     *
     * @param $slug
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function grantFeatured($slug, $id)
    {
        $user = auth()->user();

        abort_unless($user->group->is_modo || $user->group->is_internal, 403);
        $torrent = Torrent::withAnyStatus()->findOrFail($id);

        if ($torrent->featured == 0) {
            $torrent->free = '1';
            $torrent->doubleup = '1';
            $torrent->featured = '1';
            $torrent->save();

            $featured = new FeaturedTorrent();
            $featured->user_id = $user->id;
            $featured->torrent_id = $torrent->id;
            $featured->save();

            $torrent_url = hrefTorrent($torrent);
            $profile_url = hrefProfile($user);
            $this->chat->systemMessage(
                ":robot: [b][color=#fb9776]System[/color][/b] : Ladies and Gents, [url={$torrent_url}]{$torrent->name}[/url] has been added to the Featured Torrents Slider by [url={$profile_url}]{$user->username}[/url]! Grab It While You Can! :fire:"
            );

            // Activity Log
            \LogActivity::addToLog('Staff Member '.auth()->user()->username." has featured torrent, ID: {$torrent->id} NAME: {$torrent->name} .");

            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->success('Torrent Is Now Featured!', 'Yay!', ['options']));
        } else {
            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->error('Torrent Is Already Featured!', 'Whoops!', ['options']));
        }
    }

    /**
     * Double Upload A Torrent.
     *
     * @param $slug
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function grantDoubleUp($slug, $id)
    {
        $user = auth()->user();

        abort_unless($user->group->is_modo || $user->group->is_internal, 403);
        $torrent = Torrent::withAnyStatus()->findOrFail($id);
        $torrent_url = hrefTorrent($torrent);

        if ($torrent->doubleup == 0) {
            $torrent->doubleup = '1';

            $this->chat->systemMessage(
                ":robot: [b][color=#fb9776]System[/color][/b] : Ladies and Gents, [url={$torrent_url}]{$torrent->name}[/url] has been granted Double Upload! Grab It While You Can! :fire:"
            );
        } else {
            $torrent->doubleup = '0';
            $this->chat->systemMessage(
                ":robot: [b][color=#fb9776]System[/color][/b] : Ladies and Gents, [url={$torrent_url}]{$torrent->name}[/url] has been revoked of its Double Upload! :poop:"
            );
        }
        $torrent->save();

        // Activity Log
        \LogActivity::addToLog('Staff Member '.$user->username." has granted double upload on torrent, ID: {$torrent->id} NAME: {$torrent->name} .");

        return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
            ->with($this->toastr->success('Torrent DoubleUpload Has Been Adjusted!', 'Yay!', ['options']));
    }

    /**
     * Reseed Request A Torrent.
     *
     * @param $slug
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function reseedTorrent($slug, $id)
    {
        $appurl = config('app.url');
        $user = auth()->user();
        $torrent = Torrent::findOrFail($id);
        $reseed = History::where('info_hash', '=', $torrent->info_hash)->where('active', '=', 0)->get();

        if ($torrent->seeders <= 2) {
            // Send Notification
            foreach ($reseed as $r) {
                User::find($r->user_id)->notify(new NewReseedRequest($torrent));
            }

            $torrent_url = hrefTorrent($torrent);
            $profile_url = hrefProfile($user);

            $this->chat->systemMessage(
                ":robot: [b][color=#fb9776]System[/color][/b] : Ladies and Gents, a reseed request was just placed on [url={$torrent_url}]{$torrent->name}[/url] can you help out :question:"
            );

            // Activity Log
            \LogActivity::addToLog("Member {$user->username} has requested a reseed request on torrent, ID: {$torrent->id} NAME: {$torrent->name} .");

            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->success('A notification has been sent to all users that downloaded this torrent along with original uploader!', 'Yay!', ['options']));
        } else {
            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->error('This torrent doesnt meet the requirments for a reseed request.', 'Whoops!', ['options']));
        }
    }

    /**
     * Use Freeleech Token On A Torrent.
     *
     * @param $slug
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function freeleechToken($slug, $id)
    {
        $user = auth()->user();
        $torrent = Torrent::withAnyStatus()->findOrFail($id);
        $active_token = FreeleechToken::where('user_id', '=', $user->id)->where('torrent_id', '=', $torrent->id)->first();

        if ($user->fl_tokens >= 1 && ! $active_token) {
            $token = new FreeleechToken();
            $token->user_id = $user->id;
            $token->torrent_id = $torrent->id;
            $token->save();

            $user->fl_tokens -= '1';
            $user->save();

            // Activity Log
            \LogActivity::addToLog("Member {$user->username} has used a freeleech token on torrent, ID: {$torrent->id} NAME: {$torrent->name} .");

            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->success('You Have Successfully Activated A Freeleech Token For This Torrent!', 'Yay!', ['options']));
        } else {
            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->error('You Dont Have Enough Freeleech Tokens Or Already Have One Activated On This Torrent.', 'Whoops!', ['options']));
        }
    }
}
