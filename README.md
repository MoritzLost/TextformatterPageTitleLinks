# TextformatterPageTitleLinks

This is a Textformatter module for [ProcessWire](https://processwire.com/) that replaces any title of pages on your site with links to that page.

## Description

Textformatters are a type of ProcessWire that allow you to perform some automated formatting on any text field output when you access the field through the API. This module has a simple purpose: Whenever your text includes the title of another page on your site, the formatter automatically adds a link (an anchor tag) to that page. This is useful for quickly connecting multiple pages, and for SEO purposes. You can limit this functionality by template, so only pages using the specified templates will get automatically linked.

## Features

- Allows you to limit the automatic links by template.
- Only includes published & visible pages by default, with an option to include hidden pages.
- Automatically excludes the current page, with an option to change that behaviour.
- Doesn't overwrite existing links, and detects most edge cases (titles inside other tag's attributes, titles inside existing links et c.).
- Supports multi-language sites. Titles will only be linked if a title in the current language is set.
- Can add configurable attributes to all automatically created links.
    - Includes the ability to use page fields as replacement patterns for attributes. For example, you can create CSS classes that include the name of the template of the linked page.
- Queries the database directly for improved performance.
    - Only queries the database once per request & language.

### Caveats

- This uses regex, so if it's used on a site with many pages, this will have a significant performance impact! Make sure to cache the results.
- Since it's regex, it can never detect all edges cases with heavily nested HTML elements. Don't use this on a field with lots of custom HTML structures.
- If there are multiple pages with the same title, a random one will get linked (determined by MySQL, as the query uses `GROUP BY` to prevent duplicates).
- If the page has no title in the current language, it won't be linked (this could be seen as a feature as well ...).

### Planned features

- "Minimum length" setting for linked page titles.
- Make multi-language behaviour configurable.
- Inbuilt caching per field and page.

## Installation & usage

This module is available in the [module directory here](https://modules.processwire.com/modules/textformatter-page-title-links/). You can download it through the backend using the classname `TextformatterPageTitleLinks`. You can also manually clone or download the repository into your `site/modules` directory and install it through the backend.

After that, simply add the textformatter to any fields you want to via the field settings.

## Configuration

The module is configurable through the ProcessWire backend. After installation, you first have to set the templates you wish to be automatically linked. By default, no templates are selected, so this module will do nothing!

Additional settings:

- If you want to include hidden pages, activate the corresponding option.
- By default, the module will not create self-referential links (links to the page the field is on), this behaviour can be configured.
- You can add any number attributes you want to add to all automatically generated links through the settings as well. Check out the examples on the module configuration page to get started.
