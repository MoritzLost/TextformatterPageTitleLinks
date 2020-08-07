<?php
namespace Processwire;

class TextformatterPageTitleLinksConfig extends ModuleConfig
{
    public function getDefaults()
    {
        return [
            'auto_link_templates' => [],
            'minimum_length' => 0,
            'include_current_page' => false,
            'include_hidden_pages' => false,
            'attributes' => '',
            'html_tag' => 'a',
            'disable_href' => false,
            'disable_viewable_check' => false,
            'case_insensitive_match' => false,
            'force_case_sensitive_query' => false,
            'same_title_order' => 'MIN',
        ];
    }

    public function getInputFields()
    {
        $inputfields = parent::getInputfields();

        // ASM select for templates
        $asm = wire()->modules->get('InputfieldAsmSelect');
        $asm->name = 'auto_link_templates';
        $asm->label = $this->_('Templates to search for matching titles');
        $asm->setAsmSelectOption('sortable', false);
        $asm->columnWidth = 25;
        $asm->collapsed = Inputfield::collapsedNever;

        // add all non-system templates
        foreach ($this->templates as $template) {
            if (!($template->flags & Template::flagSystem)) {
                $template_name = $template->label ? "{$template->label} ({$template->name})" : $template->name;
                $asm->addOption($template->id, $template_name);
            }
        }

        // minimum length for linkable titles
        $minlen = wire()->modules->get('InputfieldInteger');
        $minlen->name = 'minimum_length';
        $minlen->label = $this->_('Minimum length for linkable titles');
        $minlen->attr('min', '0');
        $minlen->inputType = 'number';
        $minlen->columnWidth = 25;
        $minlen->collapsed = Inputfield::collapsedNever;

        // Checkbox to include current page
        $check = wire()->modules->get('InputfieldCheckbox');
        $check->name = 'include_current_page';
        $check->label = $this->_('Include the current page?');
        $check->label2 = $this->_('The title of the current page may link to itself');
        $check->columnWidth = 25;
        $check->collapsed = Inputfield::collapsedNever;

        // Checkbox to include hidden pages
        $hidden = wire()->modules->get('InputfieldCheckbox');
        $hidden->name = 'include_hidden_pages';
        $hidden->label = $this->_('Include hidden pages?');
        $hidden->label2 = $this->_('Titles of hidden pages may be linked');
        $hidden->columnWidth = 25;
        $hidden->collapsed = Inputfield::collapsedNever;

        // multi-line field for arbitrary attributes for the link
        $attributes = wire()->modules->get('InputfieldTextarea');
        $attributes->name = 'add_attributes';
        $attributes->label = $this->_('Additional attributes for the HTML tag');
        $attributes->description = $this->_("Specify each attribute on one line in the following format:\n`attribute=value` (without quotes around the value)\nThe values are parsed by [\$page->getText](https://processwire.com/api/ref/page/get-text/), so you can use replacement patterns with the link target page. For example:\n`class=automatic-link template-{template}`\n`title=Go to {template.label}: {title}`");
        $attributes->notes = $this->_("The `href` attribute is included automatically, unless disabled using the option below.");
        $attributes->placeholder = $this->_("class=auto-link template-{template}\ntitle=Go to {template.label}: {title}");
        $attributes->collapsed = Inputfield::collapsedNo;

        // option to change the html tag used to markup titles
        $html_tag = wire()->modules->get('InputfieldText');
        $html_tag->name = 'html_tag';
        $html_tag->label = $this->_('Change the HTML tag of the link element');
        $html_tag->description = $this->_('Use this to wrap titles in something other than `<a>` tags. Possible values include `span`, `mark`, `em`, `strong` or the name of any other inline HTML element.');
        $html_tag->notes = $this->_('If you use something other than regular `<a>` tags, you may want to disable the automatic `href` attribute.');
        $html_tag->placeholder = 'a';
        $html_tag->columnWidth = 33;
        $html_tag->collapsed = Inputfield::collapsedNever;

        // disable the automatically added href attribute
        $disable_href = wire()->modules->get('InputfieldCheckbox');
        $disable_href->name = 'disable_href';
        $disable_href->label = $this->_('Disable automatic href attribute?');
        $disable_href->label2 = $this->_("Don't include an `href` attribute in markup by default");
        $disable_href->description = $this->_('By default, the module automatically includes an `href` attribute in the generated link. But if you want to markup your titles with a different element, this may not be what you want, so you can disable it here.');
        $disable_href->columnWidth = 33;
        $disable_href->collapsed = Inputfield::collapsedNever;

        // disable the automatic $page->viewable() check
        $disable_viewable_check = wire()->modules->get('InputfieldCheckbox');
        $disable_viewable_check->name = 'disable_viewable_check';
        $disable_viewable_check->label = $this->_('Disable automatic visibility check?');
        $disable_viewable_check->label2 = $this->_("Don't check if a page is viewable before generating a link to it");
        $disable_viewable_check->description = $this->_("By default, the module will not generate a link to a page if it isn't viewable (using [\$page->viewable()](https://processwire.com/api/ref/page-permissions/viewable/). Use this option to disable this check.");
        $disable_viewable_check->columnWidth = 33;
        $disable_viewable_check->collapsed = Inputfield::collapsedNever;

        // case insensitive search setting
        $insensitive = wire()->modules->get('InputfieldCheckbox');
        $insensitive->name = 'case_insensitive_match';
        $insensitive->label = $this->_('Use case insensitive mode?');
        $insensitive->description = $this->_('By default, the module performs a case sensitive search and replace, so only exact title matches are linked. If you want the word `apple` (lowercase) in your text field to be linked to the page `Apple` (uppercase) as well, use this option.');
        $insensitive->columnWidth = 33;
        $insensitive->collapsed = Inputfield::collapsedNever;

        // force case sensitive database queries for non-case-sensitive collations
        $precision = wire()->modules->get('InputfieldCheckbox');
        $precision->name = 'force_case_sensitive_query';
        $precision->label = $this->_('Force case sensitive database query for title retrieval?');
        $precision->description = $this->_('Use this if your database uses a non case-sensitive collation (`_ci` suffix) and you have titles that are identical but for their casing (`apple` and `Apple`). Note that in this case, you may want to turn off case-insensitive mode.');
        $precision->notes = $this->_('**Warning: Experimental feature!**');
        $precision->columnWidth = 33;
        $precision->collapsed = Inputfield::collapsedNever;

        // preference for duplicate titles
        $order = wire()->modules->get('InputfieldSelect');
        $order->name = 'same_title_order';
        $order->label = $this->_('Preference for duplicate page titles');
        $order->description = $this->_('If you have more than one linkable pages with the same title, do you want the oldest or newest page to get linked preferentially?');
        $order->addOption('MIN', 'Prefer oldest page');
        $order->addOption('MAX', 'Prefer newest page');
        $order->required = true;
        $order->columnWidth = 33;
        $order->collapsed = Inputfield::collapsedNever;

        // fieldset for markup and output settings
        $markup = wire()->modules->get('InputfieldFieldset');
        $markup->label = $this->_('Markup and output settings');
        $markup->collapsed = Inputfield::collapsedNo;
        $markup->add($html_tag);
        $markup->add($disable_href);
        $markup->add($disable_viewable_check);

        // fieldset for advanced query settings
        $advanced = wire()->modules->get('InputfieldFieldset');
        $advanced->label = $this->_('Advanced settings');
        $advanced->collapsed = Inputfield::collapsedNo;
        $advanced->add($insensitive);
        $advanced->add($precision);
        $advanced->add($order);

        // add the settings fields in order of importance
        $inputfields->add($asm);
        $inputfields->add($minlen);
        $inputfields->add($check);
        $inputfields->add($hidden);
        $inputfields->add($attributes);
        $inputfields->add($markup);
        $inputfields->add($advanced);
        return $inputfields;
    }
}
