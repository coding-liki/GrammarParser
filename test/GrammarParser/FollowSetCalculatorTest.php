<?php
declare(strict_types=1);

namespace GrammarParser;

use CodingLiki\GrammarParser\Calculators\FollowSetCalculator;
use CodingLiki\GrammarParser\GrammarRuleParser;
use CodingLiki\GrammarParser\Rule;
use CodingLiki\GrammarParser\RulesHelper;
use PHPUnit\Framework\TestCase;

class FollowSetCalculatorTest extends TestCase
{

    /**
     * @dataProvider calculateProvider
     * @param Rule[] $rules
     * @param string $name
     * @param string[] $expectedSet
     */
    public function testCalculate(array $rules, string $name,  array $expectedSet)
    {
        if(!empty($rules)) {
            array_unshift($rules, RulesHelper::buildRootRule($rules));
        }
        $calculator = new FollowSetCalculator($rules);
        $set = $calculator->calculate($name);

        self::assertEquals($expectedSet, $set);
    }

    public function calculateProvider(): array
    {
        return [
            'void' => [
                [],
                'a',
                []
            ],
            'caclTestCase' => [
                GrammarRuleParser::parse(file_get_contents(__DIR__.'/../../grammar/calculator.grr')),
                'atom',
                [
                    'MUL',
                    'DIV',
                    'PLUS',
                    'MINUS',
                    '$',
                    'R_P',
                ]
            ]
        ];
    }
}