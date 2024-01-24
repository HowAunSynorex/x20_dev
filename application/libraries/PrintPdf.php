<?php
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\PrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\ImagickEscposImage;


require_once APPPATH . 'third_party/Mike42/Escpos/EscposImage.php';
require_once APPPATH . 'third_party/Mike42/Escpos/ImagickEscposImage.php';
require_once APPPATH . 'third_party/Mike42/Escpos/GdEscposImage.php';
require_once APPPATH . 'third_party/Mike42/Escpos/NativeEscposImage.php';
require_once APPPATH . 'third_party/Mike42/Escpos/CapabilityProfile.php';
require_once APPPATH . 'third_party/Mike42/Escpos/CodePage.php';
require_once APPPATH . 'third_party/Mike42/Escpos/Printer.php';
require_once APPPATH . 'third_party/Mike42/Escpos/PrintConnectors/PrintConnector.php';
require_once APPPATH . 'third_party/Mike42/Escpos/PrintConnectors/FilePrintConnector.php';
require_once APPPATH . 'third_party/Mike42/Escpos/PrintConnectors/WindowsPrintConnector.php';
require_once APPPATH . 'third_party/Mike42/Escpos/PrintBuffers/PrintBuffer.php';
require_once APPPATH . 'third_party/Mike42/Escpos/PrintBuffers/EscposPrintBuffer.php';

// $directory = APPPATH.'third_party/Mike42/Escpos';
// req($directory);

// foreach(['Devices', 'Experimental', 'PrintBuffers','PrintConnectors','resources',] as $e) {
//     req($directory.'/'.$e);
// }

// function req($dir){
//     foreach (glob($dir . '/*.php') as $filename) {
//         require_once $filename;
//     }
// }

class PrintPdf
{
    public function __construct()
    {
        // Initialize any configurations or settings here if needed
    }

    public function printPdf($pdf, $printerName = 'TM-T88VI')
    {
        // $connector = new WindowsPrintConnector('TM-T88VI'); // Specify the printer name
        // $connector = new WindowsPrintConnector($printerName);
        // $printer = new Printer($connector); 
        
        // try {
        //     $pages = ImagickEscposImage::loadPdf($pdf);
        //     foreach ($pages as $page) {
        //         $printer -> graphics($page);
        //     }
        //     $printer -> cut();
        // } catch (Exception $e) {
        //     /*
        // 	 * loadPdf() throws exceptions if files or not found, or you don't have the
        // 	 * imagick extension to read PDF's
        // 	 */
        //     echo 'Error: '.$e -> getMessage() . "\n";exit;
        // } finally {
        //     $printer -> close();
        // }
    }
}
