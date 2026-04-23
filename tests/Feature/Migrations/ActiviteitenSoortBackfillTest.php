<?php

namespace Tests\Feature\Migrations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ActiviteitenSoortBackfillTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_migration_schema(): void
    {
        $this->assertTrue(Schema::hasColumn('activiteiten', 'soort'));
        $this->assertTrue(Schema::hasColumn('activiteiten', 'categorie'));
        $this->assertFalse(Schema::hasColumn('activiteiten', 'template_id'));
        $this->assertFalse(Schema::hasTable('activiteit_templates'));
    }
}
