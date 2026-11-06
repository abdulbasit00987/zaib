<?php
/**
 * MR ZAIB real store content: categories, products (Kahn-vahl, Arvon), SEO,
 * and removal of the old dummy/demo data.
 *
 * @package mrzaib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** The store's categories: slug => array( name, image, description ). */
function mrzaib_store_categories() {
	return array(
		'men'      => array( 'Men', 'cat-men.jpg', 'Long lasting perfumes for men by MR ZAIB. Fresh, woody and aquatic fragrances made to leave your presence.' ),
		'women'    => array( 'Women', 'cat-women.jpg', 'Elegant, long lasting perfumes for women by MR ZAIB.' ),
		'unisex'   => array( 'Unisex', 'cat-unisex.jpg', 'Unisex perfumes by MR ZAIB that suit men and women alike.' ),
		'attar'    => array( 'Attar', 'cat-attar.jpg', 'Traditional long lasting attar (perfume oil) by MR ZAIB: rich oud, rose, musk and amber.' ),
		'tester'   => array( 'Tester', 'cat-tester.jpg', 'MR ZAIB perfume testers: try a scent before you buy the full bottle.' ),
	);
}

/** The store's products. */
function mrzaib_store_products() {
	return array(
		array(
			'name'     => 'Kahn-vahl',
			'slug'     => 'kahn-vahl-perfume',
			'subtitle' => 'Long Lasting Fresh Perfume for Men | 50ml',
			'sku'      => 'MZ-KAHNVAHL-50',
			'regular'  => '2800',
			'sale'     => '2500',
			'cats'     => array( 'men' ),
			'images'   => array( 'kahn-vahl-main.jpg', 'kahn-vahl-banner.jpg', 'kahn-vahl-notes-top.jpg', 'kahn-vahl-notes-middle.jpg', 'kahn-vahl-notes-base.jpg' ),
			'alts'     => array( 'Kahn-vahl long lasting perfume for men by MR ZAIB 50ml blue bottle', 'MR ZAIB Kahn-vahl men perfume fragrance notes', 'Kahn-vahl top notes coconut water fresh aquatic perfume', 'Kahn-vahl middle notes jasmine and iris', 'Kahn-vahl base notes vanilla sandalwood amber' ),
			'notes'    => array(
				'Top Notes'    => 'Coconut Water',
				'Middle Notes' => 'Jasmine, Iris',
				'Base Notes'   => 'Vanilla, Sandalwood, Amber',
			),
			'short'    => '<p><strong>Kahn-vahl by MR ZAIB</strong> is a long lasting, fresh aquatic perfume for men. It opens with cool <strong>coconut water</strong>, flows into an elegant heart of <strong>jasmine and iris</strong>, and settles on a warm, smooth base of <strong>vanilla, sandalwood and amber</strong>. A clean, classy men\'s fragrance for daily wear, office and evenings.</p>'
				. '<ul><li><strong>Top Notes:</strong> Coconut Water</li><li><strong>Middle Notes:</strong> Jasmine, Iris</li><li><strong>Base Notes:</strong> Vanilla, Sandalwood, Amber</li><li><strong>Size:</strong> 50ml</li><li><strong>For:</strong> Men</li></ul>',
			'long'     => '<h2>Kahn-vahl: Long Lasting Fresh Perfume for Men in Pakistan</h2>'
				. '<p><strong>Kahn-vahl</strong> is a premium men\'s perfume from <strong>MR ZAIB Luxury Perfumes</strong>, made for the man who wants to <em>leave his presence</em>. It opens fresh and bright, becomes refined in the heart and dries down warm and creamy on the skin. Fresh enough for hot days in Pakistan, smooth enough for formal evenings.</p>'
				. '<h3>Fragrance Notes of Kahn-vahl</h3>'
				. '<p><strong>Top Notes: Coconut Water.</strong> The first spray is cool, clean and watery, a light tropical freshness that feels effortless in warm weather.</p>'
				. '<p><strong>Middle Notes: Jasmine and Iris.</strong> White jasmine adds a soft floral glow, while iris brings a powdery, polished elegance that makes the scent feel expensive.</p>'
				. '<p><strong>Base Notes: Vanilla, Sandalwood and Amber.</strong> Creamy sandalwood, sweet vanilla and glowing amber give Kahn-vahl its long lasting, warm trail that people remember.</p>'
				. '<h3>Fragrance Profile</h3>'
				. '<ul><li><strong>Family:</strong> Fresh Aquatic, Floral, Woody Amber</li><li><strong>Character:</strong> Clean, fresh, elegant, warm</li><li><strong>Best season:</strong> Summer and all year round</li><li><strong>Occasions:</strong> Office, daily wear, university, dinners, date nights and gatherings</li></ul>'
				. '<h3>Why Choose MR ZAIB Kahn-vahl?</h3>'
				. '<ul><li>Fresh aquatic opening with a warm vanilla, sandalwood and amber base</li><li>Long lasting fragrance with a noticeable trail</li><li>Luxurious 50ml crystal-cut blue bottle with gold crown cap, a perfect gift for men</li><li>Affordable luxury perfume under Rs. 3000</li><li>Cash on Delivery all over Pakistan</li></ul>'
				. '<h3>How to Apply</h3>'
				. '<p>Spray 3 to 4 times on pulse points (neck, wrists, chest and behind the ears) from about 15 cm. Apply on clean, moisturised skin and do not rub, so the fragrance lasts longer.</p>'
				. '<p>Looking for the <strong>best perfume for men in Pakistan</strong> that smells fresh, classy and lasts all day? Order <strong>Kahn-vahl by MR ZAIB</strong> online today.</p>',
			'tags'     => array( 'Kahn-vahl', 'Perfume for Men', 'Men Perfume Pakistan', 'Long Lasting Perfume', 'Fresh Perfume', 'Aquatic Perfume', 'Coconut Perfume', 'Vanilla Perfume', 'Sandalwood Perfume', 'Amber Perfume', 'Summer Perfume', 'Office Perfume', 'Gift for Men', 'Luxury Perfume', 'MR ZAIB' ),
			'seo'      => array(
				'title'    => 'Kahn-vahl Long Lasting Perfume for Men 50ml | MR ZAIB Pakistan',
				'desc'     => 'Shop Kahn-vahl by MR ZAIB, a fresh long lasting perfume for men with coconut water, jasmine, iris, vanilla, sandalwood and amber. 50ml, Rs. 2,500. COD in Pakistan.',
				'focus'    => 'Kahn-vahl perfume for men',
				'keywords' => 'Kahn-vahl perfume, Kahn-vahl perfume for men, MR ZAIB perfume, perfume for men, men perfume Pakistan, long lasting perfume for men, fresh perfume for men, aquatic perfume, coconut water perfume, vanilla sandalwood amber perfume, best perfume for men in Pakistan, summer perfume for men, office perfume for men, luxury perfume Pakistan, perfume under 3000',
			),
		),
		array(
			'name'     => 'Arvon',
			'slug'     => 'arvon-perfume',
			'subtitle' => 'Long Lasting Unisex Perfume for Men & Women | 50ml',
			'sku'      => 'MZ-ARVON-50',
			'regular'  => '2800',
			'sale'     => '2500',
			'cats'     => array( 'unisex' ),
			'images'   => array( 'arvon-main.jpg', 'arvon-banner.jpg', 'arvon-notes-top.jpg', 'arvon-notes-middle.jpg', 'arvon-notes-base.jpg' ),
			'alts'     => array( 'Arvon long lasting unisex perfume by MR ZAIB 50ml black bottle', 'MR ZAIB Arvon unisex perfume fragrance notes', 'Arvon top notes pineapple nutmeg cinnamon red apple plum pepper cassia bitter orange', 'Arvon middle notes magnolia orchid lily of the valley jasmine moroccan rose iris', 'Arvon base notes cacao vanilla white musk amber sandalwood cedar' ),
			'notes'    => array(
				'Top Notes'    => 'Pineapple, Nutmeg, Cinnamon, Red Apple, Plum, Pepper, Cassia, Bitter Orange',
				'Middle Notes' => 'Magnolia, Orchid, Lily-of-the-Valley, Jasmine, Moroccan Rose, Iris',
				'Base Notes'   => 'Cacao, Vanilla, White Musk, Amber, Sandalwood, Cedar',
			),
			'short'    => '<p><strong>Arvon by MR ZAIB</strong> is a rich, long lasting unisex perfume for men and women. A juicy, spicy opening of <strong>pineapple, red apple, plum, cinnamon and bitter orange</strong> leads to a floral heart of <strong>magnolia, orchid, jasmine and Moroccan rose</strong>, then melts into a deep, warm base of <strong>cacao, vanilla, white musk, amber, sandalwood and cedar</strong>. A bold statement fragrance for evenings, parties and weddings.</p>'
				. '<ul><li><strong>Top Notes:</strong> Pineapple, Nutmeg, Cinnamon, Red Apple, Plum, Pepper, Cassia, Bitter Orange</li><li><strong>Middle Notes:</strong> Magnolia, Orchid, Lily-of-the-Valley, Jasmine, Moroccan Rose, Iris</li><li><strong>Base Notes:</strong> Cacao, Vanilla, White Musk, Amber, Sandalwood, Cedar</li><li><strong>Size:</strong> 50ml</li><li><strong>For:</strong> Men &amp; Women (Unisex)</li></ul>',
			'long'     => '<h2>Arvon: Long Lasting Unisex Perfume in Pakistan</h2>'
				. '<p><strong>Arvon</strong> is a bold, luxurious unisex fragrance from <strong>MR ZAIB Luxury Perfumes</strong>, created for men and women who want a scent that gets noticed and remembered. Fruity and spicy at first, floral and elegant in the heart, and deep, warm and gourmand in the dry-down.</p>'
				. '<h3>Fragrance Notes of Arvon</h3>'
				. '<p><strong>Top Notes: Pineapple, Nutmeg, Cinnamon, Red Apple, Plum, Pepper, Cassia and Bitter Orange.</strong> A fresh blend of fruity, spicy and citrus notes that ignites the senses: juicy pineapple and apple warmed by cinnamon, nutmeg and pepper.</p>'
				. '<p><strong>Middle Notes: Magnolia, Orchid, Lily-of-the-Valley, Jasmine, Moroccan Rose and Iris.</strong> A floral heart of elegance, where every note blooms with sophistication.</p>'
				. '<p><strong>Base Notes: Cacao, Vanilla, White Musk, Amber, Sandalwood and Cedar.</strong> A deep, warm foundation that lingers long after the first impression: dark cacao and vanilla over smooth woods, glowing amber and clean musk.</p>'
				. '<h3>Fragrance Profile</h3>'
				. '<ul><li><strong>Family:</strong> Fruity Spicy, Floral, Gourmand Amber Woody</li><li><strong>Character:</strong> Rich, warm, sweet, seductive</li><li><strong>Best season:</strong> Winter, autumn and evenings all year</li><li><strong>Occasions:</strong> Parties, weddings, dinners, date nights and special events</li></ul>'
				. '<h3>Why Choose MR ZAIB Arvon?</h3>'
				. '<ul><li>Rich fruity-spicy opening, elegant floral heart and gourmand cacao-vanilla base</li><li>Long lasting unisex fragrance with strong projection</li><li>Perfect for him, for her, or to share</li><li>Luxurious 50ml crystal-cut black bottle with gold crown cap, a perfect gift</li><li>Affordable luxury perfume under Rs. 3000</li><li>Cash on Delivery all over Pakistan</li></ul>'
				. '<h3>How to Apply</h3>'
				. '<p>Spray 3 to 4 times on pulse points (neck, wrists, chest and behind the ears) from about 15 cm. Apply on clean, moisturised skin and do not rub, so the fragrance lasts longer.</p>'
				. '<p>Looking for the <strong>best unisex perfume in Pakistan</strong> that smells rich, warm and lasts all night? Order <strong>Arvon by MR ZAIB</strong> online today.</p>',
			'tags'     => array( 'Arvon', 'Unisex Perfume', 'Perfume for Men and Women', 'Unisex Perfume Pakistan', 'Long Lasting Perfume', 'Fruity Perfume', 'Spicy Perfume', 'Gourmand Perfume', 'Vanilla Perfume', 'Amber Perfume', 'Winter Perfume', 'Party Perfume', 'Wedding Perfume', 'Luxury Perfume', 'MR ZAIB' ),
			'seo'      => array(
				'title'    => 'Arvon Long Lasting Unisex Perfume 50ml | MR ZAIB Pakistan',
				'desc'     => 'Shop Arvon by MR ZAIB, a long lasting unisex perfume with pineapple, cinnamon, Moroccan rose, cacao, vanilla and amber. 50ml, Rs. 2,500. COD in Pakistan.',
				'focus'    => 'Arvon unisex perfume',
				'keywords' => 'Arvon perfume, Arvon unisex perfume, MR ZAIB Arvon, unisex perfume, unisex perfume Pakistan, perfume for men and women, long lasting unisex perfume, fruity spicy perfume, cacao vanilla perfume, gourmand perfume, party perfume, wedding perfume, winter perfume, luxury perfume Pakistan, perfume under 3000',
			),
		),
		array(
			'name'     => 'Zay-ab',
			'slug'     => 'zay-ab-perfume',
			'subtitle' => 'Long Lasting Oriental Perfume for Men | 50ml',
			'sku'      => 'MZ-ZAYAB-50',
			'regular'  => '2800',
			'sale'     => '2500',
			'cats'     => array( 'men' ),
			'images'   => array( 'zay-ab-main.jpg', 'zay-ab-banner.jpg', 'zay-ab-notes-top.jpg', 'zay-ab-notes-middle.jpg', 'zay-ab-notes-base.jpg' ),
			'alts'     => array( 'Zay-ab long lasting oriental perfume for men by MR ZAIB 50ml red bottle', 'MR ZAIB Zay-ab men perfume fragrance notes', 'Zay-ab top notes aldehydes lily of the valley rose', 'Zay-ab middle notes frankincense mystikal labdanum', 'Zay-ab base notes white musk benzoin ambergris' ),
			'notes'    => array(
				'Top Notes'    => 'Aldehydes, Lily of the Valley, Rose',
				'Middle Notes' => 'Frankincense, Mystikal, Labdanum',
				'Base Notes'   => 'White Musk, Benzoin, Ambergris',
			),
			'short'    => '<p><strong>Zay-ab by MR ZAIB</strong> is a long lasting oriental perfume for men. It opens with sparkling <strong>aldehydes, lily of the valley and rose</strong>, deepens into a smoky, resinous heart of <strong>frankincense, mystikal and labdanum</strong>, and settles on a warm, sensual base of <strong>white musk, benzoin and ambergris</strong>. A royal, rich fragrance for evenings, weddings and special occasions.</p>'
				. '<ul><li><strong>Top Notes:</strong> Aldehydes, Lily of the Valley, Rose</li><li><strong>Middle Notes:</strong> Frankincense, Mystikal, Labdanum</li><li><strong>Base Notes:</strong> White Musk, Benzoin, Ambergris</li><li><strong>Size:</strong> 50ml</li><li><strong>For:</strong> Men</li></ul>',
			'long'     => '<h2>Zay-ab: Long Lasting Oriental Perfume for Men in Pakistan</h2>'
				. '<p><strong>Zay-ab</strong> is a royal oriental fragrance from <strong>MR ZAIB Luxury Perfumes</strong>, made for the man who wants to <em>leave his presence</em> wherever he goes. Bright rose and aldehydes on top, smoky incense and resins in the heart, and a warm musky-amber trail that stays for hours.</p>'
				. '<h3>Fragrance Notes of Zay-ab</h3>'
				. '<p><strong>Top Notes: Aldehydes, Lily of the Valley and Rose.</strong> A delicate, sparkling opening: fresh aldehydes lift a soft bouquet of lily of the valley and velvety red rose.</p>'
				. '<p><strong>Middle Notes: Frankincense, Mystikal and Labdanum.</strong> The heart turns deep and mysterious, with smoky frankincense, resinous mystikal and rich, leathery labdanum giving Zay-ab its oriental, oud-like character.</p>'
				. '<p><strong>Base Notes: White Musk, Benzoin and Ambergris.</strong> A warm, sensual foundation of clean white musk, sweet balsamic benzoin and salty ambergris that lingers long on skin and clothes.</p>'
				. '<h3>Fragrance Profile</h3>'
				. '<ul><li><strong>Family:</strong> Oriental, Incense, Amber Musk</li><li><strong>Character:</strong> Royal, smoky, warm, sensual</li><li><strong>Best season:</strong> Winter, autumn and evenings all year</li><li><strong>Occasions:</strong> Weddings, Eid, Jummah, dinners, formal events and special nights</li></ul>'
				. '<h3>Why Choose MR ZAIB Zay-ab?</h3>'
				. '<ul><li>Rich oriental blend of rose, frankincense, labdanum and ambergris</li><li>Long lasting fragrance with a strong, elegant trail</li><li>Luxurious 50ml crystal-cut red bottle with gold crown cap, a perfect gift for men</li><li>Affordable luxury perfume under Rs. 3000</li><li>Cash on Delivery all over Pakistan</li></ul>'
				. '<h3>How to Apply</h3>'
				. '<p>Spray 3 to 4 times on pulse points (neck, wrists, chest and behind the ears) from about 15 cm. Apply on clean, moisturised skin and do not rub, so the fragrance lasts longer.</p>'
				. '<p>Looking for the <strong>best oriental perfume for men in Pakistan</strong> that smells royal and lasts all day? Order <strong>Zay-ab by MR ZAIB</strong> online today.</p>',
			'tags'     => array( 'Zay-ab', 'Zayab Perfume', 'Perfume for Men', 'Men Perfume Pakistan', 'Long Lasting Perfume', 'Oriental Perfume', 'Incense Perfume', 'Frankincense Perfume', 'Rose Perfume', 'Musk Perfume', 'Amber Perfume', 'Winter Perfume', 'Wedding Perfume', 'Gift for Men', 'Luxury Perfume', 'MR ZAIB' ),
			'seo'      => array(
				'title'    => 'Zay-ab Long Lasting Oriental Perfume for Men 50ml | MR ZAIB',
				'desc'     => 'Shop Zay-ab (Zayab) by MR ZAIB, a long lasting oriental perfume for men with rose, frankincense, labdanum, musk and ambergris. 50ml, Rs. 2,500. COD in Pakistan.',
				'focus'    => 'Zay-ab perfume for men',
				'keywords' => 'Zay-ab perfume, Zayab perfume, Zay-ab perfume for men, MR ZAIB perfume, perfume for men, men perfume Pakistan, long lasting perfume for men, oriental perfume for men, frankincense perfume, incense perfume, rose musk perfume, ambergris perfume, wedding perfume for men, winter perfume for men, luxury perfume Pakistan, perfume under 3000',
			),
		),
		array(
			'name'     => 'Hoor',
			'slug'     => 'hoor-perfume',
			'subtitle' => 'Long Lasting Floral Perfume for Women | 50ml',
			'sku'      => 'MZ-HOOR-50',
			'regular'  => '2800',
			'sale'     => '2500',
			'cats'     => array( 'women' ),
			'images'   => array( 'hoor-main.jpg', 'hoor-banner.jpg', 'hoor-notes-top.jpg', 'hoor-notes-middle.jpg', 'hoor-notes-base.jpg' ),
			'alts'     => array( 'Hoor long lasting floral perfume for women by MR ZAIB 50ml pink bottle', 'MR ZAIB Hoor women perfume fragrance notes', 'Hoor top notes black currant pineapple orange red apple', 'Hoor middle notes rose freesia peony', 'Hoor base notes musk iris sandalwood' ),
			'notes'    => array(
				'Top Notes'    => 'Black Currant, Pineapple, Orange, Red Apple',
				'Middle Notes' => 'Rose, Freesia, Peony',
				'Base Notes'   => 'Musk, Iris, Sandalwood',
			),
			'short'    => '<p><strong>Hoor by MR ZAIB</strong> is a long lasting fruity floral perfume for women, inspired by Myton Floral. It opens with juicy <strong>black currant, pineapple, orange and red apple</strong>, blooms into a romantic heart of <strong>rose, freesia and peony</strong>, and settles on a soft, elegant base of <strong>musk, iris and sandalwood</strong>. A feminine, sweet and graceful fragrance for every day and every celebration.</p>'
				. '<ul><li><strong>Top Notes:</strong> Black Currant, Pineapple, Orange, Red Apple</li><li><strong>Middle Notes:</strong> Rose, Freesia, Peony</li><li><strong>Base Notes:</strong> Musk, Iris, Sandalwood</li><li><strong>Size:</strong> 50ml</li><li><strong>For:</strong> Women</li></ul>',
			'long'     => '<h2>Hoor: Long Lasting Floral Perfume for Women in Pakistan</h2>'
				. '<p><strong>Hoor</strong> is a beautiful women\'s perfume from <strong>MR ZAIB Luxury Perfumes</strong>, inspired by Myton Floral. Made for the woman who wants to <em>leave her presence</em>, Hoor is fresh and fruity at first, romantic and floral in the heart, and soft and creamy in the dry-down.</p>'
				. '<h3>Fragrance Notes of Hoor</h3>'
				. '<p><strong>Top Notes: Black Currant, Pineapple, Orange and Red Apple.</strong> A bright, juicy opening of sweet berries, tropical pineapple, sparkling orange and crisp red apple.</p>'
				. '<p><strong>Middle Notes: Rose, Freesia and Peony.</strong> A romantic floral bouquet: velvety pink rose, airy freesia and soft peony give Hoor its graceful, feminine heart.</p>'
				. '<p><strong>Base Notes: Musk, Iris and Sandalwood.</strong> A smooth, powdery and warm finish of clean musk, elegant iris and creamy sandalwood that stays close to the skin for hours.</p>'
				. '<h3>Fragrance Profile</h3>'
				. '<ul><li><strong>Family:</strong> Fruity Floral, Musky</li><li><strong>Character:</strong> Feminine, sweet, fresh, elegant</li><li><strong>Best season:</strong> Spring, summer and all year round</li><li><strong>Occasions:</strong> Daily wear, office, university, brunches, mehndi, weddings and parties</li></ul>'
				. '<h3>Why Choose MR ZAIB Hoor?</h3>'
				. '<ul><li>Fruity floral blend of black currant, rose, peony and musk</li><li>Long lasting women\'s fragrance with a soft, pretty trail</li><li>Elegant 50ml pink crystal bottle, a perfect gift for her</li><li>Affordable luxury perfume under Rs. 3000</li><li>Cash on Delivery all over Pakistan</li></ul>'
				. '<h3>How to Apply</h3>'
				. '<p>Spray 3 to 4 times on pulse points (neck, wrists and behind the ears) from about 15 cm. Apply on clean, moisturised skin and do not rub, so the fragrance lasts longer. A light mist on hair or scarf leaves a lovely trail.</p>'
				. '<p>Looking for the <strong>best perfume for women in Pakistan</strong> that smells fresh, floral and lasts all day? Order <strong>Hoor by MR ZAIB</strong> online today.</p>',
			'tags'     => array( 'Hoor', 'Perfume for Women', 'Women Perfume Pakistan', 'Ladies Perfume', 'Long Lasting Perfume', 'Floral Perfume', 'Fruity Floral Perfume', 'Rose Perfume', 'Peony Perfume', 'Musk Perfume', 'Inspired by Myton Floral', 'Summer Perfume', 'Gift for Her', 'Wedding Perfume', 'Luxury Perfume', 'MR ZAIB' ),
			'seo'      => array(
				'title'    => 'Hoor Long Lasting Perfume for Women 50ml | MR ZAIB Pakistan',
				'desc'     => 'Shop Hoor by MR ZAIB, a long lasting fruity floral perfume for women with black currant, rose, peony, musk and sandalwood. 50ml, Rs. 2,500. COD in Pakistan.',
				'focus'    => 'Hoor perfume for women',
				'keywords' => 'Hoor perfume, Hoor perfume for women, MR ZAIB Hoor, perfume for women, women perfume Pakistan, ladies perfume, long lasting perfume for women, floral perfume for women, fruity floral perfume, rose perfume for women, inspired by Myton Floral, best perfume for women in Pakistan, gift for her, luxury perfume Pakistan, perfume under 3000',
			),
		),
	);
}

/** Old demo product titles that must be removed from live stores. */
function mrzaib_old_demo_titles() {
	return array( 'Royal Oud', 'Midnight Code', 'Ocean Breeze', 'Rose Velvet', 'Green Vetiver', 'Amber Night', 'Golden Saffron', 'Blue Legend', 'Black Musk', 'Desert Rose', 'Citrus Bloom', 'Vanilla Dream', 'Leather Noir', 'Royal Duo', 'Floral Duo', 'Two Of A Kind', 'Cool Breeze Duo', 'Oud Supreme - Attar (8ML)', 'Saffron Gold - Attar (8ML)', 'White Jasmine - Attar (8ML)', 'Top 5 Samples', 'Top 10 Sample Set', 'Aroma Plug-In Diffuser', 'Smart Scent Diffuser', 'Silver Mist Body Mist', 'Pocket Oud 20ml', 'Amber Perfume Wax' );
}

/** Old demo category slugs. */
function mrzaib_old_demo_cats() {
	return array( 'perfumes-for-men', 'perfumes-for-women', 'unisex-perfumes', 'best-sellers-men', 'best-sellers-women', 'signature-range', 'executive-range', 'royal-range', 'new-arrivals', 'gift-box', 'gourmand', 'attar', 'air-care', 'oud', 'tester-box', 'under-2000', 'body-mist', 'perfume-wax', 'pocket-perfumes', 'seasonal', 'genre', 'winter-perfume', 'summer-perfume', 'spring-perfume', 'autumn-perfume', 'arabic-perfume', 'french-perfume', 'western-perfume', 'eastern-perfume' );
}

/** Delete every piece of dummy demo content. */
function mrzaib_remove_dummy() {
	$n = array( 'products' => 0, 'cats' => 0, 'posts' => 0, 'pages' => 0, 'media' => 0 );

	foreach ( mrzaib_old_demo_titles() as $title ) {
		foreach ( get_posts( array( 'post_type' => 'product', 'title' => $title, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) ) as $pid ) {
			$p = function_exists( 'wc_get_product' ) ? wc_get_product( $pid ) : null;
			if ( $p && $p->is_type( 'variable' ) ) {
				foreach ( $p->get_children() as $vid ) {
					wp_delete_post( $vid, true );
				}
			}
			wp_delete_post( $pid, true ); // also deletes its reviews/questions.
			$n['products']++;
		}
	}
	foreach ( mrzaib_old_demo_cats() as $slug ) {
		$t = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $t ) {
			wp_delete_term( $t->term_id, 'product_cat' );
			$n['cats']++;
		}
	}
	foreach ( array( 'How to Make Your Perfume Last All Day', 'Best Perfumes for Winter in Pakistan', 'Attar vs Perfume: What Is the Difference?' ) as $title ) {
		foreach ( get_posts( array( 'post_type' => 'post', 'title' => $title, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) ) as $id ) {
			wp_delete_post( $id, true );
			$n['posts']++;
		}
	}
	foreach ( array( 'quiz', 'loyalty', 'corporate', 'our-outlets', 'our-story' ) as $slug ) {
		$p = get_page_by_path( $slug );
		if ( $p ) {
			wp_delete_post( $p->ID, true );
			$n['pages']++;
		}
	}
	/* Dummy images that were copied into the media library. */
	$map  = get_option( 'mrzaib_demo_media', array() );
	$keep = array( 'logo-gold.png' );
	foreach ( mrzaib_store_categories() as $c ) {
		$keep[] = $c[1];
	}
	foreach ( mrzaib_store_products() as $p ) {
		$keep = array_merge( $keep, $p['images'] );
	}
	foreach ( $map as $file => $aid ) {
		if ( ! in_array( $file, $keep, true ) ) {
			if ( (int) get_theme_mod( 'custom_logo' ) === (int) $aid ) {
				remove_theme_mod( 'custom_logo' );
			}
			wp_delete_attachment( $aid, true );
			unset( $map[ $file ] );
			$n['media']++;
		}
	}
	update_option( 'mrzaib_demo_media', $map, false );
	/* Old menus are rebuilt from scratch. */
	foreach ( array( 'MR ZAIB Main', 'Footer About', 'Footer Support', 'Footer Quick Links' ) as $menu ) {
		if ( wp_get_nav_menu_object( $menu ) ) {
			wp_delete_nav_menu( $menu );
		}
	}
	/* Dummy Customizer texts. */
	foreach ( array( 'address', 'outlet_text', 'stat_1', 'stat_2', 'stat_3' ) as $mod ) {
		remove_theme_mod( 'mrzaib_' . $mod );
	}
	if ( function_exists( 'wc_delete_product_transients' ) ) {
		wc_delete_product_transients();
	}
	return sprintf( 'Dummy data removed: %d products, %d categories, %d blog posts, %d pages, %d images.', $n['products'], $n['cats'], $n['posts'], $n['pages'], $n['media'] );
}

/** Create the 5 categories. Returns slug => term_id. */
function mrzaib_install_categories() {
	$ids   = array();
	$order = 0;
	foreach ( mrzaib_store_categories() as $slug => $c ) {
		$t = term_exists( $slug, 'product_cat' );
		if ( ! $t ) {
			$t = wp_insert_term( $c[0], 'product_cat', array( 'slug' => $slug, 'description' => $c[2] ) );
		}
		if ( is_wp_error( $t ) ) {
			continue;
		}
		$tid          = is_array( $t ) ? (int) $t['term_id'] : (int) $t;
		$ids[ $slug ] = $tid;
		update_term_meta( $tid, 'order', $order++ );
		if ( ! get_term_meta( $tid, 'thumbnail_id', true ) ) {
			$img = mrzaib_import_image( $c[1] );
			if ( $img ) {
				update_term_meta( $tid, 'thumbnail_id', $img );
				update_post_meta( $img, '_wp_attachment_image_alt', 'MR ZAIB ' . $c[0] . ' perfumes' );
			}
		}
	}
	return $ids;
}

/** Create / update Kahn-vahl and Arvon. */
function mrzaib_install_products( $cat_ids, $only = '' ) {
	$made = 0;
	foreach ( mrzaib_store_products() as $i => $d ) {
		if ( $only && $only !== $d['slug'] ) {
			continue;
		}
		$existing = mrzaib_find_store_product( $d );
		if ( $existing && 'trash' === get_post_status( $existing[0] ) ) {
			wp_untrash_post( $existing[0] );
			wp_update_post( array( 'ID' => $existing[0], 'post_status' => 'publish' ) );
		}
		$prod     = $existing ? wc_get_product( $existing[0] ) : new WC_Product_Simple();
		if ( ! $prod ) {
			continue;
		}
		$is_new = ! $existing;
		$prod->set_name( $d['name'] );
		if ( $is_new ) {
			$prod->set_slug( $d['slug'] );
			$prod->set_status( 'publish' );
			$prod->set_catalog_visibility( 'visible' );
			$prod->set_featured( true );
			$prod->set_menu_order( $i + 1 );
			if ( ! wc_get_product_id_by_sku( $d['sku'] ) ) {
				$prod->set_sku( $d['sku'] );
			}
			$prod->set_manage_stock( false );
			$prod->set_stock_status( 'instock' );
		}
		$prod->set_regular_price( $d['regular'] );
		$prod->set_sale_price( $d['sale'] );
		$prod->set_short_description( $d['short'] );
		$prod->set_description( $d['long'] );
		$cats = array();
		foreach ( $d['cats'] as $s ) {
			if ( isset( $cat_ids[ $s ] ) ) {
				$cats[] = $cat_ids[ $s ];
			}
		}
		$prod->set_category_ids( $cats );
		$imgs = array();
		foreach ( $d['images'] as $k => $file ) {
			$aid = mrzaib_import_image( $file );
			if ( $aid ) {
				$imgs[] = $aid;
				update_post_meta( $aid, '_wp_attachment_image_alt', $d['alts'][ $k ] );
			}
		}
		$note_imgs = array();
		foreach ( $d['images'] as $k => $file ) {
			if ( false !== strpos( $file, '-notes' ) && isset( $imgs[ $k ] ) ) {
				$note_imgs[] = $imgs[ $k ];
			}
		}
		if ( $imgs ) {
			$prod->set_image_id( array_shift( $imgs ) );
			$prod->set_gallery_image_ids( $imgs );
		}
		$pid = $prod->save();
		wp_set_object_terms( $pid, $d['tags'], 'product_tag' );
		update_post_meta( $pid, '_mrzaib_subtitle', $d['subtitle'] );
		update_post_meta( $pid, '_mrzaib_notes', $d['notes'] );
		update_post_meta( $pid, '_mrzaib_note_imgs', $note_imgs );
		/* SEO: Yoast SEO fields only (edit them in the Yoast box on the product page). */
		update_post_meta( $pid, '_yoast_wpseo_title', $d['seo']['title'] );
		update_post_meta( $pid, '_yoast_wpseo_metadesc', $d['seo']['desc'] );
		update_post_meta( $pid, '_yoast_wpseo_focuskw', $d['seo']['focus'] );
		$made++;
	}
	return $made . ' MR ZAIB product(s) added/updated.';
}

/** Find a theme product by slug, SKU or title (any status, trash included). */
function mrzaib_find_store_product( $d ) {
	$q = get_posts( array( 'post_type' => 'product', 'name' => $d['slug'], 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );
	if ( ! $q ) {
		$q = get_posts( array( 'post_type' => 'product', 'name' => $d['slug'] . '__trashed', 'post_status' => 'trash', 'numberposts' => 1, 'fields' => 'ids' ) );
	}
	if ( ! $q && function_exists( 'wc_get_product_id_by_sku' ) && wc_get_product_id_by_sku( $d['sku'] ) ) {
		$q = array( wc_get_product_id_by_sku( $d['sku'] ) );
	}
	if ( ! $q ) {
		$q = get_posts( array( 'post_type' => 'product', 'title' => $d['name'], 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );
	}
	return $q;
}

/* ---------- Products > MR ZAIB Products: add / restore theme products one by one ---------- */
add_action( 'admin_menu', function () {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	add_submenu_page( 'edit.php?post_type=product', 'MR ZAIB Products', 'MR ZAIB Products', 'manage_woocommerce', 'mrzaib-products', 'mrzaib_products_page' );
}, 60 );

function mrzaib_products_page() {
	$msg = '';
	if ( isset( $_POST['mrzaib_add_product'] ) && check_admin_referer( 'mrzaib_add_product' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore
		$slug = sanitize_title( wp_unslash( $_POST['mrzaib_add_product'] ) );
		$cats = mrzaib_install_categories();
		$msg  = mrzaib_install_products( $cats, 'all' === $slug ? '' : $slug );
	}
	echo '<div class="wrap"><h1>MR ZAIB Products</h1>';
	echo '<p style="max-width:760px;font-size:14px">Add your MR ZAIB perfumes with photos, fragrance notes, short &amp; long description, tags, subtitle and Yoast SEO (title, meta description, focus keyphrase) in one click. If you deleted a product, click <strong>Add product</strong> to bring it back. Running again updates the product.</p>';
	if ( $msg ) {
		echo '<div class="notice notice-success"><p>' . esc_html( $msg ) . '</p></div>';
	}
	echo '<form method="post">';
	wp_nonce_field( 'mrzaib_add_product' );
	echo '<table class="widefat striped" style="max-width:960px"><thead><tr><th style="width:90px">Photo</th><th>Product</th><th>Category</th><th>Price</th><th>Status</th><th></th></tr></thead><tbody>';
	foreach ( mrzaib_store_products() as $d ) {
		$id     = mrzaib_find_store_product( $d );
		$status = $id ? get_post_status( $id[0] ) : '';
		$label  = $id && 'trash' !== $status ? 'Update product' : 'Add product';
		$img    = get_template_directory_uri() . '/assets/images/' . $d['images'][0];
		echo '<tr><td><img src="' . esc_url( $img ) . '" width="70" height="70" style="object-fit:cover;border-radius:6px"></td>';
		echo '<td><strong>' . esc_html( $d['name'] ) . '</strong><br><span class="description">' . esc_html( $d['subtitle'] ) . '</span></td>';
		echo '<td>' . esc_html( ucwords( implode( ', ', $d['cats'] ) ) ) . '</td>';
		echo '<td><del>Rs. ' . esc_html( number_format( (float) $d['regular'] ) ) . '</del> Rs. ' . esc_html( number_format( (float) $d['sale'] ) ) . '</td>';
		echo '<td>' . ( $id ? ( 'trash' === $status ? '&#128465; In trash' : '&#10004; ' . esc_html( ucfirst( $status ) ) . ' &middot; <a href="' . esc_url( get_edit_post_link( $id[0] ) ) . '">Edit</a>' ) : '&#10008; Not added' ) . '</td>';
		echo '<td><button class="button button-primary" name="mrzaib_add_product" value="' . esc_attr( $d['slug'] ) . '">' . esc_html( $label ) . '</button></td></tr>';
	}
	echo '</tbody></table>';
	echo '<p><button class="button" name="mrzaib_add_product" value="all">Add / update all</button></p></form></div>';
}

/* ---------- SEO is handled by the Yoast SEO plugin (theme outputs no meta tags) ---------- */

/** One-time cleanup of the theme's old custom SEO fields. */
add_action( 'admin_init', function () {
	if ( '1' === get_option( 'mrzaib_seo_cleanup' ) ) {
		return;
	}
	foreach ( array( '_mrzaib_seo_title', '_mrzaib_seo_desc', '_mrzaib_seo_keywords' ) as $k ) {
		delete_post_meta_by_key( $k );
	}
	if ( ! class_exists( 'RankMath' ) ) {
		foreach ( array( 'rank_math_title', 'rank_math_description', 'rank_math_focus_keyword' ) as $k ) {
			delete_post_meta_by_key( $k );
		}
	}
	remove_theme_mod( 'mrzaib_seo_title' );
	update_option( 'mrzaib_seo_cleanup', '1' );
} );

/**
 * Homepage "Top Perfume Categories": every top-level product category (also empty ones),
 * ordered like WooCommerce's category order. Theme categories use the theme artwork unless
 * the admin uploaded their own thumbnail.
 */
function mrzaib_home_categories() {
	$out = array();
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return $out;
	}
	$terms = get_terms( array(
		'taxonomy'   => 'product_cat',
		'parent'     => 0,
		'hide_empty' => false,
		'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		'menu_order' => 'asc',
	) );
	if ( is_wp_error( $terms ) ) {
		return $out;
	}
	$theme = mrzaib_store_categories();
	$media = array_map( 'intval', (array) get_option( 'mrzaib_demo_media', array() ) );
	foreach ( $terms as $t ) {
		$tid = (int) get_term_meta( $t->term_id, 'thumbnail_id', true );
		$own = $tid && ! in_array( $tid, $media, true ) ? wp_get_attachment_image_url( $tid, 'medium_large' ) : '';
		if ( $own ) {
			$img = $own;
		} elseif ( isset( $theme[ $t->slug ] ) ) {
			$img = mrzaib_img( $theme[ $t->slug ][1] );
		} elseif ( $tid && wp_get_attachment_image_url( $tid, 'medium_large' ) ) {
			$img = wp_get_attachment_image_url( $tid, 'medium_large' );
		} elseif ( function_exists( 'wc_placeholder_img_src' ) ) {
			$img = wc_placeholder_img_src( 'medium_large' );
		} else {
			$img = '';
		}
		$out[] = array( 'name' => $t->name, 'link' => get_term_link( $t ), 'img' => $img );
	}
	return $out;
}

/** One-time update: Ethereal category becomes Attar; old category artwork is replaced. */
function mrzaib_migrate_categories_v2() {
	if ( (int) get_option( 'mrzaib_cat_v' ) >= 3 || ! taxonomy_exists( 'product_cat' ) ) {
		return;
	}
	update_option( 'mrzaib_cat_v', 3, false );
	$eth   = get_term_by( 'slug', 'ethereal', 'product_cat' );
	$attar = get_term_by( 'slug', 'attar', 'product_cat' );
	if ( $eth && ! $attar ) {
		$c = mrzaib_store_categories();
		wp_update_term( $eth->term_id, 'product_cat', array( 'name' => 'Attar', 'slug' => 'attar', 'description' => $c['attar'][2] ) );
	} elseif ( $eth && $attar && ! $eth->count ) {
		wp_delete_term( $eth->term_id, 'product_cat' );
	}
	/* Point theme categories' thumbnails at the new artwork. */
	$map = (array) get_option( 'mrzaib_demo_media', array() );
	foreach ( array( 'cat-men.jpg', 'cat-women.jpg', 'cat-unisex.jpg', 'cat-tester.jpg', 'cat-ethereal.jpg', 'cat-attar.jpg' ) as $f ) {
		unset( $map[ $f ] );
	}
	$old = array_map( 'intval', (array) get_option( 'mrzaib_demo_media', array() ) );
	update_option( 'mrzaib_demo_media', $map, false );
	if ( ! function_exists( 'mrzaib_import_image' ) ) {
		return;
	}
	foreach ( mrzaib_store_categories() as $slug => $c ) {
		$t = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $t ) {
			continue;
		}
		$tid = (int) get_term_meta( $t->term_id, 'thumbnail_id', true );
		if ( $tid && ! in_array( $tid, $old, true ) ) {
			continue; // Admin's own image: keep it.
		}
		$img = mrzaib_import_image( $c[1] );
		if ( $img ) {
			update_term_meta( $t->term_id, 'thumbnail_id', $img );
			update_post_meta( $img, '_wp_attachment_image_alt', 'MR ZAIB ' . $c[0] . ' perfumes' );
		}
	}
}
add_action( 'admin_init', 'mrzaib_migrate_categories_v2' );

