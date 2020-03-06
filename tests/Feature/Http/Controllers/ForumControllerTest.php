<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Forum;
use App\Models\Permission;
use App\Models\User;
use GroupsTableSeeder;
use Tests\TestCase;
use UsersTableSeeder;

/**
 * @see \App\Http\Controllers\ForumController
 */
class ForumControllerTest extends TestCase
{
    /** @test */
    public function index_returns_an_ok_response()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(GroupsTableSeeder::class);

        $user = factory(User::class)->create();

        $this->actingAs($user)->get(route('forums.index'))
            ->assertOk()
            ->assertViewIs('forum.index')
            ->assertViewHas('categories')
            ->assertViewHas('num_posts')
            ->assertViewHas('num_forums')
            ->assertViewHas('num_topics');
    }

    /** @test */
    public function latest_posts_returns_an_ok_response()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(GroupsTableSeeder::class);

        $user = factory(User::class)->create();

        $this->actingAs($user)->get(route('forum_latest_posts'))
            ->assertOk()
            ->assertViewIs('forum.latest_posts')
            ->assertViewHas('results')
            ->assertViewHas('user')
            ->assertViewHas('num_posts')
            ->assertViewHas('num_forums')
            ->assertViewHas('num_topics');
    }

    /** @test */
    public function latest_topics_returns_an_ok_response()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(GroupsTableSeeder::class);

        $user = factory(User::class)->create();

        $this->actingAs($user)->get(route('forum_latest_topics'))
            ->assertOk()
            ->assertViewIs('forum.latest_topics')
            ->assertViewHas('results')
            ->assertViewHas('user')
            ->assertViewHas('num_posts')
            ->assertViewHas('num_forums')
            ->assertViewHas('num_topics');
    }

    /** @test */
    public function search_topics_returns_an_ok_response()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(GroupsTableSeeder::class);

        $user = factory(User::class)->create();

        $this->actingAs($user)->get(route('forum_search_form'))
            ->assertOk()
            ->assertViewIs('forum.results_topics')
            ->assertViewHas('categories')
            ->assertViewHas('results')
            ->assertViewHas('user')
            ->assertViewHas('name')
            ->assertViewHas('body')
            ->assertViewHas('num_posts')
            ->assertViewHas('num_forums')
            ->assertViewHas('num_topics')
            ->assertViewHas('params');
    }

    /** @test */
    public function search_posts_returns_an_ok_response()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(GroupsTableSeeder::class);

        $user = factory(User::class)->create();

        $this->actingAs($user)->call('GET', route('forum_search_form', ['body' => 1]))
            ->assertOk()
            ->assertViewIs('forum.results_posts')
            ->assertViewHas('categories')
            ->assertViewHas('results')
            ->assertViewHas('user')
            ->assertViewHas('name')
            ->assertViewHas('body')
            ->assertViewHas('num_posts')
            ->assertViewHas('num_forums')
            ->assertViewHas('num_topics')
            ->assertViewHas('params');
    }

    /** @test */
    public function show_category_returns_an_ok_response()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(GroupsTableSeeder::class);

        // This Forum has a parent Forum, which makes it a "Forum Category".

        $parentForum = factory(Forum::class)->create();

        $permissions = factory(Permission::class)->create([
            'forum_id'   => $parentForum->id,
            'show_forum' => true,
        ]);

        $forum = factory(Forum::class)->create([
            'parent_id' => $parentForum->id,
        ]);

        // Note that permissions are not necessary on the parent Forum, and
        // the Group ID is instead derived from the child ("Forum Category")
        // permissions.

        $user = factory(User::class)->create([
            'group_id' => $permissions['group_id'],
        ]);

        $this->actingAs($user)->get(route('forums.show', ['id' => $forum->id]))
            ->assertViewIs('forum.display')
            ->assertViewHas('forum')
            ->assertViewHas('topics')
            ->assertViewHas('category')
            ->assertViewHas('num_posts')
            ->assertViewHas('num_forums')
            ->assertViewHas('num_topics');
    }

    /** @test */
    public function show_forum_returns_an_ok_response()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(GroupsTableSeeder::class);

        // This Forum does not have a parent, which makes it a proper Forum
        // (and not a "Forum Category").

        $forum = factory(Forum::class)->create([
            'parent_id' => 0,
        ]);

        $permissions = factory(Permission::class)->create([
            'forum_id'   => $forum->id,
            'show_forum' => true,
        ]);

        $user = factory(User::class)->create([
            'group_id' => $permissions['group_id'],
        ]);

        $this->actingAs($user)->get(route('forums.show', ['id' => $forum->id]))
            ->assertRedirect(route('forums.categories.show', ['id' => $forum->id]));
    }

    /** @test */
    public function subscriptions_returns_an_ok_response()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(GroupsTableSeeder::class);

        $user = factory(User::class)->create();

        $this->actingAs($user)->get(route('forum_subscriptions'))
            ->assertOk()
            ->assertViewIs('forum.subscriptions')
            ->assertViewHas('results')
            ->assertViewHas('user')
            ->assertViewHas('name')
            ->assertViewHas('body')
            ->assertViewHas('num_posts')
            ->assertViewHas('num_forums')
            ->assertViewHas('num_topics')
            ->assertViewHas('params')
            ->assertViewHas('forum_neos')
            ->assertViewHas('topic_neos');
    }
}
