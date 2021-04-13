<?php
declare(strict_types=1);

namespace CodingLiki\GrammarParser\Token;

class GrammarTokenParser
{
    /**
     * @param string $tokensScript
     * @return TokenType[]
     */
    public static function parse(string $tokensScript): array
    {
        $tokenStrings = explode("\n", $tokensScript);
        $tokenTypes = [];

        foreach ($tokenStrings as $tokenString) {
            $type = self::parseType($tokenString);
            if ($type !== null) {
                $tokenTypes[] = $type;
            }
        }

        return $tokenTypes;
    }

    private static function parseType(string $tokenString): ?TokenType
    {
        $tokenString = self::normalizeString($tokenString);

        if (empty($tokenString)) {
            return null;
        }

        $parts = explode(':', $tokenString);
        $parts = array_map('trim', $parts);
        $name = array_shift($parts);

        $regex = implode('', $parts);
        return new TokenType($name, $regex);
    }

    private static function normalizeString(string $tokenString): string
    {
        $tokenString = trim($tokenString);

        if (str_starts_with($tokenString, '//') || str_starts_with($tokenString, '#')) {
            return '';
        }

        $commentPosition = strpos($tokenString, '//');

        if ($commentPosition !== false) {
            $tokenString = substr($tokenString, 0, $commentPosition);
        }
        return trim($tokenString);
    }
}