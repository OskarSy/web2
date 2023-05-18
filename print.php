<?php
    
ob_end_clean();
require('fpdf/fpdf.php');
  
// Instantiate and use the FPDF class 
$pdf = new FPDF();
  
//Add a new page
$pdf->AddPage();
  
// Set the font for the text
$pdf->SetFont('Arial', 'B', 18);
  
// Prints a cell with given text 
$pdf->MultiCell(0, 10, 'Navod: Ucitel sa prihlasi do aplikacie. Ma moznost vymedzit a vybrat z ktorych latexovych suborov si student bude generovat sadu prikladov. Definuje kolko bodov moze student za danu sadu ziskat. Ucitel vo svojom konte si moze prezerat studentovu vygenerovanu sadu prikladov ,  jeho odovzdane priklady  a jeho spravnost odpovedi. Taktiez ma moznost prezerania tabulky vsetkych studentov s informaciami o vygenerovanej sade ulohach , o pocte vygenerovanych uloh a o spravnosti uloh. Student sa  prihlasi do aplikacie a dostane moznost vybrat si z vymedzenych  sadu suborov od ucitela. Po vybrani sa mu vygeneruju priklady na ktore ma moznost odpovedat. Zaroven ma moznost si prezerat celu sadu prikladov a taktiez  vidi ktore priklady odovzdal a ktore nie.');

  
// return the generated output
$pdf->Output();
?>


  