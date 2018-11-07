<?php
namespace Processwire;

class TextformatterPageTitleLinksConfig extends ModuleConfig
{
    public function getDefaults()
    {
        return [
            'auto_link_templates' => [],
            'exclude_current_page' => false,
        ];
    }

    public function getInputFields()
    {
        $inputfields = parent::getInputfields();

        // ASM select for templates
        $asm = wire()->modules->get('InputfieldAsmSelect');
        $asm->name = 'auto_link_templates'; 
        $asm->id = 'auto_link_templates'; 
        $asm->label = $this->_('Templates to search for matching titles.');

        // Checkbox to exclude current page
        $check = wire()->modules->get('InputfieldCheckbox');
        $check->name = 'exclude_current_page';
        $check->id = 'exclude_current_page';
        $check->label = $this->_("Don't link to the current page?");

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
        return $inputfields;
    }
}
