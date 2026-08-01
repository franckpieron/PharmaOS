<!doctype html>
<html lang="en">
  <head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Bootstrap 5 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

	<title>PharmaOS - CBIP</title>
  </head>
  <body>
	<div class="container-fluid">
	<h1>PharmaOS - CBIP</h1>
	<p>Transforme les fichiers .csv dans leur correspondance .xml</p>
	<p>Les fichiers .csv sont importés de l'espace téléchargement du site du CBIP (zone développeurs). </p>
	<p># Relations CBIP<br>
		<ul><Principe: A partir du nom de marque, obtenir les composants et leurs dosages</ul>
		<ul>CSV: MP.csv</ul>
		<ul>MPnm contient le nom de marque</ul>
		<ul>MPcv contient une référence vers Sam.csv Sam.mpcv qui contient tous les produits correspondant à la marque (ex: Augmentin vers tous les augmentins mais pas Amoclane) donc plusieurs lignes</ul></p>
	<hr  />
	
	<?php
	function csvToXml(string $csvPath, string $xmlPath, string $rootName = 'root', string $itemName = 'item'): bool {
		// 1. Ouvrir le fichier CSV en lecture
		if (($handle = fopen($csvPath, "r")) === FALSE) {
			return false;
		}
	
		// 2. Extraire la première ligne pour les entêtes (séparateur ;)
		$headers = fgetcsv($handle, 0, ";");
		if (!$headers) {
			fclose($handle);
			return false;
		}
	
		// Nettoyer les entêtes (supprimer les espaces ou caractères invisibles comme le BOM)
		$headers = array_map('trim', $headers);
	
		// 3. Initialiser la structure XML avec SimpleXMLElement
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><$rootName></$rootName>");
	
		// 4. Parcourir chaque ligne du CSV
		while (($row = fgetcsv($handle, 0, ";")) !== FALSE) {
			// Ignorer les lignes vides accidentelles
			if (empty(array_filter($row))) {
				continue;
			}
	
			// Associer les entêtes aux valeurs de la ligne actuelle
			$rowData = array_combine($headers, $row);
	
			// Créer une balise enfant principale pour cette ligne
			$itemNode = $xml->addChild($itemName);
	
			// Ajouter chaque colonne comme une sous-balise XML
			foreach ($rowData as $key => $value) {
				// Nettoyer le nom de la clé pour qu'il soit valide en XML
				$cleanKey = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key);
				
				// Sécuriser la valeur contre les caractères spéciaux XML (&, <, >)
				$itemNode->addChild($cleanKey, htmlspecialchars($value, ENT_XML1, 'UTF-8'));
			}
		}
		fclose($handle);
	
		// 5. Sauvegarder le fichier XML généré avec un formatage propre
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xml->asXML());
	
		return $dom->save($xmlPath) !== false;
	}
	
	function csvToStdClassArray(string $filePath): array {		
		$objets = [];
		 if (($handle = fopen($filePath, "r")) !== FALSE) {
			// Lire les entêtes pour s'en servir comme clés d'objet
			$headers = fgetcsv($handle, 1000, ";");
			var_dump($headers);
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
				// Associer les entêtes aux valeurs de la ligne
				$rowAssoc = array_combine($headers, $data);
				
				// Convertir le tableau associatif en objet stdClass
				$objets[] = (object) $rowAssoc;
			}
			fclose($handle);
		 }
		return $objets;
	}
	?>
	
	<h2>Sam.csv vers XML</h2>	
	<?php
	csvToXml('./csv4Emd_Fr_2606A/Sam.csv', './csv4Emd_Fr_2606A/Sam.xml', 'SAM', 'sam');		// Extraction dans le même répertoire
	csvToXml('./csv4Emd_Fr_2606A/Sam.csv', 'Sam.xml', 'SAM', 'sam');						// Extraction dans le répertoire CBIP
	
	echo('<a href="./csv4Emd_Fr_2606A/Sam.xml">Sam.xml</a>');
	
	?>
		
	<hr  />
	<h2>MP.csv vers XML</h2>
	<?php
		csvToXml('./csv4Emd_Fr_2606A/MP.csv', './csv4Emd_Fr_2606A/MP.xml', 'MP', 'mp');
		echo('<a href="./csv4Emd_Fr_2606A/MP.xml">MP.xml</a>');

	?>	

	<hr  />
	<h2>Ir.csv vers XML</h2>
	<?php
		csvToXml('./csv4Emd_Fr_2606A/Ir.csv', 'Ir.xml', 'IR', 'ir');
		echo('<a href="./csv4Emd_Fr_2606A/Ir.xml">Ir.xml</a>');

	?>	

	<hr  />
	<h2>MPP.csv vers XML</h2>
	<?php
		csvToXml('./csv4Emd_Fr_2606A/MPP.csv', 'MPP.xml', 'MPP', 'mpp');
		echo('<a href="./csv4Emd_Fr_2606A/MPP.xml">MPP.xml</a>');

	?>	

	<hr  />
	<h2>Stof.csv vers XML</h2>
	<?php
		csvToXml('./csv4Emd_Fr_2606A/Stof.csv', 'Stof.xml', 'STOF', 'stof');
		echo('<a href="./csv4Emd_Fr_2606A/Stof.xml">Stof.xml</a>');

	?>	
	<hr  />
	<h2>Gal.csv vers XML</h2>
	<?php
		csvToXml('./csv4Emd_Fr_2606A/Gal.csv', 'Gal.xml', 'GAL', 'gal');
		echo('<a href="Gal.xml">Gal.xml</a>');
	
	?>	

	
	<p>Création des fichiers .xml OK</p>
	<hr>
	
	<?php
	$xmlPath = 'MP.xml';
	
	$xml = simplexml_load_file($xmlPath);
	
	if ($xml === false) {
		die('Impossible de charger le fichier XML.');
	}
	?>
	
	<select name="medicament" id="medicament">
		<option value="">-- Sélectionner un médicament --</option>
		<?php foreach ($xml->mp as $mp): ?>
			<option value="<?= htmlspecialchars((string)$mp->MPcv) ?>">
				<?= htmlspecialchars((string)$mp->MPnm) ?>
			</option>
		<?php endforeach; ?>
	</select>
	
	
	<select name="medicament" id="medicament">
	<?php
	$medics = iterator_to_array($xml->mp);
	usort($medics, fn($a, $b) => strcmp((string)$a->MPnm, (string)$b->MPnm));
	
	foreach ($medics as $mp): ?>
		<option value="<?= htmlspecialchars((string)$mp->MPcv) ?>">
			<?= htmlspecialchars((string)$mp->MPnm) ?>
		</option>
	<?php endforeach; ?>
	</select>
	
	
	
	</div>



	<!-- Optional JavaScript -->

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


  </body>
</html>