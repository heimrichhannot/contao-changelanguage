<?php
/**
 * changelanguage Extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2008-2016, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-changelanguage
 */

namespace Terminal42\ChangeLanguage\EventListener\BackendView;

use Contao\Controller;
use Contao\DataContainer;
use Contao\PageModel;
use Contao\Session;
use Contao\System;
use Terminal42\ChangeLanguage\PageFinder;

class PageViewListener extends AbstractViewListener
{
    /**
     * @inheritdoc
     */
    protected function getAvailableLanguages(DataContainer $dc)
    {
        $options = [];
        $node = Session::getInstance()->get('tl_page_node');

        if ($node > 1 && ($current = PageModel::findByPk($node)) !== null) {
            $pageFinder = new PageFinder();
            $associated = $pageFinder->findAssociatedForPage($current);

            if (count($associated) > 1) {
                $languages = System::getLanguages();

                foreach ($associated as $model) {
                    $model->loadDetails();

                    if ($model->language !== $current->language) {
                        $options[$model->id] = $languages[$model->language] ?: $model->language;
                    }
                }
            }
        }

        return $options;
    }

    /**
     * @inheritdoc
     */
    protected function doSwitchView($id)
    {
        Session::getInstance()->set('tl_page_node', $id);

        Controller::redirect(System::getReferer());
    }
}