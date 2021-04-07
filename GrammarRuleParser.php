<?php
declare(strict_types=1);

namespace CodingLiki\GrammarParser;

class GrammarRuleParser
{
    /**
     * @param string $rulesScript
     * @return Rule[]
     */
    public static function parse(string $rulesScript): array
    {
        RulesHelper::cleanCache();

        $rules = [];

        $rulesSets = self::parseRulesSets($rulesScript);
        foreach ($rulesSets as $rulesSet) {
            $nextRules = self::parseRules($rulesSet);
            array_push($rules, ...$nextRules);
        }
        return $rules;
    }

    /**
     * @param string $rulesScript
     * @return string[]
     */
    private static function parseRulesSets(string $rulesScript): array
    {
        preg_match_all('/\w+\s*:[^;]+;/u', $rulesScript, $matches);

        return $matches[0];
    }

    /**
     * @param string $rulesSet
     * @return Rule[]
     */
    private static function parseRules(string $rulesSet): array
    {
        $rulesSet = preg_replace('/[\s;]+/', ' ', $rulesSet);
        $allParts = explode(':', $rulesSet);
        $name = array_shift($allParts);
        $subRules = explode('|', $allParts[0]);
        $rules = [];
        foreach ($subRules as $subRule) {
            $parts = explode(' ', trim($subRule));
            $rules[] = new Rule($name, $parts);
        }

        return $rules;
    }
}