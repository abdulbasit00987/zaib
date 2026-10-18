<?php
/**
 * Mobile navigation (<=768px) modelled on the reference store's header drawer:
 * promo cards strip, horizontal tab bar (one tab per top-level menu item),
 * Shop tab = category list with icons + accordions, category tabs = "Explore" link
 * + mini product cards, link tabs = plain list, then a Customer Care footer.
 *
 * @package mrzaib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalised menu tree: array of [ title, url, cat_slug|null, children[] ].
 */
function mrzaib_mnav_tree() {
	$tree      = array();
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations['primary'] ) ) {
		$items = wp_get_nav_menu_items( $locations['primary'] );
		if ( $items ) {
			$by_parent = array();
			foreach ( $items as $it ) {
				$by_parent[ (int) $it->menu_item_parent ][] = $it;
			}
			$build = function ( $parent ) use ( &$build, $by_parent ) {
				$out = array();
				if ( empty( $by_parent[ $parent ] ) ) {
					return $out;
				}
				foreach ( $by_parent[ $parent ] as $it ) {
					$slug = null;
					if ( 'product_cat' === $it->object ) {
						$t = get_term( (int) $it->object_id, 'product_cat' );
						if ( $t && ! is_wp_error( $t ) ) {
							$slug = $t->slug;
						}
					}
					$out[] = array( $it->title, $it->url, $slug, $build( (int) $it->ID ) );
				}
				return $out;
			};
			$tree = $build( 0 );
		}
	}
	if ( ! $tree ) {
		foreach ( mrzaib_menu_structure() as $top ) {
			$kids = array();
			foreach ( (array) $top[2] as $c ) {
				$kids[] = array( $c[0], mrzaib_resolve_link( $c[1] ), null, array() );
			}
			$is_cat = 'shop' !== $top[1] && 0 !== strpos( $top[1], 'page:' );
			$tree[] = array( $top[0], mrzaib_resolve_link( $top[1] ), $is_cat ? $top[1] : null, $kids );
		}
	}
	return $tree;
}

/** Small icon for a category (term thumbnail, else bundled cat-{slug}.jpg). */
function mrzaib_mnav_cat_icon( $slug ) {
	if ( taxonomy_exists( 'product_cat' ) ) {
		$t = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $t ) {
			$tid = (int) get_term_meta( $t->term_id, 'thumbnail_id', true );
			if ( $tid ) {
				$u = wp_get_attachment_image_url( $tid, 'thumbnail' );
				if ( $u ) {
					return $u;
				}
			}
		}
	}
	if ( file_exists( MRZAIB_DIR . '/assets/images/cat-' . $slug . '.jpg' ) ) {
		return mrzaib_img( 'cat-' . $slug . '.jpg' );
	}
	return '';
}

/** Child categories of a product category, as submenu entries. */
function mrzaib_mnav_cat_children( $slug ) {
	$out = array();
	if ( ! $slug || ! taxonomy_exists( 'product_cat' ) ) {
		return $out;
	}
	$t = get_term_by( 'slug', $slug, 'product_cat' );
	if ( ! $t ) {
		return $out;
	}
	$kids = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => $t->term_id, 'hide_empty' => false ) );
	if ( ! is_wp_error( $kids ) ) {
		foreach ( $kids as $k ) {
			$out[] = array( $k->name, get_term_link( $k ), $k->slug, array() );
		}
	}
	return $out;
}

/** One row of the drawer list (with accordion when it has children). */
function mrzaib_mnav_row( $title, $url, $icon, $children ) {
	$has = ! empty( $children );
	echo '<li class="drawer-menu-item"><div class="drawer-menu-row"' . ( $has ? ' data-acc' : '' ) . '>';
	echo '<a href="' . esc_url( $url ) . '" class="drawer-menu-link"><span class="link-left-content">';
	if ( $icon ) {
		echo '<span class="menu-item-icon"><img src="' . esc_url( $icon ) . '" alt="" width="22" height="22" loading="lazy"></span>';
	}
	echo '<span class="menu-link-text">' . esc_html( $title ) . '</span></span></a>';
	if ( $has ) {
		echo '<span class="accordion-arrow-wrapper"><span class="arrow-icon">&rsaquo;</span></span>';
	}
	echo '</div>';
	if ( $has ) {
		echo '<div class="accordion-wrapper"><div class="accordion-inner"><ul class="drawer-submenu-list">';
		foreach ( $children as $c ) {
			echo '<li><a href="' . esc_url( $c[1] ) . '" class="drawer-submenu-link">' . esc_html( $c[0] ) . '</a></li>';
		}
		echo '</ul></div></div>';
	}
	echo '</li>';
}

/** Mini product cards for a category tab. */
function mrzaib_mnav_products( $slug ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return;
	}
	$args = array( 'limit' => 4, 'status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' );
	if ( $slug ) {
		$args['category'] = array( $slug );
	}
	$products = wc_get_products( $args );
	if ( ! $products ) {
		echo '<p class="mnav-empty">' . esc_html__( 'New arrivals coming soon.', 'mrzaib' ) . '</p>';
		return;
	}
	echo '<div class="drawer-products-scroll">';
	foreach ( $products as $p ) {
		$img = wp_get_attachment_image_url( $p->get_image_id(), 'woocommerce_thumbnail' );
		if ( ! $img && function_exists( 'wc_placeholder_img_src' ) ) {
			$img = wc_placeholder_img_src();
		}
		echo '<a href="' . esc_url( $p->get_permalink() ) . '" class="mini-product-card"><div class="mini-card-img-wrapper">';
		echo '<img src="' . esc_url( $img ) . '" alt="' . esc_attr( $p->get_name() ) . '" loading="lazy" width="100" height="100">';
		if ( $p->is_on_sale() ) {
			echo '<span class="mini-badge">' . esc_html__( 'SALE', 'mrzaib' ) . '</span>';
		}
		echo '</div><div class="mini-card-info"><span class="mini-card-title">' . esc_html( $p->get_name() ) . '</span><div class="mini-card-price">';
		$cur = $p->get_price();
		$reg = $p->get_regular_price();
		if ( $p->is_type( 'variable' ) ) {
			$cur = $p->get_variation_price( 'min' );
			$reg = $p->get_variation_regular_price( 'min' );
		}
		echo '<span class="price-current">' . wp_kses_post( wc_price( $cur ) ) . '</span>';
		if ( $p->is_on_sale() && $reg && $reg > $cur ) {
			echo '<span class="price-compare">' . wp_kses_post( wc_price( $reg ) ) . '</span>';
		}
		echo '</div></div></a>';
	}
	echo '</div>';
}

/** Full mobile nav markup. */
function mrzaib_mobile_nav() {
	$tree = mrzaib_mnav_tree();

	// Tabs: first is always "Shop" (category list); then every top-level item that isn't the shop link.
	$shop_url  = mrzaib_shop_link();
	$cat_items = array();
	$tabs      = array();
	foreach ( $tree as $t ) {
		if ( $t[2] ) {
			$cat_items[] = $t;
		}
		if ( untrailingslashit( $t[1] ) === untrailingslashit( $shop_url ) && ! $t[2] ) {
			// Shop item: its children (if any) join the Shop tab list.
			foreach ( $t[3] as $c ) {
				$cat_items[] = $c;
			}
			continue;
		}
		$tabs[] = $t;
	}
	$seen = array();
	$cat_items = array_values( array_filter( $cat_items, function ( $c ) use ( &$seen, $shop_url ) {
		$k = untrailingslashit( $c[1] );
		if ( isset( $seen[ $k ] ) || untrailingslashit( $shop_url ) === $k ) {
			return false;
		}
		$seen[ $k ] = true;
		return true;
	} ) );
	if ( ! $cat_items && taxonomy_exists( 'product_cat' ) ) {
		$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => 0, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$cat_items[] = array( $term->name, get_term_link( $term ), $term->slug, array() );
			}
		}
	}

	$promos = array(
		array( __( 'Shop All', 'mrzaib' ), $shop_url, mrzaib_img( 'mnav-shop-all.jpg' ) ),
	);
	foreach ( array_slice( $cat_items, 0, 2 ) as $c ) {
		$ic = '';
		if ( $c[2] && file_exists( MRZAIB_DIR . '/assets/images/mnav-' . $c[2] . '.jpg' ) ) {
			$ic = mrzaib_img( 'mnav-' . $c[2] . '.jpg' );
		} elseif ( $c[2] ) {
			$ic = mrzaib_mnav_cat_icon( $c[2] );
		}
		if ( $ic ) {
			$promos[] = array( $c[0], $c[1], $ic );
		}
	}
	?>
	<div class="mobile-nav-wrapper">
		<div class="drawer-top-promos-scroll"><div class="drawer-top-promos">
			<?php foreach ( $promos as $pr ) : ?>
				<a href="<?php echo esc_url( $pr[1] ); ?>" class="promo-card"><img src="<?php echo esc_url( $pr[2] ); ?>" alt="<?php echo esc_attr( $pr[0] ); ?>" class="promo-bg-img" loading="lazy"><span class="promo-overlay"><span class="promo-card-title"><?php echo esc_html( $pr[0] ); ?></span></span></a>
			<?php endforeach; ?>
		</div></div>

		<div class="drawer-tabs-scroll-container"><div class="drawer-tabs-header" role="tablist">
			<button type="button" class="drawer-tab-btn active" data-tab="mz-tab-0"><span><?php esc_html_e( 'Shop', 'mrzaib' ); ?></span></button>
			<?php foreach ( $tabs as $i => $t ) : ?>
				<button type="button" class="drawer-tab-btn" data-tab="mz-tab-<?php echo (int) $i + 1; ?>"><span><?php echo esc_html( $t[0] ); ?></span></button>
			<?php endforeach; ?>
		</div></div>

		<div class="drawer-tabs-content">
			<div id="mz-tab-0" class="tab-panel active"><ul class="drawer-menu-list">
				<?php
				foreach ( $cat_items as $c ) {
					mrzaib_mnav_row( $c[0], $c[1], $c[2] ? mrzaib_mnav_cat_icon( $c[2] ) : '', $c[3] ? $c[3] : mrzaib_mnav_cat_children( $c[2] ) );
				}
				mrzaib_mnav_row( __( 'Shop All', 'mrzaib' ), $shop_url, '', array() );
				?>
			</ul></div>
			<?php foreach ( $tabs as $i => $t ) : ?>
				<div id="mz-tab-<?php echo (int) $i + 1; ?>" class="tab-panel">
					<?php if ( $t[2] || ! $t[3] ) : ?>
						<div class="empty-tab-msg">
							<div class="direct-link-header"><a href="<?php echo esc_url( $t[1] ); ?>" class="drawer-direct-link"><?php printf( esc_html__( 'Explore %s Collection', 'mrzaib' ), esc_html( $t[0] ) ); ?> &rarr;</a></div>
							<?php if ( $t[2] ) : ?>
								<div class="drawer-collection-preview">
									<div class="preview-header"><span class="preview-title"><?php echo esc_html( $t[0] ); ?></span></div>
									<?php mrzaib_mnav_products( $t[2] ); ?>
								</div>
							<?php endif; ?>
						</div>
					<?php else : ?>
						<ul class="drawer-menu-list">
							<?php
							foreach ( $t[3] as $c ) {
								mrzaib_mnav_row( $c[0], $c[1], '', $c[3] );
							}
							?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="drawer-footer-care">
			<div class="care-header">
				<span class="care-title"><?php esc_html_e( 'CUSTOMER CARE', 'mrzaib' ); ?></span>
				<div class="social-icons">
					<?php if ( mrzaib_opt( 'whatsapp' ) ) : ?><a href="https://wa.me/<?php echo esc_attr( preg_replace( '/\D/', '', mrzaib_opt( 'whatsapp' ) ) ); ?>" target="_blank" rel="noopener" aria-label="WhatsApp" class="icon-circle"><?php echo mrzaib_icon( 'whatsapp' ); // phpcs:ignore ?></a><?php endif; ?>
					<?php if ( mrzaib_opt( 'phone' ) ) : ?><a href="tel:<?php echo esc_attr( mrzaib_opt( 'phone' ) ); ?>" aria-label="Call Us" class="icon-circle"><?php echo mrzaib_icon( 'phone' ); // phpcs:ignore ?></a><?php endif; ?>
					<?php if ( mrzaib_opt( 'email' ) ) : ?><a href="mailto:<?php echo esc_attr( mrzaib_opt( 'email' ) ); ?>" aria-label="Email Us" class="icon-circle"><?php echo mrzaib_icon( 'mail' ); // phpcs:ignore ?></a><?php endif; ?>
				</div>
			</div>
			<div class="quick-action-combined">
				<a href="<?php echo esc_url( mrzaib_page_link( 'contact-us' ) ); ?>" class="action-btn"><?php echo mrzaib_icon( 'mail' ); // phpcs:ignore ?><span><?php esc_html_e( 'Contact', 'mrzaib' ); ?></span></a>
				<a href="<?php echo esc_url( mrzaib_page_link( 'track-order' ) ); ?>" class="action-btn border-left-right"><?php echo mrzaib_icon( 'truck' ); // phpcs:ignore ?><span><?php esc_html_e( 'Tracking', 'mrzaib' ); ?></span></a>
				<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>" class="action-btn"><?php echo mrzaib_icon( 'user' ); // phpcs:ignore ?><span><?php esc_html_e( 'Account', 'mrzaib' ); ?></span></a>
			</div>
		</div>
	</div>
	<?php
}
