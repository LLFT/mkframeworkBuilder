<?php
class module_mods_bootstrap_menu extends abstract_moduleBuilder{

	protected $sModule='mods_bootstrap_menu';
	protected $sModuleView='mods/bootstrap/menu';

	private $msg=null;
	private $detail=null;
	private $tError=null;
	private $sError=null;
	private $ok=1;

	public function _index(){
		$this->process();

		$tModule=module_builder::getTools()->getListModule();
		$tModuleAndMethod=array();
		foreach($tModule as $oModule){
			$sModuleName=$oModule->getName();
			if(in_array($sModuleName,array('menu','builder','example','exampleembedded'))){
				continue;
			}
			include module_builder::getTools()->getRootWebsite().'module/'.$sModuleName.'/main.php';

			if(get_parent_class('module_'.$sModuleName)!='abstract_module'){
				continue;
			}

			$tMethods=get_class_methods('module_'.$sModuleName);
			foreach($tMethods as $i => $sMethod){
				if($sMethod[0]!='_' or substr($sMethod,0,2)=='__'){
					unset($tMethods[$i]);
				}
			}
			if(empty($tMethods)){
				continue;
			}
			$tModuleAndMethod[$sModuleName]=$tMethods;
		}

		$oDir=new _dir(module_builder::getTools()->getRootWebsite().'module/menu');
		$bExist=$oDir->exist();


		$oTpl= $this->getView('index');
		//$oTpl->var=$var;

		$oTpl->msg=$this->msg;
		$oTpl->detail=$this->detail;
		$oTpl->tError=$this->tError;
		$oTpl->sError=$this->sError;
		$oTpl->ok=$this->ok;

		$oTpl->bExist=$bExist;
		$oTpl->tModuleAndMethod=$tModuleAndMethod;

		return $oTpl;
	}
	private function process(){
		if(_root::getRequest()->isPost()==false){
			return null;
		}

		$sError=null;
		$tError=null;
		$msg=null;
		$detail=null;

		$sModule=_root::getParam('modulename');
		$tMethod=_root::getParam('tMethod');
		$tLabel=_root::getParam('tLabel');


		$ok=1;

		if(count($tMethod)){
			//check formulaire
			foreach($tMethod as $i => $sMethod){
				if($tLabel[$i]==''){
					$tError[$i]=tr('remplissezLeLibelle');
					$ok=0;
				}
			}
		}else{
			$sError=tr('selectionnezAuMoinsUneMethode');
			$ok=0;
		}

		if($ok){

			if(module_builder::getTools()->projetmkdir('module/'.$sModule)==true){
				$detail=trR('creationRepertoire',array('#REPERTOIRE#'=>'module/'.$sModule));

				if(module_builder::getTools()->projetmkdir('module/'.$sModule.'/view')==true){
					$detail.='<br />'.trR('creationRepertoire',array('#REPERTOIRE#'=>'module/'.$sModule.'/view'));

					$this->genModuleMenuMain($sModule,$tMethod,$tLabel);

					$msg=trR('moduleGenereAvecSucces',array('#MODULE#'=>$sModule));

					$detail.='<br />'.trR('CreationDuFichierVAR',array('#FICHIER#'=>'module/'.$sModule.'/main.php'));
					$detail.='<br />'.trR('CreationDuFichierVAR',array('#FICHIER#'=>'module/'.$sModule.'/view/index.php'));

					$sCode='<?php '."\n";
					$sCode.='//assignez le menu a l\'emplacement menu'."\n";
					$sCode.='$this->oLayout->addModule(\'menu\',\''.$sModule.'::index\');'."\n";

					$detail.='<br/><br/>'.tr('pourLutiliserAjoutez').'<br />
					'.highlight_string($sCode,1);

				}else{
					$detail.='<br />'.trR('repertoireDejaExistant',array('#REPERTOIRE#'=>'module/'.$sModule.'/view'));
				}


			}else{
			  $detail=trR('repertoireDejaExistant',array('#REPERTOIRE#'=>'module/'.$sModule.'/view'));
			}

		}

		$this->sError=$sError;
		$this->tError=$tError;
		$this->detail=$detail;
		$this->msg=$msg;
		$this->ok=$ok;
	}
	private function genModuleMenuMain($sModuleMenuName,$tMethod,$tLabel){

	    $sData=null;
	    foreach($tMethod as $i => $sLink){
		$sData.='\''.$tLabel[$i].'\' => \''.$sLink.'\','."\n";
	    }

	    $this->projectMkdir('module/'.$sModuleMenuName);
	    /*SOURCE*/$oSourceMain=$this->getObjectSource('example/main.php');
		/*SOURCE*/$oSourceMain->setPattern('#MODULE#',$sModuleMenuName);

	    /*SOURCE*/$oSourceMain->setPattern('#TABLEAUICI#',$sData);

	    /*SOURCE*/$oSourceMain->save();

	    $this->projectMkdir('module/'.$sModuleMenuName.'/view');

	    /*SOURCE*/$oSourceViewIndex=$this->getObjectSource('example/view/index.php');
	    /*SOURCE*/$oSourceViewIndex->setPattern('#MODULE#',$sModuleMenuName);

	    /*SOURCE*/$oSourceViewIndex->save();

	}

}
