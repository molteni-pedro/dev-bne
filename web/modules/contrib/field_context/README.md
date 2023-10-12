# Field Context
Let's say that you have a content page with a certain taxonomy term,
and on that page you want a block showing all upcoming events with the
same tag.  You can do this with a Views Context Filter by passing that
tag through the url.  But what if you wanted all the content pages to do
this, and some have the same taxonomy term?  You can't reuse the same url.
This module will pull the field value from the content page and supply it
to the context filter in the view, with no need for url restrictions.
It doesn't even need to be the same field, as this module lets you pick from
what field to fetch regardless of what field the the value is being used in
for the filter.


## Instalation
Install by normal means.  As this is not yet in the drupal community it
cannot be fetched with composer; download the module to your
webroot/modules/custom folder and then enable either with drush or in
the site ui.

## Usage
In a View, add a context filter.  In that filter settings, under
"When the filter value is NOT available" select
"Provide default value" and from the dropdown choose an option provided by
this module.
- Field from route context: lets you pick the target source field, by first
picking a content type that holds it.  If this field is found in the node
from url route, its value is sent to this filter.

## Configuration
- Nothing adjustable at this time.

## Disclaimer
This module is still narrowly tested, with only the primary feature.
It is a work in progress, use at your own risk.
