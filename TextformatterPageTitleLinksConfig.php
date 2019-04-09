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
            'same_title_order' => 'MIN',
            'force_case_sensitive_query' => false,
            'case_insensitive_match' => false,
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
        $check->columnWidth = 25;
        $check->collapsed = Inputfield::collapsedNever;

        // Checkbox to include hidden pages
        $hidden = wire()->modules->get('InputfieldCheckbox');
        $hidden->name = 'include_hidden_pages';
        $hidden->label = $this->_('Include hidden pages?');
        $hidden->columnWidth = 25;
        $hidden->collapsed = Inputfield::collapsedNever;

        // multi-line field for arbitrary attributes for the link
        $attributes = wire()->modules->get('InputfieldTextarea');
        $attributes->name = 'add_attributes';
        $attributes->label = $this->_('Additional attributes for the anchor element (&lt;a&gt;).');
        $attributes->description = $this->_("Specify each attribute on one line in the following format:\n`attribute=value` (without quotes around the value)\nThe values are parsed by [\$page->getMarkup](https://processwire.com/api/ref/page/get-markup/), so you can use replacement patterns with the link target page. For example:\n`class=automatic-link template-{template}`\n`title=Jump to {template.label}: {title}`");
        $attributes->notes = $this->_("The `href` attribute is included automatically.");
        $attributes->placeholder = $this->_("class=auto-link template-{template}\ntitle=Jump to {template.label}: {title}");
        $attributes->collapsed = Inputfield::collapsedNo;

        // case insensitive search setting
        $insensitive = wire()->modules->get('InputfieldCheckbox');
        $insensitive->name = 'case_insensitive_match';
        $insensitive->label = $this->_('Use case insensitive mode?');
        $insensitive->description = $this->_('By default, the module performs a case sensitive search, so only exact title matches are linked. If you want the word `apple` in your text field to be linked to the page `Apple` as well, use this option.');
        $insensitive->columnWidth = 33;
        $insensitive->collapsed = Inputfield::collapsedNever;

        // force case sensitive database queries for non-case-sensitive collations
        $precision = wire()->modules->get('InputfieldCheckbox');
        $precision->name = 'force_case_sensitive_query';
        $precision->label = $this->_('Force case sensitive database query for title retrieval?');
        $precision->description = $this->_('Use this if your database uses a non case-sensitive collation (`_ci` suffix) and you have titles that are identical but for their casing (`apple` and `Apple`).');
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

        // advanced settings in a collapsed group
        $advanced = wire()->modules->get('InputfieldFieldset');
        $advanced->label = $this->_('Advanced settings');
        $advanced->collapsed = Inputfield::collapsedYes;

        // fields in the "advanced" section
        $advanced->add($insensitive);
        $advanced->add($precision);
        $advanced->add($order);

        // add the settings fields in order of importance
        $inputfields->add($asm);
        $inputfields->add($minlen);
        $inputfields->add($check);
        $inputfields->add($hidden);
        $inputfields->add($attributes);
        $inputfields->add($advanced);
        return $inputfields;
    }
}
