<?php
/**
 * The template for displaying Weardale Directory Archive ("Community Directory").
 *
 * Implements a robust keyword search and filter interface for types, villages, service areas,
 * verified status, and accessibility options with semantic, keyboard-friendly markup.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.2.0
 */

get_header();

// 1. Retrieve query parameters safely
$search_query       = isset( $_GET['q'] ) ? sanitize_text_field( $_GET['q'] ) : '';
$current_type       = isset( $_GET['type'] ) ? sanitize_title( $_GET['type'] ) : '';
$current_village    = isset( $_GET['village'] ) ? sanitize_title( $_GET['village'] ) : '';
$current_area       = isset( $_GET['area'] ) ? sanitize_title( $_GET['area'] ) : '';
$current_verified   = isset( $_GET['verified'] ) ? sanitize_text_field( $_GET['verified'] ) : 'all';
$current_access     = isset( $_GET['access'] ) ? sanitize_text_field( $_GET['access'] ) : '';

$paged = isset( $_GET['dir_page'] ) ? max( 1, intval( $_GET['dir_page'] ) ) : 1;
$limit = 9; // Grid layout fits nicely in rows of 3

// 2. Formulate query args for weardale_platform_query_directory()
$query_args = array(
    'search'         => $search_query,
    'directory_type' => $current_type,
    'village'        => $current_village,
    'service_area'   => $current_area,
    'limit'          => $limit,
    'paged'          => $paged,
);

if ( '1' === $current_verified || 'yes' === $current_verified ) {
    $query_args['verified'] = 'yes';
} elseif ( '0' === $current_verified || 'no' === $current_verified ) {
    $query_args['verified'] = 'no';
}

if ( ! empty( $current_access ) ) {
    $query_args['accessibility'] = $current_access;
}

// Run Query
$directory_query = function_exists( 'weardale_platform_query_directory' ) 
    ? weardale_platform_query_directory( $query_args ) 
    : new WP_Query( array( 'post_type' => 'weardale_directory', 'posts_per_page' => $limit, 'paged' => $paged ) );

$total_results = $directory_query->found_posts;
$total_pages   = $directory_query->max_num_pages;

// Fetch Taxonomy Terms for selectors
$all_types    = get_terms( array( 'taxonomy' => 'directory_type', 'hide_empty' => false ) );
$all_villages = get_terms( array( 'taxonomy' => 'village', 'hide_empty' => false ) );
$all_areas    = get_terms( array( 'taxonomy' => 'service_area', 'hide_empty' => false ) );
?>

<main id="primary-content" class="site-main" role="main">
    
    <!-- Hero Header Banner -->
    <header class="archive-header" style="
        background: linear-gradient(135deg, var(--color-forest) 0%, var(--color-sage) 100%);
        color: var(--color-cream);
        padding: 5rem 0;
        text-align: center;
        border-bottom: 4px solid var(--color-tan);
    ">
        <div class="container">
            <span class="badge" style="
                background-color: var(--color-cream);
                color: var(--color-forest);
                margin-bottom: 1rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            ">
                <?php esc_html_e( 'Weardale Together CIC', 'weardale-together' ); ?>
            </span>
            
            <h1 class="font-display" style="
                font-size: 3.5rem;
                margin: 0 0 1rem 0;
                color: var(--color-cream);
                line-height: 1.1;
                font-weight: normal;
            ">
                <?php esc_html_e( 'Community Directory', 'weardale-together' ); ?>
            </h1>
            
            <p style="
                font-size: 1.2rem;
                max-width: 650px;
                margin: 0 auto;
                opacity: 0.95;
                line-height: 1.5;
            ">
                <?php esc_html_e( 'Connect with grassroots volunteer groups, local businesses, community transport routes, health supports, and village halls across the valley.', 'weardale-together' ); ?>
            </p>
        </div>
    </header>

    <!-- Search & Filters Container -->
    <section class="directory-filters-section" aria-label="<?php esc_attr_e( 'Directory Search and Filters', 'weardale-together' ); ?>" style="
        background-color: var(--color-cream);
        border-bottom: 1px solid var(--color-tan);
        padding: 2rem 0;
    ">
        <div class="container">
            <form method="get" action="<?php echo esc_url( get_post_type_archive_link( 'weardale_directory' ) ); ?>" style="
                background: var(--color-white);
                border: 2px solid var(--color-tan);
                border-radius: var(--border-radius-md);
                padding: 1.5rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            ">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                    
                    <!-- Keyword Search -->
                    <div>
                        <label for="dir-q" style="display:block; font-weight:700; font-size:0.85rem; text-transform:uppercase; margin-bottom:0.5rem; color:var(--color-forest);">
                            🔍 <?php esc_html_e( 'Keyword Search', 'weardale-together' ); ?>
                        </label>
                        <input type="text" id="dir-q" name="q" value="<?php echo esc_attr( $search_query ); ?>" placeholder="<?php esc_attr_e( 'Search by name, tags...', 'weardale-together' ); ?>" style="
                            width: 100%;
                            padding: 0.6rem 0.75rem;
                            border: 2px solid var(--color-tan);
                            border-radius: var(--border-radius-sm);
                            font-family: inherit;
                        ">
                    </div>

                    <!-- Directory Type Filter -->
                    <div>
                        <label for="dir-type" style="display:block; font-weight:700; font-size:0.85rem; text-transform:uppercase; margin-bottom:0.5rem; color:var(--color-forest);">
                            🏷️ <?php esc_html_e( 'Listing Type', 'weardale-together' ); ?>
                        </label>
                        <select id="dir-type" name="type" style="
                            width: 100%;
                            padding: 0.6rem 0.75rem;
                            border: 2px solid var(--color-tan);
                            border-radius: var(--border-radius-sm);
                            font-family: inherit;
                            background: var(--color-white);
                        ">
                            <option value=""><?php esc_html_e( 'All Types', 'weardale-together' ); ?></option>
                            <?php if ( ! empty( $all_types ) && ! is_wp_error( $all_types ) ) : ?>
                                <?php foreach ( $all_types as $term ) : ?>
                                    <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $current_type, $term->slug ); ?>>
                                        <?php echo esc_html( $term->name ); ?> (<?php echo esc_html( $term->count ); ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Village Filter -->
                    <div>
                        <label for="dir-village" style="display:block; font-weight:700; font-size:0.85rem; text-transform:uppercase; margin-bottom:0.5rem; color:var(--color-forest);">
                            📍 <?php esc_html_e( 'Village / Settlement', 'weardale-together' ); ?>
                        </label>
                        <select id="dir-village" name="village" style="
                            width: 100%;
                            padding: 0.6rem 0.75rem;
                            border: 2px solid var(--color-tan);
                            border-radius: var(--border-radius-sm);
                            font-family: inherit;
                            background: var(--color-white);
                        ">
                            <option value=""><?php esc_html_e( 'All Villages', 'weardale-together' ); ?></option>
                            <?php if ( ! empty( $all_villages ) && ! is_wp_error( $all_villages ) ) : ?>
                                <?php foreach ( $all_villages as $term ) : ?>
                                    <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $current_village, $term->slug ); ?>>
                                        <?php echo esc_html( $term->name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Service Area Filter -->
                    <div>
                        <label for="dir-area" style="display:block; font-weight:700; font-size:0.85rem; text-transform:uppercase; margin-bottom:0.5rem; color:var(--color-forest);">
                            🌍 <?php esc_html_e( 'Service Reach', 'weardale-together' ); ?>
                        </label>
                        <select id="dir-area" name="area" style="
                            width: 100%;
                            padding: 0.6rem 0.75rem;
                            border: 2px solid var(--color-tan);
                            border-radius: var(--border-radius-sm);
                            font-family: inherit;
                            background: var(--color-white);
                        ">
                            <option value=""><?php esc_html_e( 'All Service Areas', 'weardale-together' ); ?></option>
                            <?php if ( ! empty( $all_areas ) && ! is_wp_error( $all_areas ) ) : ?>
                                <?php foreach ( $all_areas as $term ) : ?>
                                    <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $current_area, $term->slug ); ?>>
                                        <?php echo esc_html( $term->name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                </div>

                <!-- Bottom Filter Row (Verified & Accessibility) -->
                <div style="display: flex; gap: 2rem; align-items: center; flex-wrap: wrap; border-top: 1px solid var(--color-tan); padding-top: 1.25rem; justify-content: space-between;">
                    
                    <div style="display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap;">
                        <!-- Verified Filter Selector -->
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <label for="dir-verified" style="font-weight:700; font-size:0.85rem; text-transform:uppercase; color:var(--color-forest);">
                                <?php esc_html_e( 'Status:', 'weardale-together' ); ?>
                            </label>
                            <select id="dir-verified" name="verified" style="
                                padding: 0.4rem 0.5rem;
                                border: 1px solid var(--color-tan);
                                border-radius: var(--border-radius-sm);
                                font-family: inherit;
                                font-size: 0.85rem;
                                background: var(--color-white);
                            ">
                                <option value="all" <?php selected( $current_verified, 'all' ); ?>><?php esc_html_e( 'All Listings', 'weardale-together' ); ?></option>
                                <option value="1" <?php selected( $current_verified, '1' ); ?>><?php esc_html_e( 'Verified Only', 'weardale-together' ); ?></option>
                            </select>
                        </div>

                        <!-- Accessibility Filter Selector -->
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <label for="dir-access" style="font-weight:700; font-size:0.85rem; text-transform:uppercase; color:var(--color-forest);">
                                ♿ <?php esc_html_e( 'Access Needs:', 'weardale-together' ); ?>
                            </label>
                            <select id="dir-access" name="access" style="
                                padding: 0.4rem 0.5rem;
                                border: 1px solid var(--color-tan);
                                border-radius: var(--border-radius-sm);
                                font-family: inherit;
                                font-size: 0.85rem;
                                background: var(--color-white);
                            ">
                                <option value="" <?php selected( $current_access, '' ); ?>><?php esc_html_e( 'Any Accessibility', 'weardale-together' ); ?></option>
                                <option value="wheelchair" <?php selected( $current_access, 'wheelchair' ); ?>><?php esc_html_e( 'Wheelchair / Ramp Access', 'weardale-together' ); ?></option>
                                <option value="hearing" <?php selected( $current_access, 'hearing' ); ?>><?php esc_html_e( 'Hearing Induction Loop', 'weardale-together' ); ?></option>
                            </select>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div style="display: flex; gap: 0.75rem;">
                        <a href="<?php echo esc_url( get_post_type_archive_link( 'weardale_directory' ) ); ?>" class="btn btn-secondary" style="padding: 0.6rem 1.25rem; text-decoration: none; font-size:0.9rem; font-weight: 600;">
                            <?php esc_html_e( 'Clear', 'weardale-together' ); ?>
                        </a>
                        <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.75rem; font-size:0.9rem; font-weight: 700; cursor: pointer;">
                            🔎 <?php esc_html_e( 'Apply Filters', 'weardale-together' ); ?>
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </section>

    <!-- Main Results Listings Section -->
    <section class="section-padding" style="background-color: var(--color-white); min-height: 50vh;">
        <div class="container">
            
            <!-- Result status bar -->
            <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 2px solid var(--color-tan); padding-bottom: 0.75rem; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem;">
                <h2 class="font-display" style="font-size: 2.25rem; color: var(--color-forest); margin: 0; font-weight: normal;">
                    🌿 <?php esc_html_e( 'Directory Listings', 'weardale-together' ); ?>
                </h2>
                <span style="font-size: 1.05rem; font-family: var(--font-mono); color: var(--text-secondary); font-weight: 500;">
                    <?php 
                    printf( 
                        _n( '%d matching listing found', '%d matching listings found', $total_results, 'weardale-together' ), 
                        $total_results 
                    ); 
                    ?>
                </span>
            </div>

            <?php if ( $directory_query->have_posts() ) : ?>
                <!-- Archive Grid -->
                <div class="grid grid-3" style="margin-bottom: 4rem;">
                    <?php while ( $directory_query->have_posts() ) : $directory_query->the_post(); ?>
                        <?php get_template_part( 'template-parts/directory/card' ); ?>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>

                <!-- Accessible Pagination -->
                <?php if ( $total_pages > 1 ) : ?>
                    <nav class="pagination-navigation" aria-label="<?php esc_attr_e( 'Directory pagination', 'weardale-together' ); ?>">
                        <div class="pagination" style="display: flex; gap: 0.5rem; justify-content: center; margin-top: 3rem;">
                            
                            <?php if ( $paged > 1 ) : ?>
                                <a href="<?php echo esc_url( add_query_arg( 'dir_page', $paged - 1 ) ); ?>" class="btn btn-secondary" style="padding: 0.4rem 1rem; text-decoration: none; border: 1px solid var(--color-tan); font-weight: 600;" aria-label="Previous Page">
                                    <?php esc_html_e( '« Previous', 'weardale-together' ); ?>
                                </a>
                            <?php endif; ?>

                            <?php for ( $p = 1; $p <= $total_pages; $p++ ) : ?>
                                <?php 
                                $is_current = ( $p === $paged );
                                $active_style = $is_current 
                                    ? 'background-color: var(--color-forest); color: var(--color-cream); pointer-events: none;' 
                                    : 'background-color: var(--color-cream); color: var(--color-black);';
                                $page_url = add_query_arg( 'dir_page', $p );
                                ?>
                                <a href="<?php echo esc_url( $page_url ); ?>" class="btn" <?php if ( $is_current ) echo 'aria-current="page"'; ?> style="padding: 0.4rem 1rem; text-decoration: none; border-radius: var(--border-radius-sm); border: 1px solid var(--color-tan); font-weight: 600; <?php echo $active_style; ?>">
                                    <?php echo $p; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ( $paged < $total_pages ) : ?>
                                <a href="<?php echo esc_url( add_query_arg( 'dir_page', $paged + 1 ) ); ?>" class="btn btn-secondary" style="padding: 0.4rem 1rem; text-decoration: none; border: 1px solid var(--color-tan); font-weight: 600;" aria-label="Next Page">
                                    <?php esc_html_e( 'Next »', 'weardale-together' ); ?>
                                </a>
                            <?php endif; ?>

                        </div>
                    </nav>
                <?php endif; ?>

            <?php else : ?>
                <!-- Empty State -->
                <?php get_template_part( 'template-parts/directory/empty-state' ); ?>
            <?php endif; ?>

        </div>
    </section>

</main>

<?php
get_footer();
