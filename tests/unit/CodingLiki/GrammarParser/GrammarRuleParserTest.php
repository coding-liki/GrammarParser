<?php
declare(strict_types=1);

namespace CodingLiki\GrammarParser;

use Codeception\Test\Unit;
use CodingLiki\GrammarParser\Rule\Rule;
use CodingLiki\GrammarParser\Rule\RulePart;

class GrammarRuleParserTest extends Unit
{

    /**
     * @dataProvider parseProvider
     * @param string $rulesScript
     * @param Rule[] $rules
     */
    public function testParse(string $rulesScript, array $rules): void
    {
        self::assertEquals($rules, GrammarRuleParser::parse($rulesScript));
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
                // test comment
                //another test comment
                test: first; // first rule
                another: second and first ; # shiftcomment;
                //comment in the end',
                [
                    new Rule('test', [new RulePart('first', RulePart::TYPE_NORMAL)], 'first'),
                    new Rule('another', [
                        new RulePart('second', RulePart::TYPE_NORMAL),
                        new RulePart('and', RulePart::TYPE_NORMAL),
                        new RulePart('first', RulePart::TYPE_NORMAL)
                    ], 'second and first'),
                ]
            ],
            '2 complex rules' => [
                'test: r PLUS ten
                | r MINUS ten
                | ten;
            
                ten: L | K;
                ',
                [
                    new Rule('test', [
                        new RulePart('r', RulePart::TYPE_NORMAL),
                        new RulePart('PLUS', RulePart::TYPE_NORMAL),
                        new RulePart('ten', RulePart::TYPE_NORMAL)
                    ], 'r PLUS ten'),
                    new Rule('test', [
                        new RulePart('r', RulePart::TYPE_NORMAL),
                        new RulePart('MINUS', RulePart::TYPE_NORMAL),
                        new RulePart('ten', RulePart::TYPE_NORMAL)
                    ], 'r MINUS ten'),
                    new Rule('test', [new RulePart('ten', RulePart::TYPE_NORMAL)], 'ten'),
                    new Rule('ten', [new RulePart('L', RulePart::TYPE_NORMAL)], 'L'),
                    new Rule('ten', [new RulePart('K', RulePart::TYPE_NORMAL)], 'K'),
                ]
            ],
            '1 rule with sub rule' => [
                'test: a (b | g) d;',
                [
                    new Rule('test', [
                        new RulePart('a', RulePart::TYPE_NORMAL),
                        new RulePart('test_subrule_1', RulePart::TYPE_NORMAL),
                        new RulePart('d', RulePart::TYPE_NORMAL)
                    ], 'a test_subrule_1 d'),
                    new Rule('test_subrule_1', [new RulePart('b', RulePart::TYPE_NORMAL)], 'b'),
                    new Rule('test_subrule_1', [new RulePart('g', RulePart::TYPE_NORMAL)], 'g'),
                ]
            ],
            '1 rule with may be once sub rule' => [
                'test: a (b | g)? d;',
                [
                    new Rule('test', [
                        new RulePart('a', RulePart::TYPE_NORMAL),
                        new RulePart('test_subrule_1', RulePart::TYPE_NORMAL),
                        new RulePart('d', RulePart::TYPE_NORMAL)
                    ], 'a test_subrule_1 d'),
                    new Rule('test',
                        [new RulePart('a', RulePart::TYPE_NORMAL), new RulePart('d', RulePart::TYPE_NORMAL)], 'a d'),
                    new Rule('test_subrule_1', [new RulePart('b', RulePart::TYPE_NORMAL)], 'b'),
                    new Rule('test_subrule_1', [new RulePart('g', RulePart::TYPE_NORMAL)], 'g'),
                ]
            ],
            '1 rule with must be once or more sub rule' => [
                'test: a (b | g)+ d;',
                [
                    new Rule('test', [
                        new RulePart('a', RulePart::TYPE_NORMAL),
                        new RulePart('test_subrule_1', RulePart::TYPE_MUST_BE_ONCE_OR_MORE),
                        new RulePart('d', RulePart::TYPE_NORMAL)
                    ], 'a test_subrule_1+ d'),
                    new Rule('test_subrule_1', [new RulePart('b', RulePart::TYPE_NORMAL)], 'b'),
                    new Rule('test_subrule_1', [new RulePart('g', RulePart::TYPE_NORMAL)], 'g'),
                ]
            ],
            '1 rule with may be once or more sub rule' => [
                'test: a (b | g)* d;',
                [
                    new Rule('test', [
                        new RulePart('a', RulePart::TYPE_NORMAL),
                        new RulePart('test_subrule_1', RulePart::TYPE_MUST_BE_ONCE_OR_MORE),
                        new RulePart('d', RulePart::TYPE_NORMAL)
                    ], 'a test_subrule_1+ d'),
                    new Rule('test', [
                        new RulePart('a', RulePart::TYPE_NORMAL),
                        new RulePart('d', RulePart::TYPE_NORMAL)
                    ], 'a d'),
                    new Rule('test_subrule_1', [new RulePart('b', RulePart::TYPE_NORMAL)], 'b'),
                    new Rule('test_subrule_1', [new RulePart('g', RulePart::TYPE_NORMAL)], 'g'),
                ]
            ],
            '1 rule with may be once part' => [
                'test: a? l;',
                [
                    new Rule('test',
                        [
                            new RulePart('a', RulePart::TYPE_NORMAL),
                            new RulePart('l', RulePart::TYPE_NORMAL)
                        ], 'a l'),
                    new Rule('test', [new RulePart('l', RulePart::TYPE_NORMAL)], 'l'),
                ]
            ],
            '1 rule with must be once or more part' => [
                'test: a l+;',
                [
                    new Rule('test', [
                        new RulePart('a', RulePart::TYPE_NORMAL),
                        new RulePart('l', RulePart::TYPE_MUST_BE_ONCE_OR_MORE)
                    ], 'a l+'),
                ]
            ],
            '1 rule with can be once or more part' => [
                'test: a l* d;',
                [
                    new Rule('test', [
                        new RulePart('a', RulePart::TYPE_NORMAL),
                        new RulePart('l', RulePart::TYPE_MUST_BE_ONCE_OR_MORE),
                        new RulePart('d', RulePart::TYPE_NORMAL)
                    ], 'a l+ d'),
                    new Rule('test', [
                        new RulePart('a', RulePart::TYPE_NORMAL),
                        new RulePart('d', RulePart::TYPE_NORMAL)
                    ], 'a d'),
                ]
            ],
            'mega test' => [
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
                [
                    new Rule(
                        'expression',
                        [
                            new RulePart('mulExpression', ''),
                            new RulePart('expression_subrule_1', '+'),
                        ],
                        'mulExpression expression_subrule_1+'
                    ),
                    new Rule(
                        'expression',
                        [
                            new RulePart('mulExpression', ''),
                        ],
                        'mulExpression'
                    ),
                    new Rule(
                        'expression',
                        [
                            new RulePart('mulExpression', ''),
                            new RulePart('expression_subrule_2', '+'),
                        ],
                        'mulExpression expression_subrule_2+'
                    ),
                    new Rule(
                        'expression_subrule_1',
                        [
                            new RulePart('PLUS', ''),
                            new RulePart('mulExpression', ''),
                        ],
                        'PLUS mulExpression'
                    ),
                    new Rule(
                        'expression_subrule_2',
                        [
                            new RulePart('MINUS', ''),
                            new RulePart('mulExpression', ''),
                        ],
                        'MINUS mulExpression'
                    ),
                    new Rule(
                        'mulExpression',
                        [
                            new RulePart('atom', ''),
                            new RulePart('mulExpression_subrule_1', '+'),
                        ],
                        'atom mulExpression_subrule_1+'
                    ),
                    new Rule(
                        'mulExpression',
                        [
                            new RulePart('atom', ''),
                        ],
                        'atom'
                    ),
                    new Rule(
                        'mulExpression',
                        [
                            new RulePart('atom', ''),
                            new RulePart('mulExpression_subrule_2', '+'),
                        ],
                        'atom mulExpression_subrule_2+'
                    ),
                    new Rule(
                        'mulExpression_subrule_1',
                        [
                            new RulePart('MUL', ''),
                            new RulePart('atom', ''),
                        ],
                        'MUL atom'
                    ),
                    new Rule(
                        'mulExpression_subrule_2',
                        [
                            new RulePart('DIV', ''),
                            new RulePart('atom', ''),
                        ],
                        'DIV atom'
                    ),
                    new Rule('atom', [
                        new RulePart('INT_NUM', ''),
                    ], 'INT_NUM'),
                    new Rule('atom', [
                        new RulePart('FLOAT_NUM', ''),
                    ], 'FLOAT_NUM'),
                    new Rule('atom',
                        [
                            new RulePart('L_P', ''),
                            new RulePart('expression', ''),
                            new RulePart('R_P', ''),
                        ],
                        'L_P expression R_P'
                    ),
                ]
            ]
        ];
    }
}
