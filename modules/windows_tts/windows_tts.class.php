<?php
/**
* Windows TTS
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 13:03:10 [Mar 13, 2016])
*/
class windows_tts extends module {
   /**
   * windows_tts
   *
   * Module class constructor
   *
   * @access private
   */
   function __construct()
   {
      $this->name = "windows_tts";
      $this->title = "Windows TTS";
      $this->module_category = "<#LANG_SECTION_APPLICATIONS#>";
      $this->checkInstalled();
   }

   /**
    * Saving module parameters
    * @param mixed $data
    * @return string
    * @access public
    */
   function saveParams($data = 0)
   {
      $p = array();

      if (isset($this->id))
         $p["id"] = $this->id;

      if (isset($this->view_mode))
         $p["view_mode"] = $this->view_mode;

      if (isset($this->edit_mode))
         $p["edit_mode"] = $this->edit_mode;

      if (isset($this->tab))
         $p["tab"] = $this->tab;

      return parent::saveParams($p);
   }

   /**
   * Getting module parameters from query string
   * @access public
   */
   function getParams()
   {
      global $id;
      global $mode;
      global $view_mode;
      global $edit_mode;
      global $tab;

      if (isset($id))
         $this->id = $id;

      if (isset($mode))
         $this->mode = $mode;

      if (isset($view_mode))
         $this->view_mode = $view_mode;

      if (isset($edit_mode))
         $this->edit_mode = $edit_mode;

      if (isset($tab))
         $this->tab = $tab;
   }

   /**
   * Run
   *
   * Description
   *
   * @access public
   */
   function run()
   {
      global $session;
      $out = array();

      if ($this->action == 'admin')
      {
         $this->admin($out);
      }
      else
      {
         $this->usual($out);
      }

      if (isset($this->owner->action))
         $out['PARENT_ACTION'] = $this->owner->action;

      if (isset($this->owner->name))
         $out['PARENT_NAME'] = $this->owner->name;

      $out['VIEW_MODE'] = $this->view_mode;
      $out['EDIT_MODE'] = $this->edit_mode;
      $out['MODE'] = $this->mode;
      $out['ACTION'] = $this->action;
      $this->data = $out;
      $p =new parser(DIR_TEMPLATES . $this->name . "/" . $this->name . ".html", $this->data, $this);
      $this->result = $p->result;
   }

   /**
    * Module backend
    * @param mixed &$out
    * @access public
    */
   function admin(&$out)
   {
      $this->getConfig();
      $out['DISABLED'] = $this->config['DISABLED'];

      if ($this->view_mode == 'update_settings')
      {
         global $disabled;
         $this->config['DISABLED'] = $disabled;
         $this->saveConfig();

         subscribeToEvent($this->name, 'SAY');
         $this->redirect("?ok=1");
      }

      if ($_GET['ok'])
         $out['OK'] = 1;

      /*
      global $clean;

      if ($clean)
      {
         array_map("unlink", glob(ROOT . "cms/cached/voice/*_yandex.mp3"));
         $this->redirect("?ok=1");
      }
      */
   }

   /**
    * Module frontend
    * @param mixed $out
    * @access public
    */
   function usual(&$out)
   {
      $this->admin($out);
   }

   /**
    * Summary of processSubscription
    * @param mixed $event Event
    * @param mixed $details Event detail
    */
   function processSubscription($event, &$details)
   {
      $this->getConfig();

      if ($event == 'SAY' && !$this->config['DISABLED'] && (!$details['ignoreVoice']))
      {
         $level = $details['level'];
         $message = $details['message'];

         if ($level >= (int)getGlobal('minMsgLevel') && IsWindowsOS())
         {
            safe_exec('cscript ' . DOC_ROOT . '/rc/sapi.js ' . $message, 1, $level);
            $details['ignoreVoice'] = 1;
         }
      }
   }

   /**
    * Module installation routine
    * @param mixed $data
    * @access private
   */
   function install($data = '')
   {
      subscribeToEvent($this->name, 'SAY');
      parent::install();
   }
}

/*
*
* TW9kdWxlIGNyZWF0ZWQgTWFyIDEzLCAyMDE2IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
