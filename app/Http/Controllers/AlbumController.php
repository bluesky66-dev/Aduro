<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Clients\OmdbClient;
use App\Album;
use \Toastr;

class AlbumsController extends Controller
{
    /**
     * AlbumController Constructor
     *
     * @param OmdbClient $client
     */
    public function __construct(OmdbClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get All Albums
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $albums = Album::with('artwork')->get();

        return view('gallery.index')->with('albums',$albums);
    }

    /**
     * Get A Album
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAlbum($id)
    {
        $album = Album::with('artwork')->find($id);
        $albums = Album::with('artwork')->get();

        return view('gallery.album', ['album' => $album, 'albums' => $albums]);
    }

    /**
     * Album Add Form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addForm()
    {
        return view('gallery.createalbum');
    }

    /**
     * Add A Album
     *
     * @param Request $request
     * @return Illuminate\Http\RedirectResponse
     */
    public function add(Request $request)
    {
        $imdb = starts_with($request->input('imdb'), 'tt') ? $request->input('imdb') : 'tt'.$request->input('imdb');
        $omdb = $this->client->find($imdb);

        if($omdb === null || $omdb === false) {
            return redirect()->route('create_album_form')
                ->with(Toastr::error('Bad IMDB Request!', 'Whoops!', ['options']));
        };

        $album = new Album();
        $album->user_id = auth()->user()->id;
        $album->name = $omdb['Title'] . ' (' . $omdb['Year'] . ')';
        $album->description = $request->input('description');
        $album->imdb = $request->input('imdb');

        if ($request->hasFile('cover_image') && $request->file('cover_image')->getError() == 0) {
            $image = $request->file('cover_image');
            if (in_array($image->getClientOriginalExtension(), ['png', 'PNG', 'tiff', 'TIFF']) && preg_match('#image/*#', $image->getMimeType())) {
                $filename = 'album-cover_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = public_path('/files/img/' . $filename);
                Image::make($image->getRealPath())->fit(400, 225)->encode('png', 100)->save($path);
                $album->cover_image = $filename;
            } else {
                // Image null or wrong format
                $album->cover_image = null;
            }
        } else {
            // Error on the image so null
            $album->cover_image = null;
        }

        $v = validator($album->toArray(), [
            'user_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'imdb' => 'required',
            'cover_image' => 'required|image'
        ]);

        if ($v->fails()) {
            // Delete the cover_image because the validation failed
            if (file_exists($request->file('cover_image')->move(getcwd() . '/files/img/' . $album->image))) {
                unlink($request->file('cover_image')->move(getcwd() . '/files/img/' . $album->image));
            }

            return redirect()->route('create_album_form')
                ->withErrors($v)
                ->withInput()
                ->with(Toastr::error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {
            $album->save();

            return redirect()->route('show_album', ['id' => $album->id])
                ->with(Toastr::success('Your article has successfully published!', 'Yay!', ['options']));
        }
    }

    /**
     * Delete A Article
     *
     * @param $id
     * @return Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $album = Album::findOrFail($id);
        $album->delete();

        return redirect()->route('/')
            ->with(Toastr::success('Album has successfully been deleted', 'Yay!', ['options']));
    }
}