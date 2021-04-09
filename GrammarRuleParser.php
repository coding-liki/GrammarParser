<?php
declare(strict_types=1);

namespace CodingLiki\GrammarParser;

use CodingLiki\GrammarParser\Rule\Rule;
use CodingLiki\GrammarParser\Rule\RulePart;

class GrammarRuleParser
{
    /**
     * @param string $rulesScript
     * @return Rule[]
     */
    public static function parse(string $rulesScript): array
    {
        RulesHelper::cleanCache();

        $rulesScript = self::normalizeScript($rulesScript);

        $rulesStrings = self::parseRulesStrings($rulesScript);

        $rules = [];
        foreach ($rulesStrings as $rulesData) {
            $nextRules = self::parseRulesData($rulesData);
            array_push($rules, ...$nextRules);
        }
        return $rules;
    }

    private static function normalizeScript(string $rulesScript): string
    {
        $rulesScript = self::removeComments($rulesScript);
        $rulesScript = trim(preg_replace('/\s+/', ' ', $rulesScript));

        return $rulesScript;
    }

    private static function removeComments(string $rulesScript): string
    {
        return preg_replace('/(\/\/|#).*(\n|$)/', ' ', $rulesScript);
    }


    /**
     * @param string $rulesScript
     * @return array
     */
    private static function parseRulesStrings(string $rulesScript): array
    {
        $rulesStrings = [];
        preg_match_all('/(?P<name>[a-zA-Z_]+) ?: ?(?P<partsString>[^;]*);/', $rulesScript, $matches);
        if (!empty($matches[0])) {
            foreach ($matches['name'] as $index => $name) {
                $rulesStrings[] = [
                    'name' => $name,
                    'partsString' => $matches['partsString'][$index]
                ];
            }
        }

        return $rulesStrings;
    }

    /**
     * @param array $rulesData
     * @return Rule[]
     */
    private static function parseRulesData(array $rulesData): array
    {
        $ruleName = $rulesData['name'];
        $subRules = self::parseSubRules($rulesData);

        foreach ($subRules as $key => $subRule) {
            $rulesData['partsString'] = str_replace($key, $subRule[0]->getName(), $rulesData['partsString']);
        }
        $rules = [];
        $items = explode('|', $rulesData['partsString']);
        while (!empty($items)) {
            $item = trim(array_shift($items));
            $rule = new Rule($ruleName, [], $item);
            $parts = explode(' ', $item);
            foreach ($parts as $index => $part) {
                $lastChar = substr($part, -1);
                switch ($lastChar) {
                    case RulePart::TYPE_MAY_BE_ONCE_OR_MORE:
                        $part = substr($part, 0,-1);
                        $rule->addPart(new RulePart($part, RulePart::TYPE_MAY_BE_ONCE_OR_MORE));
                        break;
                    case '?':
                        $part = substr($part, 0,-1);
                        $rule->addPart(new RulePart($part, RulePart::TYPE_NORMAL));
                        $new_parts = $parts;
                        array_splice($new_parts, $index,1);
                        array_unshift($items, implode(' ', $new_parts));
                        break;
                    case '+':
                        $part = substr($part, 0,-1);
                        $rule->addPart(new RulePart($part, RulePart::TYPE_NORMAL));
                        $rule->addPart(new RulePart($part, RulePart::TYPE_MAY_BE_ONCE_OR_MORE));
                        break;
                    default:

                        $rule->addPart(new RulePart($part, RulePart::TYPE_NORMAL));
                }
            }
            $rules[] = $rule;
        }

        $subRules = array_values($subRules);
        $subRules = array_merge(...$subRules);
        array_push($rules, ...$subRules);
        return $rules;
    }

    /**
     * @param array $rulesData
     * @return array<string, array<Rule>>
     */
    private static function parseSubRules(array $rulesData): array
    {
        $rootName = $rulesData['name'];
        $subRulePartsStrings = [];
        $subRules = [];

        preg_match_all('/\([^();]*\)/', $rulesData['partsString'], $matches);
        if (!empty($matches[0])) {
            foreach ($matches[0] as $number => $subRulePartsString) {
                $subRules[$subRulePartsString] = self::parseRulesData([
                    'name' => sprintf("%s_subrule_%d", $rootName, $number + 1),
                    'partsString' => trim($subRulePartsString, '()')
                ]);
            }
        }
        return $subRules;
    }
}