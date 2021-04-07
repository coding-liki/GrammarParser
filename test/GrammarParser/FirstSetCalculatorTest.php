<?php
declare(strict_types=1);

namespace GrammarParser;

use CodingLiki\GrammarParser\Calculators\FirstSetCalculator;
use CodingLiki\GrammarParser\GrammarRuleParser;
use CodingLiki\GrammarParser\Rule;
use CodingLiki\GrammarParser\RulesHelper;
use PHPUnit\Framework\TestCase;

class FirstSetCalculatorTest extends TestCase
{
    /**
     * @dataProvider calculateProvider
     * @param string $ruleName
     * @param array $rules
     * @param array $expectedSet
     */
    public function testCalculate(string $ruleName, array $rules, array $expectedSet)
    {
        if(!empty($rules)) {
            array_unshift($rules, RulesHelper::buildRootRule($rules));
        }
        $calculator = new FirstSetCalculator($rules);

        $set = $calculator->calculate($ruleName);

        self::assertEquals($expectedSet, $set);
    }

    public function calculateProvider(): array
    {
        return [
            'one simple rule' => [
                'A',
                [
                    new Rule('A', ['a']),
                ],
                [
                    'a'
                ]
            ],
            'one rule and first for terminal' => [
                'a',
                [
                    new Rule('A', ['a']),
                ],
                [
                    'a'
                ]
            ],
            'two simple rules' => [
                'A',
                GrammarRuleParser::parse('
                A: a;
                A: b c;
                '),
                ['a', 'b']
            ],
            'five complex rules' => [
                'A',
                GrammarRuleParser::parse('
                A: B | C;
                B: k;
                C: o;
                A: a;
                A: b c; 
                '),
                ['k', 'o', 'a', 'b']
            ],
            'five complex rules with same firsts' => [
                'A',
                GrammarRuleParser::parse('
                A: B | C;
                B: k;
                C: a n;
                A: a;
                A: b c;
                '),
                ['k', 'a', 'b']
            ],
        ];
    }
}
