<?php
/**
 * MR ZAIB blog: one article per perfume, added by the theme
 * (Posts > MR ZAIB Blogs, and automatically once after the update).
 *
 * @package MR_ZAIB
 */

defined( 'ABSPATH' ) || exit;

define( 'MRZAIB_BLOG_V', 1 );

/** Fragrance-notes block used inside the articles. */
function mrzaib_blog_notes_html( $top, $heart, $base ) {
	$out = '<div class="mzb-notes">';
	foreach ( array( array( 'Top Notes', 'The first impression', $top ), array( 'Heart Notes', 'The character', $heart ), array( 'Base Notes', 'The lasting trail', $base ) ) as $n ) {
		$out .= '<div class="mzb-note"><span class="mzb-note-k">' . $n[0] . '</span><strong>' . $n[2] . '</strong><em>' . $n[1] . '</em></div>';
	}
	return $out . '</div>';
}

/** FAQ block (native <details>, no JS). */
function mrzaib_blog_faq_html( $faqs ) {
	$out = '<h2>Frequently Asked Questions</h2><div class="mzb-faq">';
	foreach ( $faqs as $q => $a ) {
		$out .= '<details><summary>' . $q . '</summary><p>' . $a . '</p></details>';
	}
	return $out . '</div>';
}

/** Shared "make it last" tips, with one tip that belongs to the perfume. */
function mrzaib_blog_tips_html( $name, $extra ) {
	return '<h2>How to Make ' . $name . ' Last Longer</h2><ul>'
		. '<li><strong>Spray on moisturised skin.</strong> Perfume holds better on hydrated skin, so apply after a shower and an unscented lotion.</li>'
		. '<li><strong>Use pulse points.</strong> Neck, wrists, chest and behind the ears are warm spots that lift the scent through the day.</li>'
		. '<li><strong>Do not rub your wrists.</strong> Rubbing breaks the top notes and makes the opening fade faster.</li>'
		. '<li><strong>A light mist on clothes.</strong> Fabric holds fragrance for hours. Spray from about 15 cm and test on a hidden spot first.</li>'
		. '<li>' . $extra . '</li>'
		. '<li><strong>Store it right.</strong> Keep the bottle in its box, away from sunlight, heat and the bathroom.</li></ul>';
}

/** Shop button inside an article; the link is made when the page loads, so it always points to this site. */
function mrzaib_blog_cta_html( $slug, $name ) {
	return '[mrzaib_shop_button product="' . esc_attr( $slug ) . '" label="Shop ' . esc_attr( $name ) . ' now"]';
}

add_shortcode( 'mrzaib_shop_button', function ( $a ) {
	$a    = shortcode_atts( array( 'product' => '', 'label' => 'Shop now' ), $a );
	$p    = $a['product'] ? get_page_by_path( sanitize_title( $a['product'] ), OBJECT, 'product' ) : null;
	$link = $p && 'publish' === $p->post_status ? get_permalink( $p ) : home_url( '/shop/' );
	return '<p class="mzb-cta"><a class="btn" href="' . esc_url( $link ) . '">' . esc_html( $a['label'] ) . '</a></p>';
} );

/** The articles. 'product' = product slug the article is about. */
function mrzaib_blog_posts() {
	return array(
		array(
			'slug'    => 'kahn-vahl-fresh-aquatic-perfume-for-men',
			'title'   => 'Kahn-vahl: The Fresh Aquatic Perfume Every Man Needs for Pakistani Summers',
			'product' => 'kahn-vahl-perfume',
			'name'    => 'Kahn-vahl',
			'image'   => 'blog-kahn-vahl.jpg',
			'alt'     => 'Kahn-vahl fresh aquatic perfume for men by MR ZAIB blue bottle',
			'excerpt' => 'Cool coconut water, polished jasmine and iris, and a warm vanilla, sandalwood and amber base. Here is why Kahn-vahl is the easiest signature scent for hot days, the office and evenings out.',
			'tags'    => array( 'Kahn-vahl', 'Perfume for Men', 'Summer Perfume', 'Aquatic Perfume', 'Office Perfume' ),
			'seo'     => array( 'Kahn-vahl Perfume for Men: Notes, Longevity & When to Wear | MR ZAIB', 'Kahn-vahl by MR ZAIB is a fresh aquatic perfume for men with coconut water, jasmine, iris, vanilla, sandalwood and amber. Notes, occasions and tips.', 'Kahn-vahl perfume' ),
			'body'    => array(
				'intro' => '<p>When the temperature in Karachi or Lahore climbs past 40, heavy perfumes start to feel like a wool coat. That is exactly the gap <strong>Kahn-vahl by MR ZAIB</strong> was made to fill: a <strong>fresh aquatic perfume for men</strong> that stays clean and cool at noon, yet still feels refined when you walk into a dinner at night.</p><p>It comes in a deep blue 50ml bottle with a gold crown cap, and the scent matches the look: calm, confident and quietly luxurious.</p>',
				'notes' => array( 'Coconut Water', 'Jasmine, Iris', 'Vanilla, Sandalwood, Amber' ),
				'story' => '<h2>What Kahn-vahl Smells Like</h2><p>The first spray is pure refreshment. <strong>Coconut water</strong> is not the sweet, sun-cream coconut you might expect. It is light, watery and slightly milky, like the first sip from a fresh green coconut on a hot afternoon.</p><p>Within minutes the heart opens. <strong>Jasmine</strong> adds a soft white-floral glow, and <strong>iris</strong> gives a powdery, polished finish that makes the perfume feel expensive rather than sporty. This is where Kahn-vahl moves from "fresh" to "classy".</p><p>The dry-down is where it earns its place as a long lasting perfume. <strong>Sandalwood</strong> brings creamy wood, <strong>vanilla</strong> adds a gentle sweetness, and <strong>amber</strong> wraps everything in warmth. Hours later, people near you notice a clean, warm trail rather than a loud cloud.</p>',
				'wear'  => '<h2>When to Wear Kahn-vahl</h2><ul><li><strong>Office and university:</strong> fresh, non-intrusive and safe in closed rooms.</li><li><strong>Summer days:</strong> the aquatic opening feels cooling in humid weather.</li><li><strong>Casual dinners and date nights:</strong> the vanilla and amber base gives it a warm, close-to-skin finish.</li><li><strong>Gifting:</strong> the blue crystal-cut bottle looks premium on any shelf, a safe gift for a brother, husband or friend.</li></ul>',
				'tip'   => '<strong>Two to three sprays are enough for the office.</strong> Save a fourth spray on the chest for evenings, where the warm base can shine.',
				'faq'   => array(
					'Is Kahn-vahl a summer perfume?' => 'Yes. Its coconut-water opening is fresh and watery, which makes it ideal for hot Pakistani summers, while the amber base keeps it wearable all year.',
					'Is Kahn-vahl good for the office?' => 'It is one of the safest office scents in the MR ZAIB range: clean, polished and never overpowering in closed rooms.',
					'How long does Kahn-vahl last?' => 'Longevity depends on skin and weather. Applied on moisturised pulse points, the warm sandalwood, vanilla and amber base stays with you for many hours.',
				),
				'end'   => '<h2>The Verdict</h2><p>If you want one bottle that handles heat, meetings and evenings without a second thought, <strong>Kahn-vahl</strong> is the easiest choice in the MR ZAIB collection. Fresh first, elegant always, and warm enough to be remembered.</p>',
			),
		),
		array(
			'slug'    => 'arvon-unisex-perfume-for-evenings-and-weddings',
			'title'   => 'Arvon: A Bold Unisex Perfume for Evenings, Parties and Wedding Season',
			'product' => 'arvon-perfume',
			'name'    => 'Arvon',
			'image'   => 'blog-arvon.jpg',
			'alt'     => 'Arvon unisex perfume for men and women by MR ZAIB black bottle',
			'excerpt' => 'Juicy pineapple and spice, a bouquet of rose, jasmine and orchid, and a deep cacao and vanilla base. Arvon is the MR ZAIB scent made to be noticed, by men and women alike.',
			'tags'    => array( 'Arvon', 'Unisex Perfume', 'Wedding Perfume', 'Party Perfume', 'Evening Perfume' ),
			'seo'     => array( 'Arvon Unisex Perfume: Notes, Occasions & Tips | MR ZAIB Blog', 'Arvon by MR ZAIB is a bold unisex perfume with pineapple, spice, rose, jasmine, cacao, vanilla and amber. Perfect for evenings, parties and weddings.', 'Arvon unisex perfume' ),
			'body'    => array(
				'intro' => '<p>Some perfumes whisper. <strong>Arvon</strong> makes an entrance. Created by <strong>MR ZAIB</strong> as a <strong>unisex perfume for men and women</strong>, it is rich, fruity, spicy and warm, built for nights when you want people to ask, "What are you wearing?"</p><p>From mehndi functions to late dinners and winter parties, Arvon is the bottle to reach for when the occasion deserves more than a simple fresh scent.</p>',
				'notes' => array( 'Pineapple, Nutmeg, Cinnamon, Red Apple, Plum, Pepper, Cassia, Bitter Orange', 'Magnolia, Orchid, Lily-of-the-Valley, Jasmine, Moroccan Rose, Iris', 'Cacao, Vanilla, White Musk, Amber, Sandalwood, Cedar' ),
				'story' => '<h2>What Arvon Smells Like</h2><p>The opening is a burst of energy. Juicy <strong>pineapple, red apple and plum</strong> meet warm <strong>cinnamon, nutmeg and pepper</strong>, with a bright twist of <strong>bitter orange</strong>. It is fruity without being childish and spicy without being harsh.</p><p>As it settles, a full floral heart appears: <strong>magnolia, orchid, jasmine and Moroccan rose</strong>, smoothed by powdery <strong>iris</strong>. This is the part that makes Arvon truly unisex. It is elegant on a woman and adds a refined, modern edge on a man.</p><p>The base is pure comfort and luxury: dark <strong>cacao</strong>, creamy <strong>vanilla</strong>, soft <strong>white musk</strong>, glowing <strong>amber</strong> and the dry woods of <strong>sandalwood and cedar</strong>. It is a gourmand, woody trail that lingers on clothes long after the night ends.</p>',
				'wear'  => '<h2>When to Wear Arvon</h2><ul><li><strong>Weddings and functions:</strong> rich enough to stand out in a crowded hall.</li><li><strong>Evening parties and dinners:</strong> the cacao and vanilla base feels warm and inviting.</li><li><strong>Winter:</strong> spice and amber bloom beautifully in cool weather.</li><li><strong>Couples:</strong> one bottle both partners can share.</li></ul>',
				'tip'   => '<strong>Go easy in the daytime.</strong> Arvon is powerful. One spray on the neck and one on the chest is plenty for the day; add a third for evening events.',
				'faq'   => array(
					'Is Arvon for men or women?' => 'Both. Arvon is a unisex perfume: the fruity spice and woods suit men, and the rose, jasmine and orchid heart suits women.',
					'Is Arvon good for weddings?' => 'Yes. Its rich spicy-floral opening and warm cacao, vanilla and amber base make it one of the best MR ZAIB choices for weddings and evening events.',
					'Can I wear Arvon in summer?' => 'You can, especially at night. In hot daytime weather, use fewer sprays because the warm base becomes stronger in heat.',
				),
				'end'   => '<h2>The Verdict</h2><p><strong>Arvon</strong> is for people who like their perfume to be part of the outfit. Fruity, spicy, floral and deeply warm, it is the MR ZAIB scent for nights you want to remember.</p>',
			),
		),
		array(
			'slug'    => 'zay-ab-oriental-frankincense-perfume-for-men',
			'title'   => 'Zay-ab: An Oriental Frankincense Perfume for Men Who Love Depth',
			'product' => 'zay-ab-perfume',
			'name'    => 'Zay-ab',
			'image'   => 'blog-zay-ab.jpg',
			'alt'     => 'Zay-ab oriental perfume for men by MR ZAIB red bottle',
			'excerpt' => 'Sparkling aldehydes and rose open into smoky frankincense and labdanum, then settle on musk, benzoin and ambergris. Zay-ab is MR ZAIB\'s oriental signature for men.',
			'tags'    => array( 'Zay-ab', 'Oriental Perfume', 'Perfume for Men', 'Frankincense Perfume', 'Winter Perfume' ),
			'seo'     => array( 'Zay-ab Oriental Perfume for Men: Notes & Occasions | MR ZAIB Blog', 'Zay-ab by MR ZAIB is a long lasting oriental perfume for men with aldehydes, rose, frankincense, labdanum, white musk, benzoin and ambergris.', 'Zay-ab perfume' ),
			'body'    => array(
				'intro' => '<p>Oriental perfumes have deep roots in our culture: incense at home, resins in old attar shops, warm ambers at Eid. <strong>Zay-ab by MR ZAIB</strong> takes that heritage and turns it into a modern, <strong>long lasting oriental perfume for men</strong> that feels at home in a suit as much as in a kurta.</p>',
				'notes' => array( 'Aldehydes, Lily of the Valley, Rose', 'Frankincense, Mystikal, Labdanum', 'White Musk, Benzoin, Ambergris' ),
				'story' => '<h2>What Zay-ab Smells Like</h2><p>Zay-ab opens with a bright, clean sparkle. <strong>Aldehydes</strong> give that crisp, fizzy "freshly pressed shirt" feeling, softened by <strong>lily of the valley</strong> and a touch of <strong>rose</strong>.</p><p>Then the character shows. <strong>Frankincense</strong> brings a smoky, resinous, almost spiritual warmth, and <strong>labdanum</strong> adds a rich, leathery amber depth. The <strong>mystikal</strong> accord makes the heart feel mysterious and smooth rather than heavy.</p><p>The base is soft and lasting: clean <strong>white musk</strong>, sweet balsamic <strong>benzoin</strong> and the salty, skin-like glow of <strong>ambergris</strong>. It sits close to the skin and stays there, the kind of trail that is noticed in a hug or a handshake.</p>',
				'wear'  => '<h2>When to Wear Zay-ab</h2><ul><li><strong>Jummah, Eid and family gatherings:</strong> the incense heart feels traditional and respectful.</li><li><strong>Formal evenings:</strong> refined and mature with a suit or sherwani.</li><li><strong>Autumn and winter:</strong> resins and amber shine in cool air.</li><li><strong>Office in cooler months:</strong> two sprays give a quiet, confident aura.</li></ul>',
				'tip'   => '<strong>Spray on fabric for the incense effect.</strong> A light mist on a shawl or jacket keeps the frankincense heart noticeable for the whole evening.',
				'faq'   => array(
					'What is an oriental perfume?' => 'Oriental (or amber) perfumes are built on warm notes such as resins, incense, amber, vanilla and musk. Zay-ab is an oriental scent centred on frankincense and labdanum.',
					'Is Zay-ab suitable for Eid?' => 'Yes. Its frankincense, benzoin and ambergris give it a festive, traditional warmth that suits Eid and family occasions.',
					'Is Zay-ab strong?' => 'It has a clear presence but a smooth, close-to-skin base. Two to three sprays are enough for most occasions.',
				),
				'end'   => '<h2>The Verdict</h2><p>If fresh perfumes feel too simple and heavy ouds feel too much, <strong>Zay-ab</strong> sits perfectly in between: bright at first, smoky and resinous at heart, warm and soft to the end.</p>',
			),
		),
		array(
			'slug'    => 'hoor-fruity-floral-perfume-for-women',
			'title'   => 'Hoor: A Fruity Floral Perfume for Women That Feels Like Spring',
			'product' => 'hoor-perfume',
			'name'    => 'Hoor',
			'image'   => 'blog-hoor.jpg',
			'alt'     => 'Hoor floral perfume for women by MR ZAIB pink bottle',
			'excerpt' => 'Black currant, pineapple and red apple open into a bouquet of rose, freesia and peony, resting on soft musk, iris and sandalwood. Meet Hoor, the feminine favourite of MR ZAIB.',
			'tags'    => array( 'Hoor', 'Perfume for Women', 'Floral Perfume', 'Fruity Perfume', 'Gift for Her' ),
			'seo'     => array( 'Hoor Floral Perfume for Women: Notes & Gift Guide | MR ZAIB Blog', 'Hoor by MR ZAIB is a long lasting fruity floral perfume for women with black currant, pineapple, rose, freesia, peony, musk, iris and sandalwood.', 'Hoor perfume for women' ),
			'body'    => array(
				'intro' => '<p><strong>Hoor</strong> means a heavenly beauty, and MR ZAIB designed this <strong>fruity floral perfume for women</strong> to feel exactly that way: bright, graceful and effortlessly pretty. In its soft pink bottle with a gold collar, it is the kind of perfume that becomes a daily habit.</p>',
				'notes' => array( 'Black Currant, Pineapple, Orange, Red Apple', 'Rose, Freesia, Peony', 'Musk, Iris, Sandalwood' ),
				'story' => '<h2>What Hoor Smells Like</h2><p>The opening is juicy and joyful. Tart <strong>black currant</strong>, sweet <strong>pineapple</strong>, sparkling <strong>orange</strong> and crisp <strong>red apple</strong> create a fresh fruit-basket brightness that lifts the mood instantly.</p><p>The heart is a romantic bouquet: velvety <strong>rose</strong>, airy <strong>freesia</strong> and lush <strong>peony</strong>. It is feminine and elegant without ever feeling old-fashioned or heavy.</p><p>Finally, Hoor settles into a soft, skin-like base of clean <strong>musk</strong>, powdery <strong>iris</strong> and creamy <strong>sandalwood</strong>. The result is a gentle, lasting trail that feels like your own skin, only better.</p>',
				'wear'  => '<h2>When to Wear Hoor</h2><ul><li><strong>Everyday wear:</strong> fresh and light enough for university, work and errands.</li><li><strong>Brunches and day events:</strong> the fruity opening feels cheerful and bright.</li><li><strong>Bridal showers and dholkis:</strong> pretty, feminine and crowd-pleasing.</li><li><strong>Gifting:</strong> a safe, loved gift for a sister, wife, mother or friend.</li></ul>',
				'tip'   => '<strong>Try a hair mist.</strong> Spray once into the air and walk through it, or lightly onto a hairbrush. Hair carries floral notes beautifully as you move.',
				'faq'   => array(
					'Is Hoor a sweet perfume?' => 'Hoor is fruity and floral rather than sugary. The black currant and apple opening is fresh, and the base is soft musk and sandalwood.',
					'Is Hoor a good gift for women?' => 'Yes. Its fruity floral character is easy to love, and the pink bottle with gold details looks beautiful as a gift.',
					'Can I wear Hoor every day?' => 'Absolutely. Hoor is light, fresh and polite in closed spaces, which makes it an ideal everyday perfume.',
				),
				'end'   => '<h2>The Verdict</h2><p><strong>Hoor</strong> is spring in a bottle: juicy fruits, a romantic bouquet and a soft, clean finish. If you are looking for a feminine signature scent that never feels too much, start here.</p>',
			),
		),
		array(
			'slug'    => 'kireza-fruity-marine-perfume-for-women',
			'title'   => 'Kireza: The Fruity Marine Perfume for Women Who Love Fresh, Clean Scents',
			'product' => 'kireza-perfume',
			'name'    => 'Kireza',
			'image'   => 'blog-kireza.jpg',
			'alt'     => 'Kireza fruity marine perfume for women by MR ZAIB',
			'excerpt' => 'Raspberry and rose meet a cool sea breeze of seaweed and calone, finishing on musk, ambroxan and cedar. Kireza is fresh, fruity and modern, made for women who like a clean trail.',
			'tags'    => array( 'Kireza', 'Perfume for Women', 'Marine Perfume', 'Fresh Perfume', 'Summer Perfume' ),
			'seo'     => array( 'Kireza Fruity Marine Perfume for Women | MR ZAIB Blog', 'Kireza by MR ZAIB is a long lasting fruity marine perfume for women with raspberry, rose, black currant, seaweed, calone, musk, ambroxan and cedar.', 'Kireza perfume' ),
			'body'    => array(
				'intro' => '<p>Not every woman wants a heavy floral. Some prefer a scent that feels like a fresh breeze at Clifton beach with a basket of berries in hand. That is <strong>Kireza by MR ZAIB</strong>, a <strong>fruity marine perfume for women</strong> that is clean, modern and surprisingly long lasting.</p>',
				'notes' => array( 'Raspberry, Rose, Black Currant', 'Seaweed, Calone, Hedione', 'Musk, Ambroxan, Cedar' ),
				'story' => '<h2>What Kireza Smells Like</h2><p>Kireza opens bright and playful: sweet-tart <strong>raspberry</strong>, juicy <strong>black currant</strong> and a fresh <strong>rose</strong>. It is fruity, but the rose keeps it elegant.</p><p>Then the sea rolls in. <strong>Seaweed</strong> and <strong>calone</strong> create a cool, salty, ocean-air freshness, while <strong>hedione</strong> adds a radiant, jasmine-like glow that makes the whole scent feel airy and luminous.</p><p>The base is modern and clean: soft <strong>musk</strong>, the mineral, skin-hugging warmth of <strong>ambroxan</strong> and dry <strong>cedar</strong>. It leaves a fresh, polished trail that lasts well beyond the first few hours.</p>',
				'wear'  => '<h2>When to Wear Kireza</h2><ul><li><strong>Hot, humid days:</strong> the marine heart feels instantly cooling.</li><li><strong>Office and classes:</strong> fresh and clean, never overpowering.</li><li><strong>Beach trips and travel:</strong> it matches sea air perfectly.</li><li><strong>Gym and active days:</strong> a light spray afterwards feels refreshing.</li></ul>',
				'tip'   => '<strong>Layer it lightly.</strong> Marine notes are airy, so a spray on the neck plus one on a scarf keeps the freshness around you longer.',
				'faq'   => array(
					'What is a marine perfume?' => 'Marine (aquatic) perfumes use notes such as calone and seaweed to recreate sea air and water. Kireza combines them with raspberry and rose for a fruity, feminine twist.',
					'Is Kireza good for summer?' => 'Yes. Its seaweed and calone heart is cool and refreshing, ideal for Pakistani summers.',
					'Is Kireza only for women?' => 'It is designed for women, but its clean marine and cedar character can also be enjoyed by anyone who loves fresh scents.',
				),
				'end'   => '<h2>The Verdict</h2><p><strong>Kireza</strong> is fresh, fruity and quietly confident. If you love the feeling of clean sea air and want a perfume that still turns heads, Kireza is your bottle.</p>',
			),
		),
		array(
			'slug'    => 'oud-x-fresh-oud-perfume-for-men-and-women',
			'title'   => 'OUD X: A Fresh Take on Oud for Men and Women',
			'product' => 'oud-x-perfume',
			'name'    => 'OUD X',
			'image'   => 'blog-oud-x.jpg',
			'alt'     => 'OUD X long lasting unisex oud perfume by MR ZAIB',
			'excerpt' => 'Oud does not have to be heavy. OUD X opens with aquatic bergamot and citrus, warms up with cinnamon and apple, and dries down to ambergris, musk and rich woods.',
			'tags'    => array( 'OUD X', 'Oud Perfume', 'Unisex Perfume', 'Woody Perfume', 'Long Lasting Perfume' ),
			'seo'     => array( 'OUD X Unisex Oud Perfume: A Fresh Woody Oud | MR ZAIB Blog', 'OUD X by MR ZAIB is a long lasting unisex oud perfume with aquatic notes, bergamot, citrus, cinnamon, apple, ambergris, musk and woody notes.', 'OUD X oud perfume' ),
			'body'    => array(
				'intro' => '<p>For many of us, oud means something strong, smoky and best saved for special nights. <strong>OUD X by MR ZAIB</strong> changes that. It is a <strong>long lasting unisex oud perfume</strong> that starts fresh, turns warm and ends deep and woody, so you can wear oud any day you like.</p>',
				'notes' => array( 'Aquatic Notes, Bergamot, Citrus', 'Cinnamon, Apple', 'Ambergris, Musk, Woody Notes' ),
				'story' => '<h2>What OUD X Smells Like</h2><p>The surprise is the opening. Instead of smoke, you get a clean splash of <strong>aquatic notes</strong>, zesty <strong>bergamot</strong> and bright <strong>citrus</strong>. It feels modern and energetic.</p><p>The heart adds warmth and a little sweetness: <strong>cinnamon</strong> and crisp <strong>apple</strong> blend into a cosy, spiced accord that bridges the fresh top and the deep base.</p><p>Then the oud character arrives. Rich <strong>woody notes</strong>, smooth <strong>musk</strong> and salty-warm <strong>ambergris</strong> create a luxurious, lasting trail. It is the depth of oud with the wearability of a fresh fragrance.</p>',
				'wear'  => '<h2>When to Wear OUD X</h2><ul><li><strong>Everyday oud lovers:</strong> fresh enough for daytime, deep enough for night.</li><li><strong>Evenings and dinners:</strong> the woody base feels rich and elegant.</li><li><strong>Weddings and Eid:</strong> a modern oud that suits traditional occasions.</li><li><strong>Men and women:</strong> fully unisex, easy to share.</li></ul>',
				'tip'   => '<strong>Let it settle before judging.</strong> The fresh opening fades into the woody oud base after about 20 minutes, so give it time on skin.',
				'faq'   => array(
					'Is OUD X a heavy oud?' => 'No. OUD X is a fresh woody oud: it opens with aquatic and citrus notes before settling into warm woods, musk and ambergris.',
					'Is OUD X for men or women?' => 'Both. OUD X is a unisex perfume that suits men and women equally.',
					'Can I wear OUD X in summer?' => 'Yes. Its aquatic, bergamot and citrus opening makes it one of the easiest ouds to wear in warm weather.',
				),
				'end'   => '<h2>The Verdict</h2><p><strong>OUD X</strong> is oud for today: fresh at first, spiced in the middle and richly woody at the end. If you have always wanted to wear oud but found it too heavy, this is the one to try.</p>',
			),
		),
		array(
			'slug'    => 'raaz-spicy-tobacco-perfume-for-men',
			'title'   => 'RAAZ: A Spicy Tobacco Perfume for Men With a Secret to Keep',
			'product' => 'raaz-perfume',
			'name'    => 'RAAZ',
			'image'   => 'blog-raaz.jpg',
			'alt'     => 'RAAZ spicy tobacco perfume for men by MR ZAIB white bottle',
			'excerpt' => 'Red apple and mandarin, a spicy heart of pepper and sage, and a dark, smooth base of tobacco, benzoin, woods and patchouli. RAAZ is the mysterious one in the MR ZAIB family.',
			'tags'    => array( 'RAAZ', 'Perfume for Men', 'Tobacco Perfume', 'Spicy Perfume', 'Evening Perfume' ),
			'seo'     => array( 'RAAZ Spicy Tobacco Perfume for Men | MR ZAIB Blog', 'RAAZ by MR ZAIB is a long lasting spicy tobacco perfume for men with red apple, mandarin, pepper, sage, tobacco, benzoin, woody notes and patchouli.', 'RAAZ perfume for men' ),
			'body'    => array(
				'intro' => '<p><strong>Raaz</strong> means a secret, and this perfume wears its name well. <strong>RAAZ by MR ZAIB</strong> is a <strong>spicy tobacco perfume for men</strong>: smooth, warm, a little mysterious and very hard to forget. In its ivory bottle with a gold cap, it looks clean and calm, but the scent inside has real depth.</p>',
				'notes' => array( 'Red Apple, Mandarin Orange', 'Pepper, Sage', 'Tobacco, Benzoin, Woodsy Notes, Patchouli' ),
				'story' => '<h2>What RAAZ Smells Like</h2><p>RAAZ begins fresh and juicy with crisp <strong>red apple</strong> and sweet <strong>mandarin orange</strong>, a bright first impression that keeps it from feeling heavy.</p><p>The heart turns masculine and aromatic. <strong>Pepper</strong> adds a lively spice, and <strong>sage</strong> brings a herbal, slightly smoky freshness that feels grown-up and refined.</p><p>The base is the secret. Smooth, slightly sweet <strong>tobacco</strong>, balsamic <strong>benzoin</strong>, dry <strong>woody notes</strong> and earthy <strong>patchouli</strong> create a warm, rich trail that lasts for hours and gets better as the evening goes on.</p>',
				'wear'  => '<h2>When to Wear RAAZ</h2><ul><li><strong>Evenings out:</strong> the tobacco and benzoin base is warm and magnetic.</li><li><strong>Autumn and winter:</strong> spice and tobacco bloom beautifully in cool air.</li><li><strong>Formal events:</strong> mature and polished with a blazer or waistcoat.</li><li><strong>Date nights:</strong> close, warm and memorable.</li></ul>',
				'tip'   => '<strong>Spray on the chest under your shirt.</strong> Body heat slowly releases the tobacco base, so the scent keeps unfolding all night.',
				'faq'   => array(
					'Does RAAZ smell like cigarettes?' => 'No. Tobacco in perfume smells like sweet, dried tobacco leaves, honeyed and warm, not like smoke. RAAZ balances it with apple, mandarin and woods.',
					'Is RAAZ good for the office?' => 'It works in cooler months with one or two sprays, but it shines most in the evening and at events.',
					'How long does RAAZ last?' => 'Its tobacco, benzoin, woods and patchouli base is long lasting on moisturised skin and holds even longer on clothes.',
				),
				'end'   => '<h2>The Verdict</h2><p><strong>RAAZ</strong> is for the man who prefers to be remembered rather than announced: fresh at first, spicy at heart and warmly mysterious to the end.</p>',
			),
		),
	);
}

/** Full HTML of one article. */
function mrzaib_blog_content( $d ) {
	$b = $d['body'];
	return $b['intro']
		. '<h2>The Fragrance Notes of ' . $d['name'] . '</h2>'
		. mrzaib_blog_notes_html( $b['notes'][0], $b['notes'][1], $b['notes'][2] )
		. $b['story']
		. $b['wear']
		. mrzaib_blog_tips_html( $d['name'], $b['tip'] )
		. mrzaib_blog_faq_html( $b['faq'] )
		. $b['end']
		. mrzaib_blog_cta_html( $d['product'], $d['name'] );
}

/** Icegram's "new post" email would fire once per article: keep it quiet while the theme adds them. */
function mrzaib_blog_mute_newsletter() {
	global $wp_filter;
	if ( empty( $wp_filter['transition_post_status'] ) ) {
		return;
	}
	foreach ( $wp_filter['transition_post_status']->callbacks as $prio => $cbs ) {
		foreach ( $cbs as $cb ) {
			$f = $cb['function'];
			if ( is_array( $f ) && is_object( $f[0] ) && 0 === strpos( get_class( $f[0] ), 'ES_' ) ) {
				remove_action( 'transition_post_status', $f, $prio );
			}
		}
	}
}

/** The "Blog" page used as the posts page (only when none is set). */
function mrzaib_blog_page() {
	$id = (int) get_option( 'page_for_posts' );
	if ( $id && get_post( $id ) ) {
		return $id;
	}
	$p  = get_page_by_path( 'blog' );
	$id = $p ? $p->ID : wp_insert_post( array( 'post_type' => 'page', 'post_title' => 'Blog', 'post_name' => 'blog', 'post_status' => 'publish' ) );
	if ( $id && ! is_wp_error( $id ) && 'page' === get_option( 'show_on_front' ) ) {
		update_option( 'page_for_posts', $id );
		update_post_meta( $id, '_yoast_wpseo_title', 'Perfume Blog: Fragrance Notes, Tips & Guides | MR ZAIB' );
		update_post_meta( $id, '_yoast_wpseo_metadesc', 'The MR ZAIB perfume blog: fragrance notes, how to wear each scent, tips to make perfume last longer and gift ideas for men and women in Pakistan.' );
	}
	return (int) $id;
}

/** Add or update the articles. $only = article slug, or '' for all. */
function mrzaib_install_blogs( $only = '' ) {
	mrzaib_blog_mute_newsletter();
	$cat = term_exists( 'Perfume Guide', 'category' );
	if ( ! $cat ) {
		$cat = wp_insert_term( 'Perfume Guide', 'category', array( 'slug' => 'perfume-guide', 'description' => 'Fragrance notes, wearing tips and guides for every MR ZAIB perfume.' ) );
	}
	$cat_id = is_array( $cat ) ? (int) $cat['term_id'] : 0;
	mrzaib_blog_page();
	/* WordPress's sample "Hello world!" post, only if it was never edited. */
	$hw = get_page_by_path( 'hello-world', OBJECT, 'post' );
	if ( $hw && false !== strpos( $hw->post_content, 'Welcome to WordPress' ) ) {
		wp_trash_post( $hw->ID );
	}
	$n     = 0;
	$posts = mrzaib_blog_posts();
	$count = count( $posts );
	foreach ( $posts as $i => $d ) {
		if ( $only && $only !== $d['slug'] ) {
			continue;
		}
		$ex  = mrzaib_find_blog( $d['slug'] );
		$arr = array(
			'post_type'      => 'post',
			'post_title'     => $d['title'],
			'post_name'      => $d['slug'],
			'post_content'   => mrzaib_blog_content( $d ),
			'post_excerpt'   => $d['excerpt'],
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'post_category'  => $cat_id ? array( $cat_id ) : array(),
		);
		if ( $ex ) {
			$arr['ID'] = $ex->ID;
			if ( 'trash' === $ex->post_status ) {
				wp_untrash_post( $ex->ID );
			}
		} else {
			// Newest product first on the blog page, a few hours apart.
			$arr['post_date'] = gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) - ( $count - $i ) * 3 * HOUR_IN_SECONDS ); // phpcs:ignore
		}
		$id = $ex ? wp_update_post( wp_slash( $arr ) ) : wp_insert_post( wp_slash( $arr ) );
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		wp_set_post_tags( $id, array_merge( $d['tags'], array( 'MR ZAIB' ) ), false );
		update_post_meta( $id, '_mrzaib_blog_product', $d['product'] );
		if ( function_exists( 'mrzaib_import_image' ) ) {
			$aid = mrzaib_import_image( $d['image'] );
			if ( $aid ) {
				update_post_meta( $aid, '_wp_attachment_image_alt', $d['alt'] );
				set_post_thumbnail( $id, $aid );
			}
		}
		update_post_meta( $id, '_yoast_wpseo_title', $d['seo'][0] );
		update_post_meta( $id, '_yoast_wpseo_metadesc', $d['seo'][1] );
		update_post_meta( $id, '_yoast_wpseo_focuskw', $d['seo'][2] );
		$n++;
	}
	return sprintf( '%d blog article(s) added or updated.', $n );
}

/** Once after the theme update: add the articles. */
add_action( 'admin_init', function () {
	if ( (int) get_option( 'mrzaib_blog_v' ) >= MRZAIB_BLOG_V || ! current_user_can( 'publish_posts' ) || wp_doing_ajax() ) {
		return;
	}
	update_option( 'mrzaib_blog_v', MRZAIB_BLOG_V, false );
	@set_time_limit( 300 ); // phpcs:ignore
	mrzaib_install_blogs();
} );

/** Posts > MR ZAIB Blogs: add or restore any article. */
add_action( 'admin_menu', function () {
	add_submenu_page( 'edit.php', 'MR ZAIB Blogs', 'MR ZAIB Blogs', 'publish_posts', 'mrzaib-blogs', 'mrzaib_blogs_page' );
} );

function mrzaib_blogs_page() {
	$msg = '';
	if ( isset( $_POST['mrzaib_add_blog'] ) && check_admin_referer( 'mrzaib_add_blog' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore
		$slug = sanitize_title( wp_unslash( $_POST['mrzaib_add_blog'] ) );
		$msg  = mrzaib_install_blogs( 'all' === $slug ? '' : $slug );
	}
	echo '<div class="wrap"><h1>MR ZAIB Blogs</h1><p style="max-width:760px;font-size:14px">One article for every MR ZAIB perfume, with cover image, fragrance notes, FAQs, tags and Yoast SEO. Click <strong>Add</strong> to bring back a deleted article. Running again updates the article (your own edits to it are replaced).</p>';
	if ( $msg ) {
		echo '<div class="notice notice-success"><p>' . esc_html( $msg ) . '</p></div>';
	}
	echo '<form method="post">';
	wp_nonce_field( 'mrzaib_add_blog' );
	echo '<table class="widefat striped" style="max-width:960px"><thead><tr><th style="width:120px">Cover</th><th>Article</th><th>Status</th><th></th></tr></thead><tbody>';
	foreach ( mrzaib_blog_posts() as $d ) {
		$p      = mrzaib_find_blog( $d['slug'] );
		$status = $p ? $p->post_status : '';
		echo '<tr><td><img src="' . esc_url( get_template_directory_uri() . '/assets/images/' . $d['image'] ) . '" width="110" height="69" style="object-fit:cover;border-radius:6px"></td>';
		echo '<td><strong>' . esc_html( $d['title'] ) . '</strong></td>';
		echo '<td>' . ( $p && 'trash' !== $status ? '&#10004; ' . esc_html( ucfirst( $status ) ) . ' &middot; <a href="' . esc_url( get_permalink( $p ) ) . '" target="_blank">View</a>' : ( 'trash' === $status ? '&#128465; In trash' : '&#10008; Not added' ) ) . '</td>';
		echo '<td><button class="button button-primary" name="mrzaib_add_blog" value="' . esc_attr( $d['slug'] ) . '">' . ( $p && 'trash' !== $status ? 'Update' : 'Add' ) . '</button></td></tr>';
	}
	echo '</tbody></table><p><button class="button" name="mrzaib_add_blog" value="all">Add / update all</button></p></form></div>';
}

/** An article by slug, including one in the trash. */
function mrzaib_find_blog( $slug ) {
	$p = get_page_by_path( $slug, OBJECT, 'post' );
	if ( ! $p ) {
		$q = get_posts( array( 'post_type' => 'post', 'name' => $slug . '__trashed', 'post_status' => 'trash', 'numberposts' => 1 ) );
		$p = $q ? $q[0] : null;
	}
	return $p;
}

/* ---------- Template helpers ---------- */

function mrzaib_read_time( $post = null ) {
	$words = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $post ) ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/** The product an article is about (WC product or null). */
function mrzaib_blog_product( $post_id ) {
	$slug = get_post_meta( $post_id, '_mrzaib_blog_product', true );
	if ( ! $slug || ! function_exists( 'wc_get_product' ) ) {
		return null;
	}
	$p = get_page_by_path( $slug, OBJECT, 'product' );
	if ( ! $p || 'publish' !== $p->post_status ) {
		return null;
	}
	return wc_get_product( $p->ID );
}

/** FAQ schema for the articles' FAQ blocks. */
add_action( 'wp_footer', function () {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	$slug = get_post_field( 'post_name', get_queried_object_id() );
	foreach ( mrzaib_blog_posts() as $d ) {
		if ( $d['slug'] !== $slug ) {
			continue;
		}
		$q = array();
		foreach ( $d['body']['faq'] as $k => $v ) {
			$q[] = array( '@type' => 'Question', 'name' => $k, 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $v ) );
		}
		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $q ) ) . "</script>\n";
	}
} );
