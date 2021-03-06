<?php
namespace SICOR\SicAddress\Controller;


    /***************************************************************
     *
     *  Copyright notice
     *
     *  (c) 2016 SICOR DEVTEAM <dev@sicor-kdl.net>, Sicor KDL GmbH
     *
     *  All rights reserved
     *
     *  This script is part of the TYPO3 project. The TYPO3 project is
     *  free software; you can redistribute it and/or modify
     *  it under the terms of the GNU General Public License as published by
     *  the Free Software Foundation; either version 3 of the License, or
     *  (at your option) any later version.
     *
     *  The GNU General Public License can be found at
     *  http://www.gnu.org/copyleft/gpl.html.
     *
     *  This script is distributed in the hope that it will be useful,
     *  but WITHOUT ANY WARRANTY; without even the implied warranty of
     *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *  GNU General Public License for more details.
     *
     *  This copyright notice MUST APPEAR in all copies of the script!
     ***************************************************************/
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * DomainPropertyController
 */
class DomainPropertyController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * domainPropertyRepository
     *
     * @var \SICOR\SicAddress\Domain\Repository\DomainPropertyRepository
     * @inject
     */
    protected $domainPropertyRepository = NULL;

    /**
     * action create
     *
     * @param \SICOR\SicAddress\Domain\Model\DomainProperty $newDomainProperty
     * @return void
     */
    public function createAction(\SICOR\SicAddress\Domain\Model\DomainProperty $newDomainProperty)
    {
        if (TYPO3_MODE == 'BE') {
            $errorMessages = [];
            $existingObjectWithSameName = $this->domainPropertyRepository->findByTitle(strtolower($newDomainProperty->getTitle()));
            if ($existingObjectWithSameName->count() < 1) {
                $this->domainPropertyRepository->add($newDomainProperty);
            } else {
                $errorMessages[] = "A field named '" . $newDomainProperty->getTitle() . "' already exists. Please choose a unique name.";
            }
            $this->redirect('list', 'Module',null,['errorMessages'=>$errorMessages]);
        }
    }

    /**
     * action update
     *
     * @param \SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty
     * @return void
     */
    public function updateAction(\SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty)
    {
        if (TYPO3_MODE == 'BE') {
            $arguments = $this->request->getArgument("domainProperty");
            if(!array_key_exists("isListLabel",$arguments)) {
                $domainProperty->setIsListLabel(false);
            }
            $this->domainPropertyRepository->update($domainProperty);
            $this->redirect('list', 'Module');
        }
    }

    /**
     * action delete
     *
     * @param \SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty
     * @return void
     */
    public function deleteAction(\SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty)
    {
        if (TYPO3_MODE == 'BE') {
            $this->domainPropertyRepository->remove($domainProperty);
            $this->redirect('list', 'Module');
        }
    }

    /**
     * Sortable Wrapper
     * @return config
     */
    public function getFlexSortableFields($config)
    {
        return $this->getFlexFields($config, "integer,float,string,mmtable");
    }

    /**
     * Filter Wrapper
     * @return config
     */
    public function getFlexFilterFields($config)
    {
        return $this->getFlexFields($config, "mmtable");
    }

    /**
     * String Wrapper
     * @return config
     */
    public function getFlexStringFields($config)
    {
        return $this->getFlexFields($config, "string");
    }

    /**
     * Integer Wrapper
     * @return config
     */
    public function getFlexDistanceFields($config)
    {
        return $this->getFlexFields($config, "integer,float");
    }

    /**
     * @return config
     */
    public function getFlexFields($config, $types)
    {
        $where = 'deleted = 0 AND hidden = 0 AND (';
        $typeList = explode(',', $types);
        foreach ($typeList as $type) {
            $where .= '(type = "'.$type.'") OR ';
        }
        $where .= '1=0 )';

        // Query Database
        $types = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title, type, tca_label', 'tx_sicaddress_domain_model_domainproperty', $where, 'tca_label');

        $optionList = array();
        $optionList[0] = array(0 => 'Ausblenden', 1 => 'none');
        foreach ($types as $type) {
            $value = ($type["type"] == "mmtable") ? $type["title"].".title" : $type["title"];
            $optionList[] = array(0 => $type["tca_label"], 1 => $value);
        }

        $config['items'] = array_merge($config['items'], $optionList);
        return $config;
    }
}
