<?php
/**
 * Repeat-Safe News & Stories Seed Data Framework
 *
 * Automatically registers standard categories and seeds editorial news posts.
 * Utilizes robust lookup keys to dynamically establish relationship references.
 *
 * @package Weardale_Platform
 * @since 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register News Seed Framework on admin_init
 */
function weardale_platform_register_news_seeder() {
    // Check if news seeding is already completed
    if ( get_option( 'weardale_news_seeded_v1' ) !== '1' ) {
        weardale_platform_run_news_seeder();
        update_option( 'weardale_news_seeded_v1', '1' );
    }
}
add_action( 'admin_init', 'weardale_platform_register_news_seeder' );

/**
 * Main News Seeding Runner
 */
function weardale_platform_run_news_seeder() {
    // 1. Register categories if they don't exist
    $categories = array(
        'Community News' => 'community-news',
        'Events'         => 'events',
        'Volunteers'     => 'volunteers',
        'Creative Arts'  => 'creative-arts',
        'Young People'   => 'young-people',
        'Cafe'           => 'cafe',
        'Announcements'  => 'announcements',
    );

    $cat_ids = array();
    foreach ( $categories as $name => $slug ) {
        $term = get_term_by( 'slug', $slug, 'category' );
        if ( ! $term ) {
            $inserted = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
            if ( ! is_wp_error( $inserted ) ) {
                $cat_ids[$slug] = $inserted['term_id'];
            }
        } else {
            $cat_ids[$slug] = $term->term_id;
        }
    }

    // Helper to get demo directory ID by its key
    $get_dir_id = function( $key ) {
        $posts = get_posts( array(
            'post_type'   => 'weardale_directory',
            'meta_key'    => '_weardale_demo_key',
            'meta_value'  => $key,
            'post_status' => 'any',
            'numberposts' => 1,
        ) );
        return ! empty( $posts ) ? $posts[0]->ID : '';
    };

    // Helper to get demo event ID by its key (if any exist)
    $get_event_id = function( $key ) {
        $posts = get_posts( array(
            'post_type'   => 'weardale_event',
            'meta_key'    => '_weardale_demo_key', // Or other identifier
            'meta_value'  => $key,
            'post_status' => 'any',
            'numberposts' => 1,
        ) );
        return ! empty( $posts ) ? $posts[0]->ID : '';
    };

    // 2. Define the news stories
    $stories = array(
        array(
            'key'          => 'news-stanhope-bakery-sourdough',
            'title'        => 'Frosterley Sourdough: Slicing into Community Baking',
            'excerpt'      => 'A look behind the flour at our weekly baking circles, where kneaded loaves build strong connections.',
            'content'      => 'There is a special quiet that falls over the Frosterley Village Hall kitchen on Wednesday evenings. Beneath the warmth of the lights, a dozen local residents stand side-by-side, their hands coated in stoneground flour, working together to shape traditional sourdough loaves.

This is the newly launched Weardale Baking Circle, sponsored in part by The Stanhope Artisan Bakery. The project seeks to do far more than teach breadmaking skills—it uses the tactile rhythm of baking to combat social isolation across our rural communities.

"Baking sourdough takes time, patience, and warmth," says Cheryl, our Root & Branch Cafe Chef. "By gathering people to kneaded dough, we create a space where conversation happens naturally. You don\'t have to look each other in the eye the whole time; you focus on the loaf, and the stories just flow."

All loaves baked during the circle are either taken home by participants or donated directly to the Root & Branch Community Cafe for their Thursday pay-what-you-can lunch menu. If you would like to join the next cohort, details are available at the Stanhope Artisan Bakery or on our What\'s On calendar.',
            'categories'   => array( 'cafe', 'community-news' ),
            'programme'    => 'cafe',
            'dir_key'      => 'demo-stanhope-bakery',
            'event_key'    => '',
            'featured'     => '1', // Mark this story as featured!
        ),
        array(
            'key'          => 'news-forest-survival-camp',
            'title'      => 'Young Rangers Complete Forest Survival Camp',
            'excerpt'      => 'From safe fire-lighting to shelter building, local children explore Rookhope Woods.',
            'content'      => 'Twelve young explorers from across Weardale successfully completed their first three-day Forest Survival Camp in the ancient woodlands of Rookhope last weekend. Armed with rainboots, outdoor gear, and boundless enthusiasm, the young people learned essential bushcraft, tracking, and campfire cooking.

The camp, organized by the Weardale Forest School and Outdoor Exploration, is designed to build confidence, independence, and an enduring respect for our beautiful North Pennines landscape. Under the guidance of certified leaders, the children constructed waterproof temporary shelters using only fallen branches and leaf litter.

"It was so cool!" says ten-year-old Toby. "We learned how to make fire using sparks, and we cooked our own flatbreads over the open flame. I was a bit scared of the rain at first, but our shelter stayed totally dry!"

The survival camp is part of the Weardale Together Young People program, aiming to connect rural youth with hands-on nature discovery. Regular forest school sessions continue every Saturday throughout the summer.',
            'categories'   => array( 'young-people', 'events' ),
            'programme'    => 'youth',
            'dir_key'      => 'demo-high-forest-school',
            'event_key'    => '',
            'featured'     => '0',
        ),
        array(
            'key'          => 'news-creative-guild-expo',
            'title'        => 'Stanhope Creative Guild Hosts Local Woodcarving Expo',
            'excerpt'      => 'Celebrating the tactile beauty of regional timber and the talented hands of local makers.',
            'content'      => 'The Old Schoolroom in Stanhope was transformed into a woodland gallery this past Saturday as the Weardale Creative Makers Guild hosted its annual Woodcarving and Craft Expo. Featuring the work of twenty-five regional craftspeople, the event drew over 150 visitors from across County Durham.

Exhibits ranged from intricate hand-carved spoons and bowls made from fallen ash and sycamore to large-scale sculptural representations of North Pennines wildlife. In addition to static displays, several master woodcarvers held live demonstrations, sharing techniques for choosing timber and handling carving knives safely.

"Our goal is to keep traditional, slow crafts alive in the dale," explains guild member Arthur. "Carving is therapeutic. It connects you directly to the natural history of the woods around us."

The guild holds regular weekly studio sessions and workshops for makers of all skill levels, from complete beginners to experienced artists. New members are welcome to join.',
            'categories'   => array( 'creative-arts', 'community-news' ),
            'programme'    => 'creative',
            'dir_key'      => 'demo-weardale-makers',
            'event_key'    => '',
            'featured'     => '0',
        ),
        array(
            'key'          => 'news-lifeline-community-bus',
            'title'        => 'The Lifeline Bus: Connecting Isolated Moorlands',
            'excerpt'      => 'Our community transport service continues to provide critical door-to-door connections.',
            'content'      => 'For many residents living in the smaller, high-altitude settlements of Upper Weardale, simple tasks like visiting the doctor, doing the weekly shopping, or attending a social club can represent a significant logistical barrier. This is where the Weardale Community Transport Bus steps in as a vital lifeline.

Operated entirely by trained local volunteers, the passenger-accessible transport service provides crucial door-to-door journeys for individuals who cannot easily access commercial bus routes. The service ensures that no resident is cut off from essential services or friendly community contact.

"Without the minibus, I wouldn\'t be able to get to my physical therapy appointments in Stanhope," says Edith, an 82-year-old resident of Rookhope. "But it\'s more than just a lift. The drivers are incredibly kind, they help me with my bags, and I get to catch up with friends on the way. It keeps me connected."

With demand rising, Weardale Community Transport is actively seeking more volunteer drivers. If you have a clean driving license and a few free hours a week, please get in touch.',
            'categories'   => array( 'volunteers', 'community-news' ),
            'programme'    => '',
            'dir_key'      => 'demo-weardale-community-transport',
            'event_key'    => '',
            'featured'     => '0',
        ),
        array(
            'key'          => 'news-multi-sensory-playroom',
            'title'        => 'Roots & Shoots Welcomes Multi-Sensory Playroom',
            'excerpt'      => 'Unveiling a beautiful, tactile toddler space inside St John’s Town Hall.',
            'content'      => 'St John\'s Town Hall was filled with laughter and the sound of soft musical chimes this Tuesday morning as the Roots & Shoots Early Years Group celebrated the opening of their new Multi-Sensory Playroom. Designed specifically for toddlers and babies under five, the room is packed with interactive elements to promote early years discovery.

Featuring soft natural textures, organic wool cushions, handmade wooden puzzles, and a dedicated low-level clay molding station, the playroom is designed as a calm, unhurried space. It offers a gentle sanctuary away from the hustle of digital screens, allowing toddlers to explore at their own pace.

"We wanted to build something tactile and grounding," says playroom coordinator Mary. "Sensory play is vital for brain development, and by providing a beautiful, peaceful space, we support parent wellbeing too. It is a place where parents can slow down, share a cup of tea, and connect with other families."

The Roots & Shoots playgroup operates weekly during term time and is open to all parents and carers in Upper Weardale with zero booking required.',
            'categories'   => array( 'young-people', 'announcements' ),
            'programme'    => 'shoots',
            'dir_key'      => 'demo-st-johns-early-years',
            'event_key'    => '',
            'featured'     => '0',
        ),
        array(
            'key'          => 'news-museum-leadmining-artifacts',
            'title'        => 'Weardale Museum Secures Historical Leadmining Artifacts',
            'excerpt'      => 'A remarkable donation of 19th-century miners\' journals and tools comes home.',
            'content'      => 'The Weardale Museum in Ireshopeburn has proudly taken receipt of a unique donation of historic leadmining artifacts, dating back to the height of the dale\'s industrial boom in the mid-1800s. The collection includes hand-written journals, specialized surveying tools, and leather miners\' boots.

These items, preserved for generations by a local family, provide an invaluable first-hand glimpse into the challenging lives and rich culture of Weardale lead miners. The personal journals document daily weather struggles, mining techniques, and the close-knit bonds of chapel-focused village life.

"These are not just old tools; they are the tangible stories of our ancestors," says the museum curator. "To have these journals return to Weardale, where they can be studied and displayed, is a major milestone for our volunteer association."

The museum is currently preparing a dedicated new exhibition for these artifacts, set to open in late summer. Volunteers are available to assist with family history queries.',
            'categories'   => array( 'creative-arts', 'community-news' ),
            'programme'    => '',
            'dir_key'      => 'demo-weardale-history',
            'event_key'    => '',
            'featured'     => '0',
        ),
        array(
            'key'          => 'news-cross-keys-folk-session',
            'title'        => 'The Cross Keys Folk Session: Reviving Old Mountain Tunes',
            'excerpt'      => 'Acoustic musicians gather around the hearth in Eastgate to keep regional heritage alive.',
            'content'      => 'The stone walls of the Cross Keys Inn in Eastgate resonated with the vibrant sounds of fiddles, whistles, and acoustic guitars this past Monday. Musicians and listeners from across the valley gathered for the monthly Weardale Folk Music Session, a traditional acoustic jam session open to all.

The session focuses on preserving and sharing old traditional tunes from Durham, Northumberland, and the wider North Pennines area. Many of these songs tell stories of farming, leadmining, and rural love, carrying the unique heritage of the landscape down through the centuries.

"Folk music is oral history set to melody," says pub landlord and session regular Tom. "It belongs in a warm room with an open fire and friendly company. You don\'t need a stage; you just need a willing heart and a love for local stories."

The folk session takes place on the second Monday of every month. Whether you play an instrument, sing, or simply want to listen with a pint of local ale, you are warmly welcome.',
            'categories'   => array( 'creative-arts', 'announcements' ),
            'programme'    => 'creative',
            'dir_key'      => 'demo-eastgate-pub',
            'event_key'    => '',
            'featured'     => '0',
        ),
        array(
            'key'          => 'news-rookhope-weather-response',
            'title'        => 'Rookhope Severe Weather Response Team Expands',
            'excerpt'      => 'Our remote upland village strengthens emergency volunteer networks ahead of winter.',
            'content'      => 'Residents of Rookhope are taking proactive steps to ensure community safety and resilience ahead of winter weather by expanding the Rookhope Severe Weather Response Team. Based out of Rookhope Village Hall, the volunteer network coordinates support during heavy snowfalls or power emergencies.

Rookhope, nestled high in the North Pennines, can frequently experience extreme weather that cuts off road access. The emergency team, equipped with local 4x4 vehicles and snow equipment, ensures that elderly and vulnerable neighbors receive hot food, dry firewood, and necessary medical prescriptions.

"In a remote place, we are each other\'s first line of response," explains village hall volunteer David. "By organizing our local volunteer network, we can react instantly to support those in need, even before external emergency services can arrive."

The village hall has been stocked with emergency blankets, dry rations, and a standby generator. The team welcomes new volunteers to join the emergency contact list.',
            'categories'   => array( 'announcements', 'volunteers' ),
            'programme'    => '',
            'dir_key'      => 'demo-rookhope-village-hall',
            'event_key'    => '',
            'featured'     => '0',
        ),
        array(
            'key'          => 'news-botanical-ink-drawing',
            'title'        => 'Creative Botanical Ink Drawing Workshop Set for Autumn',
            'excerpt'      => 'Learn the delicate craft of extracting natural pigments from heather, berries, and bark.',
            'content'      => 'The Weardale Creative Makers Guild is delighted to announce a series of botanical ink drawing workshops scheduled for this autumn. Led by visiting artist Sarah, the sessions will teach participants how to safely extract rich, vibrant pigments from regional flora, including wild heather, blackberries, oak bark, and onion skins.

Participants will learn the slow craft of grinding and cooking botanical elements to make lasting artists\' inks. The second half of the workshop will focus on delicate pen-and-ink drawing techniques, illustrating seasonal North Pennines botanical specimens.

"There is something magical about drawing a plant using ink made from that very same plant," says Sarah. "It builds a deep, sensory connection to the seasonal rhythms of our land."

The workshops will be hosted at the Old Schoolroom in Stanhope. Due to limited spacing, advance booking is strictly required. All materials are provided.',
            'categories'   => array( 'creative-arts', 'events' ),
            'programme'    => 'creative',
            'dir_key'      => 'demo-weardale-makers',
            'event_key'    => '',
            'featured'     => '0',
        ),
        array(
            'key'          => 'news-railway-station-restoration',
            'title'        => 'Volunteers Restore Historic Stanhope Station Platform',
            'excerpt'      => 'Hand-carved wooden signs and traditional plantings bring nostalgic charm back to the railway.',
            'content'      => 'The historic Stanhope Station platform is looking its absolute best this summer thanks to several weeks of dedicated restoration work by volunteers from the Weardale Railway Heritage Volunteer Association. The team has meticulously restored vintage wooden benches, hand-painted classic platform signage, and installed beautiful seasonal planters.

The restoration aims to preserve the authentic late-Victorian and Edwardian aesthetic of the heritage line, which provides a major tourism boost to Weardale. Volunteers used traditional woodcraft techniques and historical color schemes to ensure the work is historically accurate.

"We want visitors to feel like they are stepping back in time when they arrive," says project leader George. "The station is the gateway to Weardale for many, and we take immense pride in keeping our railway heritage polished and welcoming."

The Weardale Railway is operating steam and heritage diesel runs throughout the summer season. New volunteers are always welcome to join the railway association.',
            'categories'   => array( 'volunteers' ),
            'programme'    => '',
            'dir_key'      => 'demo-weardale-volunteers',
            'event_key'    => '',
            'featured'     => '0',
        )
    );

    // 3. Seeding Loop
    foreach ( $stories as $story ) {
        // Check if already seeded by _weardale_demo_key
        $existing = get_posts( array(
            'post_type'   => 'post',
            'meta_key'    => '_weardale_demo_key',
            'meta_value'  => $story['key'],
            'post_status' => 'any',
            'numberposts' => 1,
        ) );

        if ( ! empty( $existing ) ) {
            // Already seeded! Skip to avoid duplication
            continue;
        }

        // Insert standard post
        $post_id = wp_insert_post( array(
            'post_title'   => $story['title'],
            'post_excerpt' => $story['excerpt'],
            'post_content' => $story['content'],
            'post_status'  => 'publish',
            'post_type'    => 'post',
        ) );

        if ( is_wp_error( $post_id ) || ! $post_id ) {
            continue;
        }

        // Set marking metadata to indicate it is seeded demo content
        update_post_meta( $post_id, '_weardale_demo_content', '1' );
        update_post_meta( $post_id, '_weardale_demo_key', $story['key'] );

        // Set Custom fields (Featured Story & Programme relationships)
        update_post_meta( $post_id, '_weardale_featured_post', $story['featured'] );
        update_post_meta( $post_id, '_weardale_post_programme', $story['programme'] );

        // Resolve Directory Listing ID dynamically
        if ( ! empty( $story['dir_key'] ) ) {
            $dir_id = $get_dir_id( $story['dir_key'] );
            if ( $dir_id ) {
                update_post_meta( $post_id, '_weardale_related_directory_id', $dir_id );
            }
        }

        // Resolve Event ID dynamically (if events seeded)
        if ( ! empty( $story['event_key'] ) ) {
            $event_id = $get_event_id( $story['event_key'] );
            if ( $event_id ) {
                update_post_meta( $post_id, '_weardale_related_event_id', $event_id );
            }
        }

        // Set standard categories
        $assigned_cat_ids = array();
        foreach ( $story['categories'] as $cat_slug ) {
            if ( isset( $cat_ids[$cat_slug] ) ) {
                $assigned_cat_ids[] = intval( $cat_ids[$cat_slug] );
            }
        }

        if ( ! empty( $assigned_cat_ids ) ) {
            wp_set_object_terms( $post_id, $assigned_cat_ids, 'category' );
        }
    }
}
