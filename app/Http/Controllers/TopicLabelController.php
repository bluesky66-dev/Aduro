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
 *
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers;

use App\Models\Topic;

class TopicLabelController extends Controller
{
    /**
     * Apply/Remove Approved Label.
     *
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        $topic = Topic::findOrFail($id);
        if ($topic->approved == 0) {
            $topic->approved = '1';
        } else {
            $topic->approved = '0';
        }
        $topic->save();

        return redirect()->route('forum_topic', ['id' => $topic->id])
            ->withInfo('Label Change Has Been Applied');
    }

    /**
     * Apply/Remove Denied Label.
     *
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function deny($id)
    {
        $topic = Topic::findOrFail($id);
        if ($topic->denied == 0) {
            $topic->denied = '1';
        } else {
            $topic->denied = '0';
        }
        $topic->save();

        return redirect()->route('forum_topic', ['id' => $topic->id])
            ->withInfo('Label Change Has Been Applied');
    }

    /**
     * Apply/Remove Solved Label.
     *
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function solve($id)
    {
        $topic = Topic::findOrFail($id);
        if ($topic->solved == 0) {
            $topic->solved = '1';
        } else {
            $topic->solved = '0';
        }
        $topic->save();

        return redirect()->route('forum_topic', ['id' => $topic->id])
            ->withInfo('Label Change Has Been Applied');
    }

    /**
     * Apply/Remove Invalid Label.
     *
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function invalid($id)
    {
        $topic = Topic::findOrFail($id);
        if ($topic->invalid == 0) {
            $topic->invalid = '1';
        } else {
            $topic->invalid = '0';
        }
        $topic->save();

        return redirect()->route('forum_topic', ['id' => $topic->id])
            ->withInfo('Label Change Has Been Applied');
    }

    /**
     * Apply/Remove Bug Label.
     *
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function bug($id)
    {
        $topic = Topic::findOrFail($id);
        if ($topic->bug == 0) {
            $topic->bug = '1';
        } else {
            $topic->bug = '0';
        }
        $topic->save();

        return redirect()->route('forum_topic', ['id' => $topic->id])
            ->withInfo('Label Change Has Been Applied');
    }

    /**
     * Apply/Remove Suggestion Label.
     *
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function suggest($id)
    {
        $topic = Topic::findOrFail($id);
        if ($topic->suggestion == 0) {
            $topic->suggestion = '1';
        } else {
            $topic->suggestion = '0';
        }
        $topic->save();

        return redirect()->route('forum_topic', ['id' => $topic->id])
            ->withInfo('Label Change Has Been Applied');
    }

    /**
     * Apply/Remove Implemented Label.
     *
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function implement($id)
    {
        $topic = Topic::findOrFail($id);
        if ($topic->implemented == 0) {
            $topic->implemented = '1';
        } else {
            $topic->implemented = '0';
        }
        $topic->save();

        return redirect()->route('forum_topic', ['id' => $topic->id])
            ->withInfo('Label Change Has Been Applied');
    }
}
