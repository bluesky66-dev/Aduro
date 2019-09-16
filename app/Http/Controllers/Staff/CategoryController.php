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

namespace App\Http\Controllers\Staff;

use Image;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display All Categories.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $categories = Category::all()->sortBy('position');

        return view('Staff.category.index', ['categories' => $categories]);
    }

    /**
     * Show Form For Creating A New Category.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('Staff.category.create');
    }

    /**
     * Store A Category.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $category = new Category();
        $category->name = $request->input('name');
        $category->slug = Str::slug($category->name);
        $category->position = $request->input('position');
        $category->icon = $request->input('icon');
        $category->movie_meta = $request->input('movie_meta');
        $category->tv_meta = $request->input('tv_meta');
        $category->game_meta = $request->input('game_meta');
        $category->music_meta = $request->input('music_meta');
        $category->no_meta = $request->input('no_meta');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'category-'.uniqid().'.'.$image->getClientOriginalExtension();
            $path = public_path('/files/img/'.$filename);
            Image::make($image->getRealPath())->fit(40, 40)->encode('png', 100)->save($path);
            $category->image = $filename;
        } else {
            $category->image = null;
        }

        $v = validator($category->toArray(), [
            'name'          => 'required',
            'slug'          => 'required',
            'position'      => 'required',
            'icon'          => 'required',
            'movie_meta'    => 'required',
            'tv_meta'       => 'required',
            'game_meta'     => 'required',
            'music_meta'    => 'required',
            'no_meta'       => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->route('staff.categories.index')
                ->withErrors($v->errors());
        } else {
            $category->save();

            return redirect()->route('staff.categories.index')
                ->withSuccess('Category Successfully Added');
        }
    }

    /**
     * Category Edit Form.
     *
     * @param $slug
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($slug, $id)
    {
        $category = Category::findOrFail($id);

        return view('Staff.category.edit', ['category' => $category]);
    }

    /**
     * Update A Category.
     *
     * @param \Illuminate\Http\Request $request
     * @param $slug
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $slug, $id)
    {
        $category = Category::findOrFail($id);
        $category->name = $request->input('name');
        $category->slug = Str::slug($category->name);
        $category->position = $request->input('position');
        $category->icon = $request->input('icon');
        $category->movie_meta = $request->input('movie_meta');
        $category->tv_meta = $request->input('tv_meta');
        $category->game_meta = $request->input('game_meta');
        $category->music_meta = $request->input('music_meta');
        $category->no_meta = $request->input('no_meta');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'category-'.uniqid().'.'.$image->getClientOriginalExtension();
            $path = public_path('/files/img/'.$filename);
            Image::make($image->getRealPath())->fit(40, 40)->encode('png', 100)->save($path);
            $category->image = $filename;
        }

        $v = validator($category->toArray(), [
            'name'          => 'required',
            'slug'          => 'required',
            'position'      => 'required',
            'icon'          => 'required',
            'movie_meta'    => 'required',
            'tv_meta'       => 'required',
            'game_meta'     => 'required',
            'music_meta'    => 'required',
            'no_meta'       => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->route('staff.categories.index')
                ->withErrors($v->errors());
        } else {
            $category->save();

            return redirect()->route('staff.categories.index')
                ->withSuccess('Category Successfully Modified');
        }
    }

    /**
     * Destroy A Category.
     *
     * @param $id
     * @param $slug
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function destroy($slug, $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('staff.categories.index')
            ->withSuccess('Category Successfully Deleted');
    }
}
