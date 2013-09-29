<?php
$elementSetMetadata = array(
    'name'        => 'OCR',
    'description' => 'OCR metadata and text.',
    'record_type' => 'File',
);

// Attention : contrairement à DublinCore Extended, on utilise réellement les
// noms et non les labels.
$elements = array(
    // Statistiques.
    array(
        // 'name'        => 'totalNC',
        'name'        => 'Total NC',
        'label'       => 'Total NC',
        'record_type' => 'File',
        'data_type'   => 'Integer',
        'description' => 'Statistique : total NC',
    ),
    array(
        // 'name'        => 'totalNCDico',
        'name'        => 'Total NC dictionnaire',
        'label'       => 'Total NC dictionnaire',
        'record_type' => 'File',
        'data_type'   => 'Integer',
        'description' => 'Statistique : total NC dictionnaire',
    ),
    array(
        //'name'        => 'tauxNC',
        'name'        => 'Taux NC',
        'label'       => 'Taux NC',
        'record_type' => 'File',
        'data_type'   => 'Integer',
        'description' => 'Statistique : taux NC',
    ),
    array(
        // 'name'        => 'totalCaracteres',
        'name'        => 'Nombre de caractères',
        'label'       => 'Nombre de caractères',
        'record_type' => 'File',
        'data_type'   => 'Integer',
        'description' => 'Nombre de caractères',
    ),
    array(
        // 'name'        => 'totalDouteux',
        'name'        => 'Total caractères douteux',
        'label'       => 'Total caractères douteux',
        'record_type' => 'File',
        'data_type'   => 'Integer',
        'description' => 'Statistique : total des caractères douteux',
    ),
    array(
        // 'name'        => 'tauxDouteux',
        'name'        => 'Taux caractères douteux',
        'label'       => 'Taux caractères douteux',
        'record_type' => 'File',
        'data_type'   => 'Integer',
        'description' => 'Statistique : taux des caractères douteux',
    ),
    // Correspond au champ de notice par défaut "text", mais ici au niveau de
    // chaque fichier.
    array(
        // 'name'        => 'texteAuto',
        'name'        => 'Texte auto',
        'label'       => 'Texte auto',
        'record_type' => 'File',
        'data_type'   => 'Text',
        'description' => 'Transcription automatique du texte',
    ),
    // Transcription OCR au format JSON.
    // Ce champ est un intermédiaire entre la transcription brute et le fichier
    // XML complet. Il permet de traiter facilement le surlignage en javascript.
    // Le format json simplifie le format xml et contient uniquement le texte
    // et sa position. Le contenu sur une chaine dépend du format :
    // - complet
    //   toute la chaine originale en JSON
    // - medium
    //   {"String":[{"id":"PAG_1_ST000001","cc":"10409","content":"Omeka","height":27,"hpos":306,"vpos":307,"width":62}]}';
    // - simple (par défaut)
    //   {"String":[{"q":"10409","c":"Omeka","h":27,"x":306,"y":307,"w":62}]}';
    // Les valeurs sont ceux de la norme Alto. Elles peuvent être répétées.
    array(
        // 'name'        => 'motAMot,
        'name'        => 'Mot-à-mot',
        'label'       => 'Mot-à-mot',
        'record_type' => 'File',
        'data_type'   => 'Text',
        'description' => 'Transcription brute du texte au format JSON, indiquant notamment la position de chaque mot dans l’image',
    ),
);
