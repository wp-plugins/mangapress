<?php
interface WP_Form_Validate
{
    public function isValid($value);

    public function getMessage();
}