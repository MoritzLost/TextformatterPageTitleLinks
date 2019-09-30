# Changelog

## [3.0.0] - 2019-09-30

- **Feature:** It's now possible to invoke the module with custom options that overwrite the module configuration selectively. For example, you can now automatically link pages of different templates in different contexts.
    - See the README for documentation of all available options and option formats.
    - For now, all built-in caching of the database query and other methods has been removed, because the cached values may not be correct if the module is called with a different set of options. However, the performance impact is on the scale of a couple of miliseconds at the most.
- **Feature:** Added an option for a case insensitive mode, so the word _apple_ (lowercase) in your text will link to the page _Apple_ (uppercase).
- **Feature:** Added an option to force case sensitive database queries even for case insensitive database collations. This way you, can accurately link pages that differ only in their casing or use of diacritics (_apple_ vs _Apple_ vs _Äpple_).
- **Bug fix:** The module will no longer link pages that are inactive in the current language.
- **Bug fix:** Fixed an issue where square brackets inside a page title could break the regular expressions.
- **Refactor:** Reorganized the module configuration page to be more easy to use.
    - Settings are now sorted by their importantance to the module behaviour.
    - Less important settings are now hidden in a collapsed fieldset by default.
- **Docs:** Updated all inline docblocks and documentation, and simplified some logic to be easier to read.
- **Docs:** Updated the README with the new functionality, including an expanded section on the manual usage detailled instructions for all the available options.

## [2.1.0] - 2019-02-03

- **Feature:** Prefer the oldest or the most recent page for automatic linking, in the case that two or more pages have the same title. Defaults to linking the oldest page. This is technically backwards-incompatible, as previously the selection of the linked page was pseudo-random in this case, but it's a pretty niche case.
- **Feature:** Set the minimum length for linkable pages. If you have pages with very short titles that appear commonly in different places, you might not want those linked every time. For example, if you set the minimum length to 5 characters, pages whose title is only four characters or shorter will never be linked.
    - Tiny caveat: The length detection doesn't work correctly with emoji (or probably any 4-byte UTF8 character in the title). No idea why, the query uses CHAR_LENGTH which is supposed to accurately report multi-byte string length. If you know more about this, let me know!
- **Bugfix:** Version 2.0.0 changed the query in a way that would cause errors on older versions of MySQL, this is now fixed. Previous version might have thrown errors depending on the MySQL configuration (specifically, if ONLY_FULL_GROUP_BY was active), this should also  not happen anymore.
- **Miscellaneous**
    - I expanded the README, it now has a section about how to use the formatter manually. There are a couples of gotchas to this, so it could be helpful to beginners.
    - The attribute settings are now parsed using preg_split instead of explode. This should prevent potential issues with windows-style CRLF linebreaks.

## [2.0.0] - 2019-01-17

### Backward incompatible changes

- Version 2.0.0 changes how titles are selected from the database and matched in text fields (see below for details). This is why this gets a major version number bump.
- The previous CSS classes setting has been removed in favour of the more general attributes setting (see below).

### New Features

- **Feature:** Better HTML detection and edge case prevention
    - The module got much better at checking the surroundings of a title to make sure it doesn't produce invalid HTML. In particular, it will now detect the following scenarios leave those as-is:
        - If a page title is already inside anchor-tags.
        - If a page title is inside attributes of other tags (for example, the description in a title-attribute.
    - That said, the module still uses regex to find and replace titles, so it will never be able to cover all scenarios. For example, it will not detect if a page title is inside other tags that are not anchor tags (e.g. spans) which are in turn inside another anchor. So this module shouldn't be used on text fields where you need lots of custom HTML and intricate HTML structures.
- **Feature:** Configure arbitrary attributes to include in the created links
    - You can now add attributes such as title, class, target, custom data-* attributes or anything you want to the links. This includes standalone attributes (without a value).
    - For the attribute values, you can use replacement patterns that are passed to [$page->getText()](https://processwire.com/api/ref/page/get-text/). For example, you can annotate the links with descriptive titles for accessibility or generate a class absed on the template of the linked page. Check out the examples on the module settings page!
- **Feature:** Longer titles get linked preferrentially
    - If the title of one page is contained within the title of another page, the page with the longer title will get linked, not the shorter one. For example, if you have two pages titled "Content" and "Content Management System", the entire phrase "Content Management System" will be linked to the corresponding page. The page "Content" will not be linked in that instance. Of course, if in another part of the text you only have the word "Content", it will still be linked to the page as usual.
    - This change was made possible by the detection of the edge cases mentioned above. Previously, the module would create the shorter links first to avoid creating nested links.
    - The reason for this change is that longer titles tend to be more relevant for internal links. For example, in the previous case, the word "Content" could appear in many contexts not necessarily related to the page "Content", whereas the phrase "Content Management System" is much more specific. I may make this behaviour optional if there is a usecase for it. If you need this for some reason, let me know.
- **Feature:** Unicode support
    - Now works with diacritics and emoji in the titles. To be fair, it was mostly working already, but now the regex includes the u Flag to explicitly turn off UTF-8 compatible functionality.
    - There's still an edge case regarding diacritics (try creating two pages "Apfel" and "Äpfel" and you'll see it ...). I don't have a fix for it at this point, but it only occurs in very specific scenarios, so for now it'll do.

### Bug fixes

- Correctly detects multilanguage sites. Previously it would fail on a multilanguage site that used a non-multilangue title field.
- Adjusted the main database query to not produce warnings when fed directly to MySQL in certain configurations.

## [1.0.1] - 2018-11-22

- Minor bugfixes & cleanup

## [1.0.0] - 2018-11-18

- Initial release
