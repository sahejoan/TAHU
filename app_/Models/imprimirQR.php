<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;
use SimpleSoftwareIO\QrCode\QrCodeServiceProvider;

class imprimirQR extends Model
{
  public function getImagen($cedula, $nombre, $apellido, $ancho)
  {
        try {
          $writer = new PngWriter();

          $text = $cedula;
          $etiqueta = $cedula.'--'.$nombre.'--'.$apellido;

          $qrCode = new QrCode($text);
          $qrCode->setSize($ancho);   
          $qrCode->setEncoding(new Encoding('UTF-8'));
          $qrCode->setMargin(10);
          $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelLow());
          $qrCode->setRoundBlockSizeMode(new RoundBlockSizeModeMargin());
          $qrCode->setForegroundColor(new Color(0, 0, 0));
          $qrCode->setBackgroundColor(new Color(255, 255, 255));

          if (env('APP_IMAGEN_QR')==true) {
            $logo = Logo::create(env('APP_URL_NAME').'\\image\\qrcode\\'.'logo.png')
            ->setResizeToWidth(110);
          } else {
            $logo=null;
          }

          $label = Label::create($etiqueta)
          ->setTextColor(new Color(0, 0, 0))
          ->setFont(new NotoSans(7));

          $result = $writer->write($qrCode, $logo, $label);

          // Directly output the QR code
          //header('Content-Type: '.$result->getMimeType());
          //echo $result->getString();

          // Save it to a file
          //$result->saveToFile(env('APP_URL_NAME').'\\image\\qrcode\\'.'qrcode.png');

          $dataUri = $result->getDataUri();

          return $dataUri;              

        } catch (Exception $e) {    
        }        
   }
}

?>