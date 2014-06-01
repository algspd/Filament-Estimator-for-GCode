<?php

/**
 * Filament Length/Weight/Volume Estimator for 4d G-Code
 * Original Perl code by jag http://www.thingiverse.com/thing:15499 under Public Domain
 *
 * This PHP translation code by algspd (@gmail.com) under "THE BEER-WARE LICENSE"
 *
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <algspd@gmail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return
 * ----------------------------------------------------------------------------
 *
**/

$filament_diam=3;
$filament_density=1.05;   # density in g/cc 

$total=0;
$last=0;
$line_num=0;

$opts=getopt('s:d:f:h');

if (array_key_exists("s",$opts)) { 
  $filament_diam = $opts["s"];
}

if (array_key_exists("d",$opts)) {
  $filament_density=$opts["d"];
}

$filename="-";

if (array_key_exists("f",$opts)) {
  $filename=$opts["f"];
}

if (array_key_exists("h",$opts))  {
  echo "\nUsage:\n";
  echo "filament_length [-s filament diameter] [-d filament density] [-f filename]\n";
  echo "    The density of ABS is ~1.05 g/cc, PLA is about 1.25 g/cc\n";
  exit;
}


echo "Filament diameter is $filament_diam mm\n";
echo "Filament density is $filament_density g/cc\n";


$filament_area = ($filament_diam/20)*($filament_diam/20) * 3.14159;

echo "Cross sectional area of filament: $filament_area cm^2\n";

if ($filename) {
  $file=fopen("$filename","r");
} 


while ($line = fgets($file)) {   

    rtrim($line); //chomp($line);
    
    if ( 1==preg_match("/G1.*E *(\d+\.\d*)/i",$line,$match) ) {
      $last = $match[1];
      
    } else if (1==preg_match("/G92.*E0/",$line,$match) ) {
      $total += $last/10;
      $last=0;
    }
    $line_num++;
}

$total += $last/10;
printf ("\nTotal filament: %.3f cm\n", $total);



$volume = $filament_area * $total;
printf ("Volume: %.3f cc\n", $volume);
$grams = ($filament_density * $volume);
printf ("Weight: %.3f g\n", $grams);

?>