<div class="pagination">    
    <?php if ($this->iPage !=0 ) :?>
        <?php $tParam[$this->sParamPage]=1?>
        <a href="<?php echo _root::getLink($this->sModuleAction,$tParam) ?>"> << </a>
        <?php $tParam[$this->sParamPage]=$this->iPage?>
        <a href="<?php echo _root::getLink($this->sModuleAction,$tParam) ?>"> < </a>
    <?php endif; ?>
    page <?php echo $this->iPage+1?>
    <?php if ($this->iPage != $this->iMax-1 ) :?>
        <?php $tParam[$this->sParamPage]=$this->iPage+2?>
        <a href="<?php echo _root::getLink($this->sModuleAction,$tParam) ?>"> > </a>
        <?php $tParam[$this->sParamPage]=$this->iMax?>
        <a href="<?php echo _root::getLink($this->sModuleAction,$tParam) ?>"> >> </a>
    <?php endif; ?>    
</div>
