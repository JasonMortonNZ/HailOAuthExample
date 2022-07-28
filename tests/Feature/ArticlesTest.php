<?php

namespace Tests\Feature;

use App\Hail;
use Tests\TestCase;
use Mockery\MockInterface;

class ArticlesTest extends TestCase
{

    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_index_displays_connect_to_hail_if_not_authorised()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeText('Connect to Hail');
    }

    public function test_index_does_not_display_connect_to_hail_if_authorised()
    {
        // Mock being authorised
        cache()->put('hail_token', 'abc123', 5);
        $this->instance(
            Hail::class,
            \Mockery::mock(Hail::class, function (MockInterface $mock) {
                $mock->shouldReceive('isAuthorised')->once()->andReturn(true);
                $mock->shouldReceive('getArticles')->once()->andReturn(json_decode(json_encode([])));
            })
        );

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSeeText('Connect to Hail');

        // Remove cache key
        cache()->delete('hail_token');
    }

    public function test_index_displays_articles_when_authorised()
    {
        $json = [
            ['title' => 'Foo', 'lead' => 'Some text...', 'url' => 'http://link.url', 'hero_image' => ['file_1000_url' => 'http://img1.url']],
            ['title' => 'Bar', 'lead' => 'Some text...', 'url' => 'http://link.url', 'hero_image' => ['file_1000_url' => 'http://img2.url']]
        ];

        // Mock being authorised
        cache()->put('hail_token', 'abc123', 5);
        $this->instance(
            Hail::class,
            \Mockery::mock(Hail::class, function (MockInterface $mock) use ($json) {
                $mock->shouldReceive('isAuthorised')->once()->andReturn(true);
                $mock->shouldReceive('getArticles')->once()->andReturn(json_decode(json_encode($json)));
            })
        );

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSeeText('Connect to Hail');
        $response->assertSeeText($json[0]['title']);
        $response->assertSeeText($json[1]['title']);

        // Remove cache key
        cache()->delete('hail_token');
    }
}
