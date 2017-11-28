<?php
/*******************************************************************************
* Nikya eedomus Script Emulator
********************************************************************************
* Version : 1.1
* Author : Nikya
* Origine : https://github.com/Nikya/eedomusScript_emulator
*******************************************************************************/

/** Chemin vers le fichier de donnée */
isset($eedomusScriptsEmulatorDatasetPath) or die("Variable 'eedomusScriptsEmulatorDatasetPath' inexistante");

/*******************************************************************************
* Récupère un argument $_GET[$var] et affiche un message d'erreur si l'argument n'est pas précisé.
* Le code API du périphérique courant peut être récupéré via getArg('eedomus_controller_module_id')
*/
function getArg($var, $mandatory = true, $default = ' ') {
	$v = strval($_GET[$var]);
	if(isset($v) and !empty($v) )
		return $v;
	else if ($mandatory) {
		throw new Exception( "Veuillez préciser la valeur de l'argument '$var' afin d'appeler ce script.");
		exit -1;
	}
	else
		return $default;
}

/*******************************************************************************
* Exécute une requête HTTP/HTTPS et retourne son résultat sous forme de chaine de caractère.
* Les arguments $action et $post peuvent être omis, ils peuvent être utilisés dans le cas de requêtes avancées comme un POST.
* L'argument $oauth_token est utilisé pour les scripts des objets connectés, dans les scripts personnels il peut donc être ommis ou passé à NULL
* L'argument $headers doit être fourni sous la forme d'un tableau, par exemple : $headers = array("X-Fbx-App-Auth: xxxx");
* L'argument cookies, vous permet d'activer la gestion des cookies pour la 1ère requête et celles qui suivront
*/
function httpQuery($url, $action = 'GET', $post = NULL, $oauth_token = NULL, $headers = NULL, $use_cookies = false) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $action);
	// $oauth_token
	// $headers
	// $use_cookies

	$curlRes = curl_exec($ch);
	if ($curlRes === false) {
		echo "Fail to call URL : $url";
		throw new Exception(curl_error($ch));
	}

	return $curlRes;
}

/*******************************************************************************
/** Sauvegarde le contenu d'une variable, ce qui la rend réutilisable lors d'une prochaine exécution du script (via loadVariable).
Remarque: Les variables portant le même nom ne sont pas visibles entre différents scripts. $variable_name doit être une chaine de caractères.
*/
function saveVariable($variable_name, $variable_content) {
	emulator_add2Dataset('vars', $variable_name, $variable_content);
}

/*******************************************************************************
* Charge le contenu d'une variable précédemment sauvée avec saveVariable()
* Remarque: Les variables portant le même nom ne sont pas visibles entre différents scripts. $variable_name doit être une chaine de caractères.
*/
function loadVariable($variable_name) {
	return emulator_readDataset('vars', $variable_name);
}

/*******************************************************************************
/** Convertie une chaine de caractère au format JSON vers le format XML (Utile notamment pour la réalisation de traitements xpath() par la suite)
*/
function jsonToXML($jsonStr) {
	$jArray = json_decode($jsonStr);
	$jErrorCode=json_last_error();

	if ($jErrorCode<>JSON_ERROR_NONE)
		throw new Exception("JSON_ERROR_$jErrorCode reading content $jsonStr");

	return emulator_array2xml($jArray, false);
}

function emulator_array2xml($array, $xml) {
	if($xml === false) {
		$xml = new SimpleXMLElement('<root/>');
	}

	foreach($array as $key => $value) {
		$key = preg_replace("/[^A-Za-z0-9_]/", 'e', $key);
		if (is_numeric($key)) $key = 'e'.$key;

		if(is_object($value)) {
			emulator_array2xml(get_object_vars($value), $xml->addChild($key));
		}
		else if(is_array($value)) {
			emulator_array2xml($value, $xml->addChild($key));
		} else {
			$xml->addChild($key, htmlspecialchars($value));
		}
	}

	return $xml->asXML();
}

/*******************************************************************************
/** Il s'agit des même accesseurs xpath/xquery que pour les capteurs HTTP eedomus
Le validateur xpath peuvent être utilisé pour les test:
http://doc.eedomus.com/xpath/
*/
function xpath($xml, $path) {
	return rand();
}

/*******************************************************************************
* Demande une action sur un périphérique via son code API
* Remarque 1 : l'action est exécutée de manière asynchrone, "au plus vite". En cas d'échec, elle sera retentée ultérieurement (ex. prise Z-Wave en limite de portée)
* Remarque 2 : le paramètre $verify_value_list est optionnel. S'il vaut true, la valeur ne sera acceptée que si elle existe parmi la liste des valeur référencées (ex. On/Off). L'activation de se paramètre ralentie très légèrement la fonction puisque des vérifications préliminaires sont nécessaires.
*/
function setValue($periph_id /*Code API*/, $value, $verify_value_list = false) {
	return 0;
}

/*******************************************************************************
* Actionne un périphérique via son code API et le code API de sa macro.
* L'argument $dynamic_value peut être omis, il permet de définir la durée d'une macro variable le cas échéant.
*/
function setMacro($periph_id /*Code API*/, $macro_id /*Code API Macro*/, $dynamic_value = 0) {
	return 0;
}

/*******************************************************************************
* Retourne un tableau contenant la liste de vos périphériques (Rajoute les notes utilisateurs quand $show_notes est à 1)
* Le format est similaire à celui de la requête API get -> periph.list
*/
function getPeriphList($show_notes = false) {
	return array();
}

/*******************************************************************************
* Retourne un tableau contenant la liste des valeur d'un périphérique (Valable uniquement pour les périphériques de type Liste)
* Renvoie un tableau de la forme :
* resultat[0] = array('value' => 0, 'state' => 'Off', 'state_img' => 'lamp_off.png')
*/
function getPeriphValueList($periph_id /*Code API*/) {
	return array(
			array('value' => 0, 	'state' => 'Off', 	'state_img' => 'lamp_off.png'),
			array('value' => 100, 	'state' => 'ON', 	'state_img' => 'lamp_on.png')
		);
}

/*******************************************************************************
* Retourne un tableau contenant la valeur d'un périphérique via son code API.
* Le tableau est de type array(["value"]=> xx, ["change"] => 'AAAA-MM-JJ HH:MM:SS')
*/
function getValue($periph_id /*Code API*/) {
	$v = emulator_readDataset('values', $periph_id);

	return array('value' => $v, 'change'=> date('Y-M-d H:i:s'));
}

/*******************************************************************************
* Retourne un tableau contenant le JSON décodé (Similaire à la fonction json_decode() de PHP
*/
function sdk_json_decode($json) {
	$res = json_decode($json, true);

	$jErrorCode=json_last_error();
	if ($jErrorCode<>JSON_ERROR_NONE)
		throw new Exception("JSON_ERROR_$jErrorCode reading content '$json' ");

	return $res;
}

/*******************************************************************************
* Personnalise le header de la réponse HTTP du script. Seul $content_type = 'text/xml' est supporté pour l'instant.
*/
function sdk_header($content_type) {
	header("Content-Type: $content_type");
}

/*******************************************************************************
* Envoie une donnée à un périphérique réseau (expériemental).
* Retourne une éventuelle réponse du périphérique.
* Disponible seulement sur eedomus+ à ce jour
*/
function netSend($ip, $port, $data) {
	return -1;
}

/*******************************************************************************
* Envoi une action UPnP vers l'IP (ou les IP séparées par des virgules) d'un diffuseur UPnP
* Les paramètres sont aux même format que ceux d'un actionneur UPnP (ex. pour lire un son préalablement chargé &play
*/
function sendUPNP($ip, $param) {
	return -1;
}

/*******************************************************************************
* Lire des valeurs dans le fichier de simulateur de données
*/
function emulator_readDataset($datasetId, $id) {
	global $eedomusScriptsEmulatorDatasetPath;

	@$content = file_get_contents($eedomusScriptsEmulatorDatasetPath);
	if ($content === false)
		throw new Exception("Can't read input file $eedomusScriptsEmulatorDatasetPath");

	$jDecode = json_decode($content, true);
	if ($jErrorCode<>JSON_ERROR_NONE)
		throw new Exception("JSON_ERROR_$jErrorCode reading file $eedomusScriptsEmulatorDatasetPath");


	if (!array_key_exists($id, $jDecode[$datasetId]))
		return ''; // throw new Exception("No data found in the emulator dataset : $eedomusScriptsEmulatorDatasetPath::$datasetId.$id");

	return $jDecode[$datasetId][$id];
}

/*******************************************************************************
* Enregistrer des valeurs dans le fichier de simulateur de données
*/
function emulator_add2Dataset($datasetId, $id, $value) {
	global $eedomusScriptsEmulatorDatasetPath;

	@$content = file_get_contents($eedomusScriptsEmulatorDatasetPath);
	if ($content === false)
		throw new Exception("Can't read input file $eedomusScriptsEmulatorDatasetPath");

	$jDecode = json_decode($content, true);
	if ($jErrorCode<>JSON_ERROR_NONE)
		throw new Exception("JSON_ERROR_$jErrorCode reading file $eedomusScriptsEmulatorDatasetPath");

	$jDecode[$datasetId][$id] = $value;

	$jString = json_encode($jDecode, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	$jErrorCode=json_last_error();

	if ($jString === false)
		throw new Exception("JSON_ERROR_$jErrorCode saving file $eedomusScriptsEmulatorDatasetPath");

	if (file_put_contents($eedomusScriptsEmulatorDatasetPath, $jString)===false)
		throw new Exception("Can't write the Json file $eedomusScriptsEmulatorDatasetPath");
}
