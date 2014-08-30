<?php

namespace Qafoo\SerPretty\PreProcessor;
use Qafoo\SerPretty\PreProcessor;

class DoctrineAnnotationCachePreProcessor extends PreProcessor
{
    public function process($inputString)
    {
        if (preg_match('(^<\?php return unserialize\(\'(.*)\'\);\s?$)s', $inputString, $matches)) {
            $inputString = $matches[1];
        }
        return $inputString;
    }
}
