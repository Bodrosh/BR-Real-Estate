<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * Подключение стилей и скриптов
 */
function br_restate_scripts() {
    if ( is_singular('restate') || is_post_type_archive('restate') ) {
        wp_enqueue_style('icomoon', BR_RESTATE_URL . 'assets/fonts/icomoon/icon-font.css', array(), '', 'all');
        wp_enqueue_style('br-restate', BR_RESTATE_URL . 'assets/css/style.min.css', array(), '', 'all');

        wp_enqueue_script( 'popper', BR_RESTATE_URL . 'assets/libs/bootstrap/js/popper.min.js', array(), '', true );
        wp_enqueue_script( 'bootstrap', BR_RESTATE_URL . 'assets/libs/bootstrap/js/bootstrap.min.js', array('jquery'), '', true );
        wp_enqueue_script( 'ofi', BR_RESTATE_URL . 'assets/libs/ofi/ofi.min.js', array(), '', true );
        wp_enqueue_script( 'scripts', BR_RESTATE_URL . 'assets/js/scripts.js', array(), '', true );

    }
    if ( is_singular('restate') ) {
        wp_enqueue_script( 'yandex-map', 'https://api-maps.yandex.ru/2.1/?apikey=f7f5866c-fcab-4da8-94d7-cdbdb39c7d22&lang=ru_RU', array(), '', false );
    }
    if ( is_post_type_archive('restate') ) {
        wp_enqueue_script( 'wow', BR_RESTATE_URL . 'assets/libs/wowjs/wow.min.js', array(), '', true );
        wp_enqueue_script( 'loadmore', BR_RESTATE_URL . 'assets/js/loadmore.js', array('jquery'), '', true );
        wp_enqueue_style('animate', BR_RESTATE_URL . 'assets/libs/animate/animate.min.css', array(), '', 'all');
    }
}
add_action( 'wp_enqueue_scripts', 'br_restate_scripts' );

/*
 * Api карт для ACF - key
 */
function br_acf_google_map_api( $api ){
    $api['key'] = 'AIzaSyD966qd74CDSQ21ibSq6ab0p5z6h10fPzI';
    return $api;
}
add_filter('acf/fields/google_map/api', 'br_acf_google_map_api');

/*
 * Шаблон single post Restate
 */
function br_restate_single_template( $single_post_template ) {
    global $post;
    if ( $post->post_type == 'restate' ) {
        if ( file_exists( BR_RESTATE_DIR . '/templates/single-restate.php' ) ) {
            return BR_RESTATE_DIR . '/templates/single-restate.php';
        }
    }
    return $single_post_template;
}
add_filter('single_template', 'br_restate_single_template');

/*
 * Шаблон archive post Restate
 */
function br_restate_archive_template( $archive_post_template ) {
   if ( is_post_type_archive('restate') ) {
       if ( file_exists( BR_RESTATE_DIR . '/templates/archive-restate.php' ) ) {
           return BR_RESTATE_DIR . '/templates/archive-restate.php';
       }
   }
   return $archive_post_template;
}
add_filter('archive_template', 'br_restate_archive_template');

/*
 * Выводим одно значение списка Характеристик ЖКХ
 */
function br_print_item( $label, $value, $icon = '', $tooltip = '' ) { ?>
    <li>
        <span class="<?php echo $icon; ?>"></span>
        <div class="post-specs__info">
            <span><?php echo $label; ?></span>
            <p>
                <?php
                if ( is_array( $value ) ) {
                    $last_item = end( $value );
                    foreach ( $value as $item ) {
                        echo $item . ( $last_item === $item ? '' : ', ' );
                    }
                }
                else {
                    echo $value;
                }

                if ( $tooltip ) : ?>
                    <span class="tip tip-info" data-toggle="popover" data-placement="top"
                          data-content="<?php echo $tooltip; ?>">
						            <span class="icon-prompt"></span>
                                </span>
                <? endif; ?>
            </p>
        </div>
    </li>
<?php }

/*
 * Выводим значения списка ЖКХ
 */
function br_print_list( $general_key, $value = '' ) {
    switch ( $general_key ) {
        case 'finishing':
            br_print_item('Отделка', $value, 'icon-paint', 'Описание' );
            break;
        case 'brick':
            br_print_item('Конструктив', $value, 'icon-brick' );
            break;
        case 'parking':
            br_print_item('Подземный паркинг', 'Присутствует', 'icon-parking' );
            break;
        case 'ceiling':
            br_print_item('Высокие потолки', '> 2.7 м', 'icon-ruller' );
            break;
        case 'deadline':
            $deadline_quarter = get_field( "deadline_quarter" );
            $quarter = $deadline_quarter && $deadline_quarter !== '-'
                ? $deadline_quarter . ' кв. ' : '';
            br_print_item('Срок сдачи', $quarter . $value, 'icon-calendar' );
            break;
        case 'housing':
            br_print_item('Класс жилья', $value, 'icon-building' );
            break;
        case 'proximity':
            br_print_item('Близость метро', $value . ' мин.', 'icon-stair' );
            break;
        case 'windows':
            br_print_item('Панорамные окна', 'С прекрасным видом', 'icon-rating' );
            break;
        case 'service':
            br_print_item('Сделка без переплат', 'Да', 'icon-wallet' );
            break;
    }
}

/*
 *  [value] =>  [[0] => comfort, ...]
 *  [choices] =>  [[comfort] => Комфорт, ...]
 *  Возвращам  массив с ключами из значений value и соотв. значениямми из choices
 */
function br_acf_values( $field ) {
    $new_value = [];

    if ( is_array( $field['value'] ) ) {
        if( isset( $field['choices'] ) ) {
            foreach ( $field['value'] as $value ) {
                $new_value[$value] = $field['choices'][$value];
            }
            return $new_value;
        }
    }
    return $field['value'];
}

/*
 * Получаем обекты недвижимости
 */
function br_get_restate_posts() {
    $args= [
        'post_type' => 'restate',
    ];
    return new WP_Query( $args );
}

/*
 * Включен фильтр (параметр со значением on)
 */
function br_on( $param ) {
    return isset( $_GET[$param] ) && $_GET[$param] === 'on';
}

/*
 * Параметр в $_GET
 */
function br_is( $param ) {
    return isset( $_GET[$param] ) && ! empty( $_GET[$param] );
}

/*
 * Фильтруем объекты недвижимости
 */
function my_pre_get_posts( $query ) {
    if ( is_admin() ) {
        return $query;
    }
    if ( isset( $query->query_vars['post_type'] )
        && $query->query_vars['post_type'] === 'restate' ) {

        $meta = [];
        $meta['relation'] = 'AND';

        // service
        if ( br_on( 'service') ) {
            $meta[] = [
                'key' => 'service',
                'value' => '1',
            ];
        }

        // proximity
        if ( ( br_on( '<10') || br_on( '10-20')
            || br_on( '20-40') || br_on( '40+') )
            && ! br_on( 'any') ) {
            $meta['proximity'] = [];
            $meta['proximity']['relation'] = 'OR';

            if ( br_on( '<10') ) {
                $meta['proximity'][] = [
                    'key' => 'proximity',
                    'value' => 10,
                    'type'  => 'NUMERIC',
                    'compare' => '<='
                ];
            }
            if ( br_on( '10-20') ) {
                $meta['proximity'][] = [
                    'key' => 'proximity',
                    'value' => [10, 20],
                    'type'  => 'NUMERIC',
                    'compare' => 'BETWEEN'
                ];
            }
            if ( br_on( '20-40') ) {
                $meta['proximity'][] = [
                    'key' => 'proximity',
                    'value' => [20, 40],
                    'type'  => 'NUMERIC',
                    'compare' => 'BETWEEN'
                ];
            }
            if ( br_on( '40+') ) {
                $meta['proximity'][] = [
                    'key' => 'proximity',
                    'value' => 40,
                    'type'  => 'NUMERIC',
                    'compare' => '>='
                ];
            }
        }

        // deadline
        if ( br_is( 'deadline') && $_GET['deadline'] !== 'all' ) {
            $current_year = date('Y');

            $meta['deadline'] = [];
            $meta['deadline'] = [
                'key' => 'deadline',
                'value' => $current_year,
                'type'  => 'NUMERIC',
                'compare' => '='
            ];

            switch ( $_GET['deadline'] ) {
                case 'passed':
                    // deadline quarter - будем показывать объекты,
                    // квартал сдачи которых не указан или <= текущего квартала
                    $current_quarter = intval( ( date('n') + 2 ) / 3 );
                    $meta[] = [
                        'relation' => 'OR',
                        [
                            'key' => 'deadline_quarter',
                            'value' => '-'
                        ],
                        [
                            'key' => 'deadline_quarter',
                            'value' => $current_quarter,
                            'type'  => 'NUMERIC',
                            'compare' => '<='
                        ]
                    ];
                    $meta['deadline']['compare'] = '<=';
                    break;
                case 'this-year':
                    break;
                case 'next-year':
                    $meta['deadline']['value'] += 1;
                    break;
            }
        }

        // housing
        $housing = [];
        if ( br_on( 'economical' ) ) $housing[] = 'economical';
        if ( br_on( 'comfort' ) ) $housing[] = 'comfort';
        if ( br_on( 'business' ) ) $housing[] = 'business';
        if ( br_on( 'elite' ) ) $housing[] = 'elite';

        if ( count( $housing ) ) {
            $meta['housing'] = [];
            $meta['housing']['relation'] = 'OR';

            foreach ( $housing as $item ) {
                $meta['housing'][] = [
                    'key' => 'housing',
                    'value' => $item,
                    'compare' => 'LIKE'
                ];
            }
        }

        // general
        $general = [];
        $general_object = get_field_object( "general" );
        if ( $general_object['choices'] ) {
            foreach( $general_object['choices'] as $value => $label ) {
                if ( br_on( $value ) ) $general[] = $value;
            }
        }
        if ( count( $general ) ) {
            $meta['general'] = [];
            $meta['general']['relation'] = 'AND';

            foreach ( $general as $item ) {
                $meta['general'][] = [
                    'key' => 'general',
                    'value' => $item,
                    'compare' => 'LIKE'
                ];
            }
        }

        // additional
        $additional = [];
        $additional_object = get_field_object( "additional" );
        if ( $additional_object['choices'] ) {
            foreach( $additional_object['choices'] as $value => $label ) {
                if ( br_on( $value ) ) $additional[] = $value;
            }
        }
        if ( count( $additional ) ) {
            $meta['additional'] = [];
            $meta['additional']['relation'] = 'AND';

            foreach ( $additional as $item ) {
                $meta['additional'][] = [
                    'key' => 'additional',
                    'value' => $item,
                    'compare' => 'LIKE'
                ];
            }
        }

        $query->set('meta_query', $meta);
    }
    return $query;
}
add_action('pre_get_posts', 'my_pre_get_posts');


/*
 * Показать еще
 * https://misha.agency/wordpress/ajax-pagination.html :-)
 */
function true_load_posts(){

    $args = unserialize( stripslashes( $_POST['query'] ) );
    $args['paged'] = $_POST['page'] + 1; // следующая страница
    $args['post_status'] = 'publish';

    // обычно лучше использовать WP_Query, но не здесь
    query_posts( $args );
    // если посты есть
    if( have_posts() ) :

        // запускаем цикл
        while( have_posts() ): the_post();
            include BR_RESTATE_DIR . 'templates/loop.php';
        endwhile;

    endif;
    die();
}

add_action('wp_ajax_loadmore', 'true_load_posts');
add_action('wp_ajax_nopriv_loadmore', 'true_load_posts');