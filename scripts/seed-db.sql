-- ====================================================================
-- Weardale Together CIC — WordPress Database Seeding Script (MariaDB / MySQL)
-- Configures initial pages, taxonomies, and sample event listings.
-- ====================================================================

-- 1. Insert Core Information Architecture Pages (wp_posts)
INSERT INTO `wp_posts` (`post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_name`, `post_modified`, `post_modified_gmt`, `post_type`) VALUES
(1, NOW(), NOW(), 'Welcome to the Root & Branch Café. We serve delicious, local, seasonal food prepared with care. Open Tuesday - Saturday in Stanhope.', 'Root & Branch Café', '', 'publish', 'closed', 'closed', 'cafe', NOW(), NOW(), 'page'),
(1, NOW(), NOW(), 'We support the young people of Weardale. We offer a high-energy youth club and outdoor learning opportunities.', 'Young People', '', 'publish', 'closed', 'closed', 'young-people', NOW(), NOW(), 'page'),
(1, NOW(), NOW(), 'Our Forest School provides kids with mud kitchen fun, shelter building, and woodland explorations.', 'Forest School', '', 'publish', 'closed', 'closed', 'forest-school', NOW(), NOW(), 'page'),
(1, NOW(), NOW(), 'Connecting our community through creative roots. We host ink drawing, botanical prints, and woodcarving workshops.', 'Creative Arts', '', 'publish', 'closed', 'closed', 'creative-arts', NOW(), NOW(), 'page'),
(1, NOW(), NOW(), 'An unhurried early years family playroom with soft terracotta vibes, pink clays, and sage greens. For toddlers and carers.', 'Roots & Shoots', '', 'publish', 'closed', 'closed', 'roots-shoots', NOW(), NOW(), 'page'),
(1, NOW(), NOW(), 'Who we are, our team, our mission, and our generous funders and local partners.', 'About Weardale Together', '', 'publish', 'closed', 'closed', 'about', NOW(), NOW(), 'page'),
(1, NOW(), NOW(), 'Fill out our secure form below to enquire about volunteering with us. We have roles in kitchen help, driver support, and crafting.\n\n[weardale_volunteer_page]', 'Volunteer with Us', '', 'publish', 'closed', 'closed', 'volunteer', NOW(), NOW(), 'page'),
(1, NOW(), NOW(), 'Get in touch with us. We are based in Stanhope, County Durham.\n\n[weardale_contact_page_layout]', 'Contact Us', '', 'publish', 'closed', 'closed', 'contact-us', NOW(), NOW(), 'page');

-- 2. Insert Custom WP Events (weardale_event)
INSERT INTO `wp_posts` (`post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_name`, `post_modified`, `post_modified_gmt`, `post_type`) VALUES
(1, NOW(), NOW(), 'Join us for a relaxing morning of clay sculpting and tea at the Stanhope Hub. No previous skill required.', 'Summer Clay Sculpting', 'Earthy crafting at the hub.', 'publish', 'closed', 'closed', 'summer-clay-sculpting', NOW(), NOW(), 'weardale_event'),
(1, NOW(), NOW(), 'Bring your little ones for tree planting and mud kitchen play in the local woods. Outdoor clothing required!', 'Forest School: Tree Discovery', 'Little Spouts woodland fun.', 'publish', 'closed', 'closed', 'forest-school-tree-discovery', NOW(), NOW(), 'weardale_event'),
(1, NOW(), NOW(), 'A seasonal gathering for local seniors. Come share hot home-cooked soup, tea, and warm conversations with Cheryl.', 'Senior Soup & Story Exchange', 'Cosy afternoon lunch.', 'publish', 'closed', 'closed', 'senior-soup-story-exchange', NOW(), NOW(), 'weardale_event');

-- 3. Seed Event Metadata (wp_postmeta)
-- We map meta keys corresponding to the custom post meta fields
INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) 
SELECT `ID`, '_event_date', '2026-08-10' FROM `wp_posts` WHERE `post_name` = 'summer-clay-sculpting'
UNION ALL
SELECT `ID`, '_event_time', '10:00 AM - 1:00 PM' FROM `wp_posts` WHERE `post_name` = 'summer-clay-sculpting'
UNION ALL
SELECT `ID`, '_event_location', 'Stanhope Hub Workshop Rooms' FROM `wp_posts` WHERE `post_name` = 'summer-clay-sculpting'
UNION ALL
SELECT `ID`, '_event_cost', 'Free (£2 material donation welcome)' FROM `wp_posts` WHERE `post_name` = 'summer-clay-sculpting';

INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) 
SELECT `ID`, '_event_date', '2026-08-14' FROM `wp_posts` WHERE `post_name` = 'forest-school-tree-discovery'
UNION ALL
SELECT `ID`, '_event_time', '1:30 PM - 4:00 PM' FROM `wp_posts` WHERE `post_name` = 'forest-school-tree-discovery'
UNION ALL
SELECT `ID`, '_event_location', 'Stanhope Community Woodlands' FROM `wp_posts` WHERE `post_name` = 'forest-school-tree-discovery'
UNION ALL
SELECT `ID`, '_event_cost', 'Free (Booking Required)' FROM `wp_posts` WHERE `post_name` = 'forest-school-tree-discovery';

INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) 
SELECT `ID`, '_event_date', '2026-08-18' FROM `wp_posts` WHERE `post_name` = 'senior-soup-story-exchange'
UNION ALL
SELECT `ID`, '_event_time', '12:00 PM - 2:30 PM' FROM `wp_posts` WHERE `post_name` = 'senior-soup-story-exchange'
UNION ALL
SELECT `ID`, '_event_location', 'Root & Branch Café Kitchen' FROM `wp_posts` WHERE `post_name` = 'senior-soup-story-exchange'
UNION ALL
SELECT `ID`, '_event_cost', 'Free (Lunch provided with care)' FROM `wp_posts` WHERE `post_name` = 'senior-soup-story-exchange';

-- 4. Set Up Strand Taxonomy Terms (wp_terms, wp_term_taxonomy)
INSERT INTO `wp_terms` (`term_id`, `name`, `slug`, `term_group`) VALUES
(10, 'Root & Branch Café', 'cafe', 0),
(11, 'Young People', 'youth', 0),
(12, 'Creative Arts', 'creative', 0),
(13, 'Roots & Shoots', 'roots-shoots', 0);

INSERT INTO `wp_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES
(10, 10, 'strand', 'Root & Branch Cafe taxonomy groupings', 0, 1),
(11, 11, 'strand', 'Young People and Forest School taxonomy groupings', 0, 1),
(12, 12, 'strand', 'Creative Arts taxonomy groupings', 0, 1),
(13, 13, 'strand', 'Roots & Shoots Early Years groupings', 0, 1);
