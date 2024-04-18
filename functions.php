<?php

// Script et style personnalisé du thème 
function nathalie_mota_theme_enqueue(){
    wp_enqueue_style('nathalie-mota', get_stylesheet_uri());
    wp_enqueue_style('theme-style', get_template_directory_uri() . '/assets/css/style.css');
    wp_enqueue_script('script' , get_template_directory_uri() . '/assets/js/script.js', array('jquery'), null, true );
    wp_localize_script('script', 'data', ['ajax_url' => admin_url( 'admin-ajax.php' )]);
}
add_action('wp_enqueue_scripts','nathalie_mota_theme_enqueue');

// Gestion du menu 

// créer un lien pour la gestion des menus dans l'administration et activation d'un menu pour le header et d'un menu pour le footer.
function register_my_menu(){
    register_nav_menu('main', "Menu principal");
    register_nav_menu('footer', "Menu pied de page");
 }
 add_action('after_setup_theme', 'register_my_menu');

/** Requêtes AJAX */
function load_photos(){
    $category = [];
    $format = [];
    $taxQuery = [];   
    $page = (isset($_GET['page']) && $_GET['page'] !== 'null') ? $_GET['page'] : 1; 
    $sort = (isset($_GET['sort']) && $_GET['sort'] !== 'null') ? $_GET['sort'] : 'ASC'; 
    // La fonction isset()vérifie la variable catégorie dans l'URL , si la variable existe dans l'URL $_GET prend la valeur de la variable 
    if(isset($_GET['category']) && $_GET['category'] !== 'null' && $_GET['category'] !== 'ALL'){
        $category = array(
            'taxonomy' => 'categorie', // Nom de la taxonomie
            'field' => 'slug', // Qu'on récupère par son Slug
            'terms' => $_GET['category']
        );
        // Retourne les éléments 
        array_push($taxQuery, $category);
    }

    if(isset($_GET['format']) && $_GET['format'] !== 'null' && $_GET['format'] !== 'ALL'){
        $format = array(
            'taxonomy' => 'format', // Nom de la taxonomie
            'field' => 'slug', // Qu'on récupère par son Slug
            'terms' => $_GET['format'],
        );
        array_push($taxQuery, $format);
    }
    
    $query = new WP_Query([
        'post_type' => 'photo',
        'posts_per_page' => 8,
        'order' => $sort, // par ordre aléatoire 
        'paged' => $page,
        'tax_query' => $taxQuery,
    ]);
    if($query  -> have_posts()){
        while ($query  -> have_posts()) {
            $query -> the_post();            
            echo get_template_part('/templates_part/photo-single');
        }
    }
}

add_action( 'wp_ajax_load_photos', 'load_photos' );
add_action( 'wp_ajax_nopriv_load_photos', 'load_photos' );

/* Bouton Load More */

function btn_load_more(){
    $page = (isset($_GET['page']) && $_GET['page'] !== 'null') ? $_GET['page'] : 1;
    $query = new WP_Query([
        'post_type' =>'photo',
        'post_per_page' => 8,
        'orderby' =>'rand',
        'order' =>'ASC',
        'paged' => $page,
    ]);
    if($query  -> have_posts()){
        while ($query  -> have_posts()) {
            $query -> the_post();            
            echo get_template_part('/templates_part/photo-single');
        }
    }
};
add_action('wp_ajax_btn_load_more' , 'btn_load_more');
add_action('wp_ajax_nopriv_btn_load_more' , 'btn_load_more');




?>
