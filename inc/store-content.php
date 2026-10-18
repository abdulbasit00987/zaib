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
			'subtitle' => 'Perfume for Men | 50ml',
			'sku'      => 'MZ-KAHNVAHL-50',
			'regular'  => '2800',
			'sale'     => '2500',
			'cats'     => array( 'men' ),
			'images'   => array( 'kahn-vahl-1.jpg', 'kahn-vahl-2.jpg', 'kahn-vahl-top-notes.jpg', 'kahn-vahl-middle-notes.jpg', 'kahn-vahl-base-notes.jpg', 'mr-zaib-gift-box.jpg' ),
			'alts'     => array( 'Kahn-vahl perfume for men by MR ZAIB 50ml blue bottle', 'MR ZAIB Kahn-vahl blue perfume bottle', 'Kahn-vahl top notes coconut water', 'Kahn-vahl middle notes jasmine and iris', 'Kahn-vahl base notes vanilla sandalwood amber', 'MR ZAIB luxury perfume gift box' ),
			'notes'    => array(
				'Top Notes'    => 'Coconut Water',
				'Middle Notes' => 'Jasmine, Iris',
				'Base Notes'   => 'Vanilla, Sandalwood, Amber',
			),
			'short'    => '<p><strong>Kahn-vahl</strong> by MR ZAIB is a fresh, aquatic-floral perfume for men. It opens with cool <strong>coconut water</strong>, flows into a clean heart of <strong>jasmine and iris</strong>, and settles on a smooth, warm base of <strong>vanilla, sandalwood and amber</strong>.</p><ul><li>Top: Coconut Water</li><li>Middle: Jasmine, Iris</li><li>Base: Vanilla, Sandalwood, Amber</li><li>Size: 50ml</li></ul>',
			'long'     => '<h2>Kahn-vahl Perfume for Men by MR ZAIB</h2>'
				. '<p>Kahn-vahl is a modern men\'s fragrance from MR ZAIB Luxury Perfumes, made for the man who wants to <em>leave his presence</em>. It opens fresh and bright, turns elegant in the heart and finishes warm and creamy on the skin. Fresh enough for the day and smooth enough for the evening.</p>'
				. '<h3>How Kahn-vahl smells</h3>'
				. '<p><strong>Top notes: Coconut Water.</strong> The first spray is cool, clean and watery, a light tropical freshness that feels effortless in warm weather.</p>'
				. '<p><strong>Middle notes: Jasmine and Iris.</strong> The heart turns refined. White jasmine adds a soft floral glow and iris brings a powdery, polished elegance.</p>'
				. '<p><strong>Base notes: Vanilla, Sandalwood and Amber.</strong> The dry-down is warm and smooth. Creamy sandalwood, sweet vanilla and glowing amber stay close to the skin and leave a memorable trail.</p>'
				. '<h3>When to wear it</h3>'
				. '<p>Kahn-vahl is a versatile perfume for men: office days, casual outings, dinners and summer evenings. Its fresh opening suits Pakistan\'s warm weather, while the vanilla-amber base keeps it classy after dark.</p>'
				. '<h3>Why choose MR ZAIB Kahn-vahl</h3>'
				. '<ul><li>Fresh aquatic opening with a warm, woody-amber base</li><li>Elegant 50ml bottle with a gold crown cap, ready to gift</li><li>Cash on Delivery all over Pakistan</li></ul>'
				. '<p><strong>How to apply:</strong> spray 3 to 4 times on pulse points (neck, wrists, behind the ears) from about 15 cm. Apply on moisturised skin for the best performance.</p>',
			'tags'     => array( 'Perfume for Men', 'Long Lasting Perfume', 'Aquatic Perfume', 'Vanilla Perfume', 'Sandalwood Perfume', 'MR ZAIB' ),
			'seo'      => array(
				'title'    => 'Kahn-vahl Perfume for Men 50ml | MR ZAIB Luxury Perfumes Pakistan',
				'desc'     => 'Buy Kahn-vahl by MR ZAIB, a fresh aquatic perfume for men with coconut water, jasmine, iris, vanilla, sandalwood and amber. 50ml at Rs. 2,500. Cash on Delivery in Pakistan.',
				'focus'    => 'Kahn-vahl perfume',
				'keywords' => 'Kahn-vahl perfume, MR ZAIB perfume, perfume for men, perfume for men in Pakistan, long lasting perfume for men, aquatic perfume for men, coconut perfume, vanilla sandalwood amber perfume, men fragrance Pakistan, luxury perfume Pakistan, best perfume under 3000',
			),
		),
		array(
			'name'     => 'Arvon',
			'slug'     => 'arvon-perfume',
			'subtitle' => 'Unisex Perfume | 50ml',
			'sku'      => 'MZ-ARVON-50',
			'regular'  => '2800',
			'sale'     => '2500',
			'cats'     => array( 'unisex' ),
			'images'   => array( 'arvon-1.jpg', 'mr-zaib-gift-box.jpg', 'arvon-top-notes.jpg', 'arvon-middle-notes.jpg', 'arvon-base-notes.jpg' ),
			'alts'     => array( 'Arvon unisex perfume by MR ZAIB 50ml black bottle', 'MR ZAIB luxury perfume gift box', 'Arvon top notes pineapple apple plum cinnamon bitter orange', 'Arvon middle notes magnolia orchid jasmine moroccan rose iris', 'Arvon base notes cacao vanilla white musk amber sandalwood cedar' ),
			'notes'    => array(
				'Top Notes'    => 'Pineapple, Nutmeg, Cinnamon, Red Apple, Plum, Pepper, Cassia, Bitter Orange',
				'Middle Notes' => 'Magnolia, Orchid, Lily-of-the-Valley, Jasmine, Moroccan Rose, Iris',
				'Base Notes'   => 'Cacao, Vanilla, White Musk, Amber, Sandalwood, Cedar',
			),
			'short'    => '<p><strong>Arvon</strong> by MR ZAIB is a rich unisex perfume. A juicy, spicy opening of <strong>pineapple, red apple, plum, cinnamon and bitter orange</strong> leads to a floral heart of <strong>magnolia, orchid, jasmine and Moroccan rose</strong>, then melts into a deep base of <strong>cacao, vanilla, white musk, amber, sandalwood and cedar</strong>.</p><ul><li>Top: Pineapple, Nutmeg, Cinnamon, Red Apple, Plum, Pepper, Cassia, Bitter Orange</li><li>Middle: Magnolia, Orchid, Lily-of-the-Valley, Jasmine, Moroccan Rose, Iris</li><li>Base: Cacao, Vanilla, White Musk, Amber, Sandalwood, Cedar</li><li>Size: 50ml</li></ul>',
			'long'     => '<h2>Arvon Unisex Perfume by MR ZAIB</h2>'
				. '<p>Arvon is a bold, luxurious unisex fragrance from MR ZAIB Luxury Perfumes, for men and women who want a scent that gets noticed. Fruity and spicy at first, floral and elegant in the heart, and deep, warm and gourmand in the dry-down.</p>'
				. '<h3>How Arvon smells</h3>'
				. '<p><strong>Top notes: Pineapple, Nutmeg, Cinnamon, Red Apple, Plum, Pepper, Cassia and Bitter Orange.</strong> A fresh blend of fruity, spicy and citrus notes that wakes up the senses, juicy pineapple and apple warmed by cinnamon, nutmeg and pepper.</p>'
				. '<p><strong>Middle notes: Magnolia, Orchid, Lily-of-the-Valley, Jasmine, Moroccan Rose and Iris.</strong> A floral heart of elegance where every note blooms with sophistication.</p>'
				. '<p><strong>Base notes: Cacao, Vanilla, White Musk, Amber, Sandalwood and Cedar.</strong> A deep, warm foundation that lingers long after the first impression, dark cacao and vanilla over smooth woods, amber and clean musk.</p>'
				. '<h3>When to wear it</h3>'
				. '<p>Arvon is a statement scent for evenings, parties, weddings and special occasions, and its warm cacao-vanilla base is especially beautiful in cooler weather. It works equally well on men and women.</p>'
				. '<h3>Why choose MR ZAIB Arvon</h3>'
				. '<ul><li>A rich fruity-spicy, floral and gourmand composition</li><li>Unisex: perfect for him, her or to share</li><li>Elegant 50ml bottle with a gold crown cap, ready to gift</li><li>Cash on Delivery all over Pakistan</li></ul>'
				. '<p><strong>How to apply:</strong> spray 3 to 4 times on pulse points (neck, wrists, behind the ears) from about 15 cm. Apply on moisturised skin for the best performance.</p>',
			'tags'     => array( 'Unisex Perfume', 'Long Lasting Perfume', 'Fruity Perfume', 'Gourmand Perfume', 'Perfume for Men and Women', 'MR ZAIB' ),
			'seo'      => array(
				'title'    => 'Arvon Unisex Perfume 50ml | MR ZAIB Luxury Perfumes Pakistan',
				'desc'     => 'Buy Arvon by MR ZAIB, a unisex perfume with pineapple, red apple, cinnamon, Moroccan rose, cacao, vanilla and amber. 50ml at Rs. 2,500. Cash on Delivery in Pakistan.',
				'focus'    => 'Arvon perfume',
				'keywords' => 'Arvon perfume, MR ZAIB Arvon, unisex perfume, unisex perfume Pakistan, perfume for men and women, fruity spicy perfume, pineapple perfume, cacao vanilla perfume, long lasting unisex fragrance, luxury perfume Pakistan, best perfume under 3000',
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
function mrzaib_install_products( $cat_ids ) {
	$made = 0;
	foreach ( mrzaib_store_products() as $i => $d ) {
		$existing = get_posts( array( 'post_type' => 'product', 'title' => $d['name'], 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );
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
		/* SEO: theme fallback + Yoast + Rank Math fields. */
		update_post_meta( $pid, '_mrzaib_seo_title', $d['seo']['title'] );
		update_post_meta( $pid, '_mrzaib_seo_desc', $d['seo']['desc'] );
		update_post_meta( $pid, '_mrzaib_seo_keywords', $d['seo']['keywords'] );
		update_post_meta( $pid, '_yoast_wpseo_title', $d['seo']['title'] );
		update_post_meta( $pid, '_yoast_wpseo_metadesc', $d['seo']['desc'] );
		update_post_meta( $pid, '_yoast_wpseo_focuskw', $d['seo']['focus'] );
		update_post_meta( $pid, 'rank_math_title', $d['seo']['title'] );
		update_post_meta( $pid, 'rank_math_description', $d['seo']['desc'] );
		update_post_meta( $pid, 'rank_math_focus_keyword', $d['seo']['keywords'] );
		$made++;
	}
	return $made . ' MR ZAIB products added/updated (Kahn-vahl, Arvon).';
}

/* ---------- SEO output when no SEO plugin is active ---------- */
function mrzaib_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}
add_filter( 'pre_get_document_title', function ( $t ) {
	if ( mrzaib_has_seo_plugin() || ! is_singular( 'product' ) ) {
		return $t;
	}
	$custom = get_post_meta( get_queried_object_id(), '_mrzaib_seo_title', true );
	return $custom ? $custom : $t;
}, 20 );
add_action( 'wp_head', function () {
	if ( mrzaib_has_seo_plugin() ) {
		return;
	}
	$desc = '';
	$keys = '';
	if ( is_singular( 'product' ) ) {
		$id   = get_queried_object_id();
		$desc = get_post_meta( $id, '_mrzaib_seo_desc', true );
		$keys = get_post_meta( $id, '_mrzaib_seo_keywords', true );
		if ( ! $desc ) {
			$desc = wp_trim_words( wp_strip_all_tags( get_post_field( 'post_excerpt', $id ) ), 28, '' );
		}
	} elseif ( mrzaib_is( 'is_product_category' ) ) {
		$t    = get_queried_object();
		$desc = $t && $t->description ? $t->description : '';
		$keys = $t ? $t->name . ' perfume, ' . $t->name . ' perfume Pakistan, MR ZAIB ' . $t->name : '';
	} elseif ( is_front_page() ) {
		$desc = 'MR ZAIB Luxury Perfumes: long lasting perfumes for men, women and unisex in Pakistan. Shop Kahn-vahl and Arvon with Cash on Delivery all over Pakistan.';
		$keys = 'MR ZAIB, MR ZAIB perfumes, luxury perfumes Pakistan, long lasting perfume, perfume for men, perfume for women, unisex perfume, Kahn-vahl perfume, Arvon perfume, buy perfume online Pakistan';
	}
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	if ( $keys ) {
		echo '<meta name="keywords" content="' . esc_attr( $keys ) . '">' . "\n";
	}
	if ( is_singular( 'product' ) ) {
		$img = get_the_post_thumbnail_url( get_queried_object_id(), 'large' );
		echo '<meta property="og:type" content="product">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '">' . "\n";
		if ( $img ) {
			echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";
		}
	}
}, 2 );

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
	if ( (int) get_option( 'mrzaib_cat_v' ) >= 2 || ! taxonomy_exists( 'product_cat' ) ) {
		return;
	}
	update_option( 'mrzaib_cat_v', 2, false );
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

