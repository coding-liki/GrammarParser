<?php
declare(strict_types=1);

namespace GrammarParser;

use Codeception\Test\Unit;
use CodingLiki\GrammarParser\Calculators\FirstSetCalculator;
use CodingLiki\GrammarParser\GrammarRuleParser;
use CodingLiki\GrammarParser\RulesHelper;

class FirstSetCalculatorTest extends Unit
{
    /**
     * @dataProvider calculateProvider
     * @param string $rulesScript
     * @param string $ruleName
     * @param string[] $expectedSet
     */
    public function testCalculate(string $rulesScript, string $ruleName, array $expectedSet): void
    {
        $rules = GrammarRuleParser::parse($rulesScript);
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
                'A: a;',
                'A',
                [
                    'a'
                ]
            ],
            'one rule and first for terminal' => [
                'A: a;',
                'a',
                [
                    'a'
                ]
            ],
            'two simple rules' => [
                '
                A: a;
                A: b c;
                ',
                'A',
                ['a', 'b']
            ],
            'five complex rules' => [
                '
                A: B | C;
                B: k;
                C: o;
                A: a;
                A: b c; 
                ',
                'A',
                ['k', 'o', 'a', 'b']
            ],
            'five complex rules with same firsts' => [
                '
                A: B | C;
                B: k;
                C: a n;
                A: a;
                A: b c;
                ',
                'A',
                ['k', 'a', 'b']
            ],
            'subrule test' => [
                '
                A: 
                    a b c
                    | (D | k);
                D: j;
                ',
                'A',
                ['a','j','k']
            ],
            '*  test' => [
                '
                A: 
                    a* b c | D;
                D: j;
                ',
                'A',
                ['a','b','j']
            ],
            '? test' => [
                '
                A: p? c;',
                'A',
                ['p', 'c']
            ],
            '? with * test' => [
                '
                A: p? c* d;',
                'A',
                ['p', 'c', 'd']
            ],
            '? with + test' => [
                '
                A: p? c+ d;',
                'A',
                ['p', 'c']
            ]
        ];
    }
}
