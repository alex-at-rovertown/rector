<?php

declare (strict_types=1);
namespace RectorPrefix20211003\Doctrine\Inflector\Rules\NorwegianBokmal;

use RectorPrefix20211003\Doctrine\Inflector\GenericLanguageInflectorFactory;
use RectorPrefix20211003\Doctrine\Inflector\Rules\Ruleset;
final class InflectorFactory extends \RectorPrefix20211003\Doctrine\Inflector\GenericLanguageInflectorFactory
{
    protected function getSingularRuleset() : \RectorPrefix20211003\Doctrine\Inflector\Rules\Ruleset
    {
        return \RectorPrefix20211003\Doctrine\Inflector\Rules\NorwegianBokmal\Rules::getSingularRuleset();
    }
    protected function getPluralRuleset() : \RectorPrefix20211003\Doctrine\Inflector\Rules\Ruleset
    {
        return \RectorPrefix20211003\Doctrine\Inflector\Rules\NorwegianBokmal\Rules::getPluralRuleset();
    }
}
