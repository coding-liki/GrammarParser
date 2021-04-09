<?php
declare(strict_types=1);

namespace CodingLiki\GrammarParser;

use Codeception\Test\Unit;
class RulesHelperTest extends Unit
{

    public function testCleanCache(): void
    {
        RulesHelper::cleanCache();
        self::assertEmpty(RulesHelper::$rulesByName);
    }

}
