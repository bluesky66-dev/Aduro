<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Auth\ForgotUsernameController
 */
class ForgotUsernameControllerTest extends TestCase
{


    /**
     * @test
     */
    public function send_username_reminder_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');



$response = $this->post(route('username.email'), [
            // TODO: send request data
        ]);

$response->assertRedirect(withErrors($v->errors()));

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function show_forgot_username_form_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');



$response = $this->get(route('username.request'));

$response->assertOk();
$response->assertViewIs('auth.username');

        // TODO: perform additional assertions
    }

    // test cases...
}
