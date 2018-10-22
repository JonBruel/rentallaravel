<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\Errorlog;
use App\Models\Customer;
use App\Mail\DefaultMail;
use Illuminate\Support\Facades\Mail;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception: Not implemented here but included in the render function.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response. The function alerts the user with some relatively limited
     * summary information. In addition, a much more elaborate error description is logged in the errorlog
     * table and a message is sent to the supervisor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        if (($exception) && (false))
        {
            //Count number of incident from same ipaddress from same session:
            $counter = session($_SERVER['HTTP_X_FORWARDED_FOR'],0) + 1;
            session([$_SERVER['HTTP_X_FORWARDED_FOR'] => $counter]);
            $path = session('sanitizedpath','not_found');
            $counterpath = session($path,0) + 1;
            session([$path => $counterpath]);

            $customermessage = 'We appologize: an error has occurred. <br />'
                                 . 'The error has been reported and an email is sent to the system manager.<br />'
                                 . 'If you were logged on, we may contact you to find out if <br />'
                                 . 'this happened during you order process.';

            session()->flash('error', $customermessage);

            //Save error information in errorlog
            $errorlog = new Errorlog();

            $errorlog->customermessage = $customermessage;
            $errorlog->stack = $exception->getTraceAsString();
            $customerid = session('customerid',-1);

            if ($customerid != -1)
            {
                $c = Customer::Find($customerid);
                $customer = 'User logged in as: '.$c->name.' ('.$customerid.') ';
            }
            else $customer = 'User is not logged in.';
            $situation = $customer;
            $situation .= '<br /> Url used: ' . $_SERVER["SERVER_NAME"];
            $situation .= '<br /> Proxy ip: ' . $_SERVER["REMOTE_ADDR"];
            $situation .= '<br /> User ip: ' . $_SERVER['HTTP_X_FORWARDED_FOR'];
            $situation .= '<br /> Error count: ' . $counter;
            $situation .= '<br /> User agent: ' . $_SERVER["HTTP_USER_AGENT"];
            $situation .= '<br /> This path: ' . session('sanitizedpath','not found');
            $situation .= '<br /> Last path: ' . session('sanitizedpath1back','not found');
            $situation .= '<br /> Previous path: ' . session('sanitizedpath2back','not found');
            $errorlog->situation = $situation;
            $errorlog->save();

            //Send mail if the error is not from the same request, or errors from ip address less than 5
            if (($counter < 5) && ($counterpath < 2))
            {

                $subject = 'Errormessage';
                $mailtext =  'Situation: ' . $situation . '<br />';
                $mailtext .=  'Customerinformation: ' . $customer . '<br />';
                $mailtext .=  '<br />Stack information below: <br />'.str_replace('#', '<br />#',$exception->getTraceAsString());
                Mail::to('jbr@consiglia.dk')
                    ->send(new DefaultMail($mailtext, $subject, 'jbr@consiglia.dk', 'System administrator','System administrator'));

            }
            //die($customermessage);
            return view('home/generalerror', ['error'=> $customermessage]);

        }
        return parent::render($request, $exception);
    }
}
