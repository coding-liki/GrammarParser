<?php
declare(strict_types=1);

namespace GrammarParser\Token;

use Codeception\Test\Unit;
use CodingLiki\GrammarParser\Token\GrammarTokenParser;
use CodingLiki\GrammarParser\Token\TokenType;

class GrammarTokenParserTest extends Unit
{

    /**
     * @dataProvider parseProvider
     * @param string $tokensScript
     * @param TokenType[] $types
     */
    public function testParse(string $tokensScript, array $types): void
    {
        self::assertEquals($types, GrammarTokenParser::parse($tokensScript));
    }

    public function parseProvider(): array
    {
        return [
            'void' => [
                '',
                []
            ],
            '1 type' => [
                'A: a',
                [
                    new TokenType('A', 'a')
                ]
            ],
            '3 types' => [
                'A: a
                B: b
                C: [a-z]*',
                [
                    new TokenType('A', 'a'),
                    new TokenType('B', 'b'),
                    new TokenType('C', '[a-z]*'),
                ]
            ],
            '3 types and comments' => [
                '
                // comment
                # comment
                A: a
                B: b // comment
                C: [a-z]*',
                [
                    new TokenType('A', 'a'),
                    new TokenType('B', 'b'),
                    new TokenType('C', '[a-z]*'),
                ]
            ]
        ];
    }


}
