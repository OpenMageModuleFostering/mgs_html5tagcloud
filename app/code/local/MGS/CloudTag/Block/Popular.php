<?php
class MGS_CloudTag_Block_Popular extends Mage_Core_Block_Template
{
	protected $_tags;
    protected $_minPopularity;
    protected $_maxPopularity;

    protected function _loadTags()
    {
        if (empty($this->_tags)) {
            $this->_tags = array();

            $tags = Mage::getModel('tag/tag')->getPopularCollection()
                ->joinFields(Mage::app()->getStore()->getId())
                ->limit(20)
                ->load()
                ->getItems();

            if( count($tags) == 0 ) {
                return $this;
            }


            $this->_maxPopularity = reset($tags)->getPopularity();
            $this->_minPopularity = end($tags)->getPopularity();
            $range = $this->_maxPopularity - $this->_minPopularity;
            $range = ($range == 0) ? 1 : $range;
            foreach ($tags as $tag) {
                $tag->setRatio(($tag->getPopularity()-$this->_minPopularity)/$range);
                $this->_tags[$tag->getName()] = $tag;
            }
            ksort($this->_tags);
        }
        return $this;
    }

    public function getTags()
    {
        $this->_loadTags();
        return $this->_tags;
    }

    public function getMaxPopularity()
    {
        return $this->_maxPopularity;
    }

    public function getMinPopularity()
    {
        return $this->_minPopularity;
    }

    protected function _toHtml()
    {
        if (count($this->getTags()) > 0) {
            if($this->isEnableFlashtag()){
                $this->setTemplate('mgs/cloudtag/popular.phtml');
            }
            return parent::_toHtml();
        }
        return '';
    }


    public function TagSize(){
        $tagSize = array();
        $i=0;
        foreach ($this->getTags() as $_tag){
            $i++;
            if($i<=$this->getCount()){
                $tagSize[$_tag->getName()]= $_tag->getRatio();
            }
        }
        return $tagSize;
    }
	
	public function isEnableFlashtag(){
        return Mage::getStoreConfig('aht_cloudtag/general/enabled');
    }

    public function getWidth(){
        return Mage::getStoreConfig('aht_cloudtag/general/width');
    }

    public function getHeight(){
        return Mage::getStoreConfig('aht_cloudtag/general/height');
    }
	
	public function getTextColor(){
		return Mage::getStoreConfig('aht_cloudtag/general/textcolor');
	}
	
	public function getOutLineColor(){
		return Mage::getStoreConfig('aht_cloudtag/general/outline');
	}
}