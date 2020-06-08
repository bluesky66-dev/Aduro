<?php

namespace Tests\Todo\Feature\Http\Controllers\Staff;

use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Staff\PollController
 */
class PollControllerTest extends TestCase
{
    /**
     * @test
     */
    public function create_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = factory(\App\Models\User::class)->create();

        $response = $this->actingAs($user)->get(route('staff.polls.create'));

        $response->assertOk();
        $response->assertViewIs('Staff.poll.create');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = factory(\App\Models\User::class)->create();

        $response = $this->actingAs($user)->get(route('staff.polls.index'));

        $response->assertOk();
        $response->assertViewIs('Staff.poll.index');
        $response->assertViewHas('polls');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $poll = factory(\App\Models\Poll::class)->create();
        $user = factory(\App\Models\User::class)->create();

        $response = $this->actingAs($user)->get(route('staff.polls.show', ['id' => $poll->id]));

        $response->assertOk();
        $response->assertViewIs('Staff.poll.show');
        $response->assertViewHas('poll');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = factory(\App\Models\User::class)->create();

        $response = $this->actingAs($user)->post(route('staff.polls.store'), [
            // TODO: send request data
        ]);

        $response->assertRedirect(withSuccess('Your poll has been created.'));

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_validates_with_a_form_request()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Staff\PollController::class,
            'store',
            \App\Http\Requests\StorePoll::class
        );
    }

    // test cases...
}
