<?php
declare(strict_types=1);

namespace CodingLiki\GrammarParser;

class RulesHelper
{
    public static array $rulesByName = [];

    public const ROOT_RULE_NAME = 'S\'';

    public const ROOT_SYMBOL = '^';

    /**
     * @param string $name
     * @param Rule[] $rules
     * @return array
     */
    public static function getRulesByName(string $name, array $rules): array
    {
        if (!isset(self::$rulesByName[$name])) {
            self::$rulesByName[$name] = [];
            foreach ($rules as $rule) {
                if ($rule->name === $name) {
                    self::$rulesByName[$name][] = $rule;
                }
            }
        }

        return self::$rulesByName[$name];
    }

    /**
     * @param Rule[] $rules
     * @return Rule
     */
    public static function buildRootRule(array $rules): Rule
    {
        return new Rule(self::ROOT_RULE_NAME, [$rules[0]->name]);
    }

    public static function cleanCache()
    {
        self::$rulesByName = [];
    }
}