<?php
/*******************************************************************************
* Nikya eedomus Script Nuki Smartlock
********************************************************************************
* Plugin version : 1.0
* Author : Nikya
* Origine : https://github.com/Nikya/eedomusScript_nuki_smartlock
* Nuki Bridge HTTP-API : 1.6
*******************************************************************************/

$eedomusScriptsEmulatorDatasetPath = 'eedomusScriptsEmulator_dataset.json';
require_once ("./eedomusScriptsEmulator.php");

echo "<h1>eedomus Scripts Emulatorrffr : Exemple</h1>";
echo "<h3>Config file : $eedomusScriptsEmulatorDatasetPath</h3><pre>";

echo "\n getValue ";
print_r(getValue('val2'));
echo "\n setValue ";
print_r(setValue('val2', '202'));
echo "\n getValue ";
print_r(getValue('val2'));

echo "\n loadVar ";
print_r(loadVariable('var3'));
echo "\n saveVar ";
print_r(saveVariable('var3', '404'));
echo "\n loadVar ";
print_r(loadVariable('var3'));

echo "</pre>";
