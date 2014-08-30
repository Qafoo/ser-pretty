<?php

namespace Qafoo\SerPretty\PreProcessor;

class DoctrineAnnotationCachePreProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessesIfAvailable()
    {
        $annotationCacheContent = $this->loadFixtureFrom('doctrine_annotation.cache.php');
        $preProcessor = new DoctrineAnnotationCachePreProcessor();

        $processed = $preProcessor->process($annotationCacheContent);

        $this->assertEquals(
            'a:0:{}',
            $processed
        );
    }

    /**
     * @param string $file
     * @return string
     */
    private function loadFixtureFrom($file)
    {
        return file_get_contents(
            sprintf(
                __DIR__ . '/../../../../_fixtures/Qafoo/SerPretty/PreProcessor/%s',
                $file
            )
        );
    }
}
