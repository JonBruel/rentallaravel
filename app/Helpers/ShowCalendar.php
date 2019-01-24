<?php
/**
 * Calendar Generation Class
 *
 * This class provides a simple reuasable means to produce month calendars in valid html
 *
 * @version 2.7
 * @author Jim Mayes <jim.mayes@gmail.com>
 * @link http://style-vs-substance.com
 * @copyright Copyright (c) 2008, Jim Mayes
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt GPL v2.0
 * Modified by Jon Brüel
 */

namespace App\Helpers;
use \DateTime;
use \DateInterval;
use App\Models\Period;
use App\Models\Periodcontract;
use Illuminate\Session;
use App\Models\Contract;
use Carbon\Carbon;
use Number;
use DB;

/**
 * Class ShowCalendar has a set of methods used to show the calendar of a house showing the
 * availability of the periods.
 *
 * The chache is deleted when a contract is updated. This deletes all cache files and will not
 * be optimal in a system with many houses.
 *
 * @package App\Helpers
 */
class ShowCalendar
{
    private $date;
    private $year;
    private $month;
    private $day;


    private $week_start = 7;// sunday

    public $link_to = '';

    public $houseid = 0;
    public $culture = NULL;
    static $vdays = array();

    private $mark_today = TRUE;
    private $today_date_class = 'today';

    private $occupied_dates = array();
    private $default_occupied_class = 'occupied';
    private $default_family_class = 'family';
    private $family_dates = [];

    private $halfday_dates = array();
    private $default_halfday_class = 'halfday';

    private $notoffered_dates = array();
    private $default_notoffered_class = 'notoffered';

    private $test = '';

    /**
     * ShowCalendar constructor.
     * @param Carbon $starttime
     */
    function __construct(Carbon $starttime){
        $this->date = $starttime->format('Y-m-d');
        $this->month = $starttime->format('m');
        $this->year = $starttime->format('Y');
    }


    /**
     * Not implemented, could be used for callbacks.
     *
     * @param $object
     * @param $con
     */
    function resetVdays($object, $con)
    {
        //Not implemented, see the old rental implementations
    }

    //https://medium.com/@dylanwenzlau/500x-faster-caching-than-redis-memcache-apc-in-php-hhvm-dcd26e8447ad
    static function cache_set($key, $val) {
        $val = var_export($val, true);
        // HHVM fails at __set_state, so just use object cast for now
        $val = str_replace('stdClass::__set_state', '(object)', $val);
        // Write to temp file first to ensure atomicity
        $tmp = "/tmp/$key." . uniqid('', true) . '.tmp';
        file_put_contents($tmp, '<?php $val = ' . $val . ';', LOCK_EX);
        rename($tmp, "/tmp/$key");
    }

    /**
     * The cache_delete is used every time the calendar is set (setVdays) where
     * old cached information is deleted in order to tidy up in the set of files. As
     * default it deletes files more than 24 hours old, but it may be called
     * with a second parameter set to 0 to delete all cache files.
     *
     * @param $extension
     */
    static function cache_delete($extension, $hours = 24) {
        $files = glob("/tmp/*.".$extension);
        $now   = time();
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * $hours * 1) { // 1 day
                    unlink($file);
                }
            }
        }
    }

    //https://medium.com/@dylanwenzlau/500x-faster-caching-than-redis-memcache-apc-in-php-hhvm-dcd26e8447ad
    static function cache_get($key) {
        @include "/tmp/$key";
        return isset($val) ? $val : false;
    }

    /**
     * Fills in the calendar days. If there is a chache file this one is taken. If not it is
     * calculated, which takes around 1 second.
     *
     * @param $houseid
     * @param \Illuminate\Database\Eloquent\Builder $periodcontractquery
     * @param null $culture
     * @param Carbon|NULL $starttime
     * @param null $months
     * @return bool
     */
    static function setVdays($houseid, \Illuminate\Database\Eloquent\Builder $periodcontractquery, $culture = NULL, Carbon $starttime = NULL, $months = NULL)
    {
        function addOneday($time)
        {
            return strtotime('+1 day', $time);
        }

        if ($starttime == NULL) $starttime = Carbon::parse('first day of this month');
        if ($months == NULL) $months = 12;
        $lastreserved = false;

        //Drop the "time of day part" in starttime, starttime is now just a start data, e.g. 2018-07-01
        $starttime =  Carbon::parse($starttime->toDateString());

        $apckey = Carbon::now()->toDateString().'_'.$starttime->toDateString().'_'.$houseid.'_'.$culture.'.cache';
        $vdays = static::cache_get($apckey);

        //Remove the remark below to uncache the vdays!
        //$vdays = false;
        if ($vdays)
        {
                static::$vdays = $vdays;
                return $vdays;
        }

        //Tidy up in the /tmp directory
        static::cache_delete('cache');

        //Create time limits for query
        $datemin = clone($starttime);
        $datemax = clone($starttime);
        $datemin->subDays(7);
        $datemax->addYear();
        $lastdate = $datemax->toDateString();
        $datemax->addDays(7);

        $periodcontractquery->whereBetween('from', [$datemin, $datemax])->orderBy('from');
        $periodcontracts = $periodcontractquery->get();

        $contractid = null;

        //Below we fill in the days for the calendar
        //Time figures are in seconds after Unix birth.
        foreach ($periodcontracts as $key => $periodcontract)
        {
            //If starttime is after the end of the time scope we want to show, we just skip
            if (strtotime($starttime) >= strtotime($lastdate)) continue;

            //Common preparation for all periodcontracts
            $from = $periodcontract->from;
            $to = $periodcontract->to;
            $periodid = $periodcontract->id;
            $r = $periodcontract->getRate($culture);
            $customercurrencysymbol = $r['currencysymbol'];
            $rate = $r['rate'];
            $categoryid = 0;
            $newcontractid = $periodcontract->getContractid();
            $reserved = ($newcontractid != null)?true:false;
            $newcontract = ($contractid == $newcontractid)?false:true;
            $contractid = $newcontractid;
            if ($contractid) {
                $categoryid = Contract::find($contractid)->categoryid;
            }

            //Formulate text information to be used when hovering over a day
            $periodtext = Carbon::parse($periodcontract->from)->format('Y-m-d').' '.__('to').' '.Carbon::parse($periodcontract->to)->format('Y-m-d');
            $price = '';
            if ($periodcontract->personprice>0)
            {
                $price .= __('Base price') . ': ' . $customercurrencysymbol . ' '
                    . Number::format($rate*$periodcontract->baseprice,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => $culture])
                    . ', '
                    . __('per person more than') . ' ' . $periodcontract->basepersons . ': ' . $customercurrencysymbol . ' '
                    . Number::format($rate*$periodcontract->personprice,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => $culture])
                    .  ', max. ' . $periodcontract->maxpersons . ' ' . __('persons') . '.';
            }
            else
            {
                $price .= $customercurrencysymbol . ' '
                    . Number::format($rate*$periodcontract->baseprice,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => $culture])
                    . ' ' . __('with a maximum of') . ' ' . $periodcontract->maxpersons . ' ' . __('persons');
            }


            //We fill in all $vdays up in the gaps where there are no periodcontracts, e.g. future dates
            if (strtotime($from)>strtotime($starttime))
            {
                $start = strtotime($starttime);
                $end = strtotime($from);
                $starttime = $from;
                for ($time = $start; $time <= $end; $time = addOneday($time))
                {
                    $daykey = date('Y-m-d',$time);
                    $vdays[$daykey] = array();
                    $vdays[$daykey]['periodid'] = -1;
                    $vdays[$daykey]['to'] = 0;
                    $vdays[$daykey]['occupied'] = false;
                    $vdays[$daykey]['halfday'] = false;
                    $vdays[$daykey]['notoffered'] = true;
                    $vdays[$daykey]['text'] = 'N/A';
                    $vdays[$daykey]['periodcontract'] = 'N/A';
                    $vdays[$daykey]['price'] = 'N/A';
                    $vdays[$daykey]['date'] = $daykey;
                    $vdays[$daykey]['categoryid'] = 0;

                }
            }

            //We fill $vdays data within the periodcontracts
            if (strtotime($from)<=strtotime($starttime))
            {
                $start = strtotime($starttime);
                $end = min(strtotime($to), strtotime($lastdate));
                $extraend = addOneday($end);
                //$starttime is renewed
                $starttime = date('Y-m-d',$end);
                for ($time = $start; $time <= $extraend; $time = addOneday($time))
                {
                    $daykey = date('Y-m-d',$time);

                    $vdays[$daykey] = array();
                    $vdays[$daykey]['periodid'] = $periodid;
                    $vdays[$daykey]['to'] = strtotime($to);
                    $vdays[$daykey]['occupied'] = false;
                    $vdays[$daykey]['categoryid'] = 0;
                    $vdays[$daykey]['halfday'] = false;
                    $vdays[$daykey]['notoffered'] = false;
                    $vdays[$daykey]['text'] = $periodtext . "\n" . $price;
                    $vdays[$daykey]['periodcontract'] = $periodtext;
                    $vdays[$daykey]['price'] = $price;
                    $vdays[$daykey]['date'] = $daykey;
                    if ($lastreserved)
                    {
                        if (($time == strtotime($from)) AND ($newcontract)) $vdays[$daykey]['halfday'] = true;
                        $lastreserved = false;
                    }
                    if ($reserved)
                    {
                        $lastreserved = true;
                        if (($time == strtotime($from)) AND ($newcontract)) $vdays[$daykey]['halfday'] = true;
                        elseif ($time > strtotime($to)) $vdays[$daykey]['halfday'] = true;
                        else {
                            $vdays[$daykey]['occupied'] = true;
                            $vdays[$daykey]['categoryid'] = $categoryid;
                        }
                    }
                    else
                    {
                        if ($time == strtotime($from)) $vdays[$daykey]['halfday'] = true;
                    }
                    //Change by Iben 24-1-2019, don't show halfdays:
                    $vdays[$daykey]['halfday'] = false;
                    if ($reserved)
                    {
                        $vdays[$daykey]['occupied'] = true;
                        $vdays[$daykey]['categoryid'] = $categoryid;
                    }
                }
            }
        }
        //Exit periodcontracts loop

        //We possible remaining days without periodcontracts
        if (strtotime($starttime) <= strtotime($lastdate))
        {
            $start = strtotime($starttime);
            $end = strtotime($lastdate);
            for ($time = $start; $time <= $end; $time = addOneday($time))
            {
                $daykey = date('Y-m-d',$time);
                $vdays[$daykey] = array();
                $vdays[$daykey]['periodid'] = -1;
                $vdays[$daykey]['to'] = 0;
                $vdays[$daykey]['occupied'] = false;
                $vdays[$daykey]['halfday'] = false;
                $vdays[$daykey]['notoffered'] = true;
                $vdays[$daykey]['text'] = 'N/A';
                $vdays[$daykey]['periodcontract'] = 'N/A';
                $vdays[$daykey]['price'] = 'N/A';
                $vdays[$daykey]['date'] = $daykey;
            }
        }
        //All days are filled in

        self::$vdays = $vdays;
        //echo 'vdyas størrelse: ' . sizeof(self::$vdays);
        //$s = serialize($vdays);
        #apc_define_constants($apckey, array('VDAYS'=>$s));
        static::cache_set($apckey, $vdays);
        return $vdays;
    }


    function day_of_week($date){
        $day_of_week = date("N", $date);
        if( !is_numeric($day_of_week) ){
            $day_of_week = date("w", $date);
            if( $day_of_week == 0 ){
                $day_of_week = 7;
            }
        }
        return $day_of_week;
    }

    /**
     * The function renders the calendar.
     *
     * @param null $year
     * @param null $month
     * @param string $calendar_class
     * @return string
     */
    function output_calendar($year = NULL, $month = NULL, $calendar_class = 'calendar'){

        $culture = $this->culture;
        $vdays = self::$vdays;
        $to = 0;
        setlocale(LC_TIME, $culture);

        //--------------------- override class methods if values passed directly
        $year = ( is_null($year) )? $this->year : $year;
        $month = ( is_null($month) )? $this->month : str_pad($month, 2, '0', STR_PAD_LEFT);

        //------------------------------------------- create first date of month
        $month_start_date = strtotime($year . "-" . $month . "-01");
        //------------------------- first day of month falls on what day of week
        $first_day_falls_on = $this->day_of_week($month_start_date);
        //----------------------------------------- find number of days in month
        $days_in_month = date("t", $month_start_date);
        //-------------------------------------------- create last date of month
        $month_end_date = strtotime($year . "-" . $month . "-" . $days_in_month);
        //----------------------- calc offset to find number of cells to prepend
        $start_week_offset = $first_day_falls_on - $this->week_start;
        $prepend = ( $start_week_offset < 0 )? 7 - abs($start_week_offset) : $first_day_falls_on - $this->week_start;
        //-------------------------- last day of month falls on what day of week
        $last_day_falls_on = $this->day_of_week($month_end_date);

        //------------------------------------------------- start table, caption
        $output  = "<table class=\"" . $calendar_class . "\">\n";
        $output .= "<caption>" . Carbon::parse($year . "-" . $month . "-01")->formatLocalized('%B %Y') . "</caption>\n";

        $col = '';
        $th = '';


        $monday = Carbon::now()->startOfWeek()->subDay(1);
        $weekdays = [$monday, $monday->copy()->addDays(1), $monday->copy()->addDays(2), $monday->copy()->addDays(3),
            $monday->copy()->addDays(4), $monday->copy()->addDays(5), $monday->copy()->addDays(6)];
        foreach ($weekdays as  $localized_day_name ){
            $col .= "<col />\n";
            $th .= "\t<th title=\"" . ucfirst($localized_day_name->formatLocalized('%a')) ."\"  data-toggle=\"tooltip\"  class=\"text-center\">" . strtoupper($localized_day_name->formatLocalized('%a'){0}) ."</th>\n";
        }

        //------------------------------------------------------- markup columns
        $output .= $col;

        //----------------------------------------------------------- table head
        $output .= "<thead>\n";
        $output .= "<tr>\n";

        $output .= $th;

        $output .= "</tr>\n";
        $output .= "</thead>\n";

        //---------------------------------------------------------- start tbody
        $output .= "<tbody>\n";
        $output .= "<tr>\n";

        //---------------------------------------------- initialize week counter
        $weeks = 1;

        //--------------------------------------------------- pad start of month

        //------------------------------------ adjust for week start on saturday
        for($i=1;$i<=$prepend;$i++){
            $output .= "\t<td class=\"pad\">&nbsp;</td>\n";
        }

        //--------------------------------------------------- loop days of month
        for($day=1,$cell=$prepend+1; $day<=$days_in_month; $day++,$cell++){

            /*
            if this is first cell and not also the first day, end previous row
            */
            if( $cell == 1 && $day != 1 ){
                $output .= "<tr>\n";
            }

            //-------------- zero pad day and create date string for comparisons
            $daystring = str_pad($day, 2, '0', STR_PAD_LEFT);
            $day_date = $year . "-" . $month . "-" . $daystring;
            $dayvalue = strtotime($day_date);

            //We set the rentalids and occupancy
            $this->test .= $day_date . ' ' .  $this->month . $month . ' | ';
            if (!array_key_exists($day_date, $vdays)) {
                die($this->test . ' key used: ' . $day_date . ' days in month:' . $days_in_month . ' day: ' . $daystring . $this->month . $month);
            }


            $rentalinfo = $vdays[$day_date];
            $periodid = $rentalinfo['periodid'];
            //echo sizeof($rentalinfo);

            if ($rentalinfo['halfday'] == true)
            {
                $to = $rentalinfo['to'];
                $this->halfday_dates[] = $day_date;
            }
            if ($rentalinfo['occupied'] == true)
            {
                $to = $rentalinfo['to'];
                $this->occupied_dates[] = $day_date;
                if ($rentalinfo['categoryid'] == 1) {
                    $this->family_dates[] = $day_date;
                }
            }
            if ($rentalinfo['notoffered'] == true) $this->notoffered_dates[] = $day_date;


            //-------------------------- compare day and add classes for matches
            if( $this->mark_today == TRUE && $day_date == date("Y-m-d") ){
                $classes[] = $this->today_date_class;
            }

            if( is_array($this->occupied_dates) ){
                if( in_array($day_date, $this->occupied_dates) ) {
                    if( is_array($this->family_dates) ){
                        if( in_array($day_date, $this->family_dates) ){
                            $classes[] = $this->default_family_class;
                        }
                        else $classes[] = $this->default_occupied_class;
                    }
                    else $classes[] = $this->default_occupied_class;
                    //$classes[] = $this->default_occupied_class;
                }
            }
            if( is_array($this->notoffered_dates) ){
                if( in_array($day_date, $this->notoffered_dates) ){
                    $classes[] = $this->default_notoffered_class;
                }
            }
            if( is_array($this->halfday_dates) ){
                if( in_array($day_date, $this->halfday_dates) ){
                    $classes[] = $this->default_halfday_class;
                }
            }
            //----------------- loop matching class conditions, format as string
            if (isset($classes)){
                $day_class = ' class="';
                foreach( $classes AS $value ){
                    $day_class .= $value . " ";
                }
                $day_class = substr($day_class, 0, -1) . '"';
            } else {
                $day_class = '';
            }

            //---------------------------------- start table cell, apply classes
            $period = $rentalinfo['periodcontract'];
            $price = $rentalinfo['price'];
            $text = $period . "\n" . $price;

            $output .= "<td" . $day_class . " title=\"" . $text . "\" data-toggle=\"tooltip\"" .
        " onmouseover=\"
        $('#period').html('".str_replace("\n", ": ", " ".$period)."');
        $('#price').html('".str_replace("\n", ": ", " ".$price)."');
        \""."\" onmouseout=\"$('#period').html('');$('#price').html('');
        \"". ">";

            //----------------------------------------- unset to keep loop clean
            unset($day_class, $classes);

            //-------------------------------------- conditional, start link tag


            if (((is_array($this->occupied_dates)) OR (is_array($this->notoffered_dates))) AND (time() < $dayvalue))
            {
                if ((in_array($day_date, $this->occupied_dates)) OR (in_array($day_date, $this->notoffered_dates))) $output .= $day;
                else $output .= "<a href=\"" . $this->link_to . $periodid . "\">" . $day . "</a>";
            }
            else $output .= $day;



            //------------------------------------------------- close table cell
            $output .= "</td>\n";

            //------- if this is the last cell, end the row and reset cell count
            if( $cell == 7 ){
                $output .= "</tr>\n";
                $cell = 0;
            }

        }

        //----------------------------------------------------- pad end of month
        if( $cell > 1 ){
            for($i=$cell;$i<=7;$i++){
                $output .= "\t<td class=\"pad\">&nbsp;</td>\n";
            }
            $output .= "</tr>\n";
        }

        //--------------------------------------------- close last row and table
        $output .= "</tbody>\n";
        $output .= "</table>\n";

        //--------------------------------------------------------------- return
        return $output;

    }

}
