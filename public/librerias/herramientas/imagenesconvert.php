<?php
class ConvertirImagen{

  function redim($ruta1, $ruta2, $ancho, $alto) {
    //Se obtene la dimension y tipo de imagen
    $datos=getimagesize($ruta1);
    
    $ancho_orig = $datos[0];    //Anchura de la imagen original
    $alto_orig  = $datos[1];    //Altura de la imagen original
    $tipo       = $datos[2];
    
    if ($tipo==1) {
       if (function_exists("imagecreatefromgif")) {
          $img = imagecreatefromgif($ruta1);
       } else {
          return false;
       }
    } 

    if ($tipo==2) {
       if (function_exists("imagecreatefromjpeg")) {
          $img = imagecreatefromjpeg($ruta1);
       } else {
          return false;
       }
    }
    
    if ($tipo==3) {
        if (function_exists("imagecreatefrompng")) {
            $img = imagecreatefrompng($ruta1);
        } else {
            return false;
        }
    }    

    if ($ancho_orig > $alto_orig) {
        $ancho_dest = $ancho;
        $alto_dest = ($ancho_dest / $ancho_orig) * $alto_orig;
    } else {
        $alto_dest = $alto;
        $ancho_dest = ($alto_dest / $alto_orig) * $ancho_orig;
    }

    //imagecreatetruecolor, solo estan en G.D. 2.0.1 con PHP 4.0.6+
    $img2 = @imagecreatetruecolor($ancho_dest,$alto_dest) or $img2 = imagecreate($ancho_dest,$alto_dest);
    $color = @imagecolorallocatealpha($img2, 255, 255, 255, 1);
    @imagecolortransparent($img2,$color);
    @imagefill($img2, 0, 0, $color);

    //Redimensionar
    //imagecopyresampled, solo estan en G.D. 2.0.1 con PHP 4.0.6+
    @imagecopyresampled($img2,$img,0,0,0,0,$ancho_dest,$alto_dest,$ancho_orig,$alto_orig) or imagecopyresized($img2,$img,0,0,0,0,$ancho_dest,$alto_dest,$ancho_orig,$alto_orig);

    //Crear fichero nuevo, según extensión.
    if ($tipo==1) {
       if (function_exists("imagegif")) {
          imagegif($img2, $ruta2);
       } else {
          return false;
       }
     }
    if ($tipo==2) {
       if (function_exists("imagejpeg")) {
          imagejpeg($img2, $ruta2);
       } else {
         return false;
       }
    }    
    if ($tipo==3) {
       if (function_exists("imagepng")) {
            imagepng($img2, $ruta2);
        } else {
            return false;
        }
    }
    return true;
  }
}
?>