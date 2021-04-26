<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
require_once BR_RESTATE_DIR . '/templates/header.php';
$fields = get_field_objects();
$address = $fields['address'] ? $fields['address'] : [];
?>
    <nav class="page-breadcrumb" itemprop="breadcrumb">
        <a href="/restate">Главная</a>
        <span class="breadcrumb-separator"> > </span>
        <a href="/restate">Новостройки</a><span class="breadcrumb-separator"> > </span>
        <?php the_title(); ?>
    </nav>
</div>

<div class="page-section">
    <div class="page-content">
        <article class="post">

            <div class="post-header">
                <?php the_title( '<h1 class="epage-title-h1">', '</h1>' ); ?>
                <span>ОАО Брусника</span>
                <div class="post-header__details">
                    <div class="address">
                        <?php
                        echo isset( $address['value'] ) && ! empty( $address['value'] )
                            ? $address['value']['address'] : 'Адрес не указан';
                        ?>
                    </div>
                    <div class="metro"><span class="icon-metro icon-metro--red"></span>Студенческая <span>5 мин.<span
                                    class="icon-walk-icon"></span></span></div>
                    <div class="metro"><span class="icon-metro icon-metro--green"></span>Сокол <span>25 мин.<span
                                    class="icon-bus"></span></span></div>
                    <div class="metro"><span class="icon-metro icon-metro--red"></span>Китай-Город <span>15 мин.<span
                                    class="icon-bus"></span></span></div>
                </div>
            </div>

            <?php
            global $post;

            $image_id = get_post_thumbnail_id();
            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
            $image_url = get_the_post_thumbnail_url( $post->ID, 'large' );
            ?>

            <div class="post-image">
                <img src="<?php echo ! empty( $image_url ) ? $image_url : BR_RESTATE_URL . 'assets/img/image.jpg' ?>"
                     alt="<?php echo $image_alt; ?>">
                <div class="page-loop__item-badges">
                    <?php
                    if ( $fields['housing'] ) {
                        $house_types = br_acf_values( $fields['housing'] );
                        foreach ( $house_types as $type ) : ?>
                            <span class="badge"><?php echo $type; ?>+</span>
                        <?php
                        endforeach;
                    }
                    if ( $fields['service'] && $fields['service']['value'] == 1 ) { ?>
                        <span class="badge">Услуги 0%</span>
                    <?php } ?>
                </div>

                <a href="#" class="favorites-link favorites-link__add" title="Добавить в Избранное" role="button">
                    <span class="icon-heart"><span class="path1"></span><span class="path2"></span></span>
                </a>
            </div>

            <h2 class="page-title-h1">Характеристики ЖК</h2>

            <ul class="post-specs">
                <?php
                foreach ( $fields as $key => $field  ) {
                    if ( ( $key === 'general' || $key === 'additional' ) && ! empty( $field['value'] ) ) {
                            foreach ( $field['value'] AS $general_key ) {
                                $value = $field['choices'][$general_key];
                                br_print_list( $general_key, $value );
                            }
                    }
                    else {
                        br_print_list( $key, br_acf_values($field) );
                    }
                }
                ?>
            </ul>

            <h2 class="page-title-h1">Краткое описание</h2>

            <div class="post-text">
                <?php the_content(); ?>
            </div>

            <h2 class="page-title-h1">Карта</h2>
            <div class="post-map" id="post-map" style="width: 100%; height: 300px;"></div>
        </article>
    </div>

    <div class="page-filter"></div>
</div>
<?php

if (  $address ) : ?>
    <script>
        var brMap = '<?php echo json_encode( $address ); ?>';
    </script>
<?php endif; ?>
<?php require_once BR_RESTATE_DIR . '/templates/footer.php'; ?>
