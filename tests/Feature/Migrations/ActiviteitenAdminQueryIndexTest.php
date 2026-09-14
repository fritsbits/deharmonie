<?php

namespace Tests\Feature\Migrations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ActiviteitenAdminQueryIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_activiteiten_are_indexed_for_the_default_admin_date_sort(): void
    {
        $this->assertTrue(Schema::hasIndex('activiteiten', ['datum']));
    }
}
