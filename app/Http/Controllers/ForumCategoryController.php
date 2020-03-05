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

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;

class ForumCategoryController extends Controller
{
    /**
     * Show The Forum Category.
     *
     * @param \App\Models\Forum $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        // Find the topic
        $forum = Forum::findOrFail($id);

        // Total Forums Count
        $num_forums = Forum::count();
        // Total Posts Count
        $num_posts = Post::count();
        // Total Topics Count
        $num_topics = Topic::count();

        // Check if this is a category or forum
        if ($forum->parent_id != 0) {
            return redirect()->route('forums.show', ['id' => $forum->id]);
        }

        // Check if the user has permission to view the forum
        $category = Forum::findOrFail($forum->id);
        if ($category->getPermission()->show_forum != true) {
            return redirect()->route('forums.index')
                ->withErrors('You Do Not Have Access To This Category!');
        }

        // Fetch topics->posts in descending order
        $topics = $forum->sub_topics()->latest('pinned')->latest('last_reply_at')->latest()->paginate(25);

        return view('forum.category', [
            'forum'      => $forum,
            'topics'     => $topics,
            'category'   => $category,
            'num_posts'  => $num_posts,
            'num_forums' => $num_forums,
            'num_topics' => $num_topics,
        ]);
    }
}
