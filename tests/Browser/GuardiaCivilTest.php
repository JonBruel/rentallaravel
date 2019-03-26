<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Log;
use App\Models\Identitypaper;


class GuardiaCivilTest extends DuskTestCase
{
    /**
     * @group guardia
     *
     * Test access to Guardia Civils side.
     *
     * @return void
     */
    public function testBasicExample()
    {


        $this->browse(function (Browser $browser) {
            config(['database.connections.mysql.host' => '192.168.160.55']);
            config(['database.connections.mysql.database' => 'rentallaravel']);
            //var_dump(config('database.connections.mysql.host'));
            //var_dump(config('database.connections.mysql.database'));
            // At this stage we don't use the database connection setup above.

            $path = base_path().'/storage/guardiacivil/';
            $files = array_diff(scandir($path), array('.', '..'));
            foreach ($files as $file)
            {
                //Copy to the file with the extensions 000
                $text = file_get_contents($path.$file);
                $gcid = explode('|', $text)[1];
                $filedate = ''.explode('|', $text)[3];
                $filename = $path.$gcid.'.000';
                $filetime = time($filedate);
                $contractid = explode('.', $file)[1];

                Log::notice("File is beting written for Guardia Civil, name: $filename, filedate: $filetime");
                file_put_contents($filename, $text);

                $browser->visit('https://hospederias.guardiacivil.es/hospederias/login.do')
                    ->value('[name="usuario"]', '07856AAXMS')
                    ->value('[name="pswd"]', '48D1801K03KU')
                    ->click('.button');
                $browser->visit('https://hospederias.guardiacivil.es/hospederias/cargaFichero.do')
                    ->click('[name="confirmacion"]')
                    ->click('[name="autoSeq"]')
                    ->attach('[name="fichero"]', $filename)
                    ->click('.button');

                $browser->assertDontSee('error');
                //The program will exit here if there are errors as per the assertion above
                unlink($filename);
                unlink($path.$file);
                // The deletion below is not working, the testdatabase is still used.
                // Identitypaper::where('contractid', $contractid)->delete();
            }


        });
    }
}
