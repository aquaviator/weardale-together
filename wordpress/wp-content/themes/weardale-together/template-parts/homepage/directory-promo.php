<?php
/**
 * Template part for the Community Directory Promo banner on the homepage.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.2.0
 */

$directory_url = get_post_type_archive_link( 'weardale_directory' ) ?: home_url( '/directory/' );
?>

<section id="homepage-directory-promo" class="section-padding" style="background-color: var(--color-white); border-bottom: 1px solid var(--color-tan);">
    <div class="container">
        <div style="background-color: var(--color-cream); border: 2px solid var(--color-strand-shoots); border-radius: var(--border-radius-md); padding: 4rem 3rem; display: grid; grid-template-columns: 1.25fr 1fr; gap: 4rem; align-items: center; box-shadow: 0 4px 15px rgba(59, 92, 58, 0.05);">
            
            <!-- Left block: Content & CTA -->
            <div>
                <span class="badge" style="background-color: var(--color-forest); color: var(--color-cream); margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; padding: 0.25rem 0.75rem; border-radius: var(--border-radius-pill); font-size: 0.85rem; display: inline-block;">
                    <?php esc_html_e( 'Local Connections', 'weardale-together' ); ?>
                </span>
                <h2 class="font-display" style="font-family: var(--font-headings); font-size: 2.75rem; color: var(--color-forest); margin-bottom: 1.5rem; line-height: 1.1; font-weight: normal;">
                    <?php esc_html_e( 'Explore the Community Directory', 'weardale-together' ); ?>
                </h2>
                <p style="color: var(--text-secondary); line-height: 1.6; font-size: 1.1rem; margin-bottom: 2rem;">
                    <?php esc_html_e( 'Find local groups, support services, community transport routes, village halls, and local independent businesses across Weardale and the North Pennines. Connect with volunteers and activities near you.', 'weardale-together' ); ?>
                </p>
                <a href="<?php echo esc_url( $directory_url ); ?>" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1.05rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <span>🔎</span> <?php esc_html_e( 'Search the Directory', 'weardale-together' ); ?>
                </a>
            </div>

            <!-- Right block: Aesthetic illustrative list of categories -->
            <div style="background-color: var(--color-white); border: 1px solid var(--color-tan); padding: 2rem; border-radius: var(--border-radius-sm); box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                <h3 style="font-family: var(--font-body); font-weight: 700; margin-bottom: 1.5rem; font-size: 1.15rem; color: var(--color-forest); text-transform: uppercase; letter-spacing: 0.05em;">
                    <?php esc_html_e( 'What you can find:', 'weardale-together' ); ?>
                </h3>
                
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1rem;">
                    <li style="display: flex; align-items: center; gap: 0.75rem; font-size: 1.05rem; color: var(--text-primary);">
                        <span style="font-size: 1.5rem; line-height: 1;">🏛️</span>
                        <span><strong><?php esc_html_e( 'Community Facilities & Halls', 'weardale-together' ); ?></strong></span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.75rem; font-size: 1.05rem; color: var(--text-primary);">
                        <span style="font-size: 1.5rem; line-height: 1;">🚌</span>
                        <span><strong><?php esc_html_e( 'Transport & Essential Connections', 'weardale-together' ); ?></strong></span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.75rem; font-size: 1.05rem; color: var(--text-primary);">
                        <span style="font-size: 1.5rem; line-height: 1;">🎨</span>
                        <span><strong><?php esc_html_e( 'Creative Arts, Food & Social Groups', 'weardale-together' ); ?></strong></span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.75rem; font-size: 1.05rem; color: var(--text-primary);">
                        <span style="font-size: 1.5rem; line-height: 1;">🤝</span>
                        <span><strong><?php esc_html_e( 'Support Services & Volunteering', 'weardale-together' ); ?></strong></span>
                    </li>
                </ul>

                <div style="border-top: 1px solid var(--color-tan); margin-top: 1.5rem; padding-top: 1.25rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: var(--text-light);">
                    <span style="color: #16a34a; font-size: 1.25rem;">✓</span>
                    <span><em><?php esc_html_e( 'All listings are community-reviewed and kept up to date.', 'weardale-together' ); ?></em></span>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
@media (max-width: 992px) {
    #homepage-directory-promo > .container > div {
        grid-template-columns: 1fr !important;
        gap: 3rem !important;
        padding: 3rem 1.5rem !important;
    }
}
</style>
