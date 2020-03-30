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

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MediaLanguage;
use Illuminate\Http\Request;

class MediaLanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $media_languages = MediaLanguage::all()->sortBy('name');

        return view('Staff.media_language.index', ['media_languages' => $media_languages]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('Staff.media_language.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $media_language = new MediaLanguage();
        $media_language->name = $request->input('name');
        $media_language->code = $request->input('code');

        $v = validator($media_language->toArray(), [
            'name' => 'required|unique:media_languages',
            'code' => 'required|unique:media_languages',
        ]);

        if ($v->fails()) {
            return redirect()->route('staff.media_languages.index')
                ->withErrors($v->errors());
        }
        $media_language->save();

        return redirect()->route('staff.media_languages.index')
            ->withSuccess('Media Language Successfully Added');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\MediaLanguage $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $media_language = MediaLanguage::findOrFail($id);

        return view('Staff.media_language.edit', ['media_language' => $media_language]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \App\Models\MediaLanguage $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $media_language = MediaLanguage::findOrFail($id);
        $media_language->name = $request->input('name');
        $media_language->code = $request->input('code');

        $v = validator($media_language->toArray(), [
            'name' => 'required',
            'code' => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->route('staff.media_languages.index')
                ->withErrors($v->errors());
        }
        $media_language->save();

        return redirect()->route('staff.media_languages.index')
            ->withSuccess('Media Language Successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\MediaLanguage $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $media_language = MediaLanguage::findOrFail($id);
        $media_language->delete();

        return redirect()->route('staff.media_languages.index')
            ->withSuccess('Media Language Has Successfully Been Deleted');
    }
}
