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

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{

    /**
     * Settings Admin
     *
     *
     * @access public
     * @return view::make Admin.settings.index
     */
    public function index()
    {
        return view('Staff.settings.index');
    }
}
