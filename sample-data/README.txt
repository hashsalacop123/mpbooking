Visayas sample court import

This package creates thirteen fictional pickleball-court listings across Cebu, Bohol, and Leyte in the Visayas. New listings are created as drafts. City-center map pins, example.com contact details, court rates, amenities, and schedules are demonstration data, not verified venue information.

Requirements
- The MatchPoint theme must be active so the service post type and genre taxonomy are registered.
- Advanced Custom Fields and WP-CLI must be available.

Import
1. Back up the live site and upload this sample-data directory to:
   wp-content/themes/matchpoint-template/sample-data/
2. For this Local site on macOS, run from the app/public directory:
   php -d mysqli.default_socket="$HOME/Library/Application Support/Local/run/UA5Vp4xHz/mysql/mysqld.sock" "$(command -v wp)" --no-color eval-file wp-content/themes/matchpoint-template/sample-data/import-visayas-courts.php
   Local's database uses a site-specific MySQL socket. Running plain "wp eval-file ..." from Homebrew PHP may try /tmp/mysql.sock instead and fail with "Access denied". If Local recreates the site runtime, replace UA5Vp4xHz with this site's ID from ~/Library/Application Support/Local/sites.json.
3. On a live host with its normal WP-CLI database connection, run from the WordPress installation directory:
   wp eval-file wp-content/themes/matchpoint-template/sample-data/import-visayas-courts.php
4. In WordPress Admin, open Services and review the drafts. Replace every sample field with verified venue data before publishing.

The script is repeat-safe: running it again updates its existing sample listings and creates any missing ones without duplicating them. It does not change an existing listing's publication status. Each listing is tagged only as `pickleball-court`, has one featured image, a four-photo pickleball gallery in the Media Library, two sample pickleball courts, and daily bookable availability from 6:00 AM to 10:00 PM for the next 180 days. It also sets Visayas map coordinates, amenities, and sample contact details. Existing sample listings are updated so rerunning the importer upgrades the previous tennis examples as pickleball-only listings.

Images
The sample images show pickleball courts and players. They are public files from Wikimedia Commons, not photos of the fictional Visayas venues. Image credit and license are also saved in each Media Library attachment's caption.
- Aerial Pickleball Courts by TheVillagesFL: https://commons.wikimedia.org/wiki/File:Aerial_Pickleball_Courts.jpg (CC BY-SA 4.0)
- Pickleball Courts 1-3 by GA Kevin: https://commons.wikimedia.org/wiki/File:Harry_B._Anderson_Tennis_Center_-_Pickleball_Courts_1-3.jpg (CC0)
- Pickleball Pros by Picklerpeej: https://commons.wikimedia.org/wiki/File:Pickleball_Pros.jpg (CC BY-SA 4.0)
- Pickleball Courts 10-12 by GA Kevin: https://commons.wikimedia.org/wiki/File:Harry_B._Anderson_Tennis_Center_-_Pickleball_Courts_10-12.jpg (CC0)
