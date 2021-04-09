<?php
declare(strict_types=1);

namespace GrammarParser;

use CodingLiki\GrammarParser\Calculators\FollowSetCalculator;
use CodingLiki\GrammarParser\GrammarRuleParser;
use CodingLiki\GrammarParser\RulesHelper;
use PHPUnit\Framework\TestCase;

class FollowSetCalculatorTest extends TestCase
{

    /**
     * @dataProvider calculateProvider
     * @param string $rulesScript
     * @param string $name
     * @param string[] $expectedSet
     */
    public function testCalculate(string $rulesScript, string $name, array $expectedSet): void
    {
        $rules = GrammarRuleParser::parse($rulesScript);
        if (!empty($rules)) {
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
                '',
                'a',
                []
            ],

            'calcTestCase' => [
                '
                expression: 
                    mulExpression (PLUS mulExpression)*
                    | mulExpression (MINUS mulExpression)*;
                
                mulExpression: 
                    atom (MUL atom)*
                    | atom (DIV atom)*;
                
                atom: INT_NUM | FLOAT_NUM
                | L_P expression R_P;
                ',
                'atom',
                [
                    'MUL',
                    'PLUS',
                    '$',
                    'R_P',
                    'MINUS',
                    'DIV',
                ]
            ],
            'another calc test' => [
                '
                expression: 
                    mulExpression plusMinusAction*;
                
                plusMinusAction: (PLUS|MINUS) mulExpression;
                
                mulExpression: 
                    atom mulDivAction*;
                
                mulDivAction: (MUL|DIV) atom;
                
                atom: INT_NUM | FLOAT_NUM | L_P expression R_P;
                ',
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