<?php
/**
 *            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 *                  Version 2, December 2004
 *
 * Copyright (C) 2016 Michael Dekker <prestashop@michaeldekker.com>
 *
 * Everyone is permitted to copy and distribute verbatim or modified
 * copies of this license document, and changing it is allowed as long
 * as the name is changed.
 *
 *           DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 * TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
 *
 *  @author    Michael Dekker <prestashop@michaeldekker.com>
 *  @copyright 2016 Michael Dekker
 *  @license   http://www.wtfpl.net/about/ Do What The Fuck You Want To Public License (WTFPL v2)
 */

if (!defined('_PS_VERSION_')) {
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');

    header('Location: ../');
    exit;
}

require_once dirname(__FILE__).'/classes/JustAModel.php';

/**
 * Class SecureModule
 */
class SecureModule extends Module
{
    /**
     * SecureModule constructor.
     */
    public function __construct()
    {
        $this->name = 'securemodule';
        $this->tab = 'administration';
        $this->version = '1.1.0';
        $this->author = 'Michael Dekker';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Secure module');
        $this->description = $this->l('Secure module -- best practices');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->controllers = array('cron');
    }

    /**
     * Install the module
     *
     * @return bool Whether the module has been successfully installed
     * @throws PrestaShopException
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        Configuration::updateValue('SECUREMODULE_LIVE_MODE', false);

        require_once _PS_MODULE_DIR_.$this->name.'/sql/install.php';

        return true;
    }

    /**
     * Uninstall the module
     *
     * @return bool Whether the module has been successfully uninstalled
     */
    public function uninstall()
    {
        Configuration::deleteByName('SECUREMODULE_LIVE_MODE');

        require_once _PS_MODULE_DIR_.$this->name.'/sql/uninstall.php';

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     *
     * @return string Configuration form HTML
     */
    public function getContent()
    {
        if (Tools::isSubmit('ajax')) {
            return $this->ajaxProcess();
        }
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool) Tools::isSubmit('submitSecuremoduleModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm().$this->renderModelList();
    }

    /**
     * Execute cron job
     */
    public function cron()
    {
        header('Content-Type: text/plain');
        die('Cron job executed');
    }

    /**
     * Ajax process BO ajax requests
     */
    protected function ajaxProcess()
    {
        if (Tools::isSubmit('ajax')
            && (Tools::isSubmit(JustAModel::$definition['primary']))
            && Tools::isSubmit('action')
            && Tools::isSubmit('active'.bqSQL(JustAModel::$definition['table']))) {
                $this->processStatus();
        }

        die(Tools::jsonEncode(array('false' => false, 'text' => $this->l('Could not update'))));
    }

    /**
     * Ajax process JustAModel status
     */
    protected function processStatus()
    {
        if (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.bqSQL(JustAModel::$definition['table']).'` jam
            SET jam.`active` = MOD(jam.`active` + 1, 2)
            WHERE jam.`'.bqSQL(JustAModel::$definition['primary']).'` = '.(int) Tools::getValue(JustAModel::$definition['primary'])
        )) {
            die(Tools::jsonEncode(array('success' => true, 'text' => 'Successfully updated')));
        }

        die(Tools::jsonEncode(array('success' => false, 'text' => $this->l('Could not update in database'))));
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSecuremoduleModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getCronForm(), $this->getConfigForm()));
    }

    /**
     * @return null|string
     */
    protected function renderModelList()
    {
        $return = null;

        $comments = JustAModel::getAll();

        $fieldsList = array(
            bqSQL(JustAModel::$definition['primary']) => array(
                'title' => $this->l('ID'),
                'type' => 'text',
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'type' => 'bool',
                'active' => 'active',
                'ajax' => true,
            ),
        );

        $actions = array();


        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->actions = $actions;
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->listTotal = count($comments);
        $helper->identifier = bqSQL(JustAModel::$definition['primary']);
        $helper->title = $this->l('Models');
        $helper->table = bqSQL(JustAModel::$definition['table']);
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        //$helper->tpl_vars = array('priority' => array($this->l('High'), $this->l('Medium'), $this->l('Low')));

        $return .= $helper->generateList($comments, $fieldsList);

        return $return;

    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'SECUREMODULE_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'SECUREMODULE_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'SECUREMODULE_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Create the structure of your form.
     */
    protected function getCronForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Cron job'),
                    'icon' => 'icon-cogs',
                ),
                'description' => $this->l('Use the following URL for cron jobs:').' '.$this->context->link->getModuleLink($this->name, 'cron', array(), (bool) Configuration::get('PS_SSL_ENABLED')),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'SECUREMODULE_LIVE_MODE' => Configuration::get('SECUREMODULE_LIVE_MODE', true),
            'SECUREMODULE_ACCOUNT_EMAIL' => Configuration::get('SECUREMODULE_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'SECUREMODULE_ACCOUNT_PASSWORD' => Configuration::get('SECUREMODULE_ACCOUNT_PASSWORD', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $formValues = $this->getConfigFormValues();

        foreach (array_keys($formValues) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }
}
