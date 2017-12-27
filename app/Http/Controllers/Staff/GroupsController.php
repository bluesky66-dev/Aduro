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

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Group;

use \Toastr;

class GroupsController extends Controller
{

    /**
     * Groups Admin
     *
     *
     * @access public
     * @return view::make Admin.groups.index
     */
    public function index()
    {
        $groups = Group::all()->sortBy('position');

        return view('Staff.groups.index', ['groups' => $groups]);
    }

    /**
     * Add Group
     *
     *
     */
    public function add(Request $request)
    {
        if ($request->isMethod('POST')) {
            $group = new Group();
            $group->name = $request->get('group_name');
            $group->slug = str_slug($request->get('group_name'));
            $group->position = $request->get('position');
            $group->color = $request->get('group_color');
            $group->icon = $request->get('group_icon');
            $group->effect = $request->get('group_effect');
            $group->is_modo = $request->get('group_modo');
            $group->is_admin = $request->get('group_admin');
            $group->is_trusted = $request->get('group_trusted');
            $group->is_immune = $request->get('group_immune');
            $group->is_freeleech = $request->get('group_freeleech');
            $group->autogroup = $request->get('autogroup');
            $v = Validator::make($group->toArray(), $group->rules);
            if ($v->fails()) {
                Toastr::error('Something Went Wrong!', 'Error', ['options']);
            } else {
                $group->save();
                return Redirect::route('Staff.groups.index')->with(Toastr::success('Group Was Created Successfully!', 'Yay!', ['options']));
            }
        }
        return view('Staff.groups.add');
    }

    /**
     * Edit Group
     *
     *
     */
    public function edit(Request $request, $group, $id)
    {
        $group = Group::findOrFail($id);
        if ($request->isMethod('POST')) {
            $group->name = $request->get('group_name');
            $group->slug = str_slug($group->name);
            $group->position = $request->get('position');
            $group->color = $request->get('group_color');
            $group->icon = $request->get('group_icon');
            $group->effect = $request->get('group_effect');
            $group->is_modo = $request->get('group_modo');
            $group->is_admin = $request->get('group_admin');
            $group->is_trusted = $request->get('group_trusted');
            $group->is_immune = $request->get('group_immune');
            $group->is_freeleech = $request->get('group_freeleech');
            $group->autogroup = $request->get('autogroup');
            $v = Validator::make($group->toArray(), $group->rules);
            if ($v->fails()) {
                Toastr::error('Something Went Wrong!', 'Error', ['options']);
            } else {
                $group->save();
                return Redirect::route('Staff.groups.index')->with(Toastr::success('Group Was Updated Successfully!', 'Yay!', ['options']));
            }
        }
        return view('Staff.groups.edit', ['group' => $group]);
    }
}
