<?php
ob_end_clean();

//$pdf = new FPDF();
//$pdf->AddPage('P','A4');
//$pdf->AddPage('P','Letter');

//$pdf = new JLPDF();
//$pdf->AddPage();    

$pdf = new PDF_HTML();
$pdf->AddPage();    

//$pdf = new PDF_MC_Table('L','mm',array(215,355));
//$pdf->AddPage();    

//*$pdf = new PDF_MC_Table('P','mm',array(215,355));
//*$pdf->AddPage();    

$pdf->SetLeftMargin(30);
//$pdf->SetToptMargin(25);
$pdf->SetRightMargin(20);

$pdf->SetFont('Arial','',12);

$pdf->Image('img/logo1.jpg',30,15,50,20,'jpg');

$pdf->Ln(25);
$pdf->Cell(100,2,utf8_decode('N° *************'),0,0,'L');
$pdf->Ln(5);
$pdf->MultiCell(0,5,'MEMORANDO',0,'C');
$pdf->Ln(5);
$pdf->Cell(150,2,'PARA:        '.utf8_decode(' NOMBRE DEL FUNCIONARIO '),0,0,'L');
$pdf->Ln(5);
$pdf->Cell(150,2,'                    '.utf8_decode('CÉDULA'),0,0,'L');
$pdf->Ln(5);
$pdf->Cell(150,2,'                    '.utf8_decode('CARGO DEL FUNCIONARIO'),0,0,'L');

$pdf->Ln(10);
$pdf->Cell(100,2,'DE:             '.utf8_decode(' NOMBRE GRENTE '),0,0,'L');
$pdf->Ln(5);
$pdf->Cell(100,2,'                    '.utf8_decode('REGION'),0,0,'L');

$pdf->Ln(10);
$pdf->Cell(100,2,'FECHA:       '.'  /  /    ',0,0,'L');
$pdf->Ln(5);
$pdf->Cell(100,2,'ASUNTO:    '.utf8_decode('DESIGNACIÓN'),0,0,'L');

$pdf->Ln(10);
$pdf->Cell(100,2,' ',0,0,'L');
$pdf->Ln(10);

$parrafo1 = utf8_decode('     Tengo el agrado de dirigirme a Usted en la oportunidad de extenderle un ');
$parrafo1 .= utf8_decode('cordial saludo Bolivariano, Revolucionario, Socialista y a la vez, informarle que ');
$parrafo1 .= utf8_decode('apartir de la fecha de su notificación  ha sido designado para cumplir funciones ');
$parrafo1 .= utf8_decode('en el Área de ******** - Dependencia *********************, bajo la supervición directa del Jefe *de la dependencia o del sector, ');
$parrafo1 .= utf8_decode('conservando su adscripción a esta Gerencia Regional de Tributos Internos region****.');

$parrafo2 = utf8_decode('     Espero que asuma las funciones que le serán ');
$parrafo2 .= utf8_decode('asignadas apartir de ahora, con la misma eficiencia y eficacia demostrada hasta este momento para el logro de los objetivos de esta organización.');

//Forma1
//$pdf->MultiCell(0,5,$parrafo1,0,'J');
//$pdf->Ln(3);
//$pdf->MultiCell(0,5,$parrafo2,0,'J');

//Forma2 no justifca
//$html = "<div style='text-align: justify;'>".$parrafo1."</div>";
//$pdf->WriteHTML($html);
//$pdf->Ln(5);
//$pdf->Cell(100,2,' ',0,0,'L');
//$pdf->Ln(5);
//$html = "<div style='text-align: justify;'>".$parrafo2."</div>";
//$pdf->WriteHTML($html);

//Forma3
$html = '<div style="text-align: justify;">'.$parrafo1.'</div>';
$pdf->MultiCell(0,5,$pdf->WriteHTML($html),0,'J');
$pdf->Ln(10);
$html = '<div style="text-align: justify;">'.$parrafo2.'</div>';
$pdf->MultiCell(0,5,$pdf->WriteHTML($html),0,'J');


//forma4 con texto enriquesido pero corto la columna
//$html = $parrafo1;
//$pdf->JLCell("$html",150,'j');
//$pdf->Ln(5);
//$html = $parrafo2;
//$pdf->JLCell("$html",150,'j');

$pdf->Output();
$pdf->close();     
?>
