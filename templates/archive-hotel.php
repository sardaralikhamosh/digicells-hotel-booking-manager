<div class="dghbm-cards-grid">
<?php
$query = new WP_Query(array('post_type'=>'dghbm_hotel','posts_per_page'=>12));
while($query->have_posts()): $query->the_post();
    $price = get_post_meta(get_the_ID(), '_dghbm_price_per_night', true);
    ?>
    <div class="dghbm-card">
        <?php the_post_thumbnail('medium'); ?>
        <div class="dghbm-card-content">
            <h3><?php the_title(); ?></h3>
            <p><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
            <div class="dghbm-price"><?php echo get_post_meta(get_the_ID(), '_dghbm_currency', true); ?> <?php echo number_format($price); ?> / night</div>
            <a href="<?php the_permalink(); ?>" class="dghbm-btn dghbm-btn-outline">View Details</a>
            <button class="dghbm-btn book-now-btn" data-id="<?php echo get_the_ID(); ?>" data-type="hotel">Book Now</button>
        </div>
    </div>
<?php endwhile; wp_reset_postdata(); ?>
</div>