<?php

namespace Qafoo\SerPretty;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    public function testRunCliTool()
    {
        $inputFixture = __DIR__ . '/../../../_fixtures/Qafoo/SerPretty/integration_test.ser';
        $comparisonFixture = __DIR__ . '/../../../_fixtures/Qafoo/SerPretty/integration_test.txt';

        $output = `src/bin/ser-pretty "$inputFixture"`;

        $this->assertEquals(
            file_get_contents($comparisonFixture),
            $output
        );
    }
}
