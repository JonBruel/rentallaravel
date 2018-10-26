<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 20-10-2018
 * Time: 17:05
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use App\Helpers\PictureHelpers;
use App\Helpers\ShowCalendar;
use Illuminate\Pagination\Paginator;
use Auth;
use Schema;
use Gate;
use ValidationAttributes;
use App\Models\Batchtask;
use App\Models\Customer;
use App\Models\House;
use App\Models\Standardemail;
use App\Models\StandardemailI18n;
use App\Models\Config;
use App\Models\Errorlog;
use App\Models\Batchlog;
use App\Models\Culture;
use App;
use App\Models\Contractoverview;
use Carbon\Carbon;
use Symfony\Component\Process\Process;


/**
 * Class SetupController includes a range of setup functions or administrative functions used by the Administrator
 * or above of the system.
 *
 * @package App\Http\Controllers
 */
class SetupController extends Controller
{

    public function __construct() {
        parent::__construct(\App\Models\Batchtask::class);
    }

    /**
     * Shows the phpinfo for the site.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showphpinfo()
    {
        if (!Gate::allows('Supervisor')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        ob_start();
        phpinfo();
        $phpinfo = ob_get_clean();

        return view('/setup/phpinfo', ['phpinfo' => $phpinfo]);
    }

    /**
     * Feeds the view showing the filtered batchtasks.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function listbatchtasks()
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $models = Batchtask::filter(Input::all())->orderBy('id')->get();

        $owners = ['' => __('Please select owner')] + Customer::filter()->where('customertypeid', 10)->pluck('name', 'id')->toArray();
        $houses = ['' => __('Please select house')] + House::filter(Input::all())->pluck('name', 'id')->toArray();

        return view('/setup/listbatchtasks', ['models' => $models, 'houses' => $houses, 'owners' => $owners, 'ownerid' => Input::get('ownerid', ''), 'search' => Input::all()]);
    }

    /**
     * Feeds the view for editing a batcktask. When a batchtask has been edited the workflow system will be disabled until
     * the user actively enables it again. This is a protection against sistuations where a lot of automatic actions are
     * taken before the workflow setttings have been finalized.
     *
     * @param $id batchtask id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editbatchtask($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        //Find page from id
        if (Input::get('page') == null) {
            $models = Batchtask::filter(Input::all())->sortable('id')->pluck('id')->all();
            $page = array_flip($models)[$id]+1;
            Input::merge(['page' => $page]);
        }

        $models = Batchtask::filter(Input::all())->sortable('id')->paginate(1);

        $lockbatch = $models[0]->house->lockbatch;
        $lockbatchpossibilities = [ 0 => __('Batchtask system activated'),
                                    1 => __('Batchtask system activated as usual but NOT updated. Only test mails are dispatched.'),
                                    2 => __('The queue is updated, no mails are send, maillog is NOT updated, but the queue status is updated.'),
                                    3 => __('Batchtask system deactivated.')];

        $fields = Schema::getColumnListing($models[0]->getTable());
        return view('setup/editbatchtask', ['models' => $models,
            'lockbatch' => $lockbatch,
            'lockbatchpossibilities' => $lockbatchpossibilities,
            'vattr' => new ValidationAttributes($models[0])]);
    }

    /**
     * Updates the batchtask based on the input from the edirbatchtask view.
     *
     * @param $id batchtask id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatebatchtask($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $original = Batchtask::Find($id);

        //Lock batchtask
        $original->house->lockbatch = Input::get('lockbatch', 3);
        $original->house->save();


        //Fields with "normal" values
        $fields1 = ['name', 'activefrom', 'posttypeid', 'emailid', 'batchfunctionid', 'paymentbelow', 'requiredposttypeid',
                    'timedelaystart', 'timedelayfrom', 'addposttypeid', 'dontfireifposttypeid'];

        //Yes/no fields
        $fields2 = ['usepaymentbelow', 'userequiredposttypeid', 'usetimedelaystart', 'usetimedelayfrom',
                    'useaddposttypeid', 'usedontfireifposttypeid', 'active'];

        //Multiple yes/no fields
        $fields3 = ['mailto'];

        foreach($fields1 as $field) $original->$field = Input::get($field);
        foreach($fields2 as $field) $original->$field = Input::get($field,0);
        foreach($fields3 as $field) $original->$field = implode(',', Input::get($field, []));

        $errors = '';
        $success = 'Batcktask has been updated!';
        if (!$original->save()) {
            $errors = $original->getErrors();
            $success = '';
        }

        return redirect('/setup/editbatchtask/'.$id.'?ownerid='.$original->ownerid)->with('success', $success)->with('errors', $errors);
    }

    /**
     * Feeds the view showing the standaremails, filtered according to the scope of the user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function liststandardemails()
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $models = Standardemail::filter(Input::all())->orderBy('id')->get();



        $owners = ['' => __('Please select owner')] + Customer::filter()->where('customertypeid', 10)->pluck('name', 'id')->toArray();
        $houses = ['' => __('Please select house')] + House::filter(Input::all())->pluck('name', 'id')->toArray();

        return view('/setup/liststandardemails', ['models' => $models, 'houses' => $houses, 'owners' => $owners,
            'ownerid' => Input::get('ownerid', ''), 'search' => Input::all()]);
    }

    /**
     * Feeds the view for editing the text in many languages of a standard email.
     *
     * @param $id standardemail  id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editstandardemail($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        //Find page from id
        if (Input::get('page') == null) {
            $models = Standardemail::filter(Input::all())->sortable('id')->pluck('id')->all();
            $page = array_flip($models)[$id]+1;
            Input::merge(['page' => $page]);
        }

        $models = Standardemail::filter(Input::all())->sortable('id')->paginate(1);
        $cultures = Culture::all();
        $languages = [];
        foreach($cultures as $culture) $languages[$culture->culture] = $culture->culturename;
        //Get email text
        $standardemailcontents = [];
        $cultures = explode(';', config('app.cultures'));
       // die(config('app.cultures'));
        foreach ($cultures as $culture) {
            $standardemail = StandardemailI18n::where('id', $models[0]->id)->where('culture', $culture)->first();
            $standardemailcontents[$culture] = ($standardemail)?$standardemail->contents:'';
        }
        $values = $models[0]->toArray();
        $new = new StandardemailI18N($values);



        return view('setup/editstandardemail', ['models' => $models,
            'vattr' => new ValidationAttributes($models[0]),
            'languages' => $languages,
            'standardemailcontents' => $standardemailcontents]);
    }


    /**
     * Updates the standard email in several languages based on the input from the editstandardemail view.
     *
     * @param $id standardemail id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatestandardemail($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $errors = '';
        $success = 'Standardemail has been updated!';
        $keys = '';

        //Update displayed cultere dependent contents
        $updatedcultures = [];
        foreach(Input::get('contents', []) as $key => $contents)
        {
            //Check if key exists, if not create the record
            $i18n = StandardemailI18N::where('id', $id)->where('culture', $key)->first();
            if (!$i18n) $i18n = new StandardemailI18N();

            //Update and save the record
            $i18n->contents = $contents;
            $i18n->culture = $key;
            $i18n->id = $id;
            //TODO: Check handling of error
            if (!$i18n->save())
            {
                $errors = $i18n->getErrors();
                $success = '';
            }
            $updatedcultures[] = $key;
        }

        $original = Standardemail::Find($id);
        $original->description = Input::get('description');
        if (!$original->save()) {
            $errors = $original->getErrors();
            $success = '';
        }

        return redirect('/setup/editstandardemail/'.$id.'?ownerid='.$original->ownerid)->with('success', $success)->with('errors', $errors);
    }


    /**
     * This function is the first step for making new batch tasks for existing houses. The idea is
     * that it is too complicated to start from scratch, so the ovwe or supervisor is
     * suggested to copy existing batch task from the tasks belonging to the house  with id=0.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function makebatch1()
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $houses = House::filter()->sortable('id')->where('id', '>', 0)->paginate(25);
        $batchexistss = [];
        foreach ($houses as $house)
        {
            $id = $house->id;
            $batchexistss[$id] = 0;
            if ((Batchtask::where('houseid', $id)->first()) && (Standardemail::where('houseid', $id)->first()))
            {
                $batchexistss[$id] = 1;
            }
        }
        return view('/setup/makebatch1', ['houses' => $houses, 'batchexistss' => $batchexistss, 'search' => Input::all()]);
    }

    /**
     * The function copies the batchtasks and standardemails belonging to house with id = 0 to the houses filtered to.
     * Both tables have set up an unique key name (or description), houseid.
     * This is used as a way to update the records without having to delete them first.
     * We want to avoid deletion due to foreign key contraints....
     *
     * @param int $houseid
     * @param 0|1 $overwrite
     * @param 0|1 $batchexists
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function copybatch($houseid, $overwrite, $batchexists)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $goahead = false;
        $success = __('Nothing done');
        if ((Input::get('answer', 'no') == 'yes') && ($houseid > 0)) $goahead = true;
        if ($goahead)
        {
            House::copyBatchAndMail($houseid, $overwrite);
            $success = __('Batch system updated');
        }
        if ((Input::get('answer', 'nothing') == 'nothing') && ($houseid > 0)) return view('setup/copybatch', ['houseid' => $houseid, 'overwrite' => $overwrite, 'batchexists' => $batchexists]);
        return redirect('setup/makebatch1')->with('success', $success);
    }

    /**
     * Feeds the view for searching, editing and creating translations.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edittranslations()
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        if (Input::get('Save'))  $this->savetranslations();

        $cultures = explode(';', config('app.cultures'));
        //$cultures = ['da_DK'];
        $searchkey = strtolower(Input::get('searchkey', 'search'));
        $id = session('defaultHouse');
        $translations = [];
        foreach ($cultures as $culture)
        {
            $defaultsearch[$culture] = '';
            $contents = file_get_contents(base_path().'/resources/lang/'.$culture.'.json');
            $translations[$culture] = json_decode($contents, true);
            if ($searchkey != '') $translations[$culture] = array_filter(json_decode($contents, true),
                function($key) use ($searchkey) { return (strpos(strtolower($key), $searchkey) !== false);} ,ARRAY_FILTER_USE_KEY);
        }

        $textsearches = Input::get('text', $defaultsearch);

        //The text search is somewhat tricky: We search the first language listed, subsequent ones will be ignored. When we har found
        //matches, the other arrays are limited to contain the same keys as the keys found.
        foreach($textsearches as $culture => $textsearch)
        {
            if ($textsearch != '')
            {
                $textsearch = strtolower($textsearch);
                $translations[$culture] = array_filter($translations[$culture],
                    function($value, $key) use ($textsearch) { return (strpos(strtolower($value), $textsearch) !== false);} ,ARRAY_FILTER_USE_BOTH);

                $keys = array_keys($translations[$culture]);
                $othercultures = array_diff($cultures, [$culture]);
                foreach ($othercultures as $cult)
                {
                    $translations[$cult] = array_filter($translations[$cult],
                        function($key) use ($keys) { return (in_array($key, $keys));} ,ARRAY_FILTER_USE_KEY);
                }
                break;
            }
        }

        //Turn it around so the key becomes the main entry
        $translationstartkey = [];
        $startculture = $cultures[0];
        foreach ($translations[$startculture] as $key => $notused)
        {
            foreach ($cultures as $culture)
            {
                $translation = $translations[$culture];
                if (array_key_exists($key, $translation))
                {
                    $translationstartkey[$key] = (array_key_exists($key,$translationstartkey))?$translationstartkey[$key] + [$culture => $translation[$key]]:[$culture => $translation[$key]];
                }
            }
        }
        $culturenames = [];
        foreach ($cultures as $culture)
        {
            $culturenames[$culture] = Culture::where('culture', $culture)->first()->culturename;
        }

        return view('/setup/edittranslations', ['translationstartkey' => $translationstartkey, 'textsearches' => $textsearches,
            'searchkey' => $searchkey, 'cultures' => $cultures, 'id' => $id, 'culturenames' => $culturenames]);
    }

    /**
     * Based on the input from the view edittranslations, we update the translations using the
     * static helper function updateTranslations($key, $translations)
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function savetranslations()
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $success = 'ABC';
        Input::merge(['Search' => 'Search']);
        Input::merge(['Save' => null]);
        $newkeytexts = Input::get('keytexts', []);
        $cultures = explode(';', config('app.cultures'));

        $success = '';
        $keys = Input::get('key', []);
        $deletes = Input::get('delete', []);
        if (sizeof($keys) > 0) foreach($keys as $key)
        {
            $translations = Input::get('translation')[$key];
            //New key, jkuuasg7892g is just a random string
            if ($key == 'jkuuasg7892g')
            {
                $key = Input::get('jkuuasg7892g');
            }
            static::updateTranslations($key, $translations);
            $success .= ' '.__('Translation with key').' '.$key.' '.__('updated').'.';

            //Check if key has been changed, then delete old and create new
            if (array_key_exists($key, $newkeytexts))
            {
                $newkey =  $newkeytexts[$key];
                if ($key != $newkey);
                static::destroyTranslations($key, $translations);
                static::updateTranslations($newkey, $translations);
            }
        }
        elseif (sizeof($deletes) > 0) foreach($deletes as $key)
        {
            $translations = Input::get('translation')[$key];
            static::destroyTranslations($key, $translations);
            $success .= ' '.__('Translation with key').' '.$key.' '.__('deleted').'.';
        }
        else $success = __('Nothing changed, did you tick off to indicate changes?');

       // return redirect ('/setup/edittranslations')->with('success', $success)->withInput(Input::all());
    }


    /**
     * Feeds the view for editing the translation related to images show in the gallery.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editcaptions()
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $this->checkHouseChoice('setup/editcaptions/?menupoint='.session('menupoint', 14080));
        $id = session('defaultHouse');
        $searchkey = 'gallery.'.$id.'.';
        $cultures = explode(';', config('app.cultures'));
        $translations = [];
        foreach ($cultures as $culture)
        {
            $contents = file_get_contents(base_path().'/resources/lang/'.$culture.'.json');
            $translations[$culture] = array_filter(json_decode($contents, true),
                function($key) use ($searchkey) { return (substr($key,0, strlen($searchkey)) == $searchkey);} ,ARRAY_FILTER_USE_KEY);
        }

        //Turn it around so the key becomes the main entry
        $translationstartkey = [];
        $startculture = $cultures[0];
        foreach ($translations[$startculture] as $key => $notused)
        {
            foreach ($cultures as $culture)
            {
                $translation = $translations[$culture];
                if (array_key_exists($key, $translation))
                {
                    $translationstartkey[$key] = (array_key_exists($key,$translationstartkey))?$translationstartkey[$key] + [$culture => $translation[$key]]:[$culture => $translation[$key]];
                }
            }
        }
        $culturenames = [];
        foreach ($cultures as $culture)
        {
            $culturenames[$culture] = Culture::where('culture', $culture)->first()->culturename;
        }
        return view('/setup/editcaptions', ['translationstartkey' => $translationstartkey, 'searchkey' => $searchkey, 'cultures' => $cultures, 'id' => $id, 'culturenames' => $culturenames]);
    }

    /**
     * Based on the input from editcaptions view we create, delete or update captions.
     *
     * @param int $id house id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatecaptions($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $cultures = explode(';', config('app.cultures'));
        $searchkey = 'gallery.'.$id.'.';
        $success = '';
        $keys = Input::get('key', []);
        $deletes = Input::get('delete', []);
        $keytexts = Input::get('keytexts', []);
        if (sizeof($keys) > 0) foreach($keys as $key)
        {
            $translations = Input::get('translation')[$key];
            if ($key == 'jkuuasg7892g')
            {
                $key = $searchkey.Input::get('jkuuasg7892g');
            }
            static::updateTranslations($key, $translations);
            $success .= ' '.__('Translation with key').' '.$key.' '.__('updated').'.';
        }
        elseif (sizeof($deletes) > 0) foreach($deletes as $key)
        {
            $translations = Input::get('translation')[$key];
            static::destroyTranslations($key, $translations);
            $success .= ' '.__('Translation with key').' '.$key.' '.__('deleted').'.';
        }
        else $success = __('Nothing changed, did you tick off to indicate changes?');
        return redirect ('/setup/editcaptions')->with('success', $success);
    }

    /**
     * Helper function for updating translations. The function ensures that all the resource files
     * hoding translations have the same keys and that they are sorted by the key.
     *
     * @param string $key of the text to be translated
     * @param array $culturetranslation which for each culture holds the translation
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function updateTranslations($key, $culturetranslation)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        //TODO: Safeguard against non-existing cultures
        foreach($culturetranslation as $culture => $translation)
        {
            $contents = file_get_contents(base_path().'/resources/lang/'.$culture.'.json');
            $json = json_decode($contents, true);
            $key = str_replace('_', ' ', $key); //We don't want underscores in picture captions, they are allowed in picture names.
            $json[$key] = $translation;
            uksort($json, function ($a, $b) {
                $a = mb_strtolower($a);
                $b = mb_strtolower($b);
                return strcmp($a, $b);
            });
            $contents = json_encode($json, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
            file_put_contents(base_path().'/resources/lang/'.$culture.'.json', $contents);
        }
    }

    /**
     * Helper function for deleting existing translations. The function ensures that all the resource files
     * hoding translations have the same keys and that they are sorted by the key.
     *
     * @param string $key of the text to be translated
     * @param array $culturetranslation which for each culture holds the translation. The tranlation is not used here.
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function destroyTranslations($key, $culturetranslation)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        //TODO: Safeguard against non-existing cultures
        foreach($culturetranslation as $culture => $translation)
        {
            $contents = file_get_contents(base_path().'/resources/lang/'.$culture.'.json');
            $json = json_decode($contents, true);
            unset($json[$key]);
            $contents = json_encode($json, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
            file_put_contents(base_path().'/resources/lang/'.$culture.'.json', $contents);
        }
    }

    /**
     * Feeds the view for editing the config table.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editconfig()
    {
        if (!Gate::allows('Supervisor')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $models = Config::filter(Input::all())->paginate(1);
        $fields = ['id', 'url', 'index'];
        $vattr = '';
        if ($models) $vattr = (new ValidationAttributes($models[0]))->setCast('id', 'hidden')->setCast('index', 'textarea');

        return view('/setup/editconfig', ['models' => $models, 'fields' => $fields, 'vattr' => $vattr]);
    }

    /**
     * Based in the input from the view editconfig this function updates the configuration.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateconfig()
    {
        if (!Gate::allows('Supervisor')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $id = Input::get('id');
        $model =  Config::findOrFail($id);
        $fields = ['id', 'url', 'index'];

        foreach ($fields as $field){
            $model->$field = Input::get($field);
        }
        //We save. The save validates after the Mutators have been used.
        $errors = '';
        $success = __('Configuration has been updated');
        if (!$model->save()) {
            $errors = $model->getErrors();
            $success = '';
        }
        return redirect('/setup/editconfig?url='.Input::get('url'))->with('success', $success)->with('errors', $errors);
    }

    /**
     * Not implemented
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function firstsetup()
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        //TODO: implement it
        return view('/setup/firstsetup', []);
    }


    /**
     * List the errors as reported during exceptions.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function listerrorlogs()
    {
        if (!Gate::allows('Supervisor')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $models = Errorlog::filter(Input::all())->orderBy('created_at', 'desc')->paginate(1);
        $fields = ['created_at', 'stack', 'customermessage', 'situation'];

        return view('/setup/listerrorlogs', ['models' => $models, 'fields' => $fields, 'search' => Input::all()]);
    }


    /**
     * Feeds a view listing the batchlog table
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function listqueue()
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $models = Batchlog::filter(Input::all())->orderBy('created_at', 'desc')->with(['posttype', 'batchtask', 'house'])->paginate(20);
        $model = new Batchlog();
        $fields = ['created_at', 'statusid', 'posttypeid', 'batchtaskid', 'contractid', 'emailid', 'houseid'];

        return view('/setup/listqueue', ['models' => $models, 'model' => $model, 'fields' => $fields, 'search' => Input::all()]);
    }

    /**
     * Feeds the view for editingbatchlogs.
     *
     * @param int $id batchlog id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editbatchlog($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $model = Batchlog::Find($id);
        $fields = ['id', 'created_at', 'statusid', 'posttypeid', 'batchtaskid', 'contractid', 'emailid', 'houseid'];
        $vattr = (new ValidationAttributes($model))->setCast('id', 'hidden');

        return view('/setup/editbatchlog', ['models' => [$model],  'fields' => $fields, 'search' => Input::all(), 'vattr' => $vattr]);
    }


    /**
     * Based on input from the editbatchlog view the batchlog is updated
     *
     * @param int $id batchlog id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatebatchlog($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $model = Batchlog::Find($id);
        $fields = ['created_at', 'statusid', 'posttypeid', 'batchtaskid', 'contractid', 'emailid', 'houseid'];
        foreach ($fields as $field) $model->$field = Input::Get($field);
        $success = __('Batchlog record changed.');
        $errors = '';
        if (!$model->save())
        {
            $errors = $model->getErrors();
            $success = '';
        }
        return redirect('/setup/listqueue')->with('success', $success)->with('errors', $errors);
    }

    /**
     * Deletes a batchlog.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function destroybatchlog($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        Batchlog::Find($id)->delete();
        return $this->listqueue();
    }


    /**
     * Start the gdpr delete command in the background and show the progress via ajax calls
     * which get the stated from a file updated during the process.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function gdprdelete()
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));

        $process = new Process('php artisan command:gdprdelete > /dev/null 2>&1 &', '/var/www/html/rentallaravel');
        $process->start();
        return view('/setup/gdprdelete');
    }

    /**
     * Start a process where the documentation is updated by phpDocumentor.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function updatephpdoc()
    {
        if (!Gate::allows('Supervisor')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $process = new Process('php artisan command:updatedocumentation > /dev/null 2>&1 &', '/var/www/html/rentallaravel');
        $process->start();
        return view('/setup/showdoc');
        //return redirect('/doc/index.html');
    }

    /**
     * Feeds the view that shows the documentation in an iframe.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showdocumentation()
    {
        if (!Gate::allows('Supervisor')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        return view('/setup/showdoc');
    }

    /**
     * Not implemented.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function workflow()
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        return view('/general/notimplemented');
    }

    /**
     * Not implemented.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function listbounties()
    {
        if (!Gate::allows('Supervisor')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        return view('/general/notimplemented');
    }
}
