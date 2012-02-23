<?php
/**
 * @package Framework
 * @subpackage Text
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class Text extends Element
{

    public function __toString()
    {
        $label = '';
        if (!empty($this->_label)) {
            $id = $this->get_attributes('id');
            $class = " class=\"label-$id\"";
            $label = "<label for=\"$id\"$class>$this->_label</label>\r\n";
        }

        $desc = $this->get_description();
        if ($desc) {
            $description = "<span class=\"description\">{$desc}</span>";
        }

        $attr = $this->build_attr_string();

        $htmlArray['content'] = "{$label}<input type=\"text\" $attr />\r\n{$description}";

        $this->_html = implode(' ', $htmlArray);

        return $this->_html;
    }
}

