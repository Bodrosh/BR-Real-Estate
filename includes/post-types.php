<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function br_restate_register() {

    $slug = 'restate';

    $labels = array(
        'name'               => esc_html__( 'Недвижимость', 'br_themes' ),
        'singular_name'      => esc_html__( 'Недвижимость', 'br_themes' ),
        'add_new'            => esc_html__( 'Новый объект', 'br_themes' ),
        'add_new_item'       => esc_html__( 'Добавление нового объекта', 'br_themes' ),
        'edit_item'          => esc_html__( 'Изменение объекта', 'br_themes' ),
        'new_item'           => esc_html__( 'Добавить новый объект', 'br_themes' ),
        'view_item'          => esc_html__( 'Просмотреть', 'br_themes' ),
        'search_items'       => esc_html__( 'Поиск', 'br_themes' ),
        'not_found'          => esc_html__( 'Нет добавленных объектов', 'br_themes' ),
        'not_found_in_trash' => esc_html__( 'В корзине нет объектов', 'br_themes' )
    );

    $args = array(
        'labels'          => $labels,
        'public'          => true,
        'show_ui'         => true,
        'capability_type' => 'post',
        'hierarchical'    => false,
        'menu_icon'       => 'dashicons-admin-multisite',
        'rewrite'         => array('slug' => $slug), // Permalinks format
        'supports'        => array('title', 'editor', 'thumbnail'),
        'show_in_rest'    => true,
        'rest_base'       => $slug,
        'has_archive'     => true
    );

    register_post_type( $slug , $args );
}

add_action('init', 'br_restate_register', 1);