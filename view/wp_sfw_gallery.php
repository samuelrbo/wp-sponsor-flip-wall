<div class="wp-sfw-container">

  <?php while(have_posts()): the_post(); ?>

  <div class="wp-sfw-flip-card">
    <div class="wp-sfw-flip-card-inner">
      <div class="wp-sfw-flip-card-front">
        <?php the_post_thumbnail( WP_SFW_THUMB ); ?>
      </div>

      <div class="wp-sfw-flip-card-back">
        <?php the_title( '<h2>', '</h2>' ); ?>
        <p><?php echo get_the_excerpt() ?></p>
      </div>
    </div>
  </div>

  <?php endwhile; ?>

</div>
