<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 20-10-2018
 * Time: 17:05
 */
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Accountpost;

/**
 * Class checktranslations ensures that the translation files all have the same keys
 * and that they are ordered alphabethically.
 * @package App\Console\Commands
 */
class checktranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:checktranslations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Goes through a predefined set of translation files in /resources/lang and performs some operations on each file: checks the keys and ensures that the keays are identical. If no translation is given, the key is used. Finally the files are ordered after the keys.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filenames = [ base_path().'/resources/lang/da_DK.json',  base_path().'/resources/lang/en_GB.json',  base_path().'/resources/lang/es_ES.json'];
        $keys = null;
        foreach ($filenames as $filename)
        {
            $contents = file_get_contents($filename);
            $json = json_decode($contents, true);
            $keys = ($keys)?array_unique(array_merge(array_keys($json),$json)):array_keys($json);
        }
        asort($keys);
        foreach ($filenames as $filename)
        {
            $contents = file_get_contents($filename);
            $json = json_decode($contents, true);
            uksort($json, function ($a, $b) {
                $a = mb_strtolower($a);
                $b = mb_strtolower($b);
                return strcmp($a, $b);
            });
            foreach($keys as $key)
            {
                if (!array_key_exists($key, $json)) $json[$key] = $key;
            }
            uksort($json, function ($a, $b) {
                $a = mb_strtolower($a);
                $b = mb_strtolower($b);
                return strcmp($a, $b);
            });
            $contents = json_encode($json, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
            file_put_contents($filename, $contents);
        }
    }
}
