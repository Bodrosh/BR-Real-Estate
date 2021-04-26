<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$service = get_field( "service" );
$address = get_field( "address" );
$proximity = get_field( "proximity" );
$deadline = get_field( "deadline" );
$deadline_quarter = get_field( "deadline_quarter" );
$housing = get_field_object( "housing" );

?>
<li id="restate-<?php echo $post->ID; ?>" class="page-loop__item wow animate__animated animate__fadeInUp" data-wow-duration="0.8s">

    <a href="#" class="favorites-link favorites-link__add" title="Добавить в Избранное" role="button">
        <span class="icon-heart"><span class="path1"></span><span class="path2"></span></span>
    </a>

    <a href="<?php the_permalink(); ?>" class="page-loop__item-link">
        <div class="page-loop__item-image">

            <img src="<?php echo get_the_post_thumbnail_url( $post->ID, 'medium' ); ?>" alt="">

            <div class="page-loop__item-badges">
                <?php if ( $service ) : ?>
                    <span class="badge">Услуги 0%</span>
                <?php endif;
                if( $housing['choices'] ) :
                    foreach( $housing['value'] as $value => $label ): ?>
                        <span class="badge"><?php echo $housing['choices'][$label]; ?></span>
                    <?php endforeach;
                endif; ?>
            </div>
        </div>

        <div class="page-loop__item-info">
            <h3 class="page-title-h3"><?php the_title(); ?></h3>

            <p class="page-text">
                <?php if ( $deadline ) : ?>
                    <span>Срок сдачи до
                    <?php if ( $deadline_quarter && $deadline_quarter !== '-' ) {
                        echo $deadline_quarter . ' кв. ';
                    } ?>
                    <?php echo $deadline; ?> г.
                    </span>
                <?php endif; ?>
            </p>

            <div class="page-text to-metro">
                <span class="icon-metro icon-metro--red"></span>
                <span class="page-text">Студенческая
                    <?php if ( $proximity ) : ?>
                        <span> <?php echo $proximity; ?> мин.</span>
                    <?php endif; ?>
                </span>
                <span class="icon-walk-icon"></span>
            </div>

            <span class="page-text text-desc">
                <?php if ( $address ) :
                    echo $address['street_name_short'];
                endif; ?>
            </span>

        </div>
    </a>
</li>