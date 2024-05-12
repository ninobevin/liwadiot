<?php

namespace App\Http\Controllers;
use JasperPHP\JasperPHP as JasperPHP;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    //

    public function index(){

         $jasper = new JasperPHP;
        
            // Compile a JRXML to Jasper
        //  $t=  $jasper->compile( '/var/www/html/public/jasper/hello_world.jrxml')->execute();
         
       // var_dump($t);
     
            // Process a Jasper file to PDF and RTF (you can use directly the .jrxml)
            $jasper->process(
                '/var/www/html/public/jasper/hello_world.jrxml',
                false,
                array("pdf", "rtf"),
                array("php_version" => "7.2.34")
            )->execute();

            return response()->file('/var/www/html/public/jasper/hello_world.pdf');
        
            // List the parameters from a Jasper file.
        //     $array = $jasper->list_parameters(
        //         '/var/www/html/public/jasper/hello_world.jrxml'
        //     )->execute();
        //   return   var_dump($array);
    }
}
