<?php
namespace GoMage\PostCode\Model\Source;

class Mode
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'Google', 'label' => __('Google')],
            ['value' => 'TargetLock', 'label' => __('TargetLock')]
        ];
    }
}