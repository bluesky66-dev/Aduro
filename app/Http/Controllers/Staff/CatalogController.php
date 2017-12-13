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

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

use App\User;
use App\Catalog;
use App\CatalogTorrent;

use \Toastr;

class CatalogController extends Controller
{
    /**
     * Catalog Group System
     *
     *
     */
    public function getCatalogs()
    {
        $catalogs = Catalog::orderBy('name', 'ASC')->get();
        return view('Staff.catalog.catalogs', ['catalogs' => $catalogs]);
    }

    //Add New Catalog
    public function postCatalog()
    {
        $v = Validator::make(Request::all(), [
            'catalog' => 'required|min:3|max:20|regex:/^[(a-zA-Z\-)]+$/u'
        ]);
        $catalog = Catalog::where('name', '=', Request::get('catalog'))->first();
        if ($catalog) {
            return redirect()->route('catalogs')->with(['fail' => 'Catalog ' . $catalog->name . ' is already in database']);
        }
        $catalog = new Catalog();
        $catalog->name = Request::get('catalog');
        $catalog->slug = str_slug(Request::get('catalog'));
        $catalog->save();
        return redirect()->route('getCatalog')->with(Toastr::success('Catalog ' . Request::get('catalog') . ' has been successfully added', 'Success!', ['options']));
    }

    //Delete Catalog
    public function deleteCatalog($catalog_id)
    {
        $catalog = Catalog::findOrFail($catalog_id);
        if (!$catalog) {
            return redirect()->route('getCatalog')->with(Toastr::error('That Catalog Is Not In Our DB!', 'Whoops!', ['options']));
        }
        $catalog->delete();
        return redirect()->route('getCatalog')->with(Toastr::success('Catalog ' . $catalog->name . ' has been successfully deleted', 'Success!', ['options']));
    }

    //Edit Catalog
    public function editCatalog($catalog_id)
    {
        $v = Validator::make(Request::all(), [
            'catalog' => 'required|min:3|max:20|regex:/^[(a-zA-Z\-)]+$/u'
        ]);
        $catalog = Catalog::findOrFail($catalog_id);
        if (!$catalog) {
            return redirect()->route('getCatalog')->with(Toastr::error('Catalog ' . Request::get('catalog') . ' is not in our DB!', 'Whoops!', ['options']));
        }
        $catalog->name = Request::get('catalog');
        $catalog->save();
        return redirect()->route('getCatalog')->with(Toastr::success('Catalog ' . Request::get('catalog') . ' has been successfully edited', 'Success!', ['options']));
    }

    /**
     * Catalog Torrent System
     *
     *
     */
    public function getCatalogTorrent()
    {
        $catalogs = Catalog::orderBy('name', 'ASC')->get();
        return view('Staff.catalog.catalog_torrent')->with('catalogs', $catalogs);
    }

    //Add New Catalog Torrent
    public function postCatalogTorrent()
    {
        // Find the right catalog
        $catalog = Catalog::findOrFail(Request::get('catalog_id'));
        $v = Validator::make(Request::all(), [
            'imdb' => 'required|numeric',
            'tvdb' => 'required|numeric',
            'catalog_id' => 'required|numeric|exists:catalog_id'
        ]);
        $torrent = CatalogTorrent::where('imdb', '=', Request::get('imdb'))->first();
        if ($torrent) {
            return redirect()->route('getCatalogTorrent')->with(Toastr::error('IMDB# ' . $torrent->imdb . ' is already in database', 'Fail!', ['options']));
        }
        $torrent = new CatalogTorrent();
        $torrent->imdb = Request::get('imdb');
        $torrent->catalog_id = Request::get('catalog_id');
        $torrent->save();
        // Count and save the torrent number in this catalog
        $catalog->num_torrent = CatalogTorrent::where('catalog_id', '=', $catalog->id)->count();
        $catalog->save();
        return redirect()->route('getCatalogTorrent')->with(Toastr::success('IMDB# ' . Request::get('imdb') . ' has been successfully added', 'Success!', ['options']));
    }

    // Get Catalogs Records
    public function getCatalogRecords($catalog_id)
    {
        $catalogs = Catalog::findOrFail($catalog_id);
        $records = CatalogTorrent::where('catalog_id', '=', $catalog_id)->orderBy('imdb', 'DESC')->get();
        return view('Staff.catalog.catalog_records', ['catalog' => $catalogs, 'records' => $records]);
    }
}
