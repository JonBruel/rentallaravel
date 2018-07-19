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
use App\Models\Contract;
use Carbon\Carbon;
use Number;

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

    /* CONSTRUCTOR */
    function __construct($date = NULL, $year = NULL, $month = NULL){
        $self = htmlspecialchars($_SERVER['PHP_SELF']);
        $this->link_to = $self;

        if( is_null($year) || is_null($month) ){
            if( !is_null($date) ){
                //-------- strtotime the submitted date to ensure correct format
                $this->date = date("Y-m-d", strtotime($date));
            } else {
                //-------------------------- no date submitted, use today's date
                $this->date = date("Y-m-d");
            }
            $this->set_date_parts_from_date($this->date);
        } else {
            $this->year		= $year;
            $this->month	= str_pad($month, 2, '0', STR_PAD_LEFT);
        }
    }


    //Used for callbacks
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

    static function cache_delete($extension) {
        $files = glob("/tmp/*.".$extension);
        $now   = time();
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * 1) { // 1 day
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

    //Fills in the calander details based on houseid
    static function setVdays($houseid, \Illuminate\Database\Eloquent\Builder $periodquery, $culture = NULL, Carbon $starttime = NULL, $months = NULL)
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

        //$periodquery->whereBetween('from', [$date->sub(new DateInterval('P7D')), $date->add(new DateInterval('P7D'))]);
        $periodquery->whereBetween('from', [$datemin, $datemax])
                    ->orderBy('from');

        $periods = $periodquery->get();
        //die("from:".$datemin->toDateString().' to: '.$datemax->toDateString().' period: '.sizeof($periods));

        //We prepare two arrays which are used inside the period object
        //to check if committed and find the corresponding contractid.
        //This is done for performance
        Period::setPeriodStatus($houseid, 'Committed');

        $contractid = null;
        $maxKeyOfPeriods = sizeof($periods) - 1;
        //Below we fill in the days for the calendar
        //Time figures are in seconds after Unix birth.
        foreach ($periods as $key => $period)
        {
            //If starttime is after the end of the time scope we want to show, we just skip
            if (strtotime($starttime) >= strtotime($lastdate)) continue;

            //Common preparation for all periods
            $from = $period->from;
            $to = $period->to;
            $periodid = $period->id;
            $r = $period->getRate($culture);
            $customercurrencysymbol = $r['currencysymbol'];
            $rate = $r['rate'];
            $categoryid = 0;
            $newcontractid = $period->getContractid('Committed');
            $reserved = ($newcontractid != null)?true:false;
            $newcontract = ($contractid == $newcontractid)?false:true;
            $contractid = $newcontractid;
            if ($contractid) {
                $categoryid = Contract::find($contractid)->categoryid;
            }

            //Formulate text information to be used when clicking on a day
            $periodtext = $period->from.' '.__('to').' '.$period->to;
            $price = '';
            //Number::format($value, ['minimum_fraction_digits' => 12, 'maximum_fraction_digits' => 12]);
            if ($period->personprice>0)
            {
                $price .= __('Base price') . ': ' . $customercurrencysymbol . ' '
                    . Number::format($rate*$period->baseprice,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => $culture])
                    . ', '
                    . __('per person more than') . ' ' . $period->basepersons . ': ' . $customercurrencysymbol . ' '
                    . Number::format($rate*$period->personprice,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => $culture])
                    .  ', max. ' . $period->maxpersons . ' ' . __('persons') . '.';
            }
            else
            {
                $price .= $customercurrencysymbol . ' '
                    . Number::format($rate*$period->baseprice,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => $culture])
                    . ' ' . __('with a maximum of') . ' ' . $period->maxpersons . ' ' . __('persons');
            }


            //We fill in all $vdays up to the present from-time by default values as non-scheduled periods
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
                    $vdays[$daykey]['period'] = 'N/A';
                    $vdays[$daykey]['price'] = 'N/A';
                    $vdays[$daykey]['date'] = $daykey;
                    $vdays[$daykey]['categoryid'] = 0;

                }
            }

            //We fill $vdays data within the periods
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
                    $vdays[$daykey]['period'] = $periodtext;
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
                }
            }
        }
        //Exit periods loop

        //We possible remaining days without periods
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
                $vdays[$daykey]['period'] = 'N/A';
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

    function set_date_parts_from_date($date){
        $this->year		= date("Y", strtotime($date));
        $this->month	= date("m", strtotime($date));
        $this->day		= date("d", strtotime($date));
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

        $weekdays = ['Ma', 'Ti', 'On', 'To', 'Fr', 'Lø', 'Sø'];
        foreach ($weekdays as  $localized_day_name ){
            $col .= "<col class=\"" . strtolower($localized_day_name) ."\" />\n";
            $th .= "\t<th title=\"" . ucfirst($localized_day_name) ."\">" . strtoupper($localized_day_name{0}) ."</th>\n";
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
            $period = $rentalinfo['period'];
            $price = $rentalinfo['price'];
            $text = $period . "\n\n" . $price;

            $output .= "<td" . $day_class . " title=\"" . $text .
                "\" onmouseover=\"
        jQuery('#period').text('".str_replace("\n", ": ", $period)."');
        jQuery('#price').text('".str_replace("\n", ": ", $price)."');
        \"".
                "\" onmouseout=\"
        jQuery('#period').text('');
        jQuery('#price').text('');
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