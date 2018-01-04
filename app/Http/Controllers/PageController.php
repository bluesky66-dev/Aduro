<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://choosealicense.com/licenses/gpl-3.0/  GNU General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use App\Page;
use App\User;

class PageController extends Controller
{

    /**
     * Displays the requested page
     *
     *
     */
    public function page($slug, $id)
    {
        $page = Page::findOrFail($id);

        return view('page.page', ['page' => $page]);
    }

    /**
     * Staff Page
     *
     *
     */
    public function staff()
    {
        $staff = DB::table('users')->leftJoin('groups','users.group_id','=','groups.id')->select('users.id','users.title','users.username','groups.name','groups.color','groups.icon')->where('groups.is_admin','=','1')->orWhere('groups.is_modo','=','1')->get();

        return view('page.staff', ['staff' => $staff]);
    }

    /**
     * About Us Page
     *
     *
     */
    public function about()
    {
        return view('page.aboutus');
    }

}
