<?php
declare(strict_types=1);

namespace GrammarParser;

use PHPUnit\Framework\TestCase;

class GrammarRuleParserTest extends TestCase
{
    public function testRulesParser()
    {
        $rules = GrammarRuleParser::parse('
        A: 
            a and b
            |d and c
            |f
            ;
        ');

        self::assertIsArray($rules);
        self::assertContainsOnlyInstancesOf(Rule::class, $rules);
        self::assertCount(3, $rules);
    }

    /**
     * @dataProvider parseProvider
     * @param string $rulesScript
     * @param array $expectedRules
     */
    public function testParse(string $rulesScript, array $expectedRules){
        $rules = GrammarRuleParser::parse($rulesScript);

        self::assertEquals($expectedRules, $rules);
    }

    public function parseProvider(): array
    {
        return [
            'void' => [
                '',
                []
            ],
            '2 simple rules' => [
                '
                test: first;
                another: second and first;',
                [
                    new Rule('test', ['first']),
                    new Rule('another', ['second', 'and', 'first']),
                ]
            ],
            '2 complex rules' => [
                'test: r PLUS ten
                | r MINUS ten
                | ten;
                
                ten: L | K;
                ',
                [
                    new Rule('test', ['r', 'PLUS', 'ten']),
                    new Rule('test', ['r', 'MINUS', 'ten']),
                    new Rule('test', ['ten']),
                    new Rule('ten', ['L']),
                    new Rule('ten', ['K']),

                ]
            ]
        ];
    }


}