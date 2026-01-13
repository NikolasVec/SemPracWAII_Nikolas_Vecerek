<?php
$tests = [
    'abcde1',         // 5 letters + digit
    'abcd1',          // 4 letters + digit
    'aaaaa',          // 5 letters no digit
    'áčéíô1',         // 5 letters with diacritics + digit
    'ÁČÉÍÔ1',         // uppercase with diacritics + digit
    'abčdě1',         // mix
    'a b c d e 1',    // spaces
    '12345a',         // 1 letter + digits
    'päťpäť1',        // slovak word with special letters
];

foreach ($tests as $pw) {
    $lettersCount = preg_match_all('/[A-Za-zÀ-ž]/u', $pw);
    $hasDigit = preg_match('/\d/', $pw);
    echo "Password: '" . $pw . "'\n";
    echo "  lettersCount: "; var_export($lettersCount); echo "\n";
    echo "  hasDigit: "; var_export($hasDigit); echo "\n";
    $ok = ($lettersCount !== false && $lettersCount >= 5 && $hasDigit);
    echo "  OK by rule: " . ($ok ? 'YES' : 'NO') . "\n\n";
}

