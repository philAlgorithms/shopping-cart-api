<?php

use App\Models\Currency;
use App\Models\TimeUnit;

function replace_space($string, $character = '-')
{
    return strtolower(str_replace(" ", $character, $string));
}

function add_meta_tag($tag, $html)
{
    $init_head_pos = strpos($html, '</head>');

    if (!$init_head_pos) return $html;
    return substr($html, 0, $init_head_pos) . $tag . substr($html, $init_head_pos, strlen($html));
}

// REGEX HELPERS START


/**
 * Gets a regex pattern for number range
 */
function number_range_pattern(int|string $min_digits = 1, int|string $max_digits = "", string $start_pattern = "", string $end_pattern = "", bool $tolerate_space = true, string|null $comparator = null): string
{
    $space_pattern = $tolerate_space ? "\s?" : "";
    $end_pattern = $end_pattern === "" ? $end_pattern : "\s" . $end_pattern;

    $equalityPatern = "/^" . $space_pattern . "\d{"
        . $min_digits . "," . $max_digits . "}"
        . $end_pattern . $space_pattern . "$/";

    $lesserPattern = "/^" . $space_pattern . "\b(Less\sthan)\s"
        . "\d{" . $min_digits . "," . $max_digits . "}"
        . $end_pattern . $space_pattern . "$/";

    $greaterPattern = "/^" . $space_pattern . "\b(Greater\sthan|More\sthan)\s"
        . "\d{" . $min_digits . "," . $max_digits . "}"
        . $end_pattern . $space_pattern . "$/";

    $rangePattern = "/^" . $space_pattern . $start_pattern
        . "\d{" . $min_digits . "," . $max_digits
        . "}" . $space_pattern . "-{1}" . $space_pattern
        . "\d{" . $min_digits . "," . $max_digits . "}"
        . $end_pattern . $space_pattern . "$/";

    if ($comparator === "=") {
        return $equalityPatern;
    }
    if ($comparator === "<") {
        return $lesserPattern;
    } else if ($comparator === ">") {
        return $greaterPattern;
    } else if ($comparator === "<>") {
        return $rangePattern;
    } else {
        return "/" . trim($equalityPatern, "^\$/") . "|"
            . trim($lesserPattern, "^\$/") . "|"
            . trim($greaterPattern, "^\$/") . "|"
            . trim($rangePattern, "^\$/") . "/";
    }
}

/**
 * Gets regex pattern for time units
 */
function time_unit_range_pattern(int|string $min_digits = 1, int|string $max_digits = "", string $start_pattern = "", string $end_pattern = "", bool $tolerate_space = true, string|null $comparator = null): string
{
    $time_unit_names = TimeUnit::get('name')->pluckToArray('name');
    $end_pattern = "\b(" . implode("|", $time_unit_names) . ")(s)?" . $end_pattern;

    return number_range_pattern(
        min_digits: $min_digits,
        max_digits: $max_digits,
        start_pattern: $start_pattern,
        end_pattern: $end_pattern,
        tolerate_space: $tolerate_space,
        comparator: $comparator
    );
}

/**
 * Gets regex pattern for money range using standard currency symbols
 */
function money_range_pattern(int|null $min_digits = 1, int|null $max_digits = 18, string $start_pattern = "", string $end_pattern = "", bool $tolerate_space = true, string|null $comparator = null): string
{
    $currency_acronyms = Currency::get('acronym')->pluckToArray('acronym');
    $end_pattern = "\b(" . implode("|", $currency_acronyms) . ")(s)?" . $end_pattern;

    return number_range_pattern(
        min_digits: $min_digits,
        max_digits: $max_digits,
        start_pattern: $start_pattern,
        end_pattern: $end_pattern,
        tolerate_space: $tolerate_space,
        comparator: $comparator
    );
}

/**
 * Gets values from a range string
 * 
 * @param string $str The string to be sepparated
 * @param string $range_pattern Regex pattern for the range in the string
 * @param string $validation_pattern Regex pattern that must be matched by the string before separating
 * 
 * @return false|array
 */
function separate_range_string(string $str, string $validation_pattern = "//"): false|array
{
    $str = trim($str);
    $comparator = preg_match("/\b(Less than)/", $str) ? "<"
        : (preg_match("/\b(Greater than|More than)/", $str) ? ">"
            : (preg_match("/\d{1,}\s?-{1}\s?\d{1,}/", $str) ? "<>" : "="));

    // After the last comparator, all is assumed to be "=",
    // we must check if the string actually passes as worthy
    if ($comparator === "=" and !preg_match("/^\s?\d{1,}\s/", $str)) {
        // Check if there is a leading text other than
        return false;
    }
    $range_pattern = $comparator === "=" ? "/\d{1,}\s?-{0,0}/"
        : ($comparator === "<" ? "/\b(Less than)\s\d{1,}/"
            : ($comparator === ">" ? "/\b(Greater\sthan|More\sthan)\s\d{1,}/"
                : "/\d{1,}\s?-{1}\s?\d{1,}/"));

    if (!preg_match($validation_pattern, $str)) {
        return false;
    }

    $range_matched = preg_match($range_pattern, $str, $matches); // match the string and refernce the matches to variable $matches


    if ($range_matched) {
        $range = $matches[0]; // $working range is the first item in the referenced array from $range_matched
    } else {
        return false;
    }
    $range_without_spaces = str_replace(" ", "", $range); // remove all spaces from range
    $range_values = explode("-", $range_without_spaces);

    $comparator === "=" ?  preg_match("/^\d{1,}\s?-{0,0}$/", $range, $digits) : preg_match("/\d{1,}/", $range, $digits);

    if (empty($digits)) return false;

    $digit = $digits[0];
    $unit = trim(substr($str, strpos($str, $digit) + strlen($digit)));
    // return [$range];
    if ($comparator === "=") {
        $start = $digit;
        $end = $digit;
    } else if ($comparator === "<") {
        $start = null;
        $end = $digit;
    } else if ($comparator === ">") {
        $start = $digit;
        $end = null;
    } else {
        $range_without_spaces = str_replace(" ", "", $range); // remove all spaces from range
        $range_values = explode("-", $range_without_spaces);
        $start = $range_values[0];
        $end = $range_values[1];
        $unit = trim(substr($str, strpos($str, $range) + strlen($range)));
    }
    $start = is_null($start) ? null : trim($start);
    $end = is_null($end) ? null : trim($end);
    return [
        "start" => is_numeric($start) ? (float)$start : $start,
        "end" => is_numeric($end) ? (float)$end : $end,
        "unit" => $unit
    ];
}

// REGEX HELPERS END

function stringBefore(string $string, string $character = "/", int $position = 1, bool $full = true): string|false
{
    $token = strtok($string, $character);
    $ret = $token;
    $i = 2;

    while ($i <= $position && $token != false) {
        if (!$full) {
            $token = strtok($character);
            $ret = $token;
        } else {
            $token = strtok($character);
            $ret = $ret . $character . $token;
        }
        $i++;
    }

    return $ret;
}

function forceArray($data): array
{
    return is_array($data) ? $data : [$data];
}

// CONCERNING SENSITIVE DATA
function hide_mail($email)
{
    $mail_segments = explode("@", $email);
    $mail_segments[0] = str_repeat("*", strlen($mail_segments[0]));

    return implode("@", $mail_segments);
}

function hide_string(string|null $string, int $hidden = 4)
{
    if(is_null($string)) return null;
    $len = strlen($string);
    $start = $hidden < $len ? $len - $hidden : floor($hidden/2);

    return substr($string, 0, $start * -1) . str_repeat('*', $hidden);
}
// END SENSITIVE DATA FUNCTIONS