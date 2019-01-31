<?php
namespace Processwire;

class TextformatterPageTitleLinksConfig extends ModuleConfig
{
    public function getDefaults()
    {
        return [
            'auto_link_templates' => [],
            'include_current_page' => false,
            'include_hidden_pages' => false,
            'attributes' => '',
            'minimum_length' => 0,
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
        $asm->columnWidth = 33;

        // add all non-system templates
        foreach ($this->templates as $template) {
            if (!($template->flags & Template::flagSystem)) {
                $template_name = $template->label ? "{$template->label} ({$template->name})" : $template->name;
                $asm->addOption($template->id, $template_name);
            }
        }

        // Checkbox to include current page
        $check = wire()->modules->get('InputfieldCheckbox');
        $check->name = 'include_current_page';
        $check->label = $this->_('Include the current page?');
        $check->columnWidth = 33;

        // Checkbox to include hidden pages
        $hidden = wire()->modules->get('InputfieldCheckbox');
        $hidden->name = 'include_hidden_pages';
        $hidden->label = $this->_('Include hidden pages?');
        $hidden->columnWidth = 33;

        // multi-line field for arbitrary attributes for the link
        $attributes = wire()->modules->get('InputfieldTextarea');
        $attributes->name = 'add_attributes';
        $attributes->label = $this->_('Additional attributes for the anchor element (&lt;a&gt;).');
        $attributes->description = $this->_("Specify each attribute on one line in the following format:\n`attribute=value` (without quotes around the value)\nThe values are parsed by [\$page->getMarkup](https://processwire.com/api/ref/page/get-markup/), so you can use replacement patterns with the link target page. For example:\n`class=automatic-link template-{template}`\n`title=Jump to {template.label}: {title}`");
        $attributes->notes = $this->_("The `href` attribute is included automatically.");
        $attributes->placeholder = $this->_("class=auto-link template-{template}\ntitle=Jump to {template.label}: {title}");

        // preference for duplicate titles
        $order = wire()->modules->get('InputfieldSelect');
        $order->name = 'same_title_order';
        $order->label = $this->_('Preference for duplicate page titles');
        $order->description = $this->_('If you have more than one linkable pages with the same title, do you want the oldest or newest page to get linked preferentially?');
        $order->addOption('MIN', 'Prefer oldest page');
        $order->addOption('MAX', 'Prefer newest page');
        $order->required = true;
        $order->columnWidth = 33;

        // minimum length for linkable titles
        $minlen = wire()->modules->get('InputfieldInteger');
        $minlen->name = 'minimum_length';
        $minlen->label = $this->_('Minimum length for linkable titles');
        $minlen->attr('min', '0');
        $minlen->columnWidth = 33;

        $inputfields->add($asm);
        $inputfields->add($check);
        $inputfields->add($hidden);
        $inputfields->add($attributes);
        $inputfields->add($minlen);
        $inputfields->add($order);
        return $inputfields;
    }
}
