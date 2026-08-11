<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_root_redirects_guests_to_login(): void
    {
        // La raíz redirige al catálogo, que a su vez exige autenticación.
        $this->get('/')->assertRedirect();
    }
}
