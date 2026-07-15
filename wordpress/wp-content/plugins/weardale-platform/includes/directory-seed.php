<?php
/**
 * Repeat-Safe Development Seed Data Framework
 *
 * Automatically seeds directory listings and other content with robust duplication prevention.
 *
 * @package Weardale_Platform
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Seed Framework Init
 */
function weardale_platform_register_seeder() {
    // Only run if we haven't successfully completed seeding or to check missing records
    if ( get_option( 'weardale_directory_seeded_v1' ) !== '1' ) {
        weardale_platform_run_seeder();
        update_option( 'weardale_directory_seeded_v1', '1' );
    }
}
add_action( 'admin_init', 'weardale_platform_register_seeder' );

/**
 * Main Seeding Runner
 */
function weardale_platform_run_seeder() {
    // 1. Ensure Taxonomies are registered first
    if ( ! taxonomy_exists( 'directory_type' ) || ! taxonomy_exists( 'village' ) || ! taxonomy_exists( 'service_area' ) ) {
        return;
    }

    // 2. Define standard terms for taxonomies
    $directory_types = array(
        'Community Group'       => 'community-group',
        'Business'              => 'business',
        'Community Facility'    => 'community-facility',
        'Transport'             => 'transport',
        'Support Service'       => 'support-service',
        'Volunteer Opportunity' => 'volunteer-opportunity',
        'Health & Wellbeing'    => 'health-wellbeing',
        'Arts & Culture'        => 'arts-culture',
        'Food & Drink'          => 'food-drink',
        'Sport & Recreation'    => 'sport-recreation',
    );

    $villages = array(
        'Stanhope'          => 'stanhope',
        'Wolsingham'        => 'wolsingham',
        'Frosterley'        => 'frosterley',
        'St John\'s Chapel' => 'st-johns-chapel',
        'Rookhope'          => 'rookhope',
        'Westgate'          => 'westgate',
        'Eastgate'          => 'eastgate',
        'Ireshopeburn'      => 'ireshopeburn',
        'Wearhead'          => 'wearhead',
    );

    $service_areas = array(
        'Upper Weardale' => 'upper-weardale',
        'Mid Weardale'   => 'mid-weardale',
        'Lower Weardale' => 'lower-weardale',
        'All Weardale'   => 'all-weardale',
    );

    // Create terms
    foreach ( $directory_types as $name => $slug ) {
        if ( ! term_exists( $slug, 'directory_type' ) ) {
            wp_insert_term( $name, 'directory_type', array( 'slug' => $slug ) );
        }
    }

    foreach ( $villages as $name => $slug ) {
        if ( ! term_exists( $slug, 'village' ) ) {
            wp_insert_term( $name, 'village', array( 'slug' => $slug ) );
        }
    }

    foreach ( $service_areas as $name => $slug ) {
        if ( ! term_exists( $slug, 'service_area' ) ) {
            wp_insert_term( $name, 'service_area', array( 'slug' => $slug ) );
        }
    }

    // 3. Define the demo directory entries (15 distinct listings)
    $listings = array(
        array(
            'key'         => 'demo-st-johns-hall',
            'title'       => 'St John\'s Chapel Town Hall',
            'excerpt'     => 'A vibrant community hub hosting local markets, coffee mornings, and fitness classes.',
            'content'     => 'St John\'s Chapel Town Hall is the warm beating heart of Upper Weardale. Managed entirely by local volunteers, the hall hosts various activities including a weekly Friday market, cinema nights, art exhibitions, and exercise classes. It is available for private hire, wedding receptions, and public meetings.',
            'type'        => 'community-facility',
            'village'     => 'st-johns-chapel',
            'area'        => 'upper-weardale',
            'meta'        => array(
                '_directory_address'         => 'Market Place, St John\'s Chapel, DL13 1QF',
                '_directory_phone'           => '01388 537100',
                '_directory_email'           => 'info@stjohnstownhall.org.uk',
                '_directory_website'         => 'https://stjohnstownhall.org.uk',
                '_directory_opening_hours'   => 'Open for scheduled events. Office open Mon-Wed 9am - 12pm.',
                '_directory_accessibility'   => 'Full wheelchair access, ramped entrance, disabled toilets, hearing loop.',
                '_directory_who_it_helps'    => 'All residents and visitors of Upper Weardale.',
                '_directory_pricing'         => 'Varies by event. Hall hire from £15 per hour.',
                '_directory_booking_required'=> 'no',
                '_directory_latitude'        => '54.7381',
                '_directory_longitude'       => '-2.1802',
                '_directory_facebook'        => 'https://facebook.com/StJohnsChapelTownHall',
                '_directory_verified'        => '1',
                '_directory_last_reviewed'   => '2026-07-01',
                '_directory_related_programme'=> 'Creative Arts',
            ),
        ),
        array(
            'key'         => 'demo-weardale-makers',
            'title'       => 'Weardale Creative Makers Guild',
            'excerpt'     => 'A collaborative group of local artists and crafters sharing skills and organizing exhibitions.',
            'content'     => 'The Weardale Creative Makers Guild brings together painters, woodworkers, weavers, potters, and writers from across the valley. We run weekly workshops to share skills, host seasonal craft fairs, and support members with business development and marketing. Beginners are always welcome to join our weekly social sessions.',
            'type'        => 'arts-culture',
            'village'     => 'stanhope',
            'area'        => 'all-weardale',
            'meta'        => array(
                '_directory_address'         => 'The Old Schoolroom, Front Street, Stanhope, DL13 2TS',
                '_directory_phone'           => '07700 900077',
                '_directory_email'           => 'makers@weardalecreative.co.uk',
                '_directory_website'         => 'https://weardalecreative.co.uk',
                '_directory_opening_hours'   => 'Workshops: Thu 6:30pm - 9:00pm. Open studio: Sat 10am - 4pm.',
                '_directory_accessibility'   => 'Ground floor access, nearby public parking.',
                '_directory_who_it_helps'    => 'Local artists, crafters, and anyone wanting to learn creative skills.',
                '_directory_pricing'         => 'Workshop fees apply. Annual membership £20.',
                '_directory_booking_required'=> 'yes',
                '_directory_latitude'        => '54.7483',
                '_directory_longitude'       => '-2.0118',
                '_directory_facebook'        => 'https://facebook.com/WeardaleMakers',
                '_directory_instagram'       => 'https://instagram.com/WeardaleMakers',
                '_directory_verified'        => '1',
                '_directory_last_reviewed'   => '2026-07-10',
                '_directory_related_programme'=> 'Creative Arts',
            ),
        ),
        array(
            'key'         => 'demo-frosterley-cafe',
            'title'       => 'Root & Branch Community Café',
            'excerpt'     => 'A friendly volunteer-run café offering pay-what-you-can lunches and digital support.',
            'content'     => 'Root & Branch Café is a warm, social space operating every Thursday. We serve fresh, home-cooked soups, toasties, and cakes using surplus local produce. In addition to great food, we offer a "digital drop-in" service where visitors can get help with smartphones, laptops, and online services.',
            'type'        => 'food-drink',
            'village'     => 'frosterley',
            'area'        => 'mid-weardale',
            'meta'        => array(
                '_directory_address'         => 'Frosterley Village Hall, Front Street, Frosterley, DL13 2SL',
                '_directory_email'           => 'cafe@weardaletogether.org.uk',
                '_directory_opening_hours'   => 'Thursdays 11:30am - 2:30pm.',
                '_directory_accessibility'   => 'Ramped entrance, spacious layout, accessible toilets.',
                '_directory_who_it_helps'    => 'Anyone seeking a friendly lunch, social connection, or digital advice.',
                '_directory_pricing'         => 'Pay what you can (suggested donation £3).',
                '_directory_booking_required'=> 'no',
                '_directory_latitude'        => '54.7265',
                '_directory_longitude'       => '-1.9654',
                '_directory_facebook'        => 'https://facebook.com/RootAndBranchCafe',
                '_directory_verified'        => '1',
                '_directory_last_reviewed'   => '2026-07-12',
                '_directory_related_programme'=> 'Root & Branch Café',
            ),
        ),
        array(
            'key'         => 'demo-weardale-community-transport',
            'title'       => 'Weardale Community Transport Bus',
            'excerpt'     => 'Flexible door-to-door minibus transport for medical appointments, shopping, and social trips.',
            'content'     => 'Weardale Community Transport operates a small fleet of wheelchair-accessible minibuses to support residents who cannot access regular public transport. We provide essential connections for medical appointments, local shopping runs, and organized group excursions. Our drivers are trained volunteers from the local community.',
            'type'        => 'transport',
            'village'     => 'stanhope',
            'area'        => 'all-weardale',
            'meta'        => array(
                '_directory_address'         => 'Stanhope Community Centre, Stanhope, DL13 2UR',
                '_directory_phone'           => '01388 528222',
                '_directory_email'           => 'transport@weardaletogether.org.uk',
                '_directory_opening_hours'   => 'Phone booking line open Mon-Fri 9:00am - 1:00pm.',
                '_directory_accessibility'   => 'Fully passenger-accessible, passenger lift, secure wheelchair anchors.',
                '_directory_who_it_helps'    => 'Older adults, disabled residents, and individuals without public transport access.',
                '_directory_pricing'         => 'Small mileage-based contribution or concessionary passes accepted.',
                '_directory_booking_required'=> 'yes',
                '_directory_latitude'        => '54.7490',
                '_directory_longitude'       => '-2.0102',
                '_directory_verified'        => '1',
                '_directory_last_reviewed'   => '2026-06-25',
                '_directory_related_programme'=> 'Community Transport',
            ),
        ),
        array(
            'key'         => 'demo-high-forest-school',
            'title'       => 'Weardale Forest School and Outdoor Exploration',
            'excerpt'     => 'Outdoor learning, shelter building, and fire lighting sessions for kids and families.',
            'content'     => 'Weardale Forest School offers dynamic, child-led outdoor learning experiences in the beautiful woodlands near Rookhope. Our qualified leaders facilitate tree climbing, shelter construction, campfire cooking, tool use, and nature connection. We run school holiday schemes and family weekend sessions.',
            'type'        => 'volunteer-opportunity',
            'village'     => 'rookhope',
            'area'        => 'upper-weardale',
            'meta'        => array(
                '_directory_address'         => 'Rookhope Woods, Rookhope, DL13 2AU',
                '_directory_phone'           => '07700 900112',
                '_directory_email'           => 'explore@weardaleforest.org',
                '_directory_website'         => 'https://weardaleforest.org',
                '_directory_opening_hours'   => 'Saturdays 10:00am - 1:00pm. Holiday camps: Tue-Thu 9:30am - 3:30pm.',
                '_directory_accessibility'   => 'Outdoor rugged terrain, specialized all-terrain buggy available with booking.',
                '_directory_who_it_helps'    => 'Children aged 4-14, families, and young people.',
                '_directory_pricing'         => '£10 per child per session. Concessions available.',
                '_directory_booking_required'=> 'yes',
                '_directory_latitude'        => '54.7825',
                '_directory_longitude'       => '-2.0864',
                '_directory_instagram'       => 'https://instagram.com/WeardaleForestSchool',
                '_directory_verified'        => '1',
                '_directory_last_reviewed'   => '2026-07-14',
                '_directory_related_programme'=> 'Young People and Forest School',
            ),
        ),
        array(
            'key'         => 'demo-stanhope-bakery',
            'title'       => 'The Stanhope Artisan Bakery',
            'excerpt'     => 'A local family-run bakery offering traditional sourdoughs, pastries, and community baking classes.',
            'content'     => 'An award-winning local bakery specializing in slow-fermented sourdough loaves, artisanal croissants, and savory pies. We take pride in sourcing stoneground flours locally. Every Wednesday evening, we host community baking classes to teach traditional breadmaking techniques to home bakers.',
            'type'        => 'business',
            'village'     => 'stanhope',
            'area'        => 'mid-weardale',
            'meta'        => array(
                '_directory_address'         => '42 Front Street, Stanhope, DL13 2UD',
                '_directory_phone'           => '01388 527334',
                '_directory_email'           => 'hello@stanhopebakery.co.uk',
                '_directory_website'         => 'https://stanhopebakery.co.uk',
                '_directory_opening_hours'   => 'Tue-Sat 8:00am - 3:00pm.',
                '_directory_accessibility'   => 'Small step at entrance, staff happy to assist at door.',
                '_directory_who_it_helps'    => 'Food lovers, residents, and visitors to Stanhope.',
                '_directory_pricing'         => 'Free entry. Loaves from £3.50.',
                '_directory_booking_required'=> 'no',
                '_directory_latitude'        => '54.7481',
                '_directory_longitude'       => '-2.0121',
                '_directory_verified'        => '1',
                '_directory_last_reviewed'   => '2026-07-05',
            ),
        ),
        array(
            'key'         => 'demo-weardale-foodbank',
            'title'       => 'Weardale Foodbank and Support Centre',
            'excerpt'     => 'Crisis food support, energy advice, and benefit navigation services.',
            'content'     => 'Operating under the national Trussell Trust guidelines, the Weardale Foodbank provides emergency food parcels, toiletries, and advice to individuals and families facing financial hardship. We offer wrap-around support, connecting clients with energy advisers, debt navigators, and mental health professionals.',
            'type'        => 'support-service',
            'village'     => 'wolsingham',
            'area'        => 'all-weardale',
            'meta'        => array(
                '_directory_address'         => 'The Vestry, Wolsingham Methodist Church, Wolsingham, DL13 3AB',
                '_directory_phone'           => '01388 526115',
                '_directory_email'           => 'support@weardalefoodbank.org.uk',
                '_directory_opening_hours'   => 'Mondays and Fridays 10:00am - 1:00pm.',
                '_directory_accessibility'   => 'Wheelchair accessible, quiet waiting space.',
                '_directory_who_it_helps'    => 'Anyone in Weardale experiencing financial or food emergency.',
                '_directory_pricing'         => 'Completely Free.',
                '_directory_booking_required'=> 'no',
                '_directory_latitude'        => '54.7292',
                '_directory_longitude'       => '-1.8861',
                '_directory_verified'        => '1',
                '_directory_last_reviewed'   => '2026-07-02',
            ),
        ),
        array(
            'key'         => 'demo-st-johns-early-years',
            'title'       => 'Roots & Shoots Early Years Group',
            'excerpt'     => 'A friendly stay-and-play group for parents, babies, and toddlers in Upper Weardale.',
            'content'     => 'Roots & Shoots is a volunteer-led playgroup where young children can play, sing, explore sensory boxes, and socialize. Parents and carers can enjoy a warm cup of tea and supportive conversations. We host seasonal outdoor forest-play outings and creative sessions.',
            'type'        => 'health-wellbeing',
            'village'     => 'st-johns-chapel',
            'area'        => 'upper-weardale',
            'meta'        => array(
                '_directory_address'         => 'St John\'s Town Hall, Market Place, St John\'s Chapel, DL13 1QF',
                '_directory_phone'           => '07700 900223',
                '_directory_email'           => 'roots@weardaletogether.org.uk',
                '_directory_opening_hours'   => 'Tuesdays 9:30am - 11:30am (Term time only).',
                '_directory_accessibility'   => 'Wheelchair/pushchair accessible, baby change facilities.',
                '_directory_who_it_helps'    => 'Parents, carers, babies, and toddlers under 5.',
                '_directory_pricing'         => '£1.50 per family (includes refreshments).',
                '_directory_booking_required'=> 'no',
                '_directory_latitude'        => '54.7381',
                '_directory_longitude'       => '-2.1802',
                '_directory_verified'        => '1',
                '_directory_last_reviewed'   => '2026-07-08',
                '_directory_related_programme'=> 'Roots & Shoots Early Years',
            ),
        ),
        array(
            'key'         => 'demo-weardale-history',
            'title'       => 'Weardale Museum and High House Chapel',
            'excerpt'     => 'A charming volunteer-led folk museum dedicated to the heritage of Weardale life.',
            'content'     => 'Located next to the oldest Methodist chapel in continuous use, the Weardale Museum preserves the rich leadmining, agricultural, and domestic heritage of the dale. Highlights include the Weardale Tapestry, geological collections, and family history research services.',
            'type'        => 'arts-culture',
            'village'     => 'ireshopeburn',
            'area'        => 'upper-weardale',
            'meta'        => array(
                '_directory_address'         => 'High House Chapel, Ireshopeburn, DL13 1HD',
                '_directory_phone'           => '01388 537417',
                '_directory_email'           => 'curator@weardalemuseum.org.uk',
                '_directory_website'         => 'https://weardalemuseum.org.uk',
                '_directory_opening_hours'   => 'Easter to October: Wed-Sun 1:00pm - 5:00pm.',
                '_directory_accessibility'   => 'Chapel is fully accessible. Cottage museum has stairs to the upper floor.',
                '_directory_who_it_helps'    => 'Heritage enthusiasts, school groups, families, and genealogists.',
                '_directory_pricing'         => 'Adults £4, Children £1, Under 5s Free.',
                '_directory_booking_required'=> 'no',
                '_directory_latitude'        => '54.7346',
                '_directory_longitude'       => '-2.2024',
                '_directory_facebook'        => 'https://facebook.com/WeardaleMuseum',
                '_directory_verified'        => '1',
                '_directory_last_reviewed'   => '2026-06-30',
            ),
        ),
        array(
            'key'         => 'demo-westgate-garden',
            'title'       => 'Westgate Community Orchard & Garden',
            'excerpt'     => 'A therapeutic shared garden spaces promoting sustainable growing and community harvest.',
            'content'     => 'The Westgate Community Orchard is a shared green space created by residents. We have planted over 40 heritage fruit trees, built raised vegetable beds, and created a wildflower meadow. It is a peaceful place to sit, or a space to roll up your sleeves and learn sustainable organic gardening.',
            'type'        => 'sport-recreation',
            'village'     => 'westgate',
            'area'        => 'upper-weardale',
            'meta'        => array(
                '_directory_address'         => 'The Garth, Westgate, DL13 1JS',
                '_directory_email'           => 'garden@westgatecommunity.co.uk',
                '_directory_opening_hours'   => 'Open 24/7. Community work parties on Saturday mornings from 10am.',
                '_directory_accessibility'   => 'Gravel paths, some narrow gates, sensory area is fully accessible.',
                '_directory_who_it_helps'    => 'Anyone interested in gardening, community growing, and outdoors.',
                '_directory_pricing'         => 'Free.',
                '_directory_booking_required'=> 'no',
                '_directory_latitude'        => '54.7394',
                '_directory_longitude'       => '-2.1415',
                '_directory_verified'        => '1',
                '_directory_last_reviewed'   => '2026-07-11',
            ),
        ),
        array(
            'key'         => 'demo-rookhope-village-hall',
            'title'       => 'Rookhope Village Hall',
            'excerpt'     => 'Local village hall offering spaces for community events, youth clubs, and emergency accommodation.',
            'content'     => 'Rookhope Village Hall is a vital resource for this remote upland community. It provides space for the youth club, a post office counter service twice a week, a small gym, and social evenings. The hall is also designated as an emergency severe-weather hub for the village.',
            'type'        => 'community-facility',
            'village'     => 'rookhope',
            'area'        => 'upper-weardale',
            'meta'        => array(
                '_directory_address'         => 'High Street, Rookhope, DL13 2AX',
                '_directory_phone'           => '01388 517231',
                '_directory_email'           => 'hall@rookhope.org.uk',
                '_directory_opening_hours'   => 'Open for scheduled events. Post Office: Tue 1pm - 3pm, Thu 9am - 11am.',
                '_directory_accessibility'   => 'Wheelchair ramp, disabled toilet.',
                '_directory_who_it_helps'    => 'Residents of Rookhope and surrounding moorlands.',
                '_directory_pricing'         => 'Free community entry. Room hire from £10 per hour.',
                '_directory_booking_required'=> 'no',
                '_directory_latitude'        => '54.7845',
                '_directory_longitude'       => '-2.0881',
                '_directory_verified'        => '1',
                '_directory_last_reviewed'   => '2026-07-06',
            ),
        ),
        array(
            'key'         => 'demo-stanhope-walkers',
            'title'       => 'Weardale Walkers and Ramblers',
            'excerpt'     => 'Guided social walks of various lengths exploring Weardale history and scenery.',
            'content'     => 'Weardale Walkers is a friendly volunteer group organizing guided walks twice a week. Walks range from gentle 2-mile river pathways to challenging 8-mile moorland hikes. Our trained walk leaders share interesting geological and historical stories of the landscape as we go.',
            'type'        => 'health-wellbeing',
            'village'     => 'stanhope',
            'area'        => 'all-weardale',
            'meta'        => array(
                '_directory_phone'           => '07700 900334',
                '_directory_email'           => 'walks@weardale-walkers.org.uk',
                '_directory_website'         => 'https://weardale-walkers.org.uk',
                '_directory_opening_hours'   => 'Walks depart Sundays at 10:00am and Wednesdays at 1:30pm.',
                '_directory_accessibility'   => 'Rugged public footpaths, varying difficulty levels clearly marked in schedules.',
                '_directory_who_it_helps'    => 'Walkers, hikers, older adults, and anyone wishing to stay active.',
                '_directory_pricing'         => 'Free (voluntary donations to mountain rescue welcome).',
                '_directory_booking_required'=> 'no',
                '_directory_facebook'        => 'https://facebook.com/WeardaleWalkers',
                '_directory_verified'        => '1',
                '_directory_last_reviewed'   => '2026-07-09',
            ),
        ),
        array(
            'key'         => 'demo-eastgate-pub',
            'title'       => 'The Cross Keys Inn',
            'excerpt'     => 'A historic country pub serving local ales, home-cooked food, and community folk music sessions.',
            'content'     => 'The Cross Keys is a classic Weardale country inn dating back to the 17th century. We offer a welcoming open fire, traditional locally brewed real ales, and classic hearty pub food. Every second Monday of the month we host the popular Weardale Folk Music Session, welcoming acoustic musicians.',
            'type'        => 'food-drink',
            'village'     => 'eastgate',
            'area'        => 'upper-weardale',
            'meta'        => array(
                '_directory_address'         => 'Eastgate, Stanhope, DL13 2LH',
                '_directory_phone'           => '01388 517224',
                '_directory_website'         => 'https://crosskeys-eastgate.co.uk',
                '_directory_opening_hours'   => 'Wed-Sun 12:00pm - 11:00pm. Food served 12:00pm - 8:30pm.',
                '_directory_accessibility'   => 'Main bar and dining room have step-free access.',
                '_directory_who_it_helps'    => 'Locals, tourists, and folk music lovers.',
                '_directory_pricing'         => 'Free entry.',
                '_directory_booking_required'=> 'no',
                '_directory_latitude'        => '54.7471',
                '_directory_longitude'       => '-2.0674',
                '_directory_verified'        => '1',
                '_directory_last_reviewed'   => '2026-07-13',
            ),
        ),
        array(
            'key'         => 'demo-frosterley-badminton',
            'title'       => 'Frosterley Junior Badminton Club',
            'excerpt'     => 'A fun, supportive coaching group for children to learn and play badminton.',
            'content'     => 'Frosterley Junior Badminton Club offers active sports training and recreational play for children aged 8 to 16. Led by Badminton England certified coaches, we focus on coordination, teamwork, active fitness, and friendly tournament matches. Racquets are provided.',
            'type'        => 'sport-recreation',
            'village'     => 'frosterley',
            'area'        => 'mid-weardale',
            'meta'        => array(
                '_directory_address'         => 'Frosterley Village Hall, Front Street, Frosterley, DL13 2SL',
                '_directory_phone'           => '07700 900445',
                '_directory_email'           => 'badminton@frosterleyhall.co.uk',
                '_directory_opening_hours'   => 'Fridays 5:00pm - 7:00pm.',
                '_directory_accessibility'   => 'Wheelchair accessible hall, accessible parking.',
                '_directory_who_it_helps'    => 'Young people and schoolchildren aged 8-16.',
                '_directory_pricing'         => '£3 per session.',
                '_directory_booking_required'=> 'yes',
                '_directory_latitude'        => '54.7265',
                '_directory_longitude'       => '-1.9654',
                '_directory_verified'        => '1',
                '_directory_last_reviewed'   => '2026-07-04',
            ),
        ),
        array(
            'key'         => 'demo-weardale-volunteers',
            'title'       => 'Weardale Railway Heritage Volunteer Association',
            'excerpt'     => 'Preserving and operating the heritage railway. Opportunities in steam maintenance, track work, and guiding.',
            'content'     => 'Weardale Railway Volunteers work to keep the historic 18-mile railway running between Bishop Auckland and Stanhope. We offer diverse opportunities for volunteers of all ages and skills: from maintaining steam locomotives, painting stations, and repairing track work, to guiding visitors and selling tickets.',
            'type'        => 'volunteer-opportunity',
            'village'     => 'stanhope',
            'area'        => 'all-weardale',
            'meta'        => array(
                '_directory_address'         => 'Stanhope Station, Stanhope, DL13 2YR',
                '_directory_phone'           => '01388 526200',
                '_directory_email'           => 'volunteers@weardale-railway.org.uk',
                '_directory_website'         => 'https://weardale-railway.org.uk/volunteer',
                '_directory_opening_hours'   => 'Work parties every Wed & Sat 9:00am - 4:00pm.',
                '_directory_accessibility'   => 'Station platforms are accessible. Some mechanical/track tasks require physical capability.',
                '_directory_who_it_helps'    => 'Rail enthusiasts, local community, and volunteers seeking connection and craft skills.',
                '_directory_pricing'         => 'Free (volunteering).',
                '_directory_booking_required'=> 'yes',
                '_directory_latitude'        => '54.7431',
                '_directory_longitude'       => '-2.0163',
                '_directory_facebook'        => 'https://facebook.com/WeardaleRailwayHeritage',
                '_directory_verified'        => '1',
                '_directory_last_reviewed'   => '2026-07-03',
            ),
        )
    );

    // 4. Seeding Loop
    foreach ( $listings as $list ) {
        // Build query to find if a post with this _weardale_demo_key already exists
        $existing = get_posts( array(
            'post_type'  => 'weardale_directory',
            'meta_key'   => '_weardale_demo_key',
            'meta_value' => $list['key'],
            'post_status'=> 'any',
            'limit'      => 1,
        ) );

        if ( ! empty( $existing ) ) {
            // Already seeded! Respect existing. Never overwrite edited records.
            continue;
        }

        // Insert new directory post
        $post_id = wp_insert_post( array(
            'post_title'   => $list['title'],
            'post_excerpt' => $list['excerpt'],
            'post_content' => $list['content'],
            'post_status'  => 'publish',
            'post_type'    => 'weardale_directory',
        ) );

        if ( is_wp_error( $post_id ) || ! $post_id ) {
            continue;
        }

        // Set marking metadata to indicate it is seeded demo content
        update_post_meta( $post_id, '_weardale_demo_content', '1' );
        update_post_meta( $post_id, '_weardale_demo_key', $list['key'] );

        // Set CPT custom fields metadata
        foreach ( $list['meta'] as $meta_key => $meta_val ) {
            update_post_meta( $post_id, $meta_key, $meta_val );
        }

        // Assign Taxonomy Terms
        // 1. Directory Type
        $type_term = get_term_by( 'slug', $list['type'], 'directory_type' );
        if ( $type_term ) {
            wp_set_object_terms( $post_id, $type_term->term_id, 'directory_type' );
        }

        // 2. Village
        $village_term = get_term_by( 'slug', $list['village'], 'village' );
        if ( $village_term ) {
            wp_set_object_terms( $post_id, $village_term->term_id, 'village' );
        }

        // 3. Service Area
        $area_term = get_term_by( 'slug', $list['area'], 'service_area' );
        if ( $area_term ) {
            wp_set_object_terms( $post_id, $area_term->term_id, 'service_area' );
        }
    }
}
