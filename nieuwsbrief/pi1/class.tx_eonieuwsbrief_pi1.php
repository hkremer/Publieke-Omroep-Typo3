<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * ************************************************************* */

require_once(PATH_tslib . 'class.tslib_pibase.php');

/**
 * Plugin 'EO Nieuwsbrief' for the 'eo_nieuwsbrief' extension.
 *
 * @author	MarkO
 * @package	
 * @subpackage	tx_eonieuwsbrief
 */
class tx_eonieuwsbrief_pi1 extends tslib_pibase {

    var $prefixId = 'tx_eonieuwsbrief_pi1';  // Same as class name
    var $scriptRelPath = 'pi1/class.tx_eonieuwsbrief_pi1.php'; // Path to this script relative to the extension dir.
    var $extKey = 'eo_nieuwsbrief'; // The extension key.

    /**
     * The main method of the PlugIn
     *
     * @param	string		$content: The PlugIn content
     * @param	array		$conf: The PlugIn configuration
     * @return	The content that is displayed on the website
     */

    function main($content, $conf) {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        $this->pi_initPIflexForm();

        $midrid = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'midrid', 'sDEF');
        $name = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'name', 'sDEF');
        $campaign = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'campaign', 'sDEF');

        if ($name == '') {
            $name = "Meld je aan voor de nieuwsbrief";
        }

        if ( $midrid != '' )
        {
        $content = '
            <div class="block nieuwsbrief">
                <div class="head"></div>
                <div class="body"> 
                    <h2>' . $name . '</h2> 
                    <div class="nieuwsbrief-form">
                        <form action="http://eo.dmd.omroep.nl/x/plugin/?pName=subscribe&amp;MIDRID=' . $midrid . '&amp;pLang=nl&amp;Z=-1162604425" method="post">
                            <input type="hidden" name="DMDtask" value="subscribe" />
 
                            <input class="textfield" type="text" name="email" value="E-mail-adres" onfocus="if (this.value==this.defaultValue) this.value=\'\';" size="60" maxlength="255" />
                            <input class="submit" type="submit" value="Ok" />
 
                            <div style="visibility:hidden;font-size:0">
                                Please <em>dont</em> insert text in the box below!
                                <input type="text" name="submitCheck" value="" style="width:1px;height:1px;font-size:0" />
                            </div>
                        </form>
                    </div>
                </div>
                <div class="bottom"></div>
            </div>
	    ';
        }
        else 
        {
    
        $content = '
            <div class="block nieuwsbrief">
                <div class="head"></div>
                <div class="body"> 
                     
                    <h2>' . $name . '</h2> 
                    <div id="errordiv"></div>
                    <div id="succes"></div>
                    <div class="nieuwsbrief-form">
                        <form id="submit" method="post"> 
                            <input type="hidden" id="aanmeldvink" name="aanmeldvink" value="1" />
                            <span>Emailadres:</span>  
                            <input id="email" class="text" name="email" size="30" type="text">  
                            <input type="hidden" id="campaign" name="campaign" value="' . $campaign . '"/>
                            <!-- <button class="button aanmelden"> Aanmelden </button> -->
                            <input type="submit" value="Registreer"/>
                        </form>  
                    </div>
                </div>
                <div class="bottom"></div>
            </div>
            <script src="/fileadmin/global/js/nieuwsbrief/nieuwsbrief.js" type="text/javascript"></script>
            ';
        }

        return $this->pi_wrapInBaseClass($content);
    }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eo_nieuwsbrief/pi1/class.tx_eonieuwsbrief_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eo_nieuwsbrief/pi1/class.tx_eonieuwsbrief_pi1.php']);
}
?>
