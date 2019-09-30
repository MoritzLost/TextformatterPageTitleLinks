# TextformatterPageTitleLinks

This is a Textformatter module for the [ProcessWire CMF](https://processwire.com/) that replaces any title of pages on your site with links to that page.

## Table of Contents

- [Description](#description)
- [Features](#features)
    - [Caveats](#caveats)
- [Installation](#installation)
- [Configuration](#configuration)
    - [All settings](#all-settings)
- [Usage](#Usage)
    - [Automatic usage](#automatic-usage)
    - [Manual Usage](#manual-usage)
        - [Options for manual usage](#options-for-manual-usage)
- [Changelog](#changelog)

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
- Has options to switch between case sensitive and case insensitive modes, and force case sensitive behaviour even for case insensitive database collations.
- Allows you to overwrite the module configuration via the API to call the module with different settings for different requirements on the same page.

### Caveats

- This module uses regex, so if it's used on a site with many pages, it will have a significant performance impact! Make sure to cache the results.
- Since it's regex, it can never detect all edges cases with heavily nested HTML elements. Don't use this on a field with lots of custom HTML structures.

## Installation

This module is available in the [modules directory here](https://modules.processwire.com/modules/textformatter-page-title-links/). You can download it through the backend using the classname `TextformatterPageTitleLinks`. You can also manually clone or download the repository into your `site/modules` directory and install it through the backend.

## Configuration

The module is configurable through the ProcessWire backend. After installation, you first have to set the templates you wish to be automatically linked. By default, no templates are selected, so this module will do nothing!

On the module configuration page, you can set the options that will be used when using the module as a Textformatter through the field configuration. If you call the module manually in your template code, you can overwrite those options, see the section on manual usage below.

### All settings

- **Templates to search for matching titles:** Select all the templates whose pages you want to link automatically.
- **Minimum length for linkable titles:** You can set the minimum length for linked pages. Pages with shorter titles than the defined minimum will not be linked.
- **Include the current page:** By default, the module will not create self-referential links (links to the page the field is on), but you can include the current page with this option.
- **Include hidden pages:** Check this if you want to link to hidden pages as well.
- **Additional attributes for the anchor element (&lt;a&gt;):** You can set any number of attributes (for example, custom classes) you want to add to all automatically generated links through the settings as well. You can also use replacement patterns that get passed to [$page->getText()](https://processwire.com/api/ref/page/get-text/). The `href` attribute is added automatically. Check out the examples on the module configuration page to get started.
- **Use case insensitive mode:** Add the `i` flag to the regular expressions, so that titles are matched on a case insensitive basis.
- **Force case sensitive database query for title retrieval:** The database query groups by title, so if there are multiple pages with a similar title, only one is returned. For case insensitive database collations (`_ci` suffix), this means pages whose titles only differ by their casing or use of diacritics, only one row will be returned. Check this option to group by binary representation instead, this way you get all variations from the database.
- **Preference for duplicate page titles:** If you have multiple pages with the same name, you can tell the module to prefer to link to the oldest or the newest page.

## Usage

You can use the module in two ways: automatic and manual.

### Automatic usage

ProcessWire textformatters can be added to any text(-area) field you want on the details tab of the field settings. This way, the textformatter will automatically be applied whenever you output that field in your template.

If you apply multiple formatters, mind the order. For example, if you apply the HTML entity encoder or a formatter that strips HTML tags, apply those *before* this one.

### Manual usage

Alternatively, you can manually use the formatter in your code (for example, if you only want it to apply to a field in certain places). Manual usage allows you to overwrite any options set in the module configuration on a per call basis.

First, obtain an instance of the formatter through the ProcessWire API. Then, use one of the following methods:

- Use `TextformatterPageTitleLinks::format` if you simply want to call the module with the default options set in the module configuration.
- Use `TextformatterPageTitleLinks::formatWithOptions` if you want to call the module with custom options. See below for accepted options.

```php
// get the formatter
$formatter = wire('modules')->get('TextformatterPageTitleLinks');
// we'll apply it to the 'body' field
$body = $page->body;
// call the formatter
$formatter->format($body);
echo $body;
```

Both method accepts it's parameter by reference, so passing in `$page->body` directly **won't work**, since you can't indirectly modify overloaded properties. Also, make sure not to echo the return value of the function; since it modifies the parameter it accepts by reference, it doesn't return anything.

```php
$formatter = wire('modules')->get('TextformatterPageTitleLinks');
// this will cause a warning, and it won't work
$formatter->format($page->body);

// this will echo nothing, since the format method returns nothing
$body = $page->body;
echo $formatter->format($body);
```

With the `formatWithOptions` method, you can pass an associative array with options that will overwrite the defaults from the module configuration. You can also pass in a different page to be used as the 'current' page as the third argument.

```php
$formatter = wire('modules')->get('TextformatterPageTitleLinks');
$body = $page->body;
$formatter->formatWithOptions(
    $body,
    [
        // custom options go here
    ],
    $page // this is optional
);
echo $body;
```

#### Options for manual usage

The following table lists all the available options with their respective labels in the module configuration, the array key for the options array with which you can overwrite it, as well as the option type and allowed values. The module will use the global module configuration for any options you leave out. For explanations of what each option does, check the module configuration page and the section on configuration above.

<table>
    <thead>
        <tr>
            <th>Option name</th>
            <th>Array key</th>
            <th>Type</th>
            <th>Allowed values</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Templates to search for matching titles</td>
            <td><code>auto_link_templates</code></td>
            <td>array</td>
            <td>Array of template IDs, names or Template objects.</td>
        </tr>
        <tr>
            <td>Minimum length for linkable titles</td>
            <td><code>minimum_length</code></td>
            <td>int</td>
            <td>Any positive integer</td>
        </tr>
        <tr>
            <td>Include the current page?</td>
            <td><code>include_current_page</code></td>
            <td>bool</td>
            <td>true | false</td>
        </tr>
        <tr>
            <td>Include hidden pages?</td>
            <td><code>include_hidden_pages</code></td>
            <td>bool</td>
            <td>true | false</td>
        </tr>
        <tr>
            <td>Additional attributes for the anchor element (<code>&lt;a&gt;</code>)</td>
            <td><code>add_attributes</code></td>
            <td>string</td>
            <td>
                A multi-line string, each line containing either a standalone attribute, or an attribute name and value seperated by an equals sign. E.g.: <br>
                <code>title=Jump to {title} <br> class=autolink autolink-{template}</code>
            </td>
        </tr>
        </tr>
        <tr>
            <td>Use case insensitive mode?</td>
            <td><code>case_insensitive_match</code></td>
            <td>bool</td>
            <td>true | false</td>
        </tr>
        <tr>
            <td>Force case sensitive database query for title retrieval?</td>
            <td><code>force_case_sensitive_query</code></td>
            <td>bool</td>
            <td>true | false</td>
        </tr>
        <tr>
            <td>Preference for duplicate page titles</td>
            <td><code>same_title_order</code></td>
            <td>string</td>
            <td>"MIN" (older pages first) | "MAX" (newer pages first)</td>
        </tr>
    </tbody>
</table>

## Changelog

Since version 3.0.0, the changelog is maintained within the repository in [CHANGELOG.md](CHANGELOG.md).
