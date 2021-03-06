<?php namespace O876\Symbol\Renderer;

require_once('Intf.php');

use O876\Symbol\Symbol as Symbol;
use O876\Symbol\Renderer\Intf as Intf;


class XML implements Intf {

  private $nCurseLevel;
  private $sIndentStr;
  private $nCurrentLevel;

  public $sLineBreaker;
  public $sIndenter;

  public function __construct() {
    $this->nCurseLevel = 0;
    $this->sIndentStr = '';
    $this->sLineBreaker = "\n";
    $this->sIndenter = '  ';
  }

  protected function getIndentStr() {
    if ($this->sIndenter) {
      if ($this->nCurrentLevel != $this->nCurseLevel) {
        $this->sIndentStr = str_repeat($this->sIndenter, $this->nCurseLevel);
        $this->nCurrentLevel = $this->nCurseLevel;
      }
    }
    return $this->sIndentStr;
  }

  protected function renderAttributes($aAttr) {
    if (count($aAttr) == 0) {
      return '';
    }
    $s = array();
    foreach ($aAttr as $sName => $sValue) {
      $s[] = $sName . '="' . htmlentities($sValue) . '"';
    }
    $sAttributes = implode(' ', $s);
    if ($sAttributes !== '') {
		$sAttributes = ' ' . $sAttributes;
	}
    return $sAttributes;
  }

  protected function isBlock(Symbol $oSymbol) {
	return false;
  }

  public function preRender(Symbol $oSymbol) {
    $sTag = $oSymbol->getTag();
    $bBlock = $this->isBlock($oSymbol);
    $aAttributes = $oSymbol->getAttributes();
    $sData = $oSymbol->getData();
    $sRender = '';
    $sAttr = $this->renderAttributes($aAttributes);
    $sRender .= $this->getIndentStr() . '<' . $sTag . $sAttr;
	if ($bBlock) {
      $sRender .= ' />' . $this->sLineBreaker;
    } else {
      $sRender .= '>';
    }
    $this->nCurseLevel++;
    return $sRender;
  }

  public function postRender(Symbol $oSymbol) {
    $this->nCurseLevel--;
    $sTag = $oSymbol->getTag();
    if ($this->isBlock($oSymbol)) {
      return '';
    } elseif ($oSymbol->getData()) {
      return '</' . $sTag . '>' . $this->sLineBreaker;
    } else {
      return $this->getIndentStr() . '</' . $sTag . '>' . $this->sLineBreaker;
    }
  }
}
