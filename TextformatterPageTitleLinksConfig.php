<?php
namespace Processwire;

class TextformatterPageTitleLinksConfig extends ModuleConfig
{
    public function getDefaults()
    {
        return [
            'auto_link_templates' => []
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

        // add all non-system templates
        foreach ($this->templates as $template) {
            // cs_graduate.gif
            if (!($template->flags & Template::flagSystem)) {
                $template_name = $template->label ? $template->label . ' (' . $template->name . ')' : $template->name;
                $asm->addOption($template->id, $template_name);
            }
        }

        $inputfields->add($asm);
        return $inputfields;
    }
}
