<?php namespace Anomaly\Streams\Platform\Ui\Form\Component\Button;

use Anomaly\Streams\Platform\Ui\Form\FormBuilder;

/**
 * Class ButtonNormalizer
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Ui\Button
 */
class ButtonNormalizer
{

    /**
     * Normalize button input.
     *
     * @param FormBuilder $builder
     */
    public function normalize(FormBuilder $builder)
    {
        $buttons = $builder->getButtons();

        foreach ($buttons as $key => &$button) {
            $button = $this->process($key, $button);
        }

        $builder->setButtons($buttons);
    }


    protected function process($key, $button)
    {
        /**
         * If the button is a string then use
         * it as the button parameter.
         */
        if (is_string($button)) {
            $button = [
                'button' => $button,
            ];
        }

        /**
         * If the key is a string and the button
         * is an array without a button param then
         * move the key into the button as that param.
         */
        if (!is_integer($key) && !isset($button['button'])) {
            $button['button'] = $key;
        }

        /**
         * Make sure some default parameters exist.
         */
        $button['attributes'] = array_get($button, 'attributes', []);

        /**
         * Move the HREF if any to the attributes.
         */
        if (isset($button['href'])) {
            array_set($button['attributes'], 'href', array_pull($button, 'href'));
        }

        /**
         * Move the target if any to the attributes.
         */
        if (isset($button['target'])) {
            array_set($button['attributes'], 'target', array_pull($button, 'target'));
        }

        /**
         * Move all data-* keys
         * to attributes.
         */
        foreach ($button as $attribute => $value) {
            if (str_is('data-*', $attribute)) {
                array_set($button, 'attributes.' . $attribute, array_pull($button, $attribute));
            }
        }

        /**
         * Make sure the HREF is absolute.
         */
        if (
            isset($button['attributes']['href']) &&
            is_string($button['attributes']['href']) &&
            !starts_with($button['attributes']['href'], 'http')
        ) {
            $button['attributes']['href'] = url($button['attributes']['href']);
        }

        if (isset($button['dropdown'])) {
            foreach ($button['dropdown'] as $key => &$dropdown) {
                $dropdown = $this->process($key, $dropdown);
            }
        }

        return $button;
    }
}
