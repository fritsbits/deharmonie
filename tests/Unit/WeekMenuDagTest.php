<?php

namespace Tests\Unit;

use App\Models\WeekMenuDag;
use Tests\TestCase;

class WeekMenuDagTest extends TestCase
{
    protected function tearDown(): void
    {
        app()->setLocale('nl');
        parent::tearDown();
    }

    public function test_main_accessor_returns_nl_by_default(): void
    {
        app()->setLocale('nl');
        $dag = new WeekMenuDag(['main_nl' => 'Stoofvlees', 'main_fr' => 'Carbonnade']);

        $this->assertEquals('Stoofvlees', $dag->main);
    }

    public function test_main_accessor_returns_fr(): void
    {
        app()->setLocale('fr');
        $dag = new WeekMenuDag(['main_nl' => 'Stoofvlees', 'main_fr' => 'Carbonnade']);

        $this->assertEquals('Carbonnade', $dag->main);
    }

    public function test_event_label_accessor_returns_nl(): void
    {
        app()->setLocale('nl');
        $dag = new WeekMenuDag(['event_label_nl' => 'Paasmenu', 'event_label_fr' => 'Menu de Pâques']);

        $this->assertEquals('Paasmenu', $dag->event_label);
    }

    public function test_event_label_accessor_returns_fr(): void
    {
        app()->setLocale('fr');
        $dag = new WeekMenuDag(['event_label_nl' => 'Paasmenu', 'event_label_fr' => 'Menu de Pâques']);

        $this->assertEquals('Menu de Pâques', $dag->event_label);
    }

    public function test_courses_for_locale_returns_flat_nl_array(): void
    {
        app()->setLocale('nl');
        $dag = new WeekMenuDag;
        $dag->courses = [
            ['nl' => 'Scampi met look', 'fr' => "Scampi à l'Ail"],
            ['nl' => 'Eendenborst', 'fr' => 'Magret de Canard'],
        ];

        $this->assertEquals(['Scampi met look', 'Eendenborst'], $dag->coursesForLocale);
    }

    public function test_courses_for_locale_returns_flat_fr_array(): void
    {
        app()->setLocale('fr');
        $dag = new WeekMenuDag;
        $dag->courses = [
            ['nl' => 'Scampi met look', 'fr' => "Scampi à l'Ail"],
            ['nl' => 'Eendenborst', 'fr' => 'Magret de Canard'],
        ];

        $this->assertEquals(["Scampi à l'Ail", 'Magret de Canard'], $dag->coursesForLocale);
    }

    public function test_courses_for_locale_returns_empty_array_when_null(): void
    {
        $dag = new WeekMenuDag(['courses' => null]);

        $this->assertEquals([], $dag->coursesForLocale);
    }

    public function test_courses_for_locale_returns_empty_array_when_empty(): void
    {
        $dag = new WeekMenuDag(['courses' => []]);

        $this->assertEquals([], $dag->coursesForLocale);
    }

    public function test_type_is_gesloten_when_closed(): void
    {
        $dag = new WeekMenuDag(['closed' => true, 'special_event' => false]);

        $this->assertEquals('Gesloten', $dag->type);
    }

    public function test_type_is_speciaal_for_special_event(): void
    {
        $dag = new WeekMenuDag(['closed' => false, 'special_event' => true]);

        $this->assertEquals('Speciaal', $dag->type);
    }

    public function test_type_is_normaal_for_open_day(): void
    {
        $dag = new WeekMenuDag(['closed' => false, 'special_event' => false]);

        $this->assertEquals('Normaal', $dag->type);
    }
}
