<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;
use DB;
use Carbon\Carbon;
use App\Mail\DefaultMail;
use Illuminate\Support\Facades\Mail;


/**
 * Class Batchlog
 * 
 * @property int $id
 * @property int $statusid
 * @property int $posttypeid
 * @property int $batchtaskid
 * @property int $contractid
 * @property int $accountpostid
 * @property int $emailid
 * @property int $customerid
 * @property int $houseid
 * @property int $ownerid
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Customer $customer
 * @property \App\Models\Posttype $posttype
 * @property \App\Models\Batchtask $batchtask
 * @property \App\Models\Contract $contract
 * @property \App\Models\Accountpost $accountpost
 * @property \App\Models\House $house
 * @property \App\Models\Batchstatus $batchstatus
 *
 * @package App\Models
 */
class Batchlog extends BaseModel
{
	protected $table = 'batchlog';

	protected $casts = [
		'statusid' => 'int',
		'posttypeid' => 'int',
		'batchtaskid' => 'int',
		'contractid' => 'int',
		'accountpostid' => 'int',
		'emailid' => 'int',
		'customerid' => 'int',
		'houseid' => 'int',
		'ownerid' => 'int'
	];

	protected $fillable = [
		'statusid',
		'posttypeid',
		'batchtaskid',
		'contractid',
		'accountpostid',
		'emailid',
		'customerid',
		'houseid',
		'ownerid'
	];

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'customerid');
	}

	public function posttype()
	{
		return $this->belongsTo(\App\Models\Posttype::class, 'posttypeid');
	}

	public function batchtask()
	{
		return $this->belongsTo(\App\Models\Batchtask::class, 'batchtaskid');
	}

	public function contract()
	{
		return $this->belongsTo(\App\Models\Contract::class, 'contractid');
	}

	public function accountpost()
	{
		return $this->belongsTo(\App\Models\Accountpost::class, 'accountpostid');
	}

	public function house()
	{
		return $this->belongsTo(\App\Models\House::class, 'houseid');
	}

	public function batchstatus()
	{
		return $this->belongsTo(\App\Models\Batchstatus::class, 'statusid');
	}

	/*
	 * This method updates the queue with tasks to be executed later by
	 * the other static function executequeue
	 */
	public static function executequeue($batchid = null, $onereceipient = null)
    {
        BaseModel::$ajax = true;
        $r = null;
        $helptext = '';
        //TODO: Change $mailer = sfContext::getInstance()->getMailer();

        $lockbatch = [];
        foreach (House::all() as $house) $lockbatch[$house->id] = $house->lockbatch;

        /*
         * Lockbatch values
         *
         * The lock applies to each house, default value is 0
         *  0 no lock
         *  1 the queue is populated, testmails are send, but the queue status remains as not executed (statusid = 1).
         *  2 the queue is populated, no mails are send, maillog not updated, but the queue status is updated
         *  3 the queue is not populated, the queue is not executed
         *
         * Statusid values
         *  0 test
         *  1 queued
         *  2 dispatched
         *
         */

        if ($batchid === null) $batchlogs = Batchlog::where('statusid', 1)->get();
        else $batchlogs = [Batchlog::Find($batchid)];

        foreach ($batchlogs as $batchlog) {
            if (!$batchlog) echo("Batchlog empty, error.");

            $houseid = $batchlog->houseid;
            $lockbatchvalue = $lockbatch[$houseid];

            if ($lockbatchvalue > 2) continue;
            if ($lockbatchvalue == 1) config::set('app.mailactive', 0);

            $batchtask = $batchlog->batchtask;
            $sendto = explode(',',$batchtask->mailto);
            $customerid = $batchlog->customerid;
            $house = House::Find($houseid);
            $ownerid = $batchlog->ownerid;
            $contractid = $batchlog->contractid;
            $emailid = $batchtask->emailid;
            //$helptext .= 'Ownerid: ' . $ownerid  . '<br />';
            $customer = Customer::Find($customerid);
            $contract = Contract::Find($contractid);
            $owner = Customer::Find($ownerid);
            $accountidbalance = DB::table('accountidbalance')->Find($contractid);
            if (!($accountidbalance))  die('error, accountidbalancerecord does not exist.');


            //Determine who to send to
            //The code below is not very elegant, due to my lack of understanding of Criteria
            //which gave an error when I tried to combine several criteria: $c->add($d).
            $recipients = array();
            if (!($onereceipient)) {
                if (sizeof($sendto) > 0) {
                    foreach ($sendto as $customertypeid)
                    {
                        //First one usually only for tests
                        if ($customertypeid == 1) $recipients[] = Customer::where('customertypeid', $customertypeid)->first();

                        if ($customertypeid == 10)  $recipients[] = Customer::Find($ownerid);

                        if (($customertypeid < 110) AND ( $customertypeid > 10))
                        {
                            foreach (Customer::where('ownerid', $ownerid)->where('customertypeid', $customertypeid)->get() as $recpient) $recipients[] = $recpient;
                        }
                        if ($customertypeid == 1000) $recipients[] = Customer::Find($customerid);
                        if ($customertypeid == 110)
                        {
                            foreach ($house->getMaidid() as $maidid)
                            {
                                $maidid = Customer::Find($maidid);
                                $recipients[] = $maidid;
                            }
                        }
                    }
                }
            } else $recipients = [$onereceipient];


            $topeople = '';
            foreach ($recipients as $recipient)
            {
                $culture = $recipient->culture->culture;
                $replacearray = [
                    '*CUSTOMERNAME*' => $customer->name,
                    '*RECEIPIENTNAME*' => $recipient->name,
                    '*RECIPIENTNAME*' => $recipient->name,
                    '*ACCOUNTPOSTS*' => $contract->getAccountposts($culture),
                    '*PREPAYMENTAMOUNT*' => $contract->getPrepaymentamount(),
                    '*FINALPAYMENT*' => static::format($accountidbalance->balance/$accountidbalance->usedrate, 2, $culture),
                    '*CONTRACTAMOUNT*' => static::format($contract->finalprice, 2, $culture),
                    '*CURRENCY*' => $contract->currency->currencysymbol,
                    '*PERIOD*' => $contract->getPeriodtext($culture),
                    '*HOUSENAME*' => $house->name . ', ' . $house->address1 . ', ' . $house->country,
                    '*ORDER*' => $contract->getOrder($culture),
                    '*OWNER*' => $owner->name,
                    '*CONTRACTID*' => $contract->id,
                    '*TIMELINK*' => $customer->getTimelink($contract->id, $culture),
                    '*TESTIMONIALLINK*' => $customer->getTestimoniallink($house->id, $culture),
                    '*CUSTOMERLOGIN*' => $customer->getLoginlink($culture),
                    '*LOGIN*' => $customer->email, //TODO: Change the method according to Laravel
                    '*PASSWORD*' => $customer->plain_password, //TODO: change this according the new methods
                    '*OWNERINFO*' => $owner->getCustomerinfo(),
                    '*CUSTOMERCURRENCY*' => $contract->convertCurrencyPeriodToCustomer('currencysymbol'),
                    '*MESSAGE*' => $contract->message,
                    '*PERSONS*' => $contract->persons,
                    '*ARRIVALTIME*' => $contract->getArrival($culture),
                    '*DEPARTURETIME*' => $contract->getDeparture($culture)];

                $topeople .= ' and ' . $recipient->email . ' (id: ' . $recipient->id . ' )';
                $helptext .= $customerid . ': ' . $replacearray['*CUSTOMERNAME*'] . '   ';
                $emailobject = StandardemailI18n::where('id', $emailid)->where('culture', $culture)->first();
                $emailcontent = $emailobject->contents;
                $emaildescription = __(Standardemail::Find($emailid)->description, [], $culture);

                //Execute all placemakers
                foreach ($replacearray as $key => $value)
                {
                    $emailcontent = str_replace($key, $value, $emailcontent);
                }
                $mailtext = $emailcontent;

                //Temp. hack: do not show password advaita
                $mailtext = str_replace('advaita', 'mitpassword', $mailtext);

                //Check for customer messages, only to display if message
                //The text within the enclosed *IFMESSAGE* will only be displayed if
                //the message is non-empty.
                if (strpos($mailtext, '*IFMESSAGE*') > 0)
                {
                    if (!($contract->message)) {
                        $mailtextarray = explode('*IFMESSAGE*', $mailtext);
                        if (sizeof($mailtextarray) > 2)
                            $mailtext = $mailtextarray[0] . $mailtextarray[2];
                    } else
                        $mailtext = str_replace('*IFMESSAGE*', '', $mailtext);
                }

                $attchmentdoc = [];
                $position1 = strpos($mailtext, '*ATTACHMENT*');
                if ($position1 > 0) {
                    $position1 = $position1 + strlen('*ATTACHMENT*');
                    $position2 = strpos($mailtext, '*ATTACHMENT*', $position1);
                    $attachmentstring = substr($mailtext, $position1, $position2 - $position1);
                    $attachmentarray = explode(';', $attachmentstring);
                    echo $attachmentstring;
                    $mailtext = str_replace('*ATTACHMENT*', '', $mailtext);
                    $mailtext = str_replace($attachmentstring, '', $mailtext);
                    $attchmentdir = public_path() . '/housedocuments/' . $house->id . '/'; //TODO: check public_path()
                    foreach ($attachmentarray as $key => $value)
                        $attchmentdoc[$key] = $attchmentdir . $value;
                }

                $mailtext = str_replace("\n", '', $mailtext);
                $mailtext = str_replace("\r", '<br/>', $mailtext);
                $emailaddress = $recipient->email;
                $from = $owner->email;
                $subject = $emaildescription;

                // The Middleware used in web part is not used here, so the config is the "raw" config
                if ('' != config('app.testmessage', ''))
                {
                    $subject = 'Testmail only test from new rental system: ' . $emaildescription;
                    $emailaddress = 'jbr@consiglia.dk';
                }
                if (((1 == config('app.mailactive', true)) || ( substr($emailaddress, -12) == 'consiglia.dk')) && ($lockbatchvalue < 2))
                {
                    //public function __construct($contents, $subject = '', $fromaddress = 'jbr@consiglia.dk', $fromName = 'testFromName', $toName = '', $attachements = [])
                    Mail::to($emailaddress)
                        ->send(new DefaultMail($mailtext, $subject, $from, $owner->name, $recipient->name, $attchmentdoc));
                }

                if (!($r)) $r = $mailtext;

                //Save to E-mail log
                $emaillog = new Emaillog();
                $emaillog->customerid = $customerid;
                $emaillog->houseid = $houseid;
                $emaillog->ownerid = $ownerid;
                $emaillog->from = $from;
                $emaillog->to = $emailaddress;
                $emaillog->cc = '';
                $emaillog->text = $mailtext;
                if ($lockbatchvalue < 2)   $emaillog->save();
            }

            if (($lockbatchvalue == 0) OR ( $lockbatchvalue == 2))
            {
                //Cancellation due to no or too late payment of pre-payment
                if ($batchtask->batchfunctionid == 1) {
                    Contract::commitOrder(40, 0, $contractid, $customerid);
                }

                //Just insert new accountpost
                if (($batchtask->useaddposttypeid == 1) AND ( $batchtask->addposttypeid > 0)) {
                    Contract::commitOrder($batchtask->addposttypeid, 0, $contractid, $customerid);
                }
            }

            if (($lockbatchvalue == 0) OR ( $lockbatchvalue == 2)) $batchlog->statusid = 2;
            $batchlog->emailid = $emailid;
            $batchlog->save();
        }
        return $r;
    }

    /*
     * This method executes the tasks queued in the batchlog
     */
    public static function addtoqueue()
    {
        $lockbatch = [];
        foreach (House::all() as $house) $lockbatch[$house->id] = $house->lockbatch;
        /*
         * Lockbatch values
         *
         * The lock applies to each house, default value is 0
         *  0 no lock
         *  1 the queue is populated, testmails are send, but the queue status remains as not executed.
         *  2 the queue is populated, no mails are send, maillog not updated, but the queue status is updated
         *  3 the queue is not populated, the queue is not executed
         */

        $batchviews = DB::table('batchview')->where('created_at', '>', Carbon::parse('2009-01-01'))->get();
        foreach ($batchviews as $batchview)
        {
            echo 'Looking at accountpostid: ' . $batchview->id . ' using batchtaskid: ' . $batchview->batchtaskid . "\n";
            $triggerpaymessage = Carbon::now()->subSeconds(300);
            $contractid = $batchview->contractid;
            $posttypeid = $batchview->posttypeid;
            //The one below is the only datatime in batchview
            $contractdate = Carbon::parse($batchview->created_at);
            $balance = $batchview->balance;
            $contractamount = $batchview->amount;

            //Allow for corrections by the administrator before we add to the queue:
            if ($contractdate->gt($triggerpaymessage))
            {
                if (($posttypeid == 50) OR ($posttypeid == 100)) continue;
            }

            $contract = Contractoverview::Find($contractid);
            $periodstart = $contract->from;
            $periodend = $contract->to;
            $status = $contract->status;

            if ($contractamount > 0) $leftratio = ($contractamount - $balance)/$contractamount;
            else $leftratio = 1;

            //Check if the issue is OK, if so continue
            if ($batchview->usedontfireifposttypeid == 1)
            {
                $count = Accountpost::where('contractid', $contractid)->where('posttypeid', $batchview->dontfireifposttypeid)->where('passifiedby', 0)->count();
                if ($count > 0) continue;
            }


            //Check conditions
            //True means: the action should be queued, provided other conditions are met.
            //Time from order condition
            //If there are no time conditions at all, the time condition is set to true
            $TimeFromOrderCondition = true;
            if ($batchview->usetimedelaystart== 1)
            {
                //$TimeFromOrderCondition = ((time() - $contractdate) / $secondsperday > $batchview->getTimedelaystart());
                $TimeFromOrderCondition = $contractdate->addDays($batchview->timedelaystart)->lt(Carbon::now());
                echo("TimeFromOrderCondition: $TimeFromOrderCondition \n");
            }
            //Time to period start
            $TimeToStartCondition = true;
            if ($batchview->usetimedelayfrom == 1)
            {
                $timeto = $batchview->timedelayfrom; //unit: days
                if ($timeto > 0)
                {
                    //$TimeToStartCondition = (($periodstart - time()) / $secondsperday < $timeto);
                    $TimeToStartCondition = $periodstart->subDays($timeto)->lt(Carbon::now());
                }
                else
                {
                    $TimeToStartCondition = $periodend->addDays(-$timeto)->lt(Carbon::now());
                }
            }

            //If both time conditions are switched on, the task is triggered if one of them is due!
            $timecondition = ($TimeFromOrderCondition && $TimeToStartCondition);
            if (($batchview->usetimedelaystart == 1) && ($batchview->usetimedelayfrom == 1)) $timecondition = ($TimeFromOrderCondition || $TimeToStartCondition);


            //Check moneycondition, if true, the payment conditions NOT been met
            $moneycondition = true;
            if ($batchview->usepaymentbelow == 1)
            {
                if ($batchview->paymentbelow > 0)
                {
                    $moneycondition = ($leftratio < $batchview->paymentbelow);
                    if ($contractamount == 0) $moneycondition = false;
                }
                //For negative conditions, we may fire if paid amount is greater than a certain value.
                else
                {
                    $moneycondition = ($leftratio > -$batchview->paymentbelow);
                    if ($contractamount == 0) $moneycondition = true;
                }

            }

            //Check presense of certain posttypeid
            $posttypeidpresent = true;
            if ($batchview->userequiredposttypeid == 1)
            {
                if (Accountpost::where('contractid', $contractid)->where('posttypeid', $batchview->requiredposttypeid)->count() > 0) $posttypeidpresent = false;
            }
            $totalcondition = ($timecondition AND $moneycondition AND $posttypeidpresent);

            //Prevent registration if not Committed, except when sent info. about being "kicked out" due to lack og payment
            if (($batchview->posttypeid != 40) AND ($status != 'Committed')) $totalcondition = false;

            if ($totalcondition)
            {
                $batchlog = new Batchlog();
                $batchlog->statusid = 1;
                if ($lockbatch[$batchview->houseid] > 2) $batchlog->statusid = 0;
                $batchlog->posttypeid = $batchview->posttypeid;
                $batchlog->batchtaskid = $batchview->batchtaskid;
                $batchlog->contractid = $batchview->contractid;
                $batchlog->accountpostid = $batchview->id;
                $batchlog->emailid = null;
                $batchlog->customerid = $batchview->customerid;
                $batchlog->houseid = $batchview->houseid;
                $batchlog->ownerid = $batchview->ownerid;
                $batchlog->save();
            }
        }
    }

}
