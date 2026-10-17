<?php
/**
 * Template Name: FAQs Page
 *
 * @package mrzaib
 */
get_header();
$faqs = array(
	'Orders & Delivery' => array(
		array( 'How long does delivery take?', 'Orders are delivered in 2 to 5 working days all over Pakistan.' ),
		array( 'Do you offer Cash on Delivery?', 'Yes, Cash on Delivery is available on all orders across Pakistan.' ),
		array( 'Is shipping free?', 'Shipping is free on orders over Rs. 3000. Below that, a flat delivery charge applies.' ),
		array( 'How can I track my order?', 'Use the Track Your Order page with your order number and email.' ),
	),
	'Products' => array(
		array( 'How long do your perfumes last?', 'Most MR ZAIB perfumes last 8 to 12 hours on skin, depending on skin type and weather.' ),
		array( 'Are your perfumes original?', 'All fragrances are made by MR ZAIB with high-concentration, super-macerated oils.' ),
		array( 'Can I try before buying?', 'Yes! Our Tester Box lets you sample several scents before choosing a full bottle.' ),
	),
	'Returns' => array(
		array( 'What is your return policy?', 'You can return or exchange within 15 days of delivery. The bottle must be at least 80% full with original packaging.' ),
		array( 'How do I start a return?', 'Fill the Return Form and our team will contact you within 24 hours.' ),
	),
);
?>
<main id="MainContent" class="site-main section">
	<div class="container narrow">
		<h1 class="page-title center"><?php the_title(); ?></h1>
		<?php while ( have_posts() ) : the_post(); ?><div class="entry-content"><?php the_content(); ?></div><?php endwhile; ?>
		<?php foreach ( $faqs as $group => $items ) : ?>
			<h3 class="faq-group"><?php echo esc_html( $group ); ?></h3>
			<div class="faq-list">
				<?php foreach ( $items as $f ) : ?>
					<details class="faq-item"><summary><?php echo esc_html( $f[0] ); ?><?php echo mrzaib_icon( 'down' ); // phpcs:ignore ?></summary><div class="faq-answer"><p><?php echo esc_html( $f[1] ); ?></p></div></details>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
</main>
<?php
get_footer();
