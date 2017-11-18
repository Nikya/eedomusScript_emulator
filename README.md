# eedomus script : Emulator

* Version : 1.1
* Origine : [GitHub/Nikya/eedomusScript_emulator](https://github.com/Nikya/eedomusScript_emulator "Origine sur GitHub")

## Description
***Nikya eedomus Script Emulator*** est un script PHP simple et stupide qui **simule les fonctions PHP** propre à _eedomus_.

Il est à utiliser comme un utilitaire de développement, pour des premières phase de test de script en dehors de la box.

Il simule la présence et parfois le fonctionnement de certaines fonctions comme :

* getArg
* httpQuery
* loadVariables
* saveVariables
* sdk_json_decode
* sdk_header
* ...

## Utilisation

1. Initialiser la variable de chemin vers le fichier de simulation de données.
1. Inclure ce script dans un script en cours de développement.
1. Utiliser les fonctions

### Exemple

	$eedomusScriptsEmulatorDatasetPath = '../../eedomusScript_emulator/eedomusScriptsEmulator_dataset.json';
	require_once ("../../eedomusScript_emulator/eedomusScriptsEmulator.php");
	echo getValue('PID123');

**Attention** : A supprimer ensuite avant envoie sur eedomus.

## Dataset

Est un simulateur de données.

C'est un fichier **Json** qui contient 2 entrées principales :

* `vars` : Contient des données (clé:valeur) pour les fonctions `loadVariable` et `saveVariable` : Simulation du systéme de variables de scripts
* `values` : Contient des données (clé:valeur) pour les fonctions `getValue` et `setValue` : Simulation du systéme de lecture pilotage de module
