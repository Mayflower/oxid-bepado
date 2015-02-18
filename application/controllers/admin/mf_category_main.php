<?php

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
class mf_category_main extends mf_category_main_parent
{
    /**
     * @var VersionLayerInterface
     */
    private $_oVersionLayer;

    public function render()
    {
        $aCategories = $this->getSdkCategories();

        $oxidCategoryId = parent::getEditObjectId();
        if (!isset($aCategories)) {
            $aCategories = [];
        }

        $bepadoCategory = $this->getVersionLayer()->createNewObject('oxbase');
        $bepadoCategory->init('bepado_categories');

        if ($oxidCategoryId != "-1" && isset($oxidCategoryId)){
            try {
                $query = $bepadoCategory->buildSelectString(array('catnid' => $oxidCategoryId));
                $bepadoCategoryId = $this->getVersionLayer()->getDb(true)->getOne($query);
                $bepadoCategory->load($bepadoCategoryId);
            } catch (\Exception $e) {
                // do nothing
            }
        }

        $this->_aViewData['googleCategories'] = $aCategories;
        $this->_aViewData['bepardoCategory'] = $bepadoCategory;

        return parent::render();
    }

    public function save()
    {
        parent::save();
        $myConfig = parent::getConfig();
        $oxidCategoryId = parent::getEditObjectId();

        /** @var oxBase $bepadoCategory */
        $bepadoCategory = oxNew('oxbase');
        $bepadoCategory->init('bepado_categories');
        $query = $bepadoCategory->buildSelectString(array('catnid' => $oxidCategoryId));
        $bepadoCategoryId = $this->getVersionLayer()->getDb(true)->getOne($query);
        $bepadoCategory->load($bepadoCategoryId);

        // parameter for bepado category path
        $aParams = parent::_parseRequestParametersForSave($myConfig->getRequestParameter("mf_editval"));
        $googleCategoryPath = isset($aParams['bepado_categories__path']) && "" != $aParams['bepado_categories__path']
            ? $aParams['bepado_categories__path']
            : null;


        $googleCategories = $this->getSdkCategories();
        if (isset($googleCategories[$googleCategoryPath])) {
            $bepadoCategory->assign(array(
                'bepado_categories__catnid' => $oxidCategoryId,
                'bepado_categories__path' => $googleCategoryPath,
                'bepado_categories__title' => $googleCategories[$googleCategoryPath],
            ));
            $bepadoCategory->save();
        } else {
            if ($bepadoCategory->isLoaded()) {
                $bepadoCategory->delete();
            }
        }

        return;
    }

    /**
     * @return array
     */
    private function getSdkCategories()
    {
        /** @var mf_sdk_helper $sdkHelper */
        $sdkHelper = $this->getVersionLayer()->createNewObject('mf_sdk_helper');
        $sdkConfig = $sdkHelper->createSdkConfigFromOxid();
        $sdk = $sdkHelper->instantiateSdk($sdkConfig);

        return $sdk->getCategories();
    }

    /**
     * Create and/or returns the VersionLayer.
     *
     * @return VersionLayerInterface
     */
    private function getVersionLayer()
    {
        if (null == $this->_oVersionLayer) {
            /** @var VersionLayerFactory $factory */
            $factory = oxNew('VersionLayerFactory');
            $this->_oVersionLayer = $factory->create();
        }

        return $this->_oVersionLayer;
    }

    /**
     * @param VersionLayerInterface $versionLayer
     */
    public function setVersionLayer(VersionLayerInterface $versionLayer)
    {
        $this->_oVersionLayer = $versionLayer;
    }
}
