<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_the_about_page_request(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
    }

    public function test_the_about_view_is_rendered(): void
    {
        $view=$this->withViewErrors([])->view('about');

        $view->assertSee('This is the about page');
    }
}
