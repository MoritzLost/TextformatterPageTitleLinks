# TextformatterPageTitleLinks

This is a Textformatter module for [ProcessWire](https://processwire.com/) that replaces any title of pages on your site with links to that page.

See below for features, installation, usage and configuration.

## Description

Textformatters are a type of ProcessWire that allow you to perform some automated formatting on any text field output when you access the field through the API. This module has a simple purpose: Whenever your text includes the title of another page on your site, the formatter automatically adds a link (an anchor tag) to that page. This is useful for quickly connecting multiple pages, and for SEO purposes. You can limit this functionality by template, so only pages using the specified templates will get automatically linked.

## Features

- Allows you to limit the automatic links by template.
- Only includes published & visible pages by default, with an option to include hidden pages.
- Automatically excludes the current page, with an option to change that behaviour.
- Allows you to configure the minimum title length for linked pages.
- Doesn't overwrite existing links, and detects most edge cases (titles inside other tag's attributes, titles inside existing links et c.).
- Supports multi-language sites. Titles will only be linked if a title in the current language is set.
- Can add configurable attributes to all automatically created links.
    - Includes the ability to use page fields as replacement patterns for attributes. For example, you can create CSS classes that include the name of the template of the linked page.
- Prefer oldest or newest page in the case of duplicate titles.
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

## Installation

This module is available in the [module directory here](https://modules.processwire.com/modules/textformatter-page-title-links/). You can download it through the backend using the classname `TextformatterPageTitleLinks`. You can also manually clone or download the repository into your `site/modules` directory and install it through the backend.

## Configuration

The module is configurable through the ProcessWire backend. After installation, you first have to set the templates you wish to be automatically linked. By default, no templates are selected, so this module will do nothing!

Additional settings:

- If you want to include hidden pages, activate the corresponding option.
- By default, the module will not create self-referential links (links to the page the field is on), this behaviour can be configured.
- You can add any number attributes you want to add to all automatically generated links through the settings as well. Check out the examples on the module configuration page to get started.
- You can set the minimum length for linked pages. Pages with shorter titles will never be linked.
- If you have multiple pages with the same name, you can tell the module to prefer to link to the oldest or the newest page.

## Usage

You can use the modul in two ways: automatic and manual.

### Automatic

ProcessWire textformatters can be added to any text(-area) field you want on the details tab of the field settings. This way, the textformatter will automatically be applied whenever you output that field in your template.

If you apply multiple formatters, mind the order. For example, if you apply the HTML entity encoder or a formatter that strips HTML tags, apply those *before* this one.

### Manual

Alternatively, you can manually use the formatter in your code (for example, if you only want it to apply to a field in certain places). Simply obtain an instance of the formatter through the ProcessWire API and use it's `format` method:

```php
// get the formatter
$formatter = wire('modules')->get('TextformatterPageTitleLinks');
// we'll apply it to the 'body' field
$body = $page->body;
// call the formatter
$formatter->format($body);
echo $body;
```

The method accepts it's parameter by reference, so passing in `$page->body` directly **won't work**, since you can't indirectly modify overloaded properties. Also, make sure not to echo the return value of the function; since it modifies the parameter it accepts by reference, it doesn't return anything.

```php
$formatter = wire('modules')->get('TextformatterPageTitleLinks');
// this will cause a warning, and it won't work
$formatter->format($page->body);

// this will echo nothing, since the format method returns nothing
$body = $page->body;
echo $formatter->format($body);
```

You can also use the `formatValue` method that does the work directly. In this case, make sure to pass in the current page, or the option to include/exclude the current page won't work correctly:

```php
$formatter->formatValue(wire('page'), new Field(), $body);
```

I don't recommend the second approach, as it's more code to write and easier to mess up.

## Changelog

You can find the changelog as messages in the annotated tags (see "Releases" tab). You can also read a better-formatted version in the [support thread for this module in the ProcessWire forums](https://processwire.com/talk/topic/20378-automatically-link-page-titles/?tab=comments#comment-179285).
