<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Http\Controllers\Staff;

use App\Models\Tag;
use Brian2694\Toastr\Toastr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    /**
     * @var Toastr
     */
    private $toastr;

    /**
     * RssController Constructor.
     *
     * @param Toastr $toastr
     */
    public function __construct(Toastr $toastr)
    {
        $this->toastr = $toastr;
    }

    /**
     * Get All Tags.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $tags = Tag::all()->sortBy('name');

        return view('Staff.tag.index', ['tags' => $tags]);
    }

    /**
     * Tag Add Form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addForm()
    {
        return view('Staff.tag.add');
    }

    /**
     * Add A Tag.
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\RedirectResponse
     */
    public function add(Request $request)
    {
        $tag = new Tag();
        $tag->name = $request->input('name');
        $tag->slug = str_slug($tag->name);

        $v = validator($tag->toArray(), [
            'name' => 'required|unique:tags',
            'slug' => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->route('staff_tag_index')
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {
            $tag->save();

            return redirect()->route('staff_tag_index')
                ->with($this->toastr->success('Tag Successfully Added', 'Yay!', ['options']));
        }
    }

    /**
     * Tag Edit Form.
     *
     * @param $slug
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editForm($slug, $id)
    {
        $tag = Tag::findOrFail($id);

        return view('Staff.tag.edit', ['tag' => $tag]);
    }

    /**
     * Edit A Tag.
     *
     * @param \Illuminate\Http\Request $request
     * @param $slug
     * @param $id
     * @return Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, $slug, $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->name = $request->input('name');
        $tag->slug = str_slug($tag->name);

        $v = validator($type->toArray(), [
            'name' => 'required',
            'slug' => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->route('staff_tag_index')
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {
            $tag->save();

            return redirect()->route('staff_tag_index')
                ->with($this->toastr->success('Tag Successfully Modified', 'Yay!', ['options']));
        }
    }
}
