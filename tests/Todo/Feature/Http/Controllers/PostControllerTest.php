<?php

namespace Tests\Todo\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PostController
 */
class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function post_delete_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $post = Post::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('forum_post_delete', ['postId' => $post->postId]));

        $response->assertRedirect(withSuccess('This Post Is Now Deleted!'));

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function post_edit_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $post = Post::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('forum_post_edit', ['postId' => $post->postId]), [
            // TODO: send request data
        ]);

        $response->assertRedirect(withSuccess('Post Successfully Edited!'));

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function post_edit_form_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $post = Post::factory()->create();
        $post = Post::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('forum_post_edit_form', ['id' => $post->id, 'postId' => $post->postId]));

        $response->assertOk();
        $response->assertViewIs('forum.post_edit');
        $response->assertViewHas('topic');
        $response->assertViewHas('forum');
        $response->assertViewHas('post');
        $response->assertViewHas('category');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function reply_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $post = Post::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('forum_reply', ['id' => $post->id]), [
            // TODO: send request data
        ]);

        $response->assertRedirect(withErrors('You Cannot Reply To This Topic!'));

        // TODO: perform additional assertions
    }

    // test cases...
}
