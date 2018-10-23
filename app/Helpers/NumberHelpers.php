<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 14-07-2018
 * Time: 18:32
 */

namespace App\Helpers;

use Propaganistas\LaravelIntl\Facades\Number;
use Carbon\Carbon;
use App;

/**
 * Trait NumberHelpers includes static functions to format and parse numbers and dates according to the
 * 5 character $culture code which can be supplied as a parameter.
 * @package App\Helpers
 */
trait NumberHelpers
{
    /*
     * Formats a number to a string according to the culture. If no culture is
     * given the application defined culture will be used.
     */
    static function format($number, $decimals = 2, $culture = null, $style = [])
    {
        $style = ['minimum_fraction_digits' => $decimals, 'maximum_fraction_digits' => $decimals] + $style;
        if ($culture)
        {
            return number()->usingLocale($culture, function($num) use ($decimals, $number, $style) {
                return $num->format($number, $style);
            });
        }
        return number()->format($number, $style);
    }

    /**
     * Parses a string to a number according to the culture. If no culture is
     * given the application defined culture will be used.
     *
     * @param $string
     * @param null $culture
     * @return float
     */
    static function parse($string, $culture = null)
    {
        if ($culture)
        {
            return number()->usingLocale($culture, function($num) use ($string) {
                return $num->parse($string);
            });
        }
        return number()->parse($string);
    }

    /**
     * Converts Carbon date to a I18N string. The format is the long one, e.g.
     * in English: Fri 26 Oct 2018. It uses Carbon formatLocalized and may require
     * to be changes when Laravel uses Carbon 2.
     *
     * [For other formats check]: /https://carbon.nesbot.com/docs/
     * [For other formats check]
     *
     * @param Carbon $date
     * @param null $culture
     * @return string
     */
    static function formatDateToString(Carbon $date, $culture = null)
    {
        if ($culture) {
            setlocale(LC_TIME, $culture);
            App::setLocale($culture);
        }
        return $date->formatLocalized('%a %d %b %Y');
    }

    /**
     * Converts Carbon datetime to a I18N string. The format is the long one, e.g.
     * in English: Fri 26 Oct 2018 13:45. It uses Carbon formatLocalized and may require
     * to be changes when Laravel uses Carbon 2.
     *
     * [For other formats check]: /https://carbon.nesbot.com/docs/
     * [For other formats check]
     *
     * @param Carbon $date
     * @param null $culture
     * @return string
     */
    static function formatDateTimeToString(Carbon $date, $culture = null)
    {
        if ($culture) {
            setlocale(LC_TIME, $culture);
            App::setLocale($culture);
        }
        return $date->formatLocalized('%a %d %b %Y %H:%mm');
    }
}