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

use App\Models\Language;
use Illuminate\Http\Request;
use Brian2694\Toastr\Toastr;

class LanguageController extends Controller
{
    /**
     * @var Toastr
     */
    private $toastr;

    /**
     * UserController Constructor.
     *
     * @param Toastr $toastr
     */
    public function __construct(Toastr $toastr)
    {
        $this->toastr = $toastr;
    }

    /**
     * Set locale if it's allowed.
     *
     * @param string                   $locale
     * @param \Illuminate\Http\Request $request
     **/
    private function setLocale($locale, $request)
    {
        // Check if is allowed and set default locale if not
        if (! Language::allowed($locale)) {
            $locale = config('app.locale');
        }

        if (auth()->check()) {
            auth()->user()->setAttribute('locale', $locale)->save();
        } else {
            $request->session()->put('locale', $locale);
        }
    }

    /**
     * Set locale and return home url.
     *
     * @param string                   $locale
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     **/
    public function home($locale, Request $request)
    {
        $this->setLocale($locale, $request);

        return redirect()->route('home')
            ->with($this->toastr->success('Language Changed!!', 'Yay!', ['options']));
    }

    /**
     * Set locale and return back.
     *
     * @param string                   $locale
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     **/
    public function back($locale, Request $request)
    {
        $this->setLocale($locale, $request);

        return redirect()->back()
            ->with($this->toastr->success('Language Changed!!', 'Yay!', ['options']));
    }
}
