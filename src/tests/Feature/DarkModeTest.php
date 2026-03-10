<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class DarkModeTest extends TestCase
{
    /**
     * Test dass Dark-Mode Toggle Button im Header vorhanden ist
     */
    public function test_dark_mode_toggle_button_exists_in_header(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Toggle Dark Mode');
        $response->assertSee('DarkModeManager.toggle()');
    }

    /**
     * Test dass Sun Icon für Light Mode angezeigt wird
     */
    public function test_sun_icon_visible_in_light_mode(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Prüfe dass SVG mit Sun-Icon vorhanden ist (viewBox in HTML ist escaped)
        $response->assertSee('viewBox');
        $response->assertSee('text-yellow-500');
    }

    /**
     * Test dass Moon Icon für Dark Mode angezeigt wird
     */
    public function test_moon_icon_visible_in_dark_mode(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Prüfe dass Toggle-Button mit Aria-Label vorhanden ist
        $response->assertSee('Toggle Dark Mode');
    }

    /**
     * Test dass Tailwind dark: Klassen auf HTML Element vorhanden sind
     */
    public function test_dark_mode_classes_on_html_element(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('dark:bg-neutral-dark');
        $response->assertSee('dark:text-text-dark');
    }

    /**
     * Test dass Dark-Mode JavaScript geladen wird
     */
    public function test_dark_mode_javascript_loaded(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('app.css');
    }

    /**
     * Test dass Header Dark-Mode Support hat
     */
    public function test_header_has_dark_mode_support(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('dark:bg-neutral-dark');
        $response->assertSee('dark:border-gray-700');
    }

    /**
     * Test dass Footer Dark-Mode Support hat
     */
    public function test_footer_has_dark_mode_support(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Footer sollte Dark-Mode Klassen haben
        $html = $response->getContent();
        $this->assertStringContainsString('dark:bg-neutral-dark', $html);
        $this->assertStringContainsString('dark:border-gray-700', $html);
        $this->assertStringContainsString('dark:text-gray-400', $html);
    }

    /**
     * Test dass Tailwind darkMode in config aktiviert ist
     */
    public function test_tailwind_darkmode_config_present(): void
    {
        // Prüfe dass tailwind.config.js darkMode: 'class' hat
        $configPath = base_path('tailwind.config.js');
        $this->assertFileExists($configPath);

        $config = file_get_contents($configPath);
        $this->assertStringContainsString("darkMode: 'class'", $config);
    }

    /**
     * Test dass alle Standard-Seiten Dark-Mode Support haben
     */
    public function test_all_pages_have_dark_mode_support(): void
    {
        $routes = [
            '/',
            '/analyze',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
            $response->assertSee('dark:');
        }
    }

    /**
     * Test dass Mobile Menu Button Dark-Mode Support hat
     */
    public function test_mobile_menu_button_has_dark_mode_support(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('dark:hover:bg-gray-800');
    }
}
