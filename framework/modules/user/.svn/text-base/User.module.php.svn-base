<?php
/**
* 
* @file
* User Module
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup          User_Module
*/

require_once(APP_PATH . 'Base.module.php');

/**
* 
* User Module Class
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup          User_Module
*/

class UserModule extends BaseModule {

	const NAME			= 'User Module';
	const CODE			= 'User';
	const VERSION		= '1.0';
	const VERSION_BUILD = '1';
	const VERSION_DTS	= '4/21/2010 9:30:00 PM PST';

	public $user;
	public $metaValue;

//	public $maxBatch = 500;
//	public $maxAuditBatch = 500;


	function __construct() {
		$this->enableTrace();

		$return = parent::__construct();

		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, self::NAME . ' v' . self::VERSION . '.' . self::VERSION_BUILD . ' loaded.');

		return $return;
	}


	function loadUser($userID) {
		global $phpAnvil;

		$this->user = new UserModel($phpAnvil->db, $userID);
		return $this->user->load();
	}


	function loadMetaValue($metaValueID) {
		global $phpAnvil;

		$this->metaValue = new UserMetaValueModel($phpAnvil->db, $metaValueID);
		return $this->metaValue->load();
	}


	function registerPromotionActions(Action $action) {
		global $phpAnvil;

		$phpAnvil->loadModule(MODULE_PROMO);

		$newPromoAction = new PromotionActionModel($phpAnvil->db);
		$newPromoAction->constant = 'SAVE_USER';
		$newPromoAction->loadByConstant($newPromoAction->constant);
		$newPromoAction->moduleID = MODULE_USER;
		$newPromoAction->name = 'Save User';
		$newPromoAction->detailURL = null;
		$newPromoAction->container = false;
		$newPromoAction->iconViewImage = 'iUser.png';
		$newPromoAction->processModuleID = MODULE_USER;
		$newPromoAction->processActionID = ACTION_SAVE_USER_PROMO_ACTION;
		$newPromoAction->responseModuleID = 0;
		$newPromoAction->responseActionID = 0;
		$newPromoAction->requireStepURL = false;
		$newPromoAction->continueNextStep = true;
		$newPromoAction->save();

		return true;
	}


	function processAction(Action $action) {
		global $modules, $phpAnvil;
		global $firePHP;

//		FB::log($action);
		$return = true;

		switch ($action->type) {
//			CASE ACTION_DELETE_PROMOTION:
//				$return = $this->deletePromotion($action);
//				break;

			CASE ACTION_REGISTER_PROMOTION_ACTIONS:
				$this->registerPromotionActions($action);
				$return = true;
				break;


			CASE ACTION_SAVE_USER_PROMO_ACTION:
//				FB::log('[USER] Processing ACTION_SAVE_USER_PROMO_ACTION...');
				$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, '[USER] Processing ACTION_SAVE_USER_PROMO_ACTION...');

//				if ($modules[MODULE_PROMO]->isNewClick) {
				$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'count($_POST) = ' . count($_POST));

					if (count($_POST) > 0) {
						$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Saving user meta data...');

						$this->loadMetaValue(0);
						$this->metaValue->promotionID = $modules[MODULE_PROMO]->promotion->id;
						$this->metaValue->promotionItemID = $modules[MODULE_PROMO]->item->id;
						$this->metaValue->sessionID = $phpAnvil->session->id;
						$this->metaValue->userID = $modules[MODULE_USER]->user->id;

						foreach($_POST as $key=>$value){
							if ($key != 'submit') {
								$this->metaValue->id = 0;
								$this->metaValue->name = $key;
								$this->metaValue->value = $value;
								$this->metaValue->save();
							}
						}
					}

//				}
				break;


			CASE ACTION_PROMO_RESPONSE_PHASE:
//				FB::log('[USER] Processing ACTION_PROMO_RESPONSE_PHASE...');

				$this->user->recentDTS = date('Y-m-d G:i:s');
				$this->user->recentSessionID = $phpAnvil->session->id;
				$this->user->recentAdID = $modules[MODULE_MARKETING]->ad->id;
				$this->user->recentPromotionID = $modules[MODULE_PROMO]->promotion->id;
				$this->user->save();

//				FB::log($this->user->email);
				$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $this->user->email);

				$modules[MODULE_CONTENT]->assign('user_name', $this->user->name);
				$modules[MODULE_CONTENT]->assign('user_email', $this->user->email);

				break;

		}
		return $return;
	}


}

?>