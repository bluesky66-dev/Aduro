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

use App\Category;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

use \Toastr;

class CategoryController extends Controller
{

    /**
     * Get the categories
     *
     *
     */
    public function index()
    {
        $categories = Category::all();

        return view('Staff.category.index', ['categories' => $categories]);
    }

    /**
     * Add a category
     *
     *
     */
    public function add()
    {
        if (Request::isMethod('post')) {
            $category = new Category();
            $category->name = Request::get('name');
            $category->slug = str_slug($category->name);
            $category->icon = Request::get('icon');
            $v = Validator::make($category->toArray(), $category->rules);
            if ($v->fails()) {
                Toastr::error('Something Went Wrong!', 'Error', ['options']);
            } else {
                $category->save();
                return Redirect::route('staff_category_index')->with(Toastr::info('Category Sucessfully Added', 'Yay!', ['options']));
            }
        }
        return view('Staff.category.add');
    }

    /**
     * Edit a category
     *
     *
     */
    public function edit($slug, $id)
    {
        $category = Category::findOrFail($id);
        if (Request::isMethod('post')) {
            $category->name = Request::get('name');
            $category->slug = str_slug($category->name);
            $category->icon = Request::get('icon');
            $v = Validator::make($category->toArray(), $category->rules);
            if ($v->fails()) {
                Toastr::error('Something Went Wrong!', 'Error', ['options']);
            } else {
                $category->save();
                return Redirect::route('staff_category_index')->with(Toastr::info('Category Sucessfully Modified', 'Yay!', ['options']));
            }
        }

        return view('Staff.category.edit', ['category' => $category]);
    }

    /**
     * Delete a category
     *
     *
     */
    public function delete($slug, $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return Redirect::route('staff_category_index')->with(Toastr::warning('Category Sucessfully Deleted', 'Yay!', ['options']));
    }
}
