<?php

// Start of ps v.

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Creates a new PostScript document object
 * @link http://php.net/manual/en/function.ps-new.php
 * @return resource Resource of PostScript document or false on failure. The return value
 * is passed to all other functions as the first argument.
 * @jms-builtin
 */
function ps_new () {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Deletes all resources of a PostScript document
 * @link http://php.net/manual/en/function.ps-delete.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_delete ($psdoc) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Opens a file for output
 * @link http://php.net/manual/en/function.ps-open-file.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $filename [optional] <p>
 * The name of the postscript file.
 * If <i>filename</i> is not passed the document will be
 * created in memory and all output will go straight to the browser.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_open_file ($psdoc, $filename = null) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Fetches the full buffer containig the generated PS data
 * @link http://php.net/manual/en/function.ps-get-buffer.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @return string
 * @jms-builtin
 */
function ps_get_buffer ($psdoc) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Closes a PostScript document
 * @link http://php.net/manual/en/function.ps-close.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_close ($psdoc) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Start a new page
 * @link http://php.net/manual/en/function.ps-begin-page.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $width <p>
 * The width of the page in pixel, e.g. 596 for A4 format.
 * </p>
 * @param float $height <p>
 * The height of the page in pixel, e.g. 842 for A4 format.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_begin_page ($psdoc, $width, $height) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * End a page
 * @link http://php.net/manual/en/function.ps-end-page.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_end_page ($psdoc) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Gets certain values
 * @link http://php.net/manual/en/function.ps-get-value.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $name <p>
 * Name of the value.
 * </p>
 * @param float $modifier [optional] <p>
 * The parameter <i>modifier</i> specifies the resource
 * for which the value is to be retrieved. This can be the id of a font or
 * an image.
 * </p>
 * @return float the value of the parameter or false.
 * @jms-builtin
 */
function ps_get_value ($psdoc, $name, $modifier = null) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets certain values
 * @link http://php.net/manual/en/function.ps-set-value.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $name <p>
 * The <i>name</i> can be one of the following:
 * textrendering
 * <p>
 * The way how text is shown.
 * </p>
 * @param float $value <p>
 * The value of the parameter.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_set_value ($psdoc, $name, $value) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Gets certain parameters
 * @link http://php.net/manual/en/function.ps-get-parameter.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $name <p>
 * Name of the parameter.
 * </p>
 * @param float $modifier [optional] <p>
 * An identifier needed if a parameter of a resource is requested,
 * e.g. the size of an image. In such a case the resource id is
 * passed.
 * </p>
 * @return string the value of the parameter or false on failure.
 * @jms-builtin
 */
function ps_get_parameter ($psdoc, $name, $modifier = null) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets certain parameters
 * @link http://php.net/manual/en/function.ps-set-parameter.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $name <p>
 * For a list of possible names see <b>ps_get_parameter</b>.
 * </p>
 * @param string $value <p>
 * The value of the parameter.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_set_parameter ($psdoc, $name, $value) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Loads a font
 * @link http://php.net/manual/en/function.ps-findfont.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $fontname <p>
 * The name of the font.
 * </p>
 * @param string $encoding <p>
 * <b>ps_findfont</b> will try to load the file passed in
 * the parameter <i>encoding</i>. Encoding files are of
 * the same syntax as those used by dvips(1). They
 * contain a font encoding vector (which is currently not used but must be
 * present) and a list of extra ligatures to extend the list of ligatures
 * derived from the afm file.
 * </p>
 * <p>
 * <i>encoding</i> can be null or the empty string if
 * the default encoding (TeXBase1) shall be used.
 * </p>
 * <p>
 * If the encoding is set to builtin then there
 * will be no reencoding and the font specific encoding will be used. This
 * is very useful with symbol fonts.
 * </p>
 * @param bool $embed [optional] <p>
 * If set to a value >0 the font will be embedded into the document. This
 * requires the font outline (.pfb file) to be present.
 * </p>
 * @return int the identifier of the font or zero in case of an error. The
 * identifier is a positive number.
 * @jms-builtin
 */
function ps_findfont ($psdoc, $fontname, $encoding, $embed = false) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets font to use for following output
 * @link http://php.net/manual/en/function.ps-setfont.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param int $fontid <p>
 * The font identifier as returned by <b>ps_findfont</b>.
 * </p>
 * @param float $size <p>
 * The size of the font.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_setfont ($psdoc, $fontid, $size) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Output text
 * @link http://php.net/manual/en/function.ps-show.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $text <p>
 * The text to be output.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_show ($psdoc, $text) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Output text at given position
 * @link http://php.net/manual/en/function.ps-show-xy.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $text <p>
 * The text to be output.
 * </p>
 * @param float $x <p>
 * x-coordinate of the lower left corner of the box surrounding the text.
 * </p>
 * @param float $y <p>
 * y-coordinate of the lower left corner of the box surrounding the text.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_show_xy ($psdoc, $text, $x, $y) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Output a text at current position
 * @link http://php.net/manual/en/function.ps-show2.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $text
 * @param int $len
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_show2 ($psdoc, $text, $len) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Output text at position
 * @link http://php.net/manual/en/function.ps-show-xy2.php
 * @param resource $psdoc
 * @param string $text
 * @param int $len
 * @param float $xcoor
 * @param float $ycoor
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_show_xy2 ($psdoc, $text, $len, $xcoor, $ycoor) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Continue text in next line
 * @link http://php.net/manual/en/function.ps-continue-text.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $text <p>
 * The text to output.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_continue_text ($psdoc, $text) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Output text in a box
 * @link http://php.net/manual/en/function.ps-show-boxed.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $text <p>
 * The text to be output into the given box.
 * </p>
 * @param float $left <p>
 * x-coordinate of the lower left corner of the box.
 * </p>
 * @param float $bottom <p>
 * y-coordinate of the lower left corner of the box.
 * </p>
 * @param float $width <p>
 * Width of the box.
 * </p>
 * @param float $height <p>
 * Height of the box.
 * </p>
 * @param string $hmode <p>
 * The parameter <i>hmode</i> can be "justify",
 * "fulljustify", "right", "left", or "center". The difference of
 * "justify" and "fulljustify" just affects the last line of the box. In
 * fulljustify mode the last line will be left and right justified unless
 * this is also the last line of paragraph. In justify mode it will always
 * be left justified.
 * </p>
 * @param string $feature [optional] <p>
 * </p>
 * @return int Number of characters that could not be written.
 * @jms-builtin
 */
function ps_show_boxed ($psdoc, $text, $left, $bottom, $width, $height, $hmode, $feature = null) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Gets width of a string
 * @link http://php.net/manual/en/function.ps-stringwidth.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $text <p>
 * The text for which the width is to be calculated.
 * </p>
 * @param int $fontid [optional] <p>
 * The identifier of the font to be used. If not font is specified
 * the current font will be used.
 * </p>
 * @param float $size [optional] <p>
 * The size of the font. If no size is specified the current size
 * is used.
 * </p>
 * @return float Width of a string in points.
 * @jms-builtin
 */
function ps_stringwidth ($psdoc, $text, $fontid = 0, $size = 0.0) {}

/**
 * (PECL ps &gt;= 1.2.0)<br/>
 * Gets geometry of a string
 * @link http://php.net/manual/en/function.ps-string-geometry.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $text <p>
 * The text for which the geometry is to be calculated.
 * </p>
 * @param int $fontid [optional] <p>
 * The identifier of the font to be used. If not font is specified
 * the current font will be used.
 * </p>
 * @param float $size [optional] <p>
 * The size of the font. If no size is specified the current size
 * is used.
 * </p>
 * @return array An array of the dimensions of a string. The element 'width' contains the
 * width of the string as returned by <b>ps_stringwidth</b>. The
 * element 'descender' contains the maximum descender and 'ascender' the
 * maximum ascender of the string.
 * @jms-builtin
 */
function ps_string_geometry ($psdoc, $text, $fontid = 0, $size = 0.0) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets position for text output
 * @link http://php.net/manual/en/function.ps-set-text-pos.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $x <p>
 * x-coordinate of the new text position.
 * </p>
 * @param float $y <p>
 * y-coordinate of the new text position.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_set_text_pos ($psdoc, $x, $y) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets appearance of a dashed line
 * @link http://php.net/manual/en/function.ps-setdash.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $on <p>
 * The length of the dash.
 * </p>
 * @param float $off <p>
 * The length of the gap between dashes.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_setdash ($psdoc, $on, $off) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets appearance of a dashed line
 * @link http://php.net/manual/en/function.ps-setpolydash.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $arr <p>
 * <i>arr</i> is a list of length elements alternately for
 * the black and white portion.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_setpolydash ($psdoc, $arr) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets flatness
 * @link http://php.net/manual/en/function.ps-setflat.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $value <p>
 * The <i>value</i> must be between 0.2 and 1.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_setflat ($psdoc, $value) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets how contected lines are joined
 * @link http://php.net/manual/en/function.ps-setlinejoin.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param int $type <p>
 * The way lines are joined. Possible values are
 * PS_LINEJOIN_MITER,
 * PS_LINEJOIN_ROUND, or
 * PS_LINEJOIN_BEVEL.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_setlinejoin ($psdoc, $type) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets appearance of line ends
 * @link http://php.net/manual/en/function.ps-setlinecap.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param int $type <p>
 * The type of line ends. Possible values are
 * PS_LINECAP_BUTT,
 * PS_LINECAP_ROUND, or
 * PS_LINECAP_SQUARED.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_setlinecap ($psdoc, $type) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets the miter limit
 * @link http://php.net/manual/en/function.ps-setmiterlimit.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $value <p>
 * The maximum ratio between the miter length and the line width. Larger
 * values (&gt; 10) will result in very long spikes when two lines meet
 * in a small angle. Keep the default unless you know what you are doing.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_setmiterlimit ($psdoc, $value) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets width of a line
 * @link http://php.net/manual/en/function.ps-setlinewidth.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $width <p>
 * The width of lines in points.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_setlinewidth ($psdoc, $width) {}

/**
 * (PECL ps &gt;= 1.3.0)<br/>
 * Sets overprint mode
 * @link http://php.net/manual/en/function.ps-setoverprintmode.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param int $mode
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_setoverprintmode ($psdoc, $mode) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Save current context
 * @link http://php.net/manual/en/function.ps-save.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_save ($psdoc) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Restore previously save context
 * @link http://php.net/manual/en/function.ps-restore.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_restore ($psdoc) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets translation
 * @link http://php.net/manual/en/function.ps-translate.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $x <p>
 * x-coordinate of the origin of the translated coordinate system.
 * </p>
 * @param float $y <p>
 * y-coordinate of the origin of the translated coordinate system.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_translate ($psdoc, $x, $y) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets scaling factor
 * @link http://php.net/manual/en/function.ps-scale.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $x <p>
 * Scaling factor in horizontal direction.
 * </p>
 * @param float $y <p>
 * Scaling factor in vertical direction.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_scale ($psdoc, $x, $y) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets rotation factor
 * @link http://php.net/manual/en/function.ps-rotate.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $rot <p>
 * Angle of rotation in degree.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_rotate ($psdoc, $rot) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets current point
 * @link http://php.net/manual/en/function.ps-moveto.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $x <p>
 * x-coordinate of the point to move to.
 * </p>
 * @param float $y <p>
 * y-coordinate of the point to move to.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_moveto ($psdoc, $x, $y) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Draws a line
 * @link http://php.net/manual/en/function.ps-lineto.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $x <p>
 * x-coordinate of the end point of the line.
 * </p>
 * @param float $y <p>
 * y-coordinate of the end point of the line.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_lineto ($psdoc, $x, $y) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Draws a curve
 * @link http://php.net/manual/en/function.ps-curveto.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $x1 <p>
 * x-coordinate of first control point.
 * </p>
 * @param float $y1 <p>
 * y-coordinate of first control point.
 * </p>
 * @param float $x2 <p>
 * x-coordinate of second control point.
 * </p>
 * @param float $y2 <p>
 * y-coordinate of second control point.
 * </p>
 * @param float $x3 <p>
 * x-coordinate of third control point.
 * </p>
 * @param float $y3 <p>
 * y-coordinate of third control point.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_curveto ($psdoc, $x1, $y1, $x2, $y2, $x3, $y3) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Draws a circle
 * @link http://php.net/manual/en/function.ps-circle.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $x <p>
 * The x-coordinate of the circle's middle point.
 * </p>
 * @param float $y <p>
 * The y-coordinate of the circle's middle point.
 * </p>
 * @param float $radius <p>
 * The radius of the circle
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_circle ($psdoc, $x, $y, $radius) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Draws an arc counterclockwise
 * @link http://php.net/manual/en/function.ps-arc.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $x <p>
 * The x-coordinate of the circle's middle point.
 * </p>
 * @param float $y <p>
 * The y-coordinate of the circle's middle point.
 * </p>
 * @param float $radius <p>
 * The radius of the circle
 * </p>
 * @param float $alpha <p>
 * The start angle given in degrees.
 * </p>
 * @param float $beta <p>
 * The end angle given in degrees.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_arc ($psdoc, $x, $y, $radius, $alpha, $beta) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Draws an arc clockwise
 * @link http://php.net/manual/en/function.ps-arcn.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $x <p>
 * The x-coordinate of the circle's middle point.
 * </p>
 * @param float $y <p>
 * The y-coordinate of the circle's middle point.
 * </p>
 * @param float $radius <p>
 * The radius of the circle
 * </p>
 * @param float $alpha <p>
 * The starting angle given in degrees.
 * </p>
 * @param float $beta <p>
 * The end angle given in degrees.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_arcn ($psdoc, $x, $y, $radius, $alpha, $beta) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Draws a rectangle
 * @link http://php.net/manual/en/function.ps-rect.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $x <p>
 * x-coordinate of the lower left corner of the rectangle.
 * </p>
 * @param float $y <p>
 * y-coordinate of the lower left corner of the rectangle.
 * </p>
 * @param float $width <p>
 * The width of the image.
 * </p>
 * @param float $height <p>
 * The height of the image.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_rect ($psdoc, $x, $y, $width, $height) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Closes path
 * @link http://php.net/manual/en/function.ps-closepath.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_closepath ($psdoc) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Draws the current path
 * @link http://php.net/manual/en/function.ps-stroke.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_stroke ($psdoc) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Closes and strokes path
 * @link http://php.net/manual/en/function.ps-closepath-stroke.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_closepath_stroke ($psdoc) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Fills the current path
 * @link http://php.net/manual/en/function.ps-fill.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_fill ($psdoc) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Fills and strokes the current path
 * @link http://php.net/manual/en/function.ps-fill-stroke.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_fill_stroke ($psdoc) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Clips drawing to current path
 * @link http://php.net/manual/en/function.ps-clip.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_clip ($psdoc) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Opens image from file
 * @link http://php.net/manual/en/function.ps-open-image-file.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $type <p>
 * The type of the image. Possible values are png,
 * jpeg, or eps.
 * </p>
 * @param string $filename <p>
 * The name of the file containing the image data.
 * </p>
 * @param string $stringparam [optional] <p>
 * Not used.
 * </p>
 * @param int $intparam [optional] <p>
 * Not used.
 * </p>
 * @return int identifier of image or zero in case of an error. The identifier is
 * a positive number greater than 0.
 * @jms-builtin
 */
function ps_open_image_file ($psdoc, $type, $filename, $stringparam = null, $intparam = 0) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Reads an image for later placement
 * @link http://php.net/manual/en/function.ps-open-image.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $type <p>
 * The type of the image. Possible values are png,
 * jpeg, or eps.
 * </p>
 * @param string $source <p>
 * Not used.
 * </p>
 * @param string $data <p>
 * The image data.
 * </p>
 * @param int $lenght
 * @param int $width <p>
 * The width of the image.
 * </p>
 * @param int $height <p>
 * The height of the image.
 * </p>
 * @param int $components <p>
 * The number of components for each pixel. This can be
 * 1 (gray scale images), 3 (rgb images), or 4 (cmyk, rgba images).
 * </p>
 * @param int $bpc <p>
 * Number of bits per component (quite often 8).
 * </p>
 * @param string $params
 * @return int identifier of image or zero in case of an error. The identifier is
 * a positive number greater than 0.
 * @jms-builtin
 */
function ps_open_image ($psdoc, $type, $source, $data, $lenght, $width, $height, $components, $bpc, $params) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Closes image and frees memory
 * @link http://php.net/manual/en/function.ps-close-image.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param int $imageid <p>
 * Resource identifier of the image as returned by
 * <b>ps_open_image</b> or
 * <b>ps_open_image_file</b>.
 * </p>
 * @return void null on success or false on failure.
 * @jms-builtin
 */
function ps_close_image ($psdoc, $imageid) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Places image on the page
 * @link http://php.net/manual/en/function.ps-place-image.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param int $imageid <p>
 * The resource identifier of the image as returned by
 * <b>ps_open_image</b> or
 * <b>ps_open_image_file</b>.
 * </p>
 * @param float $x <p>
 * x-coordinate of the lower left corner of the image.
 * </p>
 * @param float $y <p>
 * y-coordinate of the lower left corner of the image.
 * </p>
 * @param float $scale <p>
 * The scaling factor for the image. A scale of 1.0 will result
 * in a resolution of 72 dpi, because each pixel is equivalent to
 * 1 point.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_place_image ($psdoc, $imageid, $x, $y, $scale) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Add bookmark to current page
 * @link http://php.net/manual/en/function.ps-add-bookmark.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $text <p>
 * The text used for displaying the bookmark.
 * </p>
 * @param int $parent [optional] <p>
 * A bookmark previously created by this function which
 * is used as the parent of the new bookmark.
 * </p>
 * @param int $open [optional] <p>
 * If <i>open</i> is unequal to zero the bookmark will
 * be shown open by the pdf viewer.
 * </p>
 * @return int The returned value is a reference for the bookmark. It is only used if
 * the bookmark shall be used as a parent. The value is greater zero if
 * the function succeeds. In case of an error zero will
 * be returned.
 * @jms-builtin
 */
function ps_add_bookmark ($psdoc, $text, $parent = 0, $open = 0) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets information fields of document
 * @link http://php.net/manual/en/function.ps-set-info.php
 * @param resource $p
 * @param string $key <p>
 * The name of the information field to set. The values which can be
 * set are Keywords, Subject,
 * Title, Creator,
 * Author, BoundingBox, and
 * Orientation. Be aware that some of them has a
 * meaning to PostScript viewers.
 * </p>
 * @param string $val
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_set_info ($p, $key, $val) {}

/**
 * (PECL ps &gt;= 1.3.4)<br/>
 * Reads an external file with raw PostScript code
 * @link http://php.net/manual/en/function.ps-include-file.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $file
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_include_file ($psdoc, $file) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Adds note to current page
 * @link http://php.net/manual/en/function.ps-add-note.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $llx <p>
 * The x-coordinate of the lower left corner.
 * </p>
 * @param float $lly <p>
 * The y-coordinate of the lower left corner.
 * </p>
 * @param float $urx <p>
 * The x-coordinate of the upper right corner.
 * </p>
 * @param float $ury <p>
 * The y-coordinate of the upper right corner.
 * </p>
 * @param string $contents <p>
 * The text of the note.
 * </p>
 * @param string $title <p>
 * The title of the note as displayed in the header of the note.
 * </p>
 * @param string $icon <p>
 * The icon shown if the note is folded. This parameter can be set
 * to comment, insert,
 * note, paragraph,
 * newparagraph, key, or
 * help.
 * </p>
 * @param int $open <p>
 * If <i>open</i> is unequal to zero the note will
 * be shown unfolded after opening the document with a pdf viewer.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_add_note ($psdoc, $llx, $lly, $urx, $ury, $contents, $title, $icon, $open) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Adds link to a page in a second pdf document
 * @link http://php.net/manual/en/function.ps-add-pdflink.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $llx <p>
 * The x-coordinate of the lower left corner.
 * </p>
 * @param float $lly <p>
 * The y-coordinate of the lower left corner.
 * </p>
 * @param float $urx <p>
 * The x-coordinate of the upper right corner.
 * </p>
 * @param float $ury <p>
 * The y-coordinate of the upper right corner.
 * </p>
 * @param string $filename <p>
 * The name of the pdf document to be opened when clicking on
 * this link.
 * </p>
 * @param int $page <p>
 * The page number of the destination pdf document
 * </p>
 * @param string $dest <p>
 * The parameter <i>dest</i> determines how the document
 * is being viewed. It can be fitpage,
 * fitwidth, fitheight, or
 * fitbbox.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_add_pdflink ($psdoc, $llx, $lly, $urx, $ury, $filename, $page, $dest) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Adds link to a page in the same document
 * @link http://php.net/manual/en/function.ps-add-locallink.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $llx <p>
 * The x-coordinate of the lower left corner.
 * </p>
 * @param float $lly <p>
 * The y-coordinate of the lower left corner.
 * </p>
 * @param float $urx <p>
 * The x-coordinate of the upper right corner.
 * </p>
 * @param float $ury <p>
 * The y-coordinate of the upper right corner.
 * </p>
 * @param int $page <p>
 * The number of the page displayed when clicking on the link.
 * </p>
 * @param string $dest <p>
 * The parameter <i>dest</i> determines how the document
 * is being viewed. It can be fitpage,
 * fitwidth, fitheight, or
 * fitbbox.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_add_locallink ($psdoc, $llx, $lly, $urx, $ury, $page, $dest) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Adds link which launches file
 * @link http://php.net/manual/en/function.ps-add-launchlink.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $llx <p>
 * The x-coordinate of the lower left corner.
 * </p>
 * @param float $lly <p>
 * The y-coordinate of the lower left corner.
 * </p>
 * @param float $urx <p>
 * The x-coordinate of the upper right corner.
 * </p>
 * @param float $ury <p>
 * The y-coordinate of the upper right corner.
 * </p>
 * @param string $filename <p>
 * The path of the program to be started, when the link is clicked on.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_add_launchlink ($psdoc, $llx, $lly, $urx, $ury, $filename) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Adds link to a web location
 * @link http://php.net/manual/en/function.ps-add-weblink.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $llx <p>
 * The x-coordinate of the lower left corner.
 * </p>
 * @param float $lly <p>
 * The y-coordinate of the lower left corner.
 * </p>
 * @param float $urx <p>
 * The x-coordinate of the upper right corner.
 * </p>
 * @param float $ury <p>
 * The y-coordinate of the upper right corner.
 * </p>
 * @param string $url <p>
 * The url of the hyperlink to be opened when clicking on
 * this link, e.g. http://www.php.net.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_add_weblink ($psdoc, $llx, $lly, $urx, $ury, $url) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets border style of annotations
 * @link http://php.net/manual/en/function.ps-set-border-style.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $style <p>
 * <i>style</i> can be solid or
 * dashed.
 * </p>
 * @param float $width <p>
 * The line width of the border.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_set_border_style ($psdoc, $style, $width) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets color of border for annotations
 * @link http://php.net/manual/en/function.ps-set-border-color.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $red <p>
 * The red component of the border color.
 * </p>
 * @param float $green <p>
 * The green component of the border color.
 * </p>
 * @param float $blue <p>
 * The blue component of the border color.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_set_border_color ($psdoc, $red, $green, $blue) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets length of dashes for border of annotations
 * @link http://php.net/manual/en/function.ps-set-border-dash.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $black <p>
 * The length of the dash.
 * </p>
 * @param float $white <p>
 * The length of the gap between dashes.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_set_border_dash ($psdoc, $black, $white) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Sets current color
 * @link http://php.net/manual/en/function.ps-setcolor.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $type <p>
 * The parameter <i>type</i> can be
 * both, fill, or
 * fillstroke.
 * </p>
 * @param string $colorspace <p>
 * The colorspace should be one of gray,
 * rgb, cmyk,
 * spot, pattern. Depending on the
 * colorspace either only the first, the first three or all parameters
 * will be used.
 * </p>
 * @param float $c1 <p>
 * Depending on the colorspace this is either the red component (rgb),
 * the cyan component (cmyk), the gray value (gray), the identifier of
 * the spot color or the identifier of the pattern.
 * </p>
 * @param float $c2 <p>
 * Depending on the colorspace this is either the green component (rgb),
 * the magenta component (cmyk).
 * </p>
 * @param float $c3 <p>
 * Depending on the colorspace this is either the blue component (rgb),
 * the yellow component (cmyk).
 * </p>
 * @param float $c4 <p>
 * This must only be set in cmyk colorspace and specifies the black
 * component.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_setcolor ($psdoc, $type, $colorspace, $c1, $c2, $c3, $c4) {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Create spot color
 * @link http://php.net/manual/en/function.ps-makespotcolor.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $name <p>
 * Name of the spot color, e.g. Pantone 5565.
 * </p>
 * @param int $reserved [optional]
 * @return int The id of the new spot color or 0 in case of an error.
 * @jms-builtin
 */
function ps_makespotcolor ($psdoc, $name, $reserved = 0) {}

/**
 * (PECL ps &gt;= 1.2.0)<br/>
 * Start a new pattern
 * @link http://php.net/manual/en/function.ps-begin-pattern.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $width <p>
 * The width of the pattern in pixel.
 * </p>
 * @param float $height <p>
 * The height of the pattern in pixel.
 * </p>
 * @param float $xstep
 * @param float $ystep
 * @param int $painttype <p>
 * Must be 1 or 2.
 * </p>
 * @return int The identifier of the pattern or false on failure.
 * @jms-builtin
 */
function ps_begin_pattern ($psdoc, $width, $height, $xstep, $ystep, $painttype) {}

/**
 * (PECL ps &gt;= 1.2.0)<br/>
 * End a pattern
 * @link http://php.net/manual/en/function.ps-end-pattern.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_end_pattern ($psdoc) {}

/**
 * (PECL ps &gt;= 1.2.0)<br/>
 * Start a new template
 * @link http://php.net/manual/en/function.ps-begin-template.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param float $width <p>
 * The width of the template in pixel.
 * </p>
 * @param float $height <p>
 * The height of the template in pixel.
 * </p>
 * @return int true on success or false on failure.
 * @jms-builtin
 */
function ps_begin_template ($psdoc, $width, $height) {}

/**
 * (PECL ps &gt;= 1.2.0)<br/>
 * End a template
 * @link http://php.net/manual/en/function.ps-end-template.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_end_template ($psdoc) {}

/**
 * (PECL ps &gt;= 1.3.0)<br/>
 * Fills an area with a shading
 * @link http://php.net/manual/en/function.ps-shfill.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param int $shadingid <p>
 * The identifier of a shading previously created with
 * <b>ps_shading</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_shfill ($psdoc, $shadingid) {}

/**
 * (PECL ps &gt;= 1.3.0)<br/>
 * Creates a shading for later use
 * @link http://php.net/manual/en/function.ps-shading.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $type <p>
 * The type of shading can be either radial or
 * axial. Each shading starts with the current fill
 * color and ends with the given color values passed in the parameters
 * <i>c1</i> to <i>c4</i>
 * (see <b>ps_setcolor</b> for their meaning).
 * </p>
 * @param float $x0
 * @param float $y0
 * @param float $x1
 * @param float $y1
 * @param float $c1
 * @param float $c2
 * @param float $c3
 * @param float $c4
 * @param string $optlist <p>
 * If the shading is of type radial the
 * <i>optlist</i> must also contain the parameters
 * r0 and r1 with the radius of the
 * start and end circle.
 * </p>
 * @return int the identifier of the pattern or false on failure.
 * @jms-builtin
 */
function ps_shading ($psdoc, $type, $x0, $y0, $x1, $y1, $c1, $c2, $c3, $c4, $optlist) {}

/**
 * (PECL ps &gt;= 1.3.0)<br/>
 * Creates a pattern based on a shading
 * @link http://php.net/manual/en/function.ps-shading-pattern.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param int $shadingid <p>
 * The identifier of a shading previously created with
 * <b>ps_shading</b>.
 * </p>
 * @param string $optlist <p>
 * This argument is not currently used.
 * </p>
 * @return int The identifier of the pattern or false on failure.
 * @jms-builtin
 */
function ps_shading_pattern ($psdoc, $shadingid, $optlist) {} 

/** @jms-builtin */
function ps_begin_font () {} 

/** @jms-builtin */
function ps_end_font () {} 

/** @jms-builtin */
function ps_begin_glyph () {} 

/** @jms-builtin */
function ps_end_glyph () {}

/**
 * (PECL ps &gt;= 1.1.0)<br/>
 * Takes an GD image and returns an image for placement in a PS document
 * @link http://php.net/manual/en/function.ps-open-memory-image.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param int $gd
 * @return int
 * @jms-builtin
 */
function ps_open_memory_image ($psdoc, $gd) {}

/**
 * (PECL ps &gt;= 1.1.1)<br/>
 * Hyphenates a word
 * @link http://php.net/manual/en/function.ps-hyphenate.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param string $text <p>
 * <i>text</i> should not contain any non alpha
 * characters. Possible positions for breaks are returned in an array of
 * interger numbers. Each number is the position of the char in
 * <i>text</i> after which a hyphenation can take place.
 * </p>
 * @return array An array of integers indicating the position of possible breaks in
 * the text or false on failure.
 * @jms-builtin
 */
function ps_hyphenate ($psdoc, $text) {}

/**
 * (PECL ps &gt;= 1.2.0)<br/>
 * Output a glyph
 * @link http://php.net/manual/en/function.ps-symbol.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param int $ord <p>
 * The position of the glyph in the font encoding vector.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function ps_symbol ($psdoc, $ord) {}

/**
 * (PECL ps &gt;= 1.2.0)<br/>
 * Gets name of a glyph
 * @link http://php.net/manual/en/function.ps-symbol-name.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param int $ord <p>
 * The parameter <i>ord</i> is the position of the glyph
 * in the font encoding vector.
 * </p>
 * @param int $fontid [optional] <p>
 * The identifier of the font to be used. If not font is specified
 * the current font will be used.
 * </p>
 * @return string The name of a glyph in the given font.
 * @jms-builtin
 */
function ps_symbol_name ($psdoc, $ord, $fontid = 0) {}

/**
 * (PECL ps &gt;= 1.2.0)<br/>
 * Gets width of a glyph
 * @link http://php.net/manual/en/function.ps-symbol-width.php
 * @param resource $psdoc <p>
 * Resource identifier of the postscript file
 * as returned by <b>ps_new</b>.
 * </p>
 * @param int $ord <p>
 * The position of the glyph in the font encoding vector.
 * </p>
 * @param int $fontid [optional] <p>
 * The identifier of the font to be used. If not font is specified
 * the current font will be used.
 * </p>
 * @param float $size [optional] <p>
 * The size of the font. If no size is specified the current size
 * is used.
 * </p>
 * @return float The width of a glyph in points.
 * @jms-builtin
 */
function ps_symbol_width ($psdoc, $ord, $fontid = 0, $size = 0.0) {} 

/** @jms-builtin */
function ps_glyph_show () {} 

/** @jms-builtin */
function ps_glyph_width () {} 

/** @jms-builtin */
function ps_glyph_list () {} 

/** @jms-builtin */
function ps_add_kerning () {} 

/** @jms-builtin */
function ps_add_ligature () {}

define ('PS_LINECAP_BUTT', 0);
define ('PS_LINECAP_ROUND', 1);
define ('PS_LINECAP_SQUARED', 2);
define ('PS_LINEJOIN_MITER', 0);
define ('PS_LINEJOIN_ROUND', 1);
define ('PS_LINEJOIN_BEVEL', 2);

// End of ps v.
?>
