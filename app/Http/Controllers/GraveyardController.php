<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://choosealicense.com/licenses/gpl-3.0/  GNU General Public License v3.0
 * @author     BluCrew
 */
 
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

use App\Torrent;
use App\History;
use App\Graveyard;
use Carbon\Carbon;

use \Toastr;

class GraveyardController extends Controller
{

    /**
     * Graveyard Manager
     *
     *
     * @access public
     * @return view graveyard.index
     */
    public function index()
    {
      $user = Auth::user();
      $dead = Torrent::where('seeders', '=', '0')->orderBy('leechers', 'desc')->paginate(50);
      $deadcount = Torrent::where('seeders', '=', '0')->count();
      $time = '2592000';

      return view('graveyard.index', compact('dead','deadcount','user','time'));
    }

    public function resurrect($id)
    {
      $user = Auth::user();
      $torrent = Torrent::findOrFail($id);
      $resurrected = Graveyard::where('torrent_id', '=', $torrent->id)->first();
      if ($resurrected) {
        return Redirect::route('graveyard')->with(Toastr::warning('Torrent Resurrection Failed! This torrent is already pending a resurrection.', 'Yay!', ['options']));
      }
      if ($user->id != $torrent->user_id) {
      $resurrection = Graveyard::create([
          'user_id' => $user->id,
          'torrent_id' => $torrent->id,
          'seedtime' => Request::get('seedtime')
      ]);
      return Redirect::route('graveyard')->with(Toastr::success('Torrent Resurrection Complete! You will be rewarded automatically once seedtime requirements are met.', 'Yay!', ['options']));
    } else {
      return Redirect::route('graveyard')->with(Toastr::error('Torrent Resurrection Failed! You cannot resurrect your own uploads.', 'Yay!', ['options']));
    }
  }

}
