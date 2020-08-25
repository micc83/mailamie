<?php declare(strict_types=1);

namespace Tests\Unit;

use Mailamie\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /** @test */
    public function it_allows_to_retrieve_a_value_with_dot_notation(): void
    {
        $config = new Config(['level1' => ['level2' => 'value']]);

        $this->assertEquals('value', $config->get('level1.level2'));
    }

    /** @test */
    public function it_allows_to_retrieve_a_value_from_default_or_alt_values(): void
    {
        $config = new Config([
            'a' => 'a_default_value',
            'b' => 'b_default_value',
            'c' => 'c_default_value'
        ], [
            'a' => 'alt_value',
            'b' => null
        ]);

        $this->assertEquals('alt_value', $config->get('a'));
        $this->assertNull($config->get('b'));
        $this->assertEquals('c_default_value', $config->get('c'));
    }
}
