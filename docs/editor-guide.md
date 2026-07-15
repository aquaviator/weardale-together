# Weardale Together CIC — Content Editor Guide

Welcome to the Weardale Together content team! This guide will help you manage information across all of our community strands without writing any code.

---

## 1. Managing standard pages
To edit pages like **Root & Branch Café**, **Young People**, or **Roots & Shoots**:
1. Log in to the WordPress Dashboard (`/wp-admin/`).
2. Click **Pages** in the left menu.
3. Hover over the page you want to update and click **Edit**.
4. Use the WordPress Block Editor (Gutenberg) to modify headings, text paragraphs, or upload images.
5. Click **Update** in the top right to make your changes live.

---

## 2. Publishing news and blog posts
Our news area is the record of our grassroots stories. To publish an update:
1. Go to **Posts > Add New** in the sidebar.
2. Enter your story title and write the copy.
3. In the right sidebar panel:
   * Select a **Featured Image** (this displays as a preview on the homepage news grid).
   * In the **Strands** taxonomy box, check the strand this news relates to (e.g., check `Café` to style the page with warm sandy tones!).
4. Click **Publish**.

---

## 3. Adding Weardale Together Events
We manage our own events through a custom dashboard. To create a new event listing:
1. Click **WT Events** in the sidebar.
2. Click **Add New Event**.
3. Input the title (e.g., *Summer Clay Crafting*) and describe the activity in the editor.
4. Scroll down to the **Weardale Event Details** panel and fill out:
   * **Schedule & Timing**:
     * **Start Date & End Date**: Choose dates. Events can span multiple days!
     * **All-Day Event**: Toggle this if the event runs all day (this hides the specific hours text).
     * **Time Text**: e.g., `10:00 AM - 1:00 PM`.
   * **Location & Map**:
     * **Venue Name**: e.g., `Stanhope Hub Community Garden`.
     * **Full Location Address**: e.g., `12 Front Street, Stanhope, DL13 2TY`.
     * **Google Map URL**: Paste a map share link. A dynamic "Get Directions" button will automatically render on the single event page!
   * **Audience & Requirements**:
     * **Target Audience**: e.g., `Isolated Seniors`, `Families with toddlers`.
     * **Age Guidance**: e.g., `Under 12s must be accompanied`.
     * **Accessibility Information**: e.g., `Wheelchair ramp available. Hearing loop active.`
   * **Registration & Bookings**:
     * **Booking Status**: Choose from `No Booking Required`, `Booking Recommended`, `Booking Required`, `Sold Out`, or `Cancelled`. Elegant colored badges will render on the card and detail page!
     * **Booking URL**: Link to Eventbrite or ticketing system.
     * **Booking Instructions**: Short notes (e.g., *Register at the desk on arrival*).
   * **Organiser Information**:
     * **Organiser Name**: e.g., `Cheryl Thompson`.
     * **Organiser Contact**: Phone/email for resident enquiries.
5. Set a **Featured Image** (this will display nicely at the top of the event card and single page).
6. Check the associated strand taxonomy (e.g. `Creative Arts`) to style the page header and accents!
7. Click **Publish**. It will automatically appear under **What's Happening This Week** on the homepage and on the main `/whats-on/` directory page!

---

## 4. Setting Up Menus & Navigation
To rearrange links in the main header or footer navigation:
1. Navigate to **Appearance > Menus** (or use **Appearance > Customize**).
2. Select the menu you want to edit (e.g., *Primary Navigation*).
3. Drag and drop links to rearrange them, or check pages and click **Add to Menu**.
4. Click **Save Menu**.

---

## 5. Integrating Form Embeds (Mailchimp / Contact Forms)
* **Mailchimp Newsletter**:
  *(Please Note: The default homepage newsletter subscription input is a visual placeholder awaiting formal Mailchimp integration. It is not operationally live in the default baseline setup).*
  To wire it to a live list or replace it with your active sign-up form, copy your embedded HTML form code from your Mailchimp account dashboard. In WordPress, navigate to **Appearance > Widgets**, locate the **Footer Column 4** or **Newsletter Widget Area**, add a **Custom HTML** block, and paste your form code.
* **Map embeds**:
  Go to Google Maps, search for your Stanhope location, click **Share > Embed a map**, copy the `<iframe>` code, and paste it into a **Custom HTML** block on your **Contact Us** page.
