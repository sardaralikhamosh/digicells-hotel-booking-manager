<?php get_header(); ?>
<div class="dghb-single-wrapper" style="max-width:1200px; margin:40px auto; padding:0 20px;">
    <?php while (have_posts()) : the_post();
        $hotel_id = get_the_ID();
        $price = get_post_meta($hotel_id, '_dghb_price_per_night', true);
        $currency = get_post_meta($hotel_id, '_dghb_currency', true);
        $status = get_post_meta($hotel_id, '_dghb_status', true);
        $checkin = get_post_meta($hotel_id, '_dghb_checkin_time', true);
        $checkout = get_post_meta($hotel_id, '_dghb_checkout_time', true);
        $capacity = get_post_meta($hotel_id, '_dghb_guest_capacity', true);
        $rooms = get_post_meta($hotel_id, '_dghb_total_rooms', true);
        $city = get_post_meta($hotel_id, '_dghb_city', true);
        $country = get_post_meta($hotel_id, '_dghb_country', true);
        $owner_name = get_post_meta($hotel_id, '_dghb_owner_name', true);
        $owner_phone = get_post_meta($hotel_id, '_dghb_owner_phone', true);
        $amenities = get_post_meta($hotel_id, '_dghb_amenities', true);
        $map = get_post_meta($hotel_id, '_dghb_map_embed', true);
        $hotel_type = get_post_meta($hotel_id, '_dghb_hotel_type', true);
        $gallery = get_post_meta($hotel_id, '_dghb_gallery', true);
        $gallery_ids = $gallery ? explode(',', $gallery) : [];
        // Get reviews
        global $wpdb;
        $reviews = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}dghb_reviews WHERE hotel_id = %d AND status = 'approved' ORDER BY id DESC", $hotel_id));
        $avg_rating = 0;
        if ($reviews) {
            $total = array_sum(array_column($reviews, 'rating'));
            $avg_rating = round($total / count($reviews), 1);
        }
        ?>
        <h1><?php the_title(); ?></h1>
        <!-- Gallery Slider -->
        <?php if (!empty($gallery_ids)) : ?>
        <div class="dghb-gallery-slider">
            <div class="dghb-slider-container">
                <?php foreach ($gallery_ids as $id) : if ($id) : ?>
                    <div class="dghb-slide"><?php echo wp_get_attachment_image($id, 'large'); ?></div>
                <?php endif; endforeach; ?>
            </div>
            <button class="dghb-slider-prev">❮</button>
            <button class="dghb-slider-next">❯</button>
        </div>
        <?php else : ?>
            <div><?php the_post_thumbnail('large', ['style'=>'width:100%; border-radius:12px;']); ?></div>
        <?php endif; ?>
        <!-- Main info -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:30px; margin-top:30px;">
            <div>
                <div class="dghb-status-badge <?php echo esc_attr($status); ?>"><?php echo ucfirst(str_replace('_',' ',$status)); ?></div>
                <div class="dghb-price"><?php echo esc_html($currency) . ' ' . number_format($price); ?> / night</div>
                <p><strong>Hotel Type:</strong> <?php echo esc_html($hotel_type); ?></p>
                <p><strong>Location:</strong> <?php echo esc_html($city . ', ' . $country); ?></p>
                <p><strong>Check-in:</strong> <?php echo esc_html($checkin); ?> | <strong>Check-out:</strong> <?php echo esc_html($checkout); ?></p>
                <p><strong>Guest capacity:</strong> <?php echo esc_html($capacity); ?> | <strong>Rooms:</strong> <?php echo esc_html($rooms); ?></p>
                <?php if ($owner_name) : ?><p><strong>Hosted by:</strong> <?php echo esc_html($owner_name); ?> (<?php echo esc_html($owner_phone); ?>)</p><?php endif; ?>
                <?php if (is_array($amenities) && !empty($amenities)) : ?><p><strong>Amenities:</strong> <?php echo implode(', ', $amenities); ?></p><?php endif; ?>
                <?php if ($map) : ?><div class="dghb-map"><?php echo $map; ?></div><?php endif; ?>
                <button class="dghb-btn dghb-book-now" data-id="<?php echo $hotel_id; ?>" data-price="<?php echo esc_attr($price); ?>" data-currency="<?php echo esc_attr($currency); ?>">Book Now</button>
            </div>
            <div>
                <div class="dghb-description"><?php the_content(); ?></div>
            </div>
        </div>
        <!-- Reviews Section -->
        <div class="dghb-reviews-section" style="margin-top:50px;">
            <h3>Guest Reviews (<?php echo count($reviews); ?>)</h3>
            <div class="dghb-average-rating">Average Rating: <?php echo str_repeat('★', floor($avg_rating)) . str_repeat('☆', 5-floor($avg_rating)); ?> (<?php echo $avg_rating; ?>/5)</div>
            <?php if ($reviews) : ?>
                <?php foreach ($reviews as $r) : ?>
                    <div class="dghb-review">
                        <div class="dghb-review-rating"><?php echo str_repeat('★', $r->rating) . str_repeat('☆', 5-$r->rating); ?></div>
                        <div class="dghb-reviewer"><strong><?php echo esc_html($r->reviewer_name); ?></strong> - <?php echo date('M d, Y', strtotime($r->created_at)); ?></div>
                        <div class="dghb-review-text"><?php echo esc_html($r->review_text); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No reviews yet. Be the first to review!</p>
            <?php endif; ?>
            <!-- Submit Review Form -->
            <div class="dghb-submit-review">
                <h4>Write a Review</h4>
                <form id="dghb-review-form">
                    <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
                    <div><input type="text" name="name" placeholder="Your Name *" required style="width:100%; padding:8px; margin-bottom:10px;"></div>
                    <div><input type="email" name="email" placeholder="Your Email (optional)" style="width:100%; padding:8px; margin-bottom:10px;"></div>
                    <div class="dghb-rating-stars">
                        <label>Rating: </label>
                        <span data-value="1">☆</span><span data-value="2">☆</span><span data-value="3">☆</span><span data-value="4">☆</span><span data-value="5">☆</span>
                        <input type="hidden" name="rating" id="dghb_rating_value" required>
                    </div>
                    <div><textarea name="review_text" placeholder="Your review (optional)" rows="4" style="width:100%; padding:8px; margin-bottom:10px;"></textarea></div>
                    <button type="submit" class="dghb-btn">Submit Review</button>
                    <div class="dghb-review-message"></div>
                </form>
            </div>
        </div>
        <!-- Comments Section (if enabled) -->
        <?php if (get_option('dghb_enable_comments', 'yes') == 'yes') : ?>
            <div class="dghb-comments-section" style="margin-top:50px;">
                <?php comments_template(); ?>
            </div>
        <?php endif; ?>
    <?php endwhile; ?>
</div>
<script>
// Simple gallery slider (optional)
document.addEventListener('DOMContentLoaded', function() {
    let slideIndex = 0;
    const slides = document.querySelectorAll('.dghb-slide');
    if (slides.length > 0) {
        function showSlides() {
            slides.forEach((s, i) => s.style.display = i === slideIndex ? 'block' : 'none');
        }
        showSlides();
        document.querySelector('.dghb-slider-prev')?.addEventListener('click', () => { slideIndex = (slideIndex > 0) ? slideIndex-1 : slides.length-1; showSlides(); });
        document.querySelector('.dghb-slider-next')?.addEventListener('click', () => { slideIndex = (slideIndex+1) % slides.length; showSlides(); });
    }
    // Star rating
    const stars = document.querySelectorAll('.dghb-rating-stars span');
    stars.forEach(star => {
        star.addEventListener('click', function() {
            let value = this.getAttribute('data-value');
            document.getElementById('dghb_rating_value').value = value;
            stars.forEach((s, idx) => {
                s.innerHTML = (idx < value) ? '★' : '☆';
            });
        });
    });
});
</script>
<?php get_footer(); ?>