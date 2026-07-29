# PharmaOS


# Import CBIP
Aller dans l'espace téléchargement du site CBIP.BE
Télécharger les fichiers .csv actualisés
Extraire les csv vers Excel (Donnees -> Obtenir des données -> A partir d'un fichier Texte)
csv délimité par point-virgule, Texte Unicode UTF8

# Relations CBIP
Principe: A partir du nom de marque, obtenir les composants et leurs dosages
CSV: MP.csv
MPnm contient le nom de marque
MPcv contient une référence vers Sam.csv Sam.mpcv qui contient tous les produits correspondant à la marque (ex: Augmentin vers tous les augmentins mais pas Amoclane) donc plusieurs lignes
