# TextformatterPageTitleLinks

**WARNING**: This module is still in development and should be considered **unstable**.

This is a Textformatter module for [ProcessWire](https://processwire.com/) that replaces any title of pages on your site with links to that page.

## Description

Textformatters are a type of ProcessWire that allow you to perform some automated formatting on any text field output when you access the field through the API. This module has a simple purpose: Whenever your text includes the title of another page on your site, the formatter automatically adds a link (an anchor tag) to that page. This is useful for quickly connecting multiple pages, and for SEO purposes. You can limit this functionality by template, so only pages using the specified templates will get automatically linked.

## Features

- Queries the database directly to get page titles for performance.
- Supports multi-language sites.
- Only queries the database once per request.
- Allows you to set which templates will get automatically linked.
- Has an option to exclude the current page.

### Caveats

- This uses regex, so if for a page with many pages, this will have a significant performance impact! Make sure to cache the results.
- The formatter does not check if the title is already placed within an anchor tag (this is non-trivial as the replacement is done using regex, which is not great at backtracking - let me know if you have any suggestions).
- If there are multiple pages with the same title, a random one will get linked (determined by MySQL, as the query uses `GROUP BY` to prevent duplicates).
- If the page has no title in the current language, it won't be linked (this could be seen as a feature as well).

### Planned features

- Add a configurable CSS class to all automatically created links.
- Inbuilt caching per field and page.

## Installation & usage

At the moment, this plugin is not available in the ProcessWire module directory, so you have to manually clone or download the repository it into your `site/modules` directory and install it through the backend.

After that, simply add the textformatter to any fields you want to via the field settings.

## Settings

The module is configurable through the ProcessWire backend. After installation, you first have to set the templates you wish to be automatically linked. By default, no templates are selected, so this module will do nothing!

You can also choose to exclude the current page, so you won't end up with a link to the page you are on. This behaviour is turned off by default.
