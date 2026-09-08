<?php
/**
 *--------------------------------------------------------------------
 *
 * Class to create DataMatrix barcode
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGBarcode2D.php');

class BCGdatamatrix extends BCGBarcode2D {
	const DEBUG = false;

	const DATAMATRIX_SIZE_SMALLEST = 1;
	const DATAMATRIX_SIZE_SQUARE = 2;
	const DATAMATRIX_SIZE_RECTANGLE = 3;

	const DATAMATRIX_ENCODING_UNKNOWN = 0;
	const DATAMATRIX_ENCODING_ASCII = 1;		// ASCII, (0-127)=1byte, (128-255)=0.5byte, (numbers)=2bytes per keyword
	const DATAMATRIX_ENCODING_C40 = 2;			// Capital letters, 1.5byte per keyword
	const DATAMATRIX_ENCODING_TEXT = 3;			// Lower Case, 1.5byte per keyword
	const DATAMATRIX_ENCODING_X12 = 4;			// ANSI X12, 1.5byte per keyword
	const DATAMATRIX_ENCODING_EDIFACT = 5;		// ASCII 32-94, 1.33byte per keyword
	const DATAMATRIX_ENCODING_BASE256 = 6;		// ASCII 0-255, 1byte per keyword

	const DATAMATRIX_FNC1_NONE = 0;
	const DATAMATRIX_FNC1_GS1 = 1;
	const DATAMATRIX_FNC1_AIM = 2;

	const DATAMATRIX_MACRO_NONE = 0;
	const DATAMATRIX_MACRO_05 = 1;
	const DATAMATRIX_MACRO_06 = 2;

	const _GF = 256;
	const _MODULUS = 301;

	private $text;				// Saved Text
	private $data;				// Saved Data
	private $bitstream;		// Saved Bitstream
	private $quietZoneSize;	// int
	private $symbolNumber;		// int (for Structured Append)
	private $symbolTotal;		// int (for Structured Append)
	private $symbolIdentification;		// int (for Structured Append)
	private $acceptECI;		// bool
	private $tilde;		// bool
	private $fnc1;					// int
	private $size;				// int
	private $forceSymbolIndex;	// int
	private $encoding; // int
	private $macro;

	private $symbols;

	private $BASE_offset;
	private $BASE_C40;
	private $BASE_TEXT;
	private $SHIFT1;
	private $SHIFT2;
	private $SHIFT3_C40;
	private $SHIFT3_TEXT;
	private $X12;

	private $debug = '';

	private $currentSymbolIndex = 0;

	private $sequenceSorting = array(
				self::DATAMATRIX_ENCODING_ASCII,
				self::DATAMATRIX_ENCODING_BASE256,
				self::DATAMATRIX_ENCODING_EDIFACT,
				self::DATAMATRIX_ENCODING_TEXT,
				self::DATAMATRIX_ENCODING_X12,
				self::DATAMATRIX_ENCODING_C40
			);

	/**
	 * Constructor to create DataMatrix barcode.
	 *
	 * This method calls other public functions to set default values
	 */
	public function __construct() {
		parent::__construct();
		$this->initialize();

		$this->setAcceptECI(false);
		$this->setTilde(false);
		$this->setFNC1(self::DATAMATRIX_FNC1_NONE);
		$this->setQuietZoneSize(1);
		$this->setSize(self::DATAMATRIX_SIZE_SQUARE);
		$this->setStructuredAppend(0, 0, null);
		$this->setDatamatrixSize(-1);
		$this->setScale(4);
		$this->setEncoding(self::DATAMATRIX_ENCODING_UNKNOWN);
		$this->setMacro(self::DATAMATRIX_MACRO_NONE);
	}

	/**
	 * Gets the size of the barcode.
	 *
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * Sets the size of the barcode. Could be different value:
	 *  - DATAMATRIX_SIZE_SMALLEST: generates the smallest size (default)
	 *  - DATAMATRIX_SIZE_SQUARE: generates a square code
	 *  - DATAMATRIX_SIZE_RECTANGLE: generates a rectangle code
	 *
	 * @param int $size
	 */
	public function setSize($size) {
		$size = (int)$size;
		if ($size !== self::DATAMATRIX_SIZE_SMALLEST && $size !== self::DATAMATRIX_SIZE_SQUARE && $size !== self::DATAMATRIX_SIZE_RECTANGLE) {
			throw new BCGArgumentException('The size argument must be BCGdatamatrix::DATAMATRIX_SIZE_SMALLEST, BCGdatamatrix::DATAMATRIX_SIZE_SQUARE, or BCGdatamatrix::DATAMATRIX_SIZE_RECTANGLE.', 'size');
		}

		$this->size = $size;
	}

	/**
	 * Gets the datamatrix size.
	 *
	 * @return int[]
	 */
	public function getDatamatrixSize() {
		if ($this->forceSymbolIndex === -1) {
			return array(-1, -1);
		} else {
			return array($this->symbols[$this->forceSymbolIndex]->row, $this->symbols[$this->forceSymbolIndex]->col);
		}
	}

	/**
	 * Sets the Datamatrix you wish to use.
	 * -1 is automatic.
	 * If you don't set the second argument, it will create a square symbol.
	 *
	 * @param int $row
	 * @param int $col
	 */
	public function setDatamatrixSize($row, $col = null) {
		$r = (int)$row;
		$c = (int)$col;

		$this->forceSymbolIndex = -1;
		if ($col === null) {
			$c = $r;
		}

		if ($r > 0) {
			$nb = count($this->symbols);
			for ($i = 0; $i < $nb; $i++) {
				if ($this->symbols[$i]->row === $r && $this->symbols[$i]->col === $c) {
					$this->forceSymbolIndex = $i;
					break;
				}
			}

			if ($this->forceSymbolIndex === -1) {
				throw new BCGArgumentException('The symbol you provided doesn\'t exists.', 'row');
			}
		}
	}

	/**
	 * Gets the Quiet zone size.
	 *
	 * @return int
	 */
	public function getQuietZoneSize() {
		return $this->quietZoneSize;
	}

	/**
	 * Sets the Quiet Zone Size
	 *
	 * @param int $quietZoneSize
	 */
	public function setQuietZoneSize($quietZoneSize) {
		$quietZoneSize = (int)$quietZoneSize;
		if ($quietZoneSize < 0) {
			throw new BCGArgumentException('The quiet zone must be equal or bigger than 0.', 'quietZoneSize');
		}

		$this->quietZoneSize = $quietZoneSize;
	}

	/**
	 * Gets if it accepts the backslash as a special character.
	 *
	 * @return bool
	 */
	public function getAcceptECI() {
		return $this->acceptECI;
	}

	/**
	 * Accepts ECI code to be process as a special character.
	 * If true, you can do this:
	 *  - \\	: to make ONE backslash
	 *  - \xxxxxx	: with x a number between 0 and 9
	 *
	 * @param bool $accept
	 */
	public function setAcceptECI($accept) {
		$this->acceptECI = (bool)$accept;
	}

	/**
	 * Gets if it accepts the tilde as a special character.
	 *
	 * @return bool
	 */
	public function getTilde() {
		return $this->tilde;
	}

	/**
	 * Accepts tilde to be process as a special character.
	 * If true, you can do this:
	 *  - ~~	: to make ONE tilde
	 *  - ~F	: to insert FCN1
	 *
	 * @param boolean $accept
	 */
	public function setTilde($accept) {
		$this->tilde = (bool)$accept;
	}

	/**
	 * Datamatrix symbol can be appended to another one.
	 * The $symbolTotal must remain the same across all the Datamatrix group.
	 * Up to 16 symbols total.
	 * The first symbol is 1.
	 * If you want to reset and not use this Structured Append, set the $symbolNumber to 0.
	 * Returns true on success, false on failure.
	 * The $symbolIdentification is a number between 1 and 64516. This is used to identify symbols that belong
	 * together. The number must remain the same accross the symbols.
	 * 
	 * @param int $symbolNumber
	 * @param int $symbolTotal
	 * @param string $symbolIdentification
	 * @return bool
	 */
	public function setStructuredAppend($symbolNumber, $symbolTotal = 0, $symbolIdentification = 1) {
		if ($symbolTotal == 0) { // Keep weak
			$this->symbolNumber = 0;
			$this->symbolTotal = 0;
			$this->symbolIdentification = null;
			return true;
		} else {
			if ($this->macro !== self::DATAMATRIX_MACRO_NONE) {
				throw new BCGArgumentException('You cannot use the structured append with the macro syntax.', 'symbolNumber');
			}

			$symbolNumber = (int)$symbolNumber;
			$symbolTotal = (int)$symbolTotal;
			$symbolIdentification = (int)$symbolIdentification;

			if ($symbolNumber <= 0) {
				throw new BCGArgumentException('The symbol number must be equal or bigger than 1.', 'symbolNumber');
			}

			if ($symbolNumber > $symbolTotal) {
				throw new BCGArgumentException('The symbol number must be equal or lower than the symbol total.', 'symbolNumber');
			}

			if ($symbolTotal < 2 && $symbolTotal > 16) {
				throw new BCGArgumentException('The symbol total must be between 2 and 16.', 'symbolTotal');
			}

			if ($symbolIdentification < 1 || $symbolIdentification > 64516) {
				throw new BCGArgumentException('The symbol identification must be between 1 and 64516.', 'symbolIdentification');
			}

			$this->symbolNumber = $symbolNumber;
			$this->symbolTotal = $symbolTotal;
			$this->symbolIdentification = $symbolIdentification;

			return true;
		}
	}

	/**
	 * Sets the FNC1 type for the barcode. The argument $fnc1Type can be:
	 *  - DATAMATRIX_FNC1_NONE: No FNC1 will be used
	 *  - DATAMATRIX_FNC1_GS1: FNC1 will be used with GS1 standard.
	 *  - DATAMATRIX_FNC1_AIM: FNC1 will be used with AIM standard
	 *
	 * @param int $fnc1Type
	 * @param mixed $fnc1Id
	 */
	public function setFNC1($fnc1Type) {
		$fnc1Type = (int)$fnc1Type;
		if ($fnc1Type !== self::DATAMATRIX_FNC1_NONE && $fnc1Type !== self::DATAMATRIX_FNC1_GS1 && $fnc1Type !== self::DATAMATRIX_FNC1_AIM) {
			throw new BCGArgumentException('The FNC1 type must be BCGdatamatrix::DATAMATRIX_FNC1_NONE, BCGdatamatrix::DATAMATRIX_FNC1_GS1, or BCGdatamatrix::DATAMATRIX_FNC1_AIM.', 'fnc1Type');
		}

		$this->fnc1 = $fnc1Type;
	}

	/**
	 * Gets the forced encoding.
	 *
	 * @return int
	 */
	public function getEncoding() {
		return $this->encoding;
	}

	/**
	 * Forces the encoding to be used.
	 *
	 * @param int $encoding
	 */
	public function setEncoding($encoding) {
		$encoding = (int)$encoding;
		if ($encoding !== self::DATAMATRIX_ENCODING_UNKNOWN && $encoding !== self::DATAMATRIX_ENCODING_ASCII && $encoding !== self::DATAMATRIX_ENCODING_C40 && $encoding !== self::DATAMATRIX_ENCODING_TEXT && $encoding !== self::DATAMATRIX_ENCODING_X12 && $encoding !== self::DATAMATRIX_ENCODING_EDIFACT && $encoding !== self::DATAMATRIX_ENCODING_BASE256) {
			throw new BCGArgumentException('The encoding argument must be BCGdatamatrix::DATAMATRIX_ENCODING_UNKNOWN, BCGdatamatrix::DATAMATRIX_ENCODING_ASCII, BCGdatamatrix::DATAMATRIX_ENCODING_C40, BCGdatamatrix::DATAMATRIX_ENCODING_TEXT, BCGdatamatrix::DATAMATRIX_ENCODING_X12, BCGdatamatrix::DATAMATRIX_ENCODING_EDIFACT, or BCGdatamatrix::DATAMATRIX_ENCODING_BASE256.', 'encoding');
		}

		$this->encoding = $encoding;
	}

	/**
	 * Gets the macro.
	 *
	 * @return int
	 */
	public function getMacro() {
		return $this->macro;
	}

	/**
	 * Sets the macro.
	 *
	 * @param int $macro
	 */
	public function setMacro($macro) {
		$macro = (int)$macro;
		if ($macro !== self::DATAMATRIX_MACRO_NONE && $macro !== self::DATAMATRIX_MACRO_05 && $macro !== self::DATAMATRIX_MACRO_06) {
			throw new BCGArgumentException('The macro argument must be BCGdatamatrix::DATAMATRIX_MACRO_NONE, BCGdatamatrix::DATAMATRIX_MACRO_05, or BCGdatamatrix::DATAMATRIX_MACRO_06.', 'macro');
		}

		if ($macro !== self::DATAMATRIX_MACRO_NONE && $this->symbolNumber !== 0) {
			throw new BCGArgumentException('You cannot use the macro syntax with the structured append.', 'macro');
		}

		$this->macro = $macro;
	}

	/**
	 * Draws the barcode.
	 *
	 * @param resource $im
	 */
	public function draw($im) {
		$symbol = $this->symbols[$this->currentSymbolIndex];

		if ($this->quietZoneSize) {
			$this->drawFilledRectangle($im, 0, 0, $symbol->col + $this->quietZoneSize * 2 - 1, $symbol->row + $this->quietZoneSize * 2 - 1, BCGBarcode::COLOR_BG);
		}

		$mappingSize = $symbol->getMappingSize();
		$placement = new _BCGdatamatrix_placement($mappingSize[0], $mappingSize[1]);
		$matrix = $placement->getMatrix($this->bitstream);

		$this->drawAlignmentsAndMatrix($im, $matrix);

		$this->drawText($im, 0, 0, $symbol->col + $this->quietZoneSize * 2, $symbol->row + $this->quietZoneSize * 2);
	}

	/**
	 * Parses the text before displaying it.
	 *
	 * @param mixed $text
	 */
	public function parse($text) {
		if (strlen($text) === 0) {
			return;
		}

		$seq = $this->getSequence($text);

		if ($seq !== '') {
			$bitstream = $this->createBinaryStream($text, $seq);
			$this->bitstream = $bitstream;
		}
	}

	/**
	 * Returns the maximal size of a barcode.
	 *
	 * @param int $w
	 * @param int $h
	 * @return int[]
	 */
	public function getDimension($w, $h) {
		$symbol = $this->symbols[$this->currentSymbolIndex];
		$w += $symbol->col + $this->quietZoneSize * 2;
		$h += $symbol->row + $this->quietZoneSize * 2;

		return parent::getDimension($w, $h);
	}

	/**
	 * Finds the mode we have to encode the following data starting at position $i.
	 *
	 * @param string $text The full text
	 * @param int $i The position of where we are encoding
	 * @param int $currentMode The current mode
	 * @return int or false
	 */
	protected function getSequenceLookAhead($text, $i, $currentMode) {
		if ($this->encoding !== self::DATAMATRIX_ENCODING_UNKNOWN) {
			if ($i >= strlen($text)) {
				return false;
			}

			return $this->encoding;
		}

		$count = array();

		if ($currentMode === self::DATAMATRIX_ENCODING_ASCII) {
			$count = array(
				self::DATAMATRIX_ENCODING_ASCII => 0,
				self::DATAMATRIX_ENCODING_C40 => 1,
				self::DATAMATRIX_ENCODING_TEXT => 1,
				self::DATAMATRIX_ENCODING_X12 => 1,
				self::DATAMATRIX_ENCODING_EDIFACT => 1,
				self::DATAMATRIX_ENCODING_BASE256 => 1.25
			);
		} else {
			$count = array(
				self::DATAMATRIX_ENCODING_ASCII => 1,
				self::DATAMATRIX_ENCODING_C40 => 2,
				self::DATAMATRIX_ENCODING_TEXT => 2,
				self::DATAMATRIX_ENCODING_X12 => 2,
				self::DATAMATRIX_ENCODING_EDIFACT => 2,
				self::DATAMATRIX_ENCODING_BASE256 => 2.25
			);
		}

		$count[$currentMode] = 0;

		$c = strlen($text);
		for ($counter = 1; $i < $c; $i++, $counter++) {
			$t = $text[$i];
			$o = ord($t);
			$isFNC1 = false;

			// If \ and we accept ECI, then we must go to ASCII
			if ($this->isCharECI($text, $i)) {
				return self::DATAMATRIX_ENCODING_ASCII;
			}

			if ($this->isCharFNC1($text, $i)) {
				$i++;
				$isFNC1 = true;
			}

			// ASCII count
			if ($isFNC1) {
				$count[self::DATAMATRIX_ENCODING_ASCII] = ceil($count[self::DATAMATRIX_ENCODING_ASCII]) + 1;
			} elseif (is_numeric($t)) {
				$count[self::DATAMATRIX_ENCODING_ASCII] += 0.5;
			} elseif ($o > 127) {
				$count[self::DATAMATRIX_ENCODING_ASCII] = ceil($count[self::DATAMATRIX_ENCODING_ASCII]) + 2;
			} else {
				$count[self::DATAMATRIX_ENCODING_ASCII] = ceil($count[self::DATAMATRIX_ENCODING_ASCII]) + 1;
			}

			// C40 count
			if ($isFNC1 || strpos($this->BASE_C40, $t) !== false) {
				$count[self::DATAMATRIX_ENCODING_C40] += 2 / 3;
			} elseif ($o > 127) {
				$count[self::DATAMATRIX_ENCODING_C40] += 8 / 3;
			} else {
				$count[self::DATAMATRIX_ENCODING_C40] += 4 / 3;
			}

			// Text count
			if ($isFNC1 || strpos($this->BASE_TEXT, $t) !== false) {
				$count[self::DATAMATRIX_ENCODING_TEXT] += 2 / 3;
			} elseif ($o > 127) {
				$count[self::DATAMATRIX_ENCODING_TEXT] += 8 / 3;
			} else {
				$count[self::DATAMATRIX_ENCODING_TEXT] += 4 / 3;
			}

			// X12 count
			if ($isFNC1) {
				$count[self::DATAMATRIX_ENCODING_X12] += 10 / 3;
			} elseif (strpos($this->X12, $t) !== false) {
				$count[self::DATAMATRIX_ENCODING_X12] += 2 / 3;
			} elseif ($o > 127) {
				$count[self::DATAMATRIX_ENCODING_X12] += 13 / 3;
			} else {
				$count[self::DATAMATRIX_ENCODING_X12] += 10 / 3;
			}

			// EDF count
			if ($isFNC1) {
				$count[self::DATAMATRIX_ENCODING_EDIFACT] += 13 / 4;
			} elseif ($o >= 32 && $o <= 94) {
				$count[self::DATAMATRIX_ENCODING_EDIFACT] += 3 / 4;
			} elseif ($o > 127) {
				$count[self::DATAMATRIX_ENCODING_EDIFACT] += 17 / 4;
			} else {
				$count[self::DATAMATRIX_ENCODING_EDIFACT] += 13 / 4;
			}

			// Base256 count
			if ($isFNC1 /* TODO Reader Program, Code Page */) {
				$count[self::DATAMATRIX_ENCODING_BASE256] += 4;
			} else {
				$count[self::DATAMATRIX_ENCODING_BASE256] += 1;
			}

			// Check return condition
			if ($counter >= 4) {
				$returnCondition = $this->checkReturnCondition($count, false, $text, $i + 1);
				if ($returnCondition !== false) {
					return $returnCondition;
				}
			}
		}

		// We are here so we have no more data
		return $this->checkReturnCondition($count, true);
	}

	private function initialize() {
		$this->BASE_offset = 3;
		$this->BASE_C40 = ' 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$this->BASE_TEXT = strtolower($this->BASE_C40);

		$this->SHIFT1 = '';
		for ($i = 0; $i <= 31; $i++) {
			$this->SHIFT1 .= chr($i);
		}

		$this->SHIFT2 = '!"#$%&\'()*+,-./:;<=>?@[\\]^_';

		$this->SHIFT3_C40 = chr(96) . 'abcdefghijklmnopqrstuvwxyz{|}~' . chr(127);
		$this->SHIFT3_TEXT = strtoupper($this->SHIFT3_C40);

		$this->X12 = chr(13) . '*> 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$this->symbols[] = new _BCGdatamatrix_InfoSquare(10, 1, 3, 5, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(12, 1, 5, 7, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(14, 1, 8, 10, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(16, 1, 12, 12, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(18, 1, 18, 14, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(20, 1, 22, 18, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(22, 1, 30, 20, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(24, 1, 36, 24, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(26, 1, 44, 28, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(32, 4, 62, 36, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(36, 4, 86, 42, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(40, 4, 114, 48, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(44, 4, 144, 56, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(48, 4, 174, 68, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(52, 4, 102, 42, 2);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(64, 16, 140, 56, 2);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(72, 16, 92, 36, 4);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(80, 16, 114, 48, 4);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(88, 16, 144, 56, 4);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(96, 16, 174, 68, 4);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(104, 16, 136, 56, 6);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(120, 36, 175, 68, 6);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(132, 36, 163, 62, 8);
		$this->symbols[] = new _BCGdatamatrix_InfoSquare(144, 36, array(156, 155), array(62, 62), array(8, 2));

		$this->symbols[] = new _BCGdatamatrix_InfoRectangle(8, 18, 1, 5, 7, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoRectangle(8, 32, 2, 10, 11, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoRectangle(12, 26, 1, 16, 14, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoRectangle(12, 36, 2, 22, 18, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoRectangle(16, 36, 2, 32, 24, 1);
		$this->symbols[] = new _BCGdatamatrix_InfoRectangle(16, 48, 2, 49, 28, 1);

		$this->currentSymbolIndex = 0;

		usort($this->symbols, array($this, 'sortSymbol'));
	}

	private function sortSymbol($a, $b) {
		$s1 = $a->getDataSize();
		$s2 = $b->getDataSize();
		if ($s1 < $s2) {
			return -1;
		} elseif ($s1 > $s2) {
			return 1;
		} else {
			return 0;
		}
	}

	private function accomodateSymbol($size) {
		if ($this->forceSymbolIndex > -1) {
			$this->currentSymbolIndex = null;
			if ($size <= $this->symbols[$this->forceSymbolIndex]->getDataSize()) {
				$this->currentSymbolIndex = $this->forceSymbolIndex;
			}
		} else {
			$c = count($this->symbols);
			$startingIndex = $this->currentSymbolIndex;
			$this->currentSymbolIndex = null;
			for ($i = $startingIndex; $i < $c; $i++) {
				if ($this->size === self::DATAMATRIX_SIZE_SQUARE && !($this->symbols[$i] instanceof _BCGdatamatrix_InfoSquare)) {
					continue;
				}

				if ($this->size === self::DATAMATRIX_SIZE_RECTANGLE && !($this->symbols[$i] instanceof _BCGdatamatrix_InfoRectangle)) {
					continue;
				}

				if ($size <= $this->symbols[$i]->getDataSize()) {
					$this->currentSymbolIndex = $i;
					break;
				}
			}
		}

		if ($this->currentSymbolIndex === null) {
			$extendedMessage = ($this->forceSymbolIndex > -1) ? ' Specify a bigger Datamatrix size or let the application choose by default.' : '';
			throw new BCGParseException('datamatrix', 'There is no valid symbol that can fit your data.' . $extendedMessage);
		}
	}

	/**
	 * Creates the binary stream based on the $text and the 3 sequences $seq passed.
	 * The output will be an array of string containing 8 bits 1 and 0 (binary as string)
	 *
	 * @param string $text
	 * @param string[] $seq
	 * @return string[]
	 */
	private function createBinaryStream($text, $seq) {
		$symbol = $this->symbols[$this->currentSymbolIndex];

		if (is_array($symbol->data)) {
			$finalError = self::getFinalError($seq, $symbol->block[0], $symbol->data[0], $symbol->error[0], $symbol->block[1]);
		} else {
			$finalError = self::getFinalError($seq, $symbol->block, $symbol->data, $symbol->error);
		}

		$finalMerged = array_merge($seq, $finalError);

		return $finalMerged;
	}

	private function drawAlignmentsAndMatrix($im, $matrix) {
		$symbol = $this->symbols[$this->currentSymbolIndex];

		$line = (int)sqrt($symbol->region);
		$col = $symbol->region == 2 ? 2 : $line;
		$w = $symbol->col / $col;
		$h = $symbol->row / $line;

		for ($i = 0; $i < $col; $i++) {
			for ($j = 0; $j < $line; $j++) {
				$this->drawAlignment($im, $w * $i + $this->quietZoneSize, $h * $j + $this->quietZoneSize, $w, $h);
			}
		}

		$c1 = count($matrix);
		$c2 = count($matrix[0]);
		for ($y = $i = 0; $i < $c1; $y++, $i++) { // Line
			for ($x = $j = 0; $j < $c2; $x++, $j++) { // Column
				$xT = $x + intval($j / ($w - 2)) * 2;
				$yT = $y + intval($i / ($h - 2)) * 2;

				$this->drawPixel($im, 1 + $this->quietZoneSize + $xT, 1 + $this->quietZoneSize + $yT, $matrix[$i][$j] ? BCGBarcode::COLOR_FG : BCGBarcode::COLOR_BG);
			}
		}
	}
	
	/**
	 * Draws the alignment pattern at position $x & $y (top left) of width $w and height $h
	 */
	private function drawAlignment($im, $x, $y, $w, $h) {
		$this->drawRectangle($im, $x, $y, $x, $y + $h - 1, BCGBarcode::COLOR_FG);
		$this->drawRectangle($im, $x, $y + $h - 1, $x + $w - 1, $y + $h - 1, BCGBarcode::COLOR_FG);

		$color = array(BCGBarcode::COLOR_FG , BCGBarcode::COLOR_BG);
		for ($i = 1; $i < $w; $i++) {
			$this->drawPixel($im, $x + $i, $y, $color[$i % 2]);
		}

		for ($i = 0; $i < $h; $i++) {
			$this->drawPixel($im, $x + $w - 1, $y + $i, $color[($i + 1) % 2]);
		}
	}

	/**
	 * Returns if the character represents the ECI code \Exxxxxx
	 *
	 * @param string $text
	 * @param int $i
	 * @return bool
	 */
	private function isCharECI($text, $i) {
		if ($this->acceptECI) {
			if ($text[$i] === '\\') {
				$temp = substr($text, $i + 1, 6);
				if (strlen($temp) === 6 && is_numeric($temp)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Returns if the character represents the FNC1 ~F
	 *
	 * @param string $text
	 * @param int $i
	 * @return bool
	 */
	private function isCharFNC1($text, $i) {
		if ($this->tilde) {
			if ($text[$i] === '~') {
				if (isset($text[$i + 1]) && $text[$i + 1] === 'F') {
					return true;
				}
			}
		}

		return false;
	}

	private function checkReturnCondition($count, $eod, $text = null, $i = 0) {
		if ($eod) {
			while (list($key, $value) = each($count)) {
				$count[$key] = ceil($value);
			}

			// Sorting
			_BCGdatamatrix_sort::sort($count, $this->sequenceSorting);
			$type1 = each($count);
			$type2 = each($count);

			if ($type1['value'] == 0) { // Keep weak check
				return false;
			} else {
				// The documentation says ASCII+1. We do not follow.
				return $type1['key'];
			}
		} else {
			// Sorting
			_BCGdatamatrix_sort::sort($count, $this->sequenceSorting);
			$type1 = each($count);
			$type2 = each($count);
			
			switch ($type1['key']) {
				case self::DATAMATRIX_ENCODING_ASCII:
					if ($type1['value'] + 1 <= $type2['value']) {
						return self::DATAMATRIX_ENCODING_ASCII;
					}
					break;
				case self::DATAMATRIX_ENCODING_BASE256:
					if (($type2['key'] === self::DATAMATRIX_ENCODING_ASCII && $type1['value'] + 1 <= $type2['value']) || ($type2['key'] !== self::DATAMATRIX_ENCODING_ASCII && $type1['value'] < $type2['value'])) {
						return self::DATAMATRIX_ENCODING_BASE256;
					}
					break;
				case self::DATAMATRIX_ENCODING_EDIFACT:
				case self::DATAMATRIX_ENCODING_TEXT:
					if ($type1['value'] < $type2['value']) {
						return $type1['key'];
					}
					break;
				case self::DATAMATRIX_ENCODING_C40:
					if ($type1['value'] + 1 < $type2['value'] && ($type2['key'] === self::DATAMATRIX_ENCODING_ASCII || $type2['key'] === self::DATAMATRIX_ENCODING_BASE256 || $type2['key'] === self::DATAMATRIX_ENCODING_EDIFACT || $type2['key'] === self::DATAMATRIX_ENCODING_TEXT)) {
						if ($type1['value'] < $count[self::DATAMATRIX_ENCODING_X12]) {
							return self::DATAMATRIX_ENCODING_C40;
						} elseif ($type1['value'] == $count[self::DATAMATRIX_ENCODING_X12]) { // keep weak
							$found = true;
							$c = strlen($text);
							for (; $i < $c; $i++) {
								$pos = strpos($this->X12, $text[$i]);
								if ($pos === 0 || $pos === 1 || $pos === 2) {
									$found = true;
									break;
								} elseif ($pos === false) {
									break;
								}
							}

							if ($found) {
								return self::DATAMATRIX_ENCODING_X12;
							} else {
								return self::DATAMATRix_ENCODING_C40;
							}
						}
					}
					break;
			}

			return false;
		}
	}
	
	/**
	 * Adds the $data to the next $sequence in the correct $mode
	 * If the current sequence used is $mode, then we append the $data to the current sequence.
	 * Otherwise we create a new sequence.
	 *
	 * @param mixed $sequence
	 * @param int $mode
	 * @param string $data
	 */
	private function addToSequence(&$sequence, $mode, $data) {
		$c = count($sequence);
		$currentSequence = null;
		if ($c > 0) {
			$currentSequence = $sequence[$c - 1][0];
		}

		if ($currentSequence !== $mode) {
			$sequence[] = array($mode, $data);
		} else {
			$sequence[$c - 1][1] .= $data;
		}
	}

	/**
	 * Will check if we have correct \ if acceptECI is activated.
	 * Will check if we have correct ~ if tilde is activated.
	 * Returns the sanitized string.
	 *
	 * @param string $text
	 * @return string
	 */
	private function sanitizeText($text) {
		if ($this->acceptECI || $this->tilde) {
			$c = strlen($text);
			for ($i = 0; $i < $c; $i++) {
				if ($this->acceptECI && $text[$i] === '\\' && !$this->isCharECI($text, $i)) {
					// We got a \\, do we have a following one?
					if (isset($text[$i + 1]) && $text[$i + 1] === '\\') {
						$text = substr($text, 0, $i) . substr($text, $i + 1);
						$c--;
					} else {
						throw new BCGParseException('datamatrix', 'Incorrect ECI code detected. ECI code must contain a backslash followed by 6 digits or double the backslash to write one backslash.');
					}
				} elseif ($this->tilde && $text[$i] === '~' && !$this->isCharFNC1($text, $i)) {
					// We got a ~, do we have a following one?
					if (isset($text[$i + 1]) && $text[$i + 1] === '~') {
						$text = substr($text, 0, $i) . substr($text, $i + 1);
						$c--;
					} else {
						throw new BCGParseException('datamatrix', 'Incorrect tilde code detected. Tilde code must be ~~ or ~F.');
					}
				}
			}
		}

		return $text;
	}

	private function getSequence($text) {
		$this->currentSymbolIndex = 0;

		// Start with the Structured Append if present.
		$this->data = $this->getStructuredAppendOrMacroCodewords();

		$currentMode = self::DATAMATRIX_ENCODING_ASCII;

		$text = $this->sanitizeText($text);
		$c = strlen($text);
		for ($i = 0; $i < $c; $i++) {
			$nextMode = $this->getSequenceLookAhead($text, $i, $currentMode);

			switch ($currentMode) {
				case self::DATAMATRIX_ENCODING_ASCII:
					if ($this->isCharECI($text, $i)) {
						$this->data[] = 241;
						$eci = (int)substr($text, $i + 1, 6);
						self::debug('[ASCII ECI] '. $eci);
						if ($eci <= 126) {
							$this->data[] = $eci + 1;
						} else if ($eci <= 16382) {
							$this->data[] = intval(($eci - 127) / 254) + 128;
							$this->data[] = ($eci - 127) % 254 + 1;
						} else {
							$this->data[] = intval(($eci - 16383) / 64516) + 192;
							$this->data[] = intval(($eci - 16383) / 254) % 254 + 1;
							$this->data[] = ($eci - 16383) % 254 + 1;
						}

						$i += 6;
					} elseif ($this->isCharFNC1($text, $i)) {
						$this->data[] = 232;
						$i += 1;
					} else {
						$o1 = isset($text[$i]) ? ord($text[$i]) : null;
						$o2 = isset($text[$i + 1]) ? ord($text[$i + 1]) : null;
						$mode = null;

						if ($o1 >= 0x30 && $o1 <= 0x39 && $o2 >= 0x30 && $o2 <= 0x39) {
							self::debug('[ASCII NUMBER] '. $o1 . ' ' . $o2 . ' - ' . $text[$i] . $text[$i + 1]);
							$this->debug .= "Ascii Number\n";
							$this->data[] = intval($text[$i] . $text[$i + 1]) + 130;
							$i++;
						} elseif ($nextMode !== self::DATAMATRIX_ENCODING_ASCII) {
							if ($nextMode === self::DATAMATRIX_ENCODING_BASE256) {
								// Pb3
								self::debug('[ASCII SWITCHING] '. $nextMode);
								$currentMode = $nextMode;
								$i--;
							} else {
								self::debug('[ASCII SWITCHING] '. $nextMode);
								$currentMode = $nextMode;
								$i--;
							}
						} elseif ($o1 > 127) {
							self::debug('[ASCII EXTENDED] '. $o1 . ' - ' . $text[$i]);
							$this->debug .= "Ascii Extended\n";
							$this->data[] = 235;
							$this->data[] = $o1 - 127;
						} else {
							self::debug('[ASCII NORMAL] '. ($o1 + 1) . ' - ' . $text[$i]);
							$this->debug .= "Ascii Normal\n";
							$this->data[] = $o1 + 1;
						}
					}
				break;
				case self::DATAMATRIX_ENCODING_C40:
					$subText = '';
					$counter = 0;
					do {
						$subText .= $text[$i];
						$i++;
						$counter++;
					} while ($counter < 2 || ($nextMode = $this->getSequenceLookAhead($text, $i, $currentMode)) === self::DATAMATRIX_ENCODING_C40);

					self::debug('[C40] '. $subText);
					$this->debug .= "C40\n";
					$this->data = array_merge($this->data, $this->parseC40($subText, $nextMode === false));
					$currentMode = $nextMode;
					$i--;
				break;
				case self::DATAMATRIX_ENCODING_TEXT:
					$subText = '';
					$counter = 0;
					do {
						$subText .= $text[$i];
						$i++;
						$nextMode = $this->getSequenceLookAhead($text, $i, $currentMode);
						$counter++;
					} while (isset($text[$i]) && ($counter < 2 || ($nextMode = $this->getSequenceLookAhead($text, $i, $currentMode)) === self::DATAMATRIX_ENCODING_TEXT));

					self::debug('[TEXT] '. $subText);
					$this->debug .= "TEXT\n";
					$this->data = array_merge($this->data, $this->parseText($subText, $nextMode === false));
					$currentMode = $nextMode;
					$i--;
				break;
				case self::DATAMATRIX_ENCODING_X12:
					$subText = '';
					$counter = 0;
					do {
						$subText .= $text[$i];
						$i++;
						$counter++;
					} while (isset($text[$i]) && ($counter < 2 || ($nextMode = $this->getSequenceLookAhead($text, $i, $currentMode)) === self::DATAMATRIX_ENCODING_X12));

					self::debug('[X12] '. $subText);
					$this->debug .= "X12\n";
					$this->data = array_merge($this->data, $this->parseX12($subText, $nextMode === false));
					$currentMode = $nextMode;
					$i--;
				break;
				case self::DATAMATRIX_ENCODING_EDIFACT:
					$subText = '';
					$counter = 0;
					do {
						$subText .= $text[$i];
						$i++;
						$counter++;
					} while (isset($text[$i]) && ($counter < 3 || ($nextMode = $this->getSequenceLookAhead($text, $i, $currentMode)) === self::DATAMATRIX_ENCODING_EDIFACT));

					self::debug('[EDIFACT] '. $subText);
					$this->debug .= "Edifact\n";
					$this->data = array_merge($this->data, $this->parseEdifact($subText));

					$currentMode = $nextMode;
					$i--;
				break;
				case self::DATAMATRIX_ENCODING_BASE256:
					$subText = '';
					do {
						$subText .= $text[$i];
						$i++;
						$nextMode = $this->getSequenceLookAhead($text, $i, $currentMode);
					} while (isset($text[$i]) && $nextMode === self::DATAMATRIX_ENCODING_BASE256);

					self::debug('[BASE256] '. $subText);
					$this->debug .= "Base256\n";
					$this->data = array_merge($this->data, $this->parseBase256($subText));
					$currentMode = $nextMode;
					$i--;
				break;
			}

			$this->accomodateSymbol(count($this->data));
		}

		// We have FNC1?
		if ($this->fnc1 !== self::DATAMATRIX_FNC1_NONE) {
			$position = 0;
			if ($this->fnc1 === self::DATAMATRIX_FNC1_AIM) {
				$position = 1;
			}

			if ($this->symbolNumber === 1) {
				$position += 4;
			}

			if ($this->symbolNumber === 0 || $this->symbolNumber === 1) {
				array_splice($this->data, $position, 0, 232);
				$this->accomodateSymbol(count($this->data));
			}
		}

		// We need padding?
		$c = count($this->data);
		$fullLength = $this->symbols[$this->currentSymbolIndex]->getDataSize();
		if ($fullLength - $c > 0) {
			$this->data[] = 129;

			$morePad = $fullLength - $c - 1;
			if ($morePad > 0) {
				$pad = array_fill(0, $morePad, 129);
				$this->data = array_merge($this->data, $this->randomize253($pad, $c + 2));
			}
		}

		return $this->data;
	}

	/**
	 * Returns the 4 codewords for Structured Append only if activated
	 * Or return 1 codeword if the macro is activated
	 *
	 * @return int[]
	 */
	private function getStructuredAppendOrMacroCodewords() {
		$codewords = array();
		if ($this->symbolNumber > 0) {
			$codewords[0] = 233;
			$codewords[1] = ($this->symbolNumber - 1) << 4 | (17 - $this->symbolTotal);
			$codewords[2] = intval(($this->symbolIdentification - 1) / 254) + 1;
			$codewords[3] = ($this->symbolIdentification - 1) % 254 + 1;
		} elseif ($this->macro === self::DATAMATRIX_MACRO_05) {
			$codewords[] = 236;
		} elseif ($this->macro === self::DATAMATRIX_MACRO_06) {
			$codewords[] = 237;
		}

		return $codewords;
	}

	/**
	 * Parses Ascii.
	 *
	 * The method will try to optimize the code receive in argument to transfer
	 * it into keywords.
	 * The $text value should be clean and all characters are supposed to be allowed.
	 *
	 * @param string $text
	 * @return array Keywords
	 */
	private function parseAscii($text) {
		$c = strlen($text);

		$data = array();
		for ($i = 0; $i < $c; $i++) {
			if ($this->isCharFNC1($text, $i)) {
				$data[] = 232;
				$i++;
				continue;
			}

			$o = ord($text[$i]);

			// Do we have 2 numbers in a row?
			if ($o >= 48 && $o <= 57 && isset($text[$i + 1])) {
				// We have the first number. Check second
				$o2 = ord($text[$i + 1]);
				if ($o2 >= 48 && $o2 <= 57) {
					$data[] = intval($text[$i] . $text[$i + 1]) + 130;
					$i++;
					continue;
				}
			}

			if ($o <= 127) {
				$data[] = $o + 1;
			} else {
				$data[] = 235;
				$data[] = $o - 127;
			}
		}

		return $data;
	}

	/**
	 * Parses C40.
	 *
	 * The method will try to optimize the code receive in argument to transfer
	 * it into keywords. C40 uses mostly capital letters.
	 * The $text value should be clean and all characters are supposed to be allowed.
	 *
	 * @param string $text
	 * @param bool $last
	 * @return array Keywords
	 */
	private function parseC40($text, $last) {
		return array_merge(array(230), $this->parseC40AndText('C40', $text, $last));
	}

	/**
	 * Parses Text.
	 *
	 * The method will try to optimize the code receive in argument to transfer
	 * it into keywords. Text uses mostly lower case.
	 * The $text value should be clean and all characters are supposed to be allowed.
	 *
	 * @param string $text
	 * @param bool $last
	 * @return array Keywords
	 */
	private function parseText($text, $last) {
		return array_merge(array(239), $this->parseC40AndText('TEXT', $text, $last));
	}

	/**
	 * Parses X12.
	 *
	 * The method will transorm the text into keywords.
	 * The $text value should be clean and all characters are supposed to be allowed.
	 *
	 * @param string $text
	 * @param bool $last
	 * @return array Keywords
	 */
	private function parseX12($text, $last) {
		$code = array();

		$c = strlen($text);
		for ($i = 0; $i < $c; $i++) {
			$currentChar = $text[$i];

			// Search in X12
			$pos = strpos($this->X12, $currentChar);
			if ($pos !== false) {
				$code[] = $pos;
			}
		}

		return array_merge(array(238), $this->calculateX12C40Text($code, $currentChar, true, $last));
	}

	/**
	 * Parses Edifact.
	 *
	 * The method will transorm the text into keywords.
	 * The $text value should be clean and all characters are supposed to be allowed.
	 *
	 * @param string $text
	 * @return array Keywords
	 */
	private function parseEdifact($text) {
		$fullFill = false;		// TODO
	
		$c = strlen($text);

		$edifact = array();
		for ($i = 0; $i < $c; $i++) {
			$o = ord($text[$i]);
			if ($o >= 64) {
				$edifact[] = $o - 64;
			} else {
				$edifact[] = $o;
			}
		}

		if (!$fullFill) {
			$edifact[] = 31;
		}

		$data = array();
		$c = count($edifact);
		for ($i = 0; $i < $c; $i += 4) {
			$s = $edifact[$i] * 262144;
			$nbData = 1;
			if (isset($edifact[$i + 1])) {
				$nbData = 2;
				$s += $edifact[$i + 1] * 4096;
				if (isset($edifact[$i + 2])) {
					$nbData = 3;
					$s += $edifact[$i + 2] * 64;
					if (isset($edifact[$i + 3])) {
						$nbData = 3; // Data 3
						$s += $edifact[$i + 3];
					}
				}
			}

			$data[] = intval($s / 65536);

			if ($nbData >= 2) {
				$data[] = intval(($s % 65536) / 256);
				if ($nbData >= 3) {
					$data[] = $s % 256;
				}
			}
		}

		return array_merge(array(240), $data);
	}

	private function randomize253($input, $position) {
		$c = count($input);
		for ($i = 0; $i < $c; $i++) {
			$random = ((149 * ($i + $position)) % 253) + 1;
			$input[$i] = ($input[$i] + $random) % 254;
		}

		return $input;
	}

	private function randomize255($input, $position) {
		$c = count($input);
		for ($i = 0; $i < $c; $i++) {
			$random = ((149 * ($i + $position)) % 255) + 1;
			$input[$i] = ($input[$i] + $random) % 256;
		}

		return $input;
	}

	/**
	 * Parses Base256.
	 *
	 * The method will transorm the text into keywords.
	 * The $text value should be clean and all characters are supposed to be allowed.
	 *
	 * @param string $text
	 * @return array Keywords
	 */
	private function parseBase256($text) {
		$fullFill = false;		// TODO
		$data = array();
		$convert = str_split($text, 1555);

		$c = count($convert);
		for ($i = 0; $i < $c; $i++) {
			$len = strlen($convert[$i]);
			
			$current = array();

			// Encodes the "Lenght"
			if ($fullFill && ($i + 1) === $c) {
				$current[] = 0;
			} elseif ($len < 250) {
				$current[] = $len;
			} else if ($len >= 250) {
				$current[] = intval($len / 250) + 249;
				$current[] = $len % 250;
			}

			for ($j = 0; $j < $len; $j++) {
				$current[] = ord($convert[$i][$j]);
			}

			// Encodes the data - 255-state algorithm
			$data = array_merge($data, array(231), $this->randomize255($current, count($this->data) + count($data) + 2));
		}

		return $data;
	}

	/**
	 * Parses the $text and returns the correct keywords depending
	 * on the $type.
	 *
	 * @param string $type C40 or TEXT
	 * @param string $text
	 * @param bool $last
	 * @return int[]
	 */
	private function parseC40AndText($type, $text, $last) {
		$code = array();

		$isCurrentCharData = false;
		$c = strlen($text);
		for ($i = 0; $i < $c; $i++) {
			$isCurrentCharData = false;
			$isExtendedChar = false;
			$currentChar = $text[$i];

			// FNC1 ?
			if ($this->isCharFNC1($text, $i)) {
				$code[] = 1; // Shift 1
				$code[] = 27; // FNC1
				$i++;
				continue;
			}

			$o = ord($text[$i]);
			// Extended Char?
			if ($o >= 128) {
				$code[] = 1; // Shift 1
				$code[] = 30; // Upper Shift
				$currentChar = chr($o - 128);
			}

			// Search in BASE
			$pos = strpos($this->{'BASE_' . $type}, $currentChar);
			if ($pos !== false) {
				$isCurrentCharData = $isExtendedChar ? false : true;
				$code[] = $pos + $this->BASE_offset;
				continue;
			}

			// Search in SHIFT3
			$pos = strpos($this->{'SHIFT3_' . $type}, $currentChar);
			if ($pos !== false) {
				$code[] = 2; // SHIFT3 Code
				$code[] = $pos;
				continue;
			}

			// Search in SHIFT2
			$pos = strpos($this->SHIFT2, $currentChar);
			if ($pos !== false) {
				$code[] = 1; // SHIFT2 Code
				$code[] = $pos;
				continue;
			}

			// Search in SHIFT1
			$pos = strpos($this->SHIFT1, $currentChar);
			if ($pos !== false) {
				$code[] = 0; // SHIFT1 Code
				$code[] = $pos;
				continue;
			}
		}

		return $this->calculateX12C40Text($code, $currentChar, $isCurrentCharData, $last);
	}

	/**
	 * This method will pad the $code Array to have a count
	 * which can be divided by $mod. We will pad with $padCode
	 *
	 * @param int[] $code
	 * @param int $mod
	 * @param int $padCode
	 * @return int[]
	 */
	private function padWithCode($code, $mod, $padCode) {
		// Group by $mod
		$n = count($code) % $mod;
		if ($n > 0) {
			$c = $mod - $n;
			for ($i = 0; $i < $c; $i++) {
				$code[] = $padCode;
			}
		}

		return $code;
	}

	private function calculateX12C40TextEncoding($code) {
		$data = array();
		$c = count($code);

		for ($i = 0; $i < $c; $i += 3) {
			$c1 = $code[$i];
			$c2 = isset($code[$i + 1]) ? $code[$i + 1] : 0;
			$c3 = isset($code[$i + 2]) ? $code[$i + 2] : 0;
			$v = (1600 * $c1) + (40 * $c2) + $c3 + 1;
			$data[] = intval($v / 256);
			$data[] = $v % 256;
		}

		return $data;
	}

	/**
	 * Creates the keywords for X12, C40, and TEXT mode.
	 * count($code) must be dividable by 3.
	 *
	 * @param int[]
	 * @param bool $last
	 * @return int[]
	 */
	private function calculateX12C40Text($code, $lastCharacter, $isLastCharacterData, $last) {
		$data = array();

		// We will take care of the last 1, 2, or 3 $code separately
		$nbCodeBeforePadding = count($code);
		$code = $this->padWithCode($code, 3, 0);
		$nbCodeAfterPadding = count($code);

		$data = $this->calculateX12C40TextEncoding(array_splice($code, 0, -3));

		// +1 for the latch
		$requiredCodewords = count($this->data) + count($data) + 1;
		$this->accomodateSymbol($requiredCodewords);

		$remainingCodewords = $this->symbols[$this->currentSymbolIndex]->getDataSize() - $requiredCodewords;

		// If we filled completely, it won't be possible to add the last code if we have any
		if ($remainingCodewords === 0 && $nbCodeAfterPadding - $nbCodeBeforePadding > 0) {
			$this->accomodateSymbol($requiredCodewords + 1);
			$remainingCodewords = $this->symbols[$this->currentSymbolIndex]->getDataSize() - $requiredCodewords;
		}

		$unlatch = true;
		$normalEncoding = true;
		$nbCodeWasRemaining = 3 - ($nbCodeAfterPadding - $nbCodeBeforePadding);

		if ($last) {
			if ($remainingCodewords === 2) {
				switch ($nbCodeWasRemaining) {
					case 3: // Option A
						self::debug('[ENCODING COMMON] Option A');
						$unlatch = false;
					break;
					case 2: // Option B
						if ($isLastCharacterData) {
							self::debug('[ENCODING COMMON] Option B');
							$unlatch = false;
						}
					break;
				}
			} elseif ($remainingCodewords === 1) { // Option D
				if ($nbCodeWasRemaining === 1 && $isLastCharacterData) {
						self::debug('[ENCODING COMMON] Option D');
						$unlatch = false;
						$normalEncoding = false;
				}
			}
		}

		// Option C
		// Can happen at any time but not when option D has been selected
		if ($nbCodeWasRemaining === 1 && $unlatch === true && $isLastCharacterData) {
			self::debug('[ENCODING COMMON] Option C');
			$normalEncoding = false;
		}

		if ($normalEncoding) {
			$data = array_merge($data, $this->calculateX12C40TextEncoding(array_splice($code, -3)));
		}

		if ($unlatch) {
			$data[] = 254;
		}

		if (!$normalEncoding) {
			$data = array_merge($data, $this->parseAscii($lastCharacter));
		}

		$this->accomodateSymbol(count($this->data) + count($data) + 1);

		return $data;
	}

	/**
	 * Saves data into the classes.
	 *
	 * This method will save data, calculate real column number
	 * (if -1 was selected), the real error level (if -1 was
	 * selected)... It will add Padding to the end and generate
	 * the error codes.
	 *
	 * @param array $data
	 */
	private function setData($data) {
		$this->data = $data;
	}

	/**
	 * Reed Solomon
	 *
	 * @param int[] $wd
	 * @param int $nd
	 * @param int $nc
	 * @param int $gf
	 * @param int $pp
	 * @return int[]
	 */
	private static function reedSolomon($wd, $nd, $nc, $gf, $pp) {
		$log = array_fill(0, $gf, 0);
		$alog = array_fill(0, $gf, 0);
		$log[0] = 1 - $gf;
		$alog[0] = 1;
		for ($i = 1; $i < $gf; $i++) {
			$alog[$i] = $alog[$i - 1] * 2;
			if ($alog[$i] >= $gf) {
				$alog[$i] ^= $pp;
			}

			$log[$alog[$i]] = $i;
		}

		$c = array_fill(0, $nc + 1, 0);
		$c[0] = 1;

		for ($i = 1; $i <= $nc; $i++) {
			$c[$i] = $c[$i - 1];
			for ($j = $i - 1; $j >= 1; $j--) {
				$c[$j] = $c[$j - 1] ^ self::prod($c[$j], $alog[$i], $log, $alog, $gf);
			}

			$c[0] = self::prod($c[0], $alog[$i], $log, $alog, $gf);
		}

		$t = $nd + $nc;
		for ($i = $nd; $i <= $t; $i++) {
			$wd[$i] = 0;
		}

		for ($i = 0; $i < $nd; $i++) {
			$k = $wd[$nd] ^ $wd[$i];
			for ($j = 0; $j < $nc; $j++) {
				$wd[$nd + $j] = $wd[$nd + $j + 1] ^ self::prod($k, $c[$nc - $j - 1], $log, $alog, $gf);
			}
		}

		$r = array();
		for ($i = $nd; $i < $t; $i++) {
			$r[] = $wd[$i];
		}

		return $r;
	}

	/**
	 * products x times y in array
	 *
	 * @param int $x
	 * @param int $y
	 * @param int[] $log
	 * @param int[] $alog
	 * @return int
	 */
	private static function prod($x, $y, $log, $alog) {
		if ($x === 0 || $y === 0) {
			return 0;
		} else {
			return $alog[($log[$x] + $log[$y]) % (self::_GF - 1)];
		}
	}

	/**
	 * Creates the reed solomon error and do the interleaved if needed to be done.
	 * The $nextMoreBlock is used for only one special case: 144x144
	 *
	 * @param int[] $data
	 * @param int $nbBlocks
	 * @param int $nbData
	 * @param int $nbError
	 * @param int $nextMoreBlock
	 * @return int[]
	 */
	private static function getFinalError($data, $nbBlock, $nbData, $nbError, $nextMoreBlock = 0) {
		$finalError = array();
		for ($i = 0; $i < $nbBlock + $nextMoreBlock; $i++) {
			$nbDataFinal = $nbData;

			if ($i >= $nbBlock) {
				$nbDataFinal--;
			}

			$finalData = array();
			for ($j = 0; $j < $nbDataFinal; $j++) {
				$finalData[] = $data[$i + $j * ($nbBlock + $nextMoreBlock)];
			}

			$error = self::reedSolomon($finalData, $nbDataFinal, $nbError, self::_GF, self::_MODULUS);
			for ($j = 0; $j < $nbError; $j++) {
				$finalError[$i + $j * ($nbBlock + $nextMoreBlock)] = $error[$j];
			}
		}

		ksort($finalError);

		return $finalError;
	}

	/**
	 * Debug
	 *
	 * @param string $text
	 * @param bool $newline
	 */
	private static function debug($text, $newline = true) {
		if (self::DEBUG) {
			echo $text;
			if ($newline === true) {
				echo "<br />\n";
			}
		}
	}
}

class _BCGdatamatrix_sort {
	private $count;
	private $keyOrder;

	private function __construct(&$count, $keyOrder) {
		$this->count = &$count;
		$this->keyOrder = $keyOrder;
		uksort($this->count, array($this, 'internalSort'));
	}

	public static function sort(&$count, $keyOrder) {
		new _BCGdatamatrix_sort($count, $keyOrder);
	}

	private function internalSort($a, $b) {
		$v1 = $this->count[$a];
		$v2 = $this->count[$b];

		if ($v1 < $v2) {
			return -1;
		} elseif ($v1 > $v2) {
			return 1;
		} else {
			return (array_search($a, $this->keyOrder) < array_search($b, $this->keyOrder)) ? -1 : 1;
		}
	}
}

abstract class _BCGdatamatrix_Info {
	public $row;		// int
	public $col;		// int
	public $region;// int
	public $data;	// int
	public $error;	// int
	public $block;	// int

	public function __construct($row, $col, $region, $data, $error, $block) {
		$this->row = $row;
		$this->col = $col;
		$this->region = $region;
		$this->data = $data;
		$this->error = $error;
		$this->block = $block;
	}

	public function getDataSize() {
		if (is_array($this->data)) {
			return $this->data[0] * $this->block[0] + $this->data[1] * $this->block[1];
		} else {
			return $this->data * $this->block;
		}
	}

	public function getMappingSize() {
		$minus = sqrt($this->region) * 2;
		return array(intval($this->row - $minus), intval($this->col - $minus));
	}
}

class _BCGdatamatrix_InfoSquare extends _BCGdatamatrix_Info {
	public function __construct($row, $region, $data, $error, $block) {
		parent::__construct($row, $row, $region, $data, $error, $block);
	}
}

class _BCGdatamatrix_InfoRectangle extends _BCGdatamatrix_Info {
	public function __construct($row, $col, $region, $data, $error, $block) {
		parent::__construct($row, $col, $region, $data, $error, $block);
	}

	public function getMappingSize() {
		if ($this->region === 1) {
			return parent::getMappingSize();
		} else {
			return array($this->row - 2, $this->col - 4);
		}
	}
}

/**
 * Calculates and creates the matrix for display
 */
class _BCGdatamatrix_placement {
	private $nrow = 0;
	private $ncol = 0;
	private $arr = array();

	/**
	 * Checks for valid command line entries, then computes & displays array
	 */
	public function __construct($nrow, $ncol) {
		$this->nrow = (int)$nrow;
		$this->ncol = (int)$ncol;
	}

	/*
	 * Creates the $matrix with the real value passed as $data
	 * @param int[] $data
	 */
	public function getMatrix($data) {
		$matrix = array();
		$x = $y = $z = 0;
		$i = $b = 0;
		if ($this->nrow >= 6 && $this->ncol >= 6) {
			$this->arr = array();
			$this->ECC200();
			for ($x = 0; $x < $this->nrow; $x++) {
				$matrix[$x] = array();
				for ($y = 0; $y < $this->ncol; $y++) {
					$z = $this->arr[$x * $this->ncol + $y];

					if ($z === 0) {
						$matrix[$x][$y] = 0;
					} else if ($z === 1) {
						$matrix[$x][$y] = 1;
					} else {
						$i = intval($z / 10) - 1;
						$b = $z % 10;

						$matrix[$x][$y] = ($data[$i] & (0x1 << (8 - $b))) ? 1 : 0;
					}

					////if ($z == 0) echo 'WHI';
					////else if ($z == 1) echo 'BLK';
					////else echo $i.'.'.$b.' ';
				}
				////echo '<br />';
			}
		}

		return $matrix;
	}

	/**
	 * Places "chr+bit" with appropriate wrapping within arr[]
	 *
	 * @param int $row
	 * @param int $col
	 * @param int $chr
	 * @param int $bin
	 */
	private function module($row, $col, $chr, $bit) {
		if ($row < 0) {
			$row += $this->nrow;
			$col += 4 - (($this->nrow + 4) % 8);
		}

		if ($col < 0) {
			$col += $this->ncol;
			$row += 4 - (($this->ncol + 4) % 8);
		}

		$this->arr[$row * $this->ncol + $col] = 10 * $chr + $bit;
	}

	/**
	 * Places the 8 bits of a utah-shaped symbol character in ECC200
	 *
	 * @param int $row
	 * @param int $col
	 * @param int $chr
	 */
	private function utah($row, $col, $chr) {
		$this->module($row - 2, $col - 2, $chr, 1);
		$this->module($row - 2, $col - 1, $chr, 2);
		$this->module($row - 1, $col - 2, $chr, 3);
		$this->module($row - 1, $col - 1, $chr, 4);
		$this->module($row - 1, $col, $chr, 5);
		$this->module($row, $col - 2, $chr, 6);
		$this->module($row, $col - 1, $chr, 7);
		$this->module($row, $col, $chr, 8);
	}
	
	/**
	 * Places 8 bits of the four special corner cases in ECC200.
	 * Corner 1.
	 *
	 * @param int $chr
	 */
	private function corner1($chr) {
		$this->module($this->nrow - 1, 0, $chr, 1);
		$this->module($this->nrow - 1, 1, $chr, 2);
		$this->module($this->nrow - 1, 2, $chr, 3);
		$this->module(0, $this->ncol - 2, $chr, 4);
		$this->module(0, $this->ncol - 1, $chr, 5);
		$this->module(1, $this->ncol - 1, $chr, 6);
		$this->module(2, $this->ncol - 1, $chr, 7);
		$this->module(3, $this->ncol - 1, $chr, 8);
	} 

	/**
	 * Places 8 bits of the four special corner cases in ECC200.
	 * Corner 2.
	 *
	 * @param int $chr
	 */
	private function corner2($chr) {
		$this->module($this->nrow - 3, 0, $chr, 1);
		$this->module($this->nrow - 2, 0, $chr, 2);
		$this->module($this->nrow - 1, 0, $chr, 3);
		$this->module(0, $this->ncol - 4, $chr, 4);
		$this->module(0, $this->ncol - 3, $chr, 5);
		$this->module(0, $this->ncol - 2, $chr, 6);
		$this->module(0, $this->ncol - 1, $chr, 7);
		$this->module(1, $this->ncol - 1, $chr, 8);
	} 
	
	/**
	 * Places 8 bits of the four special corner cases in ECC200.
	 * Corner 3.
	 *
	 * @param int $chr
	 */
	private function corner3($chr) {
		$this->module($this->nrow - 3, 0, $chr, 1);
		$this->module($this->nrow - 2, 0, $chr, 2);
		$this->module($this->nrow - 1, 0, $chr, 3);
		$this->module(0, $this->ncol - 2, $chr, 4);
		$this->module(0, $this->ncol - 1, $chr, 5);
		$this->module(1, $this->ncol - 1, $chr, 6);
		$this->module(2, $this->ncol - 1, $chr, 7);
		$this->module(3, $this->ncol - 1, $chr, 8);
	} 
	
	/**
	 * Places 8 bits of the four special corner cases in ECC200.
	 * Corner 4.
	 *
	 * @param int $chr
	 */
	private function corner4($chr) {
		$this->module($this->nrow - 1, 0, $chr, 1);
		$this->module($this->nrow - 1, $this->ncol - 1, $chr, 2);
		$this->module(0, $this->ncol - 3, $chr, 3);
		$this->module(0, $this->ncol - 2, $chr, 4);
		$this->module(0, $this->ncol - 1, $chr, 5);
		$this->module(1, $this->ncol - 3, $chr, 6);
		$this->module(1, $this->ncol - 2, $chr, 7);
		$this->module(1, $this->ncol - 1, $chr, 8);
	} 

	/**
	 * Fills an nrow x ncol array with appropriate values for ECC200
	 */
	private function ECC200() { 
		$row = $col = $chr = 0;

		/* First, fill the arr[] with invalid entries */
		for ($row = 0; $row < $this->nrow; $row++) {
			for ($col = 0; $col < $this->ncol; $col++) {
				$this->arr[$row * $this->ncol + $col] = 0;
			}
		}

		/* Starting in the correct location for character #1, bit 8,... */
		$chr = 1;
		$row = 4;
		$col = 0;
		do {
			/* repeatedly first check for one of the special corner cases, then... */
			if ($row === $this->nrow && $col === 0) $this->corner1($chr++);
			if ($row === $this->nrow - 2 && $col === 0 && $this->ncol % 4) $this->corner2($chr++);
			if ($row === $this->nrow - 2  && $col === 0 && $this->ncol % 8 === 4) $this->corner3($chr++);
			if ($row === $this->nrow + 4 && $col === 2 && !($this->ncol % 8)) $this->corner4($chr++);
			/* sweep upward diagonally, inserting successive characters,... */
			do {
				if ($row < $this->nrow && $col >= 0 && !$this->arr[$row * $this->ncol + $col]) {
					$this->utah($row, $col, $chr++);
				}

				$row -= 2;
				$col += 2;
			} while ($row >= 0 && $col < $this->ncol);
			$row += 1;
			$col += 3;
			/* & then sweep downward diagonally, inserting successive characters,... */
			do {
				if ($row >= 0 && $col < $this->ncol && !$this->arr[$row * $this->ncol + $col]) {
					$this->utah($row, $col, $chr++);
				}

				$row += 2;
				$col -= 2;
			} while ($row < $this->nrow && $col >= 0);
			$row += 3;
			$col += 1;
			/* ... until the entire array is scanned */
		} while ($row < $this->nrow || $col < $this->ncol );

		/* Lastly, if the lower righthand corner is untouched, fill in fixed pattern */
		if (!$this->arr[$this->nrow * $this->ncol - 1]) {
			$this->arr[$this->nrow * $this->ncol - 1] = $this->arr[$this->nrow * $this->ncol - $this->ncol - 2] = 1;
		}
	}
}
?>