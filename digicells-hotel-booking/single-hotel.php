<?php get_header(); ?>
<div class="dghb-single-wrapper">
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
        $map_embed = get_post_meta($hotel_id, '_dghb_map_embed', true);
        $hotel_type = get_post_meta($hotel_id, '_dghb_hotel_type', true);
        $gallery = get_post_meta($hotel_id, '_dghb_gallery', true);
        $gallery_ids = $gallery ? explode(',', $gallery) : [];
        
        // Process map embed: if it's a share link (maps.app.goo.gl), convert to embed iframe
        if (strpos($map_embed, 'maps.app.goo.gl') !== false) {
            // Extract place ID or coordinates? For simplicity, we'll use a generic embed URL using the share link.
            // But better to use Google Maps Embed API with a query. We'll output a link instead.
            $map_html = '<a href="' . esc_url($map_embed) . '" target="_blank" class="dghb-map-link">📍 View on Google Maps</a>';
        } elseif (strpos($map_embed, '<iframe') !== false) {
            $map_html = $map_embed;
        } else {
            $map_html = '<div class="dghb-map">' . $map_embed . '</div>';
        }
        
        // Reviews
        global $wpdb;
        $reviews = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}dghb_reviews WHERE hotel_id = %d AND status = 'approved' ORDER BY id DESC", $hotel_id));
        $avg_rating = 0;
        if ($reviews) {
            $total = array_sum(array_column($reviews, 'rating'));
            $avg_rating = round($total / count($reviews), 1);
        }
        // Comments control
        $comment_setting = get_post_meta($hotel_id, '_dghb_enable_comments', true);
        $global_comments = get_option('dghb_enable_comments', 'yes');
        $show_comments = ($comment_setting == 'default') ? ($global_comments == 'yes') : ($comment_setting == 'yes');
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
            <div class="dghb-single-image"><?php the_post_thumbnail('large'); ?></div>
        <?php endif; ?>
        
        <!-- Main info grid -->
        <div class="dghb-single-grid">
            <div class="dghb-single-left">
                <div class="dghb-status-badge <?php echo esc_attr($status); ?>"><?php echo ucfirst(str_replace('_',' ',$status)); ?></div>
                <div class="dghb-price"><?php echo esc_html($currency) . ' ' . number_format($price); ?> <span>/ night</span></div>
                <p><strong>Hotel Type:</strong> <?php echo esc_html($hotel_type); ?></p>
                <p><strong>Location:</strong> <?php echo esc_html($city . ', ' . $country); ?></p>
                <p><strong>Check-in:</strong> <?php echo esc_html($checkin); ?> | <strong>Check-out:</strong> <?php echo esc_html($checkout); ?></p>
                <p><strong>Guest capacity:</strong> <?php echo esc_html($capacity); ?> | <strong>Rooms:</strong> <?php echo esc_html($rooms); ?></p>
                <?php if ($owner_name) : ?><p><strong>Hosted by:</strong> <?php echo esc_html($owner_name); ?> (<?php echo esc_html($owner_phone); ?>)</p><?php endif; ?>
                <?php if (is_array($amenities) && !empty($amenities)) : ?><p><strong>Amenities:</strong> <?php echo implode(', ', $amenities); ?></p><?php endif; ?>
                
                <!-- Google Map -->
                <?php if ($map_embed) : ?>
                    <div class="dghb-map-container"><?php echo $map_html; ?></div>
                <?php endif; ?>
                
                <button class="dghb-btn dghb-book-now" data-id="<?php echo $hotel_id; ?>" data-price="<?php echo esc_attr($price); ?>" data-currency="<?php echo esc_attr($currency); ?>">Book Now</button>
            </div>
            <div class="dghb-single-right">
                <div class="dghb-description"><?php the_content(); ?></div>
            </div>
        </div>
        
        <!-- Reviews Section -->
        <div class="dghb-reviews-section">
            <h3>Guest Reviews <span class="dghb-review-count">(<?php echo count($reviews); ?>)</span></h3>
            <div class="dghb-average-rating">
                <span class="dghb-stars"><?php echo str_repeat('★', floor($avg_rating)) . str_repeat('☆', 5 - floor($avg_rating)); ?></span>
                <span class="dghb-rating-value"><?php echo $avg_rating; ?></span> / 5
            </div>
            <div id="dghb-reviews-list">
                <?php if ($reviews) : ?>
                    <?php foreach ($reviews as $r) : ?>
                        <div class="dghb-review">
                            <div class="dghb-review-rating"><?php echo str_repeat('★', $r->rating) . str_repeat('☆', 5 - $r->rating); ?></div>
                            <div class="dghb-reviewer"><strong><?php echo esc_html($r->reviewer_name); ?></strong> - <?php echo date('M d, Y', strtotime($r->created_at)); ?></div>
                            <div class="dghb-review-text"><?php echo nl2br(esc_html($r->review_text)); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="dghb-no-reviews">No reviews yet. Be the first to review!</p>
                <?php endif; ?>
            </div>
            <!-- Submit Review Form -->
            <div class="dghb-submit-review">
                <h4>Write a Review</h4>
                <form id="dghb-review-form">
                    <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
                    <div><input type="text" name="name" placeholder="Your Name *" required></div>
                    <div><input type="email" name="email" placeholder="Your Email (optional)"></div>
                    <div class="dghb-rating-stars">
                        <label>Rating: </label>
                        <span data-value="1">☆</span>
                        <span data-value="2">☆</span>
                        <span data-value="3">☆</span>
                        <span data-value="4">☆</span>
                        <span data-value="5">☆</span>
                        <input type="hidden" name="rating" id="dghb_rating_value" required>
                    </div>
                    <div><textarea name="review_text" placeholder="Your review (optional)" rows="4"></textarea></div>
                    <button type="submit" class="dghb-btn">Submit Review</button>
                    <div class="dghb-review-message"></div>
                </form>
            </div>
        </div>
        
        <!-- Comments Section (if enabled) -->
        <?php if ($show_comments) : ?>
            <div class="dghb-comments-section">
                <?php comments_template(); ?>
            </div>
        <?php endif; ?>
        
    <?php endwhile; ?>
</div>

<style>
.dghb-single-wrapper { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
.dghb-single-image img { width: 100%; border-radius: 12px; }
.dghb-single-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin: 30px 0; }
.dghb-single-left { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.dghb-single-right { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.dghb-map-container { margin: 20px 0; }
.dghb-map-link { display: inline-block; background: #4285f4; color: white; padding: 8px 15px; border-radius: 30px; text-decoration: none; }
.dghb-average-rating { font-size: 1.2rem; margin: 15px 0; }
.dghb-stars { color: #f68511; letter-spacing: 2px; }
.dghb-review { border-bottom: 1px solid #eee; padding: 15px 0; }
.dghb-review-rating { color: #f68511; font-size: 1.1rem; margin-bottom: 5px; }
.dghb-reviewer { color: #666; font-size: 0.9rem; margin-bottom: 8px; }
.dghb-rating-stars span { font-size: 1.8rem; cursor: pointer; margin-right: 5px; color: #ddd; transition: color 0.2s; }
.dghb-rating-stars span.active, .dghb-rating-stars span:hover { color: #f68511; }
@media (max-width: 768px) {
    .dghb-single-grid { grid-template-columns: 1fr; }
    .dghb-gallery-slider .dghb-slide img { max-height: 300px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gallery slider
    let slideIndex = 0;
    const slides = document.querySelectorAll('.dghb-slide');
    if (slides.length > 0) {
        function showSlides() { slides.forEach((s, i) => s.style.display = i === slideIndex ? 'block' : 'none'); }
        showSlides();
        document.querySelector('.dghb-slider-prev')?.addEventListener('click', () => { slideIndex = (slideIndex > 0) ? slideIndex-1 : slides.length-1; showSlides(); });
        document.querySelector('.dghb-slider-next')?.addEventListener('click', () => { slideIndex = (slideIndex+1) % slides.length; showSlides(); });
    }
    // Star rating for review
    const stars = document.querySelectorAll('.dghb-rating-stars span');
    stars.forEach(star => {
        star.addEventListener('click', function() {
            let value = parseInt(this.getAttribute('data-value'));
            document.getElementById('dghb_rating_value').value = value;
            stars.forEach((s, idx) => {
                if (idx < value) s.classList.add('active');
                else s.classList.remove('active');
            });
        });
    });
});
</script>
<?php get_footer(); ?>