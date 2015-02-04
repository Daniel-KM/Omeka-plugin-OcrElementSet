<?php
$elementSetMetadata = array(
    'name' => 'OCR',
    'description' => 'OCR metadata and text.',
    'record_type' => 'File',
);

// Attention : contrairement à DublinCore Extended, on utilise réellement les
// noms et non les labels.
$elements = array(
    // Statistiques.
    array(
        // 'name' => 'totalNC',
        'name' => 'Total NC',
        'label' => 'Total NC',
        'record_type' => 'File',
        'data_type' => 'Integer',
        'description' => 'Statistique : total NC',
    ),
    array(
        // 'name' => 'totalNCDico',
        'name' => 'Total NC dictionnaire',
        'label' => 'Total NC dictionnaire',
        'record_type' => 'File',
        'data_type' => 'Integer',
        'description' => 'Statistique : total NC dictionnaire',
    ),
    array(
        //'name' => 'tauxNC',
        'name' => 'Taux NC',
        'label' => 'Taux NC',
        'record_type' => 'File',
        'data_type' => 'Integer',
        'description' => 'Statistique : taux NC',
    ),
    array(
        // 'name' => 'totalCaracteres',
        'name' => 'Nombre de caractères',
        'label' => 'Nombre de caractères',
        'record_type' => 'File',
        'data_type' => 'Integer',
        'description' => 'Nombre de caractères',
    ),
    array(
        // 'name' => 'totalDouteux',
        'name' => 'Total caractères douteux',
        'label' => 'Total caractères douteux',
        'record_type' => 'File',
        'data_type' => 'Integer',
        'description' => 'Statistique : total des caractères douteux',
    ),
    array(
        // 'name' => 'tauxDouteux',
        'name' => 'Taux caractères douteux',
        'label' => 'Taux caractères douteux',
        'record_type' => 'File',
        'data_type' => 'Integer',
        'description' => 'Statistique : taux des caractères douteux',
    ),
    // Correspond au champ de notice par défaut "text", mais ici au niveau de
    // chaque fichier.
    array(
        // 'name' => 'texteAuto',
        'name' => 'Texte auto',
        'label' => 'Texte auto',
        'record_type' => 'File',
        'data_type' => 'Text',
        'description' => 'Transcription automatique du texte',
    ),
    // Transcription OCR au format JSON.
    // Ce champ est un intermédiaire entre la transcription brute et le fichier
    // XML complet. Il permet de traiter facilement le surlignage en javascript.
    // Le champ contient uniquement le texte et sa position, sous la forme :
    // "position d'une chaîne dans le texte" => "données à cette position".
    // Le format des données peut-être de plusieurs types :
    // - complet :
    //   toute la chaine originale en JSON
    // - medium :
    //   {"String":{"0":{"id":"PAG_1_ST000001","cc":"184","content":"Ome","subs-content":"Omeka","height":27,"hpos":306,"vpos":307,"width":62}}}
    // - simple :
    //   {"String":{"0":{"q":"184","c":"Ome","s":"Omeka","h":27,"x":306,"y":307,"w":62}}}'
    // - court, recommandé pour diminuer la mémoire, surtout pour les gros
    //   documents (attention à l'ordre des données : contenu, x, y, w, h, qualité, sous-contenu) :
    //   {"String":{"0":["Ome",306,307,62,27,"184","Omeka"]}}
    // Les valeurs sont ceux de la norme Alto et peuvent être répétées.
    // Les outils doivent être adaptés au format choisi (import et affichage).
    //
    // La qualité est appréciée par lettre du contenu, de 0 (certitude) à 9 (erreur).
    // Le sous-contenu est le mot dont le contenu est une partie.
    //
    // Il est conseillé d'utiliser ce champ uniquement de manière transitoire,
    // en particulier seulement pour la phase d'import (XmlImport et CsvImport
    // permettent d'importer uniquement des éléments standards), car sinon,
    // ces éléments seront dupliqués dans la table de recherche des textes,
    // ce qui est inutile et peut doubler la taille de la base.
    // Le plugin Bibnum contient les outils standards nécessaires pour déplacer
    // et récupérer ces données.
    array(
        // 'name' => 'motAMot,
        'name' => 'Mot-à-mot',
        'label' => 'Mot-à-mot',
        'record_type' => 'File',
        'data_type' => 'Text',
        'description' => 'Transcription brute du texte au format JSON, indiquant la position de chaque mot dans l’image',
    ),
);
