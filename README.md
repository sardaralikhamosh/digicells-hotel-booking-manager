# Digicells Hotel Booking Management Plugin

**Contributors:** Sardar Ali Khamosh (digicells)  
**Tags:** hotel booking, guest house, property rental, booking system, wordpress plugin, ajax booking  
**Requires at least:** 5.0  
**Tested up to:** 6.4  
**Stable tag:** 1.0.0  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

A professional, modular WordPress plugin to manage hotel, guest house, and private property bookings with email notifications, admin interface, and shortcodes.

## Description

**Digicells Hotel Booking Management Plugin** is a powerful yet easy-to-use booking solution for property owners and managers. It supports three distinct listing types:

- **Hotels** – with star ratings, room types, amenities, and owner email alerts.
- **Guest Houses** – family, budget, or luxury guest houses with shared/private rooms.
- **Private Properties** – houses, apartments, villas, cottages for daily/weekly/monthly rent.

### Main Features

- **Custom Post Types & Taxonomies** – dedicated post types for each property type, with custom taxonomies (locations, categories, amenities).
- **Rich Meta Boxes** – all relevant fields (price, discount, capacity, check-in/out, owner details, map, room info, amenities, etc.).
- **AJAX Booking Form** – modal popup with validation, loading spinner, and nonce security.
- **Email Notifications** – sends beautifully formatted HTML emails to both admin and property owner on every booking.
- **Admin Booking Management** – view, approve, reject, change status (pending, confirmed, rejected, completed), search, and export bookings.
- **Shortcodes** – display listings, search forms anywhere via shortcodes.
- **Grid / List Views** – toggle between layouts on the frontend.
- **AJAX Search & Filter** – search by location and property type without page reload.
- **Responsive Design** – looks great on all devices (mobile, tablet, desktop).
- **Secure** – nonce verification, CSRF protection, sanitization, escaping.
- **SEO Friendly** – semantic HTML, schema markup ready.

## Installation

1. **Upload the plugin**
   - Download the plugin ZIP file.
   - Go to **WordPress Admin → Plugins → Add New → Upload Plugin**.
   - Choose the ZIP file and click **Install Now**.

2. **Activate the plugin** – after installation, click **Activate**.

3. **Set up Permalinks** – go to **Settings → Permalinks** and click **Save Changes** (this flushes rewrite rules for custom post types).

4. **Start Adding Listings**
   - Navigate to **Hotel Booking → Add New Hotel** (or Guest House / Private Property).
   - Fill in all meta boxes (basic info, details, location, owner, amenities, rooms).
   - Publish the listing.

5. **Insert Shortcodes** into any page or post (see below).

## Shortcodes

| Shortcode | Description |
|-----------|-------------|
| `[dghbm_hotel_listing per_page="12" view="grid"]` | Displays hotels in grid/list view. |
| `[dghbm_guest_house_listing per_page="12" view="grid"]` | Displays guest houses. |
| `[dghbm_property_listing per_page="12" view="grid"]` | Displays private properties. |
| `[dghbm_search_form]` | Renders a search form (location + property type). |

**Attributes:**
- `per_page` – number of items per page (default 12).
- `view` – `grid` or `list` (default grid).

## Usage

### Frontend Workflow

1. Visitors browse listings via shortcode pages.
2. They click **View Details** to see single property page (gallery, full details, map, amenities).
3. Click **Book Now** button (sticky on single page or on listing cards).
4. A modal popup appears with booking form.
5. User fills in details (name, email, dates, guests, rooms, special requests).
6. On submit, the booking is saved in the database and emails are sent to admin & owner.
7. User sees success message.

### Admin Workflow

1. Go to **Hotel Booking → All Bookings**.
2. View all booking requests with customer details, dates, status.
3. Change status (Pending → Confirmed → Completed / Rejected).
4. Search by name, email, or booking ID.
5. Export bookings to CSV (optional – implement if needed).

## Customization

- **CSS / JS** – plugin loads its own styles (`assets/css/frontend.css`) and scripts (`assets/js/frontend.js`). You can override them in your theme or via custom CSS.
- **Templates** – copy any template from `/templates/` folder into your theme (e.g., `wp-content/your-theme/dghbm/archive-hotel.php`) to override.
- **Email Template** – modify `includes/class-email-handler.php` to change email HTML.

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- MySQL 5.6 or higher

## Frequently Asked Questions

**Q: Does this plugin support multiple currencies?**  
A: Yes, you can set a currency per listing in the meta box (e.g., PKR, USD, EUR). The frontend will display that currency symbol.

**Q: Can I set different prices for different room types?**  
A: Currently the plugin uses a base price per night for the property. Extended room-based pricing can be added as a premium feature.

**Q: Where are booking emails sent?**  
A: Admin email (from WordPress settings) and the owner email specified in the listing's meta box. If owner email is empty, only admin receives.

**Q: Is the plugin GDPR compliant?**  
A: The plugin does not store cookies or track visitors. It only stores booking data when user explicitly submits the form. You should add a consent checkbox if needed.

## Changelog

### 1.0.0
- Initial release.
- Support for Hotels, Guest Houses, Private Properties.
- AJAX booking form, email notifications, admin booking management.
- Shortcodes and responsive frontend.

## Credits

- Developed by **Sardar Ali Khamosh (Digicells)** – [digicellinternational.github.io](https://digicellinternational.github.io/)
- Icons: Emoji / Unicode (no external dependencies)
- Font: Poppins (Google Fonts)

## License

This plugin is licensed under the **GPLv2 or later** – you are free to modify and distribute it under the same license.

---

**Support:** For issues or feature requests, please open a ticket on the [GitHub repository](https://github.com/sardaralikhamosh/digicells-hotel-booking-manager).

**Demo:** Not available yet – try it on your local WordPress installation.
