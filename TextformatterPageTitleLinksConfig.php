<?php
namespace Processwire;

class TextformatterPageTitleLinksConfig extends ModuleConfig
{
    public function getDefaults()
    {
        return [
            'auto_link_templates' => [],
            'exclude_current_page' => false,
            'include_hidden_pages' => false,
            'add_link_class' => '',
        ];
    }

    public function getInputFields()
    {
        $inputfields = parent::getInputfields();

        // ASM select for templates
        $asm = wire()->modules->get('InputfieldAsmSelect');
        $asm->name = 'auto_link_templates'; 
        $asm->label = $this->_('Templates to search for matching titles.');
        $asm->setAsmSelectOption('sortable', false);
        $asm->columnWidth = 33;

        // Checkbox to exclude current page
        $check = wire()->modules->get('InputfieldCheckbox');
        $check->name = 'exclude_current_page';
        $check->label = $this->_("Don't link to the current page?");
        $check->columnWidth = 33;

        // Checkbox to include hidden pages
        $hidden = wire()->modules->get('InputfieldCheckbox');
        $hidden->name = 'include_hidden_pages';
        $hidden->label = $this->_("Include hidden pages?");
        $hidden->columnWidth = 33;

        // text field for css classes
        $class = wire()->modules->get('InputfieldText');
        $class->name = 'add_link_class';
        $class->label = $this->_('Optional class(es) to add to the links.');
        $class->notes = $this->_('You can use this to style automatically created links differently in CSS, or to target them with JavaScript.');

        // add all non-system templates
        foreach ($this->templates as $template) {
            // cs_graduate.gif
            if (!($template->flags & Template::flagSystem)) {
                $template_name = $template->label ? $template->label . ' (' . $template->name . ')' : $template->name;
                $asm->addOption($template->id, $template_name);
            }
        }

        $inputfields->add($asm);
        $inputfields->add($check);
        $inputfields->add($hidden);
        $inputfields->add($class);
        return $inputfields;
    }
}
