<?php

namespace App\Components;

use Phalcon\Di\Injectable;
use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;

class Locale extends Injectable
{
    /**
     * @return NativeArray
     */
    public function getTranslator(): NativeArray
    {
        $language = $this->request->get("Locale");
        // print_r($language);
        // die;
        $messages = [];
        if(empty($language))
        {
            $language="English";
        }

        $translationFile = APP_PATH.'/messages/' . $language . '.php';

        if (true !== file_exists($translationFile)) {
            $translationFile = APP_PATH.'/messages/English.php';
        }

        require $translationFile;

        $interpolator = new InterpolatorFactory();
        $factory      = new TranslateFactory($interpolator);

        return $factory->newInstance(
            'array',
            [
                'content' => $messages,
            ]
        );
    }
}