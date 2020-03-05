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
use App\Models\Forum;
use App\Models\Group;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    /**
     * Display All Forums.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $categories = Forum::where('parent_id', '=', 0)->get()->sortBy('position');

        return view('Staff.forum.index', ['categories' => $categories]);
    }

    /**
     * Show Forum Create Form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $categories = Forum::where('parent_id', '=', 0)->get();
        $groups = Group::all();

        return view('Staff.forum.create', ['categories' => $categories, 'groups' => $groups]);
    }

    /**
     * Store A New Forum.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $groups = Group::all();

        $forum = new Forum();
        $forum->name = $request->input('title');
        $forum->position = $request->input('position');
        $forum->slug = Str::slug($request->input('title'));
        $forum->description = $request->input('description');
        $forum->parent_id = $request->input('parent_id');
        $forum->save();

        // Permissions
        foreach ($groups as $k => $group) {
            $perm = Permission::whereRaw('forum_id = ? AND group_id = ?', [$forum->id, $group->id])->first();
            if ($perm == null) {
                $perm = new Permission();
            }
            $perm->forum_id = $forum->id;
            $perm->group_id = $group->id;
            if (array_key_exists($group->id, $request->input('permissions'))) {
                $perm->show_forum = isset($request->input('permissions')[$group->id]['show_forum']);
                $perm->read_topic = isset($request->input('permissions')[$group->id]['read_topic']);
                $perm->reply_topic = isset($request->input('permissions')[$group->id]['reply_topic']);
                $perm->start_topic = isset($request->input('permissions')[$group->id]['start_topic']);
            } else {
                $perm->show_forum = false;
                $perm->read_topic = false;
                $perm->reply_topic = false;
                $perm->start_topic = false;
            }
            $perm->save();
        }

        return redirect()->route('staff.forums.index')
            ->withSuccess('Forum has been created successfully');
    }

    /**
     * Forum Edit Form.
     *
     * @param \App\Models\Forum $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $forum = Forum::findOrFail($id);
        $categories = Forum::where('parent_id', '=', 0)->get();
        $groups = Group::all();

        return view('Staff.forum.edit', [
            'categories' => $categories,
            'groups'     => $groups,
            'forum'      => $forum,
        ]);
    }

    /**
     * Edit A Forum.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Forum        $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $forum = Forum::findOrFail($id);
        $groups = Group::all();

        $forum->name = $request->input('title');
        $forum->position = $request->input('position');
        $forum->slug = Str::slug($request->input('title'));
        $forum->description = $request->input('description');
        $forum->parent_id = $request->input('forum_type') == 'category' ? 0 : $request->input('parent_id');
        $forum->save();

        // Permissions
        foreach ($groups as $k => $group) {
            $perm = Permission::whereRaw('forum_id = ? AND group_id = ?', [$forum->id, $group->id])->first();
            if ($perm == null) {
                $perm = new Permission();
            }
            $perm->forum_id = $forum->id;
            $perm->group_id = $group->id;
            if (array_key_exists($group->id, $request->input('permissions'))) {
                $perm->show_forum = isset($request->input('permissions')[$group->id]['show_forum']);
                $perm->read_topic = isset($request->input('permissions')[$group->id]['read_topic']);
                $perm->reply_topic = isset($request->input('permissions')[$group->id]['reply_topic']);
                $perm->start_topic = isset($request->input('permissions')[$group->id]['start_topic']);
            } else {
                $perm->show_forum = false;
                $perm->read_topic = false;
                $perm->reply_topic = false;
                $perm->start_topic = false;
            }
            $perm->save();
        }

        return redirect()->route('staff.forums.index')
            ->withSuccess('Forum has been edited successfully');
    }

    /**
     * Delete A Forum.
     *
     * @param \App\Models\Forum $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Forum to delete
        $forum = Forum::findOrFail($id);

        $permissions = Permission::where('forum_id', '=', $forum->id)->get();
        foreach ($permissions as $p) {
            $p->delete();
        }
        unset($permissions);

        if ($forum->parent_id == 0) {
            $category = $forum;
            $permissions = Permission::where('forum_id', '=', $category->id)->get();
            foreach ($permissions as $p) {
                $p->delete();
            }

            $forums = $category->getForumsInCategory();
            foreach ($forums as $forum) {
                $permissions = Permission::where('forum_id', '=', $forum->id)->get();
                foreach ($permissions as $p) {
                    $p->delete();
                }

                foreach ($forum->topics as $t) {
                    foreach ($t->posts as $p) {
                        $p->delete();
                    }
                    $t->delete();
                }
                $forum->delete();
            }
            $category->delete();
        } else {
            $permissions = Permission::where('forum_id', '=', $forum->id)->get();
            foreach ($permissions as $p) {
                $p->delete();
            }
            foreach ($forum->topics as $t) {
                foreach ($t->posts as $p) {
                    $p->delete();
                }
                $t->delete();
            }
            $forum->delete();
        }

        return redirect()->route('staff.forums.index')
            ->withSuccess('Forum has been deleted successfully');
    }
}
