<?php
class ButtonProvider
{
    public static function createButton($imageSrc, $action, $class)
    {
        $image = ($imageSrc == null) ? "" : "<img src='$imageSrc'>";
        return "<button class='$class' onClick='$action'>
                    $image
                </button>";
    }
}
