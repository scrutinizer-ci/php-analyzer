<?php

// Start of imagick v.2.3.0

/** @jms-builtin */
class ImagickException extends Exception  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;


	final private function __clone () {}

	/**
	 * @param message[optional]
	 * @param code[optional]
	 * @param previous[optional]
	 */
	public function __construct ($message, $code, $previous) {}

	final public function getMessage () {}

	final public function getCode () {}

	final public function getFile () {}

	final public function getLine () {}

	final public function getTrace () {}

	final public function getPrevious () {}

	final public function getTraceAsString () {}

	public function __toString () {}

}

/** @jms-builtin */
class ImagickDrawException extends Exception  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;


	final private function __clone () {}

	/**
	 * @param message[optional]
	 * @param code[optional]
	 * @param previous[optional]
	 */
	public function __construct ($message, $code, $previous) {}

	final public function getMessage () {}

	final public function getCode () {}

	final public function getFile () {}

	final public function getLine () {}

	final public function getTrace () {}

	final public function getPrevious () {}

	final public function getTraceAsString () {}

	public function __toString () {}

}

/** @jms-builtin */
class ImagickPixelIteratorException extends Exception  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;


	final private function __clone () {}

	/**
	 * @param message[optional]
	 * @param code[optional]
	 * @param previous[optional]
	 */
	public function __construct ($message, $code, $previous) {}

	final public function getMessage () {}

	final public function getCode () {}

	final public function getFile () {}

	final public function getLine () {}

	final public function getTrace () {}

	final public function getPrevious () {}

	final public function getTraceAsString () {}

	public function __toString () {}

}

/** @jms-builtin */
class ImagickPixelException extends Exception  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;


	final private function __clone () {}

	/**
	 * @param message[optional]
	 * @param code[optional]
	 * @param previous[optional]
	 */
	public function __construct ($message, $code, $previous) {}

	final public function getMessage () {}

	final public function getCode () {}

	final public function getFile () {}

	final public function getLine () {}

	final public function getTrace () {}

	final public function getPrevious () {}

	final public function getTraceAsString () {}

	public function __toString () {}

}

/** @jms-builtin */
class Imagick implements Iterator, Traversable {
	const COLOR_BLACK = 11;
	const COLOR_BLUE = 12;
	const COLOR_CYAN = 13;
	const COLOR_GREEN = 14;
	const COLOR_RED = 15;
	const COLOR_YELLOW = 16;
	const COLOR_MAGENTA = 17;
	const COLOR_OPACITY = 18;
	const COLOR_ALPHA = 19;
	const COLOR_FUZZ = 20;
	const IMAGICK_EXTNUM = 20300;
	const IMAGICK_EXTVER = "2.3.0";
	const COMPOSITE_DEFAULT = 40;
	const COMPOSITE_UNDEFINED = 0;
	const COMPOSITE_NO = 1;
	const COMPOSITE_ADD = 2;
	const COMPOSITE_ATOP = 3;
	const COMPOSITE_BLEND = 4;
	const COMPOSITE_BUMPMAP = 5;
	const COMPOSITE_CLEAR = 7;
	const COMPOSITE_COLORBURN = 8;
	const COMPOSITE_COLORDODGE = 9;
	const COMPOSITE_COLORIZE = 10;
	const COMPOSITE_COPYBLACK = 11;
	const COMPOSITE_COPYBLUE = 12;
	const COMPOSITE_COPY = 13;
	const COMPOSITE_COPYCYAN = 14;
	const COMPOSITE_COPYGREEN = 15;
	const COMPOSITE_COPYMAGENTA = 16;
	const COMPOSITE_COPYOPACITY = 17;
	const COMPOSITE_COPYRED = 18;
	const COMPOSITE_COPYYELLOW = 19;
	const COMPOSITE_DARKEN = 20;
	const COMPOSITE_DSTATOP = 21;
	const COMPOSITE_DST = 22;
	const COMPOSITE_DSTIN = 23;
	const COMPOSITE_DSTOUT = 24;
	const COMPOSITE_DSTOVER = 25;
	const COMPOSITE_DIFFERENCE = 26;
	const COMPOSITE_DISPLACE = 27;
	const COMPOSITE_DISSOLVE = 28;
	const COMPOSITE_EXCLUSION = 29;
	const COMPOSITE_HARDLIGHT = 30;
	const COMPOSITE_HUE = 31;
	const COMPOSITE_IN = 32;
	const COMPOSITE_LIGHTEN = 33;
	const COMPOSITE_LUMINIZE = 35;
	const COMPOSITE_MINUS = 36;
	const COMPOSITE_MODULATE = 37;
	const COMPOSITE_MULTIPLY = 38;
	const COMPOSITE_OUT = 39;
	const COMPOSITE_OVER = 40;
	const COMPOSITE_OVERLAY = 41;
	const COMPOSITE_PLUS = 42;
	const COMPOSITE_REPLACE = 43;
	const COMPOSITE_SATURATE = 44;
	const COMPOSITE_SCREEN = 45;
	const COMPOSITE_SOFTLIGHT = 46;
	const COMPOSITE_SRCATOP = 47;
	const COMPOSITE_SRC = 48;
	const COMPOSITE_SRCIN = 49;
	const COMPOSITE_SRCOUT = 50;
	const COMPOSITE_SRCOVER = 51;
	const COMPOSITE_SUBTRACT = 52;
	const COMPOSITE_THRESHOLD = 53;
	const COMPOSITE_XOR = 54;
	const MONTAGEMODE_FRAME = 1;
	const MONTAGEMODE_UNFRAME = 2;
	const MONTAGEMODE_CONCATENATE = 3;
	const STYLE_NORMAL = 1;
	const STYLE_ITALIC = 2;
	const STYLE_OBLIQUE = 3;
	const STYLE_ANY = 4;
	const FILTER_UNDEFINED = 0;
	const FILTER_POINT = 1;
	const FILTER_BOX = 2;
	const FILTER_TRIANGLE = 3;
	const FILTER_HERMITE = 4;
	const FILTER_HANNING = 5;
	const FILTER_HAMMING = 6;
	const FILTER_BLACKMAN = 7;
	const FILTER_GAUSSIAN = 8;
	const FILTER_QUADRATIC = 9;
	const FILTER_CUBIC = 10;
	const FILTER_CATROM = 11;
	const FILTER_MITCHELL = 12;
	const FILTER_LANCZOS = 13;
	const FILTER_BESSEL = 14;
	const FILTER_SINC = 15;
	const IMGTYPE_UNDEFINED = 0;
	const IMGTYPE_BILEVEL = 1;
	const IMGTYPE_GRAYSCALE = 2;
	const IMGTYPE_GRAYSCALEMATTE = 3;
	const IMGTYPE_PALETTE = 4;
	const IMGTYPE_PALETTEMATTE = 5;
	const IMGTYPE_TRUECOLOR = 6;
	const IMGTYPE_TRUECOLORMATTE = 7;
	const IMGTYPE_COLORSEPARATION = 8;
	const IMGTYPE_COLORSEPARATIONMATTE = 9;
	const IMGTYPE_OPTIMIZE = 10;
	const RESOLUTION_UNDEFINED = 0;
	const RESOLUTION_PIXELSPERINCH = 1;
	const RESOLUTION_PIXELSPERCENTIMETER = 2;
	const COMPRESSION_UNDEFINED = 0;
	const COMPRESSION_NO = 1;
	const COMPRESSION_BZIP = 2;
	const COMPRESSION_FAX = 3;
	const COMPRESSION_GROUP4 = 4;
	const COMPRESSION_JPEG = 5;
	const COMPRESSION_JPEG2000 = 6;
	const COMPRESSION_LOSSLESSJPEG = 7;
	const COMPRESSION_LZW = 8;
	const COMPRESSION_RLE = 9;
	const COMPRESSION_ZIP = 10;
	const PAINT_POINT = 1;
	const PAINT_REPLACE = 2;
	const PAINT_FLOODFILL = 3;
	const PAINT_FILLTOBORDER = 4;
	const PAINT_RESET = 5;
	const GRAVITY_NORTHWEST = 1;
	const GRAVITY_NORTH = 2;
	const GRAVITY_NORTHEAST = 3;
	const GRAVITY_WEST = 4;
	const GRAVITY_CENTER = 5;
	const GRAVITY_EAST = 6;
	const GRAVITY_SOUTHWEST = 7;
	const GRAVITY_SOUTH = 8;
	const GRAVITY_SOUTHEAST = 9;
	const STRETCH_NORMAL = 1;
	const STRETCH_ULTRACONDENSED = 2;
	const STRETCH_CONDENSED = 4;
	const STRETCH_SEMICONDENSED = 5;
	const STRETCH_SEMIEXPANDED = 6;
	const STRETCH_EXPANDED = 7;
	const STRETCH_EXTRAEXPANDED = 8;
	const STRETCH_ULTRAEXPANDED = 9;
	const STRETCH_ANY = 10;
	const ALIGN_UNDEFINED = 0;
	const ALIGN_LEFT = 1;
	const ALIGN_CENTER = 2;
	const ALIGN_RIGHT = 3;
	const DECORATION_NO = 1;
	const DECORATION_UNDERLINE = 2;
	const DECORATION_OVERLINE = 3;
	const DECORATION_LINETROUGH = 4;
	const NOISE_UNIFORM = 1;
	const NOISE_GAUSSIAN = 2;
	const NOISE_MULTIPLICATIVEGAUSSIAN = 3;
	const NOISE_IMPULSE = 4;
	const NOISE_LAPLACIAN = 5;
	const NOISE_POISSON = 6;
	const NOISE_RANDOM = 7;
	const CHANNEL_UNDEFINED = 0;
	const CHANNEL_RED = 1;
	const CHANNEL_GRAY = 1;
	const CHANNEL_CYAN = 1;
	const CHANNEL_GREEN = 2;
	const CHANNEL_MAGENTA = 2;
	const CHANNEL_BLUE = 4;
	const CHANNEL_YELLOW = 4;
	const CHANNEL_ALPHA = 8;
	const CHANNEL_OPACITY = 8;
	const CHANNEL_MATTE = 8;
	const CHANNEL_BLACK = 32;
	const CHANNEL_INDEX = 32;
	const CHANNEL_ALL = 255;
	const CHANNEL_DEFAULT = 247;
	const METRIC_UNDEFINED = 0;
	const METRIC_MEANABSOLUTEERROR = 2;
	const METRIC_MEANSQUAREERROR = 4;
	const METRIC_PEAKABSOLUTEERROR = 5;
	const METRIC_PEAKSIGNALTONOISERATIO = 6;
	const METRIC_ROOTMEANSQUAREDERROR = 7;
	const PIXEL_CHAR = 1;
	const PIXEL_DOUBLE = 2;
	const PIXEL_FLOAT = 3;
	const PIXEL_INTEGER = 4;
	const PIXEL_LONG = 5;
	const PIXEL_QUANTUM = 6;
	const PIXEL_SHORT = 7;
	const EVALUATE_UNDEFINED = 0;
	const EVALUATE_ADD = 1;
	const EVALUATE_AND = 2;
	const EVALUATE_DIVIDE = 3;
	const EVALUATE_LEFTSHIFT = 4;
	const EVALUATE_MAX = 5;
	const EVALUATE_MIN = 6;
	const EVALUATE_MULTIPLY = 7;
	const EVALUATE_OR = 8;
	const EVALUATE_RIGHTSHIFT = 9;
	const EVALUATE_SET = 10;
	const EVALUATE_SUBTRACT = 11;
	const EVALUATE_XOR = 12;
	const COLORSPACE_UNDEFINED = 0;
	const COLORSPACE_RGB = 1;
	const COLORSPACE_GRAY = 2;
	const COLORSPACE_TRANSPARENT = 3;
	const COLORSPACE_OHTA = 4;
	const COLORSPACE_LAB = 5;
	const COLORSPACE_XYZ = 6;
	const COLORSPACE_YCBCR = 7;
	const COLORSPACE_YCC = 8;
	const COLORSPACE_YIQ = 9;
	const COLORSPACE_YPBPR = 10;
	const COLORSPACE_YUV = 11;
	const COLORSPACE_CMYK = 12;
	const COLORSPACE_SRGB = 13;
	const COLORSPACE_HSB = 14;
	const COLORSPACE_HSL = 15;
	const COLORSPACE_HWB = 16;
	const COLORSPACE_REC601LUMA = 17;
	const COLORSPACE_REC709LUMA = 19;
	const COLORSPACE_LOG = 21;
	const VIRTUALPIXELMETHOD_UNDEFINED = 0;
	const VIRTUALPIXELMETHOD_BACKGROUND = 1;
	const VIRTUALPIXELMETHOD_CONSTANT = 2;
	const VIRTUALPIXELMETHOD_EDGE = 4;
	const VIRTUALPIXELMETHOD_MIRROR = 5;
	const VIRTUALPIXELMETHOD_TILE = 7;
	const VIRTUALPIXELMETHOD_TRANSPARENT = 8;
	const PREVIEW_UNDEFINED = 0;
	const PREVIEW_ROTATE = 1;
	const PREVIEW_SHEAR = 2;
	const PREVIEW_ROLL = 3;
	const PREVIEW_HUE = 4;
	const PREVIEW_SATURATION = 5;
	const PREVIEW_BRIGHTNESS = 6;
	const PREVIEW_GAMMA = 7;
	const PREVIEW_SPIFF = 8;
	const PREVIEW_DULL = 9;
	const PREVIEW_GRAYSCALE = 10;
	const PREVIEW_QUANTIZE = 11;
	const PREVIEW_DESPECKLE = 12;
	const PREVIEW_REDUCENOISE = 13;
	const PREVIEW_ADDNOISE = 14;
	const PREVIEW_SHARPEN = 15;
	const PREVIEW_BLUR = 16;
	const PREVIEW_THRESHOLD = 17;
	const PREVIEW_EDGEDETECT = 18;
	const PREVIEW_SPREAD = 19;
	const PREVIEW_SOLARIZE = 20;
	const PREVIEW_SHADE = 21;
	const PREVIEW_RAISE = 22;
	const PREVIEW_SEGMENT = 23;
	const PREVIEW_SWIRL = 24;
	const PREVIEW_IMPLODE = 25;
	const PREVIEW_WAVE = 26;
	const PREVIEW_OILPAINT = 27;
	const PREVIEW_CHARCOALDRAWING = 28;
	const PREVIEW_JPEG = 29;
	const RENDERINGINTENT_UNDEFINED = 0;
	const RENDERINGINTENT_SATURATION = 1;
	const RENDERINGINTENT_PERCEPTUAL = 2;
	const RENDERINGINTENT_ABSOLUTE = 3;
	const RENDERINGINTENT_RELATIVE = 4;
	const INTERLACE_UNDEFINED = 0;
	const INTERLACE_NO = 1;
	const INTERLACE_LINE = 2;
	const INTERLACE_PLANE = 3;
	const INTERLACE_PARTITION = 4;
	const INTERLACE_GIF = 5;
	const INTERLACE_JPEG = 6;
	const INTERLACE_PNG = 7;
	const FILLRULE_UNDEFINED = 0;
	const FILLRULE_EVENODD = 1;
	const FILLRULE_NONZERO = 2;
	const PATHUNITS_UNDEFINED = 0;
	const PATHUNITS_USERSPACE = 1;
	const PATHUNITS_USERSPACEONUSE = 2;
	const PATHUNITS_OBJECTBOUNDINGBOX = 3;
	const LINECAP_UNDEFINED = 0;
	const LINECAP_BUTT = 1;
	const LINECAP_ROUND = 2;
	const LINECAP_SQUARE = 3;
	const LINEJOIN_UNDEFINED = 0;
	const LINEJOIN_MITER = 1;
	const LINEJOIN_ROUND = 2;
	const LINEJOIN_BEVEL = 3;
	const RESOURCETYPE_UNDEFINED = 0;
	const RESOURCETYPE_AREA = 1;
	const RESOURCETYPE_DISK = 2;
	const RESOURCETYPE_FILE = 3;
	const RESOURCETYPE_MAP = 4;
	const RESOURCETYPE_MEMORY = 5;
	const DISPOSE_UNRECOGNIZED = 0;
	const DISPOSE_UNDEFINED = 0;
	const DISPOSE_NONE = 1;
	const DISPOSE_BACKGROUND = 2;
	const DISPOSE_PREVIOUS = 3;
	const INTERPOLATE_UNDEFINED = 0;
	const INTERPOLATE_AVERAGE = 1;
	const INTERPOLATE_BICUBIC = 2;
	const INTERPOLATE_BILINEAR = 3;
	const INTERPOLATE_FILTER = 4;
	const INTERPOLATE_INTEGER = 5;
	const INTERPOLATE_MESH = 6;
	const INTERPOLATE_NEARESTNEIGHBOR = 7;
	const INTERPOLATE_SPLINE = 8;
	const LAYERMETHOD_UNDEFINED = 0;
	const LAYERMETHOD_COALESCE = 1;
	const LAYERMETHOD_COMPAREANY = 2;
	const LAYERMETHOD_COMPARECLEAR = 3;
	const LAYERMETHOD_COMPAREOVERLAY = 4;
	const LAYERMETHOD_DISPOSE = 5;
	const LAYERMETHOD_OPTIMIZE = 6;
	const LAYERMETHOD_OPTIMIZEPLUS = 8;
	const LAYERMETHOD_OPTIMIZEIMAGE = 7;
	const LAYERMETHOD_OPTIMIZETRANS = 9;
	const LAYERMETHOD_REMOVEDUPS = 10;
	const LAYERMETHOD_REMOVEZERO = 11;
	const LAYERMETHOD_COMPOSITE = 12;
	const ORIENTATION_UNDEFINED = 0;
	const ORIENTATION_TOPLEFT = 1;
	const ORIENTATION_TOPRIGHT = 2;
	const ORIENTATION_BOTTOMRIGHT = 3;
	const ORIENTATION_BOTTOMLEFT = 4;
	const ORIENTATION_LEFTTOP = 5;
	const ORIENTATION_RIGHTTOP = 6;
	const ORIENTATION_RIGHTBOTTOM = 7;
	const ORIENTATION_LEFTBOTTOM = 8;
	const DISTORTION_UNDEFINED = 0;
	const DISTORTION_AFFINE = 1;
	const DISTORTION_AFFINEPROJECTION = 2;
	const DISTORTION_ARC = 3;
	const DISTORTION_BILINEAR = 4;
	const DISTORTION_PERSPECTIVE = 5;
	const DISTORTION_PERSPECTIVEPROJECTION = 6;
	const DISTORTION_SCALEROTATETRANSLATE = 7;
	const LAYERMETHOD_MERGE = 13;
	const LAYERMETHOD_FLATTEN = 14;
	const LAYERMETHOD_MOSAIC = 15;


	/**
	 * Removes repeated portions of images to optimize
	 * @link http://www.php.net/manual/en/function.imagick-optimizeimagelayers.php
	 * @return bool &imagick.return.success;
	 */
	public function optimizeimagelayers () {}

	/**
	 * Returns the maximum bounding region between images
	 * @link http://www.php.net/manual/en/function.imagick-compareimagelayers.php
	 * @param method int <p>
	 * One of the layer method constants.
	 * </p>
	 * @return Imagick &imagick.return.success;
	 */
	public function compareimagelayers ($method) {}

	/**
	 * Quickly fetch attributes
	 * @link http://www.php.net/manual/en/function.imagick-pingimageblob.php
	 * @param image string <p>
	 * A string containing the image.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function pingimageblob ($image) {}

	/**
	 * Get basic image attributes in a lightweight manner
	 * @link http://www.php.net/manual/en/function.imagick-pingimagefile.php
	 * @param filehandle resource <p>
	 * An open filehandle to the image.
	 * </p>
	 * @param fileName string[optional] <p>
	 * Optional filename for this image.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function pingimagefile ($filehandle, $fileName = null) {}

	/**
	 * Creates a vertical mirror image
	 * @link http://www.php.net/manual/en/function.imagick-transposeimage.php
	 * @return bool &imagick.return.success;
	 */
	public function transposeimage () {}

	/**
	 * Creates a horizontal mirror image
	 * @link http://www.php.net/manual/en/function.imagick-transverseimage.php
	 * @return bool &imagick.return.success;
	 */
	public function transverseimage () {}

	/**
	 * Remove edges from the image
	 * @link http://www.php.net/manual/en/function.imagick-trimimage.php
	 * @param fuzz float <p>
	 * By default target must match a particular pixel color exactly.
	 * However, in many cases two colors may differ by a small amount.
	 * The fuzz member of image defines how much tolerance is acceptable
	 * to consider two colors as the same. This parameter represents the variation
	 * on the quantum range.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function trimimage ($fuzz) {}

	/**
	 * Applies wave filter to the image
	 * @link http://www.php.net/manual/en/function.imagick-waveimage.php
	 * @param amplitude float <p>
	 * The amplitude of the wave.
	 * </p>
	 * @param length float <p>
	 * The length of the wave.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function waveimage ($amplitude, $length) {}

	/**
	 * Adds vignette filter to the image
	 * @link http://www.php.net/manual/en/function.imagick-vignetteimage.php
	 * @param blackPoint float <p>
	 * The black point.
	 * </p>
	 * @param whitePoint float <p>
	 * The white point
	 * </p>
	 * @param x int <p>
	 * X offset of the ellipse
	 * </p>
	 * @param y int <p>
	 * Y offset of the ellipse
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function vignetteimage ($blackPoint, $whitePoint, $x, $y) {}

	/**
	 * Discards all but one of any pixel color
	 * @link http://www.php.net/manual/en/function.imagick-uniqueimagecolors.php
	 * @return bool &imagick.return.success;
	 */
	public function uniqueimagecolors () {}

	/**
	 * Return if the image has a matte channel
	 * @link http://www.php.net/manual/en/function.imagick-getimagematte.php
	 * @return int Returns true on success or false on failure.
	 */
	public function getimagematte () {}

	/**
	 * Sets the image matte channel
	 * @link http://www.php.net/manual/en/function.imagick-setimagematte.php
	 * @param matte bool <p>
	 * True activates the matte channel and false disables it.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagematte ($matte) {}

	/**
	 * Adaptively resize image with data dependent triangulation
	 * @link http://www.php.net/manual/en/function.imagick-adaptiveresizeimage.php
	 * @param columns int <p>
	 * The number of columns in the scaled image.
	 * </p>
	 * @param rows int <p>
	 * The number of rows in the scaled image.
	 * </p>
	 * @param bestfit bool[optional] <p>
	 * Whether to fit the image inside a bounding box.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function adaptiveresizeimage ($columns, $rows, $bestfit = null) {}

	/**
	 * Simulates a pencil sketch
	 * @link http://www.php.net/manual/en/function.imagick-sketchimage.php
	 * @param radius float <p>
	 * The radius of the Gaussian, in pixels, not counting the center pixel
	 * </p>
	 * @param sigma float <p>
	 * The standard deviation of the Gaussian, in pixels.
	 * </p>
	 * @param angle float <p>
	 * Apply the effect along this angle.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function sketchimage ($radius, $sigma, $angle) {}

	/**
	 * Creates a 3D effect
	 * @link http://www.php.net/manual/en/function.imagick-shadeimage.php
	 * @param gray bool <p>
	 * A value other than zero shades the intensity of each pixel.
	 * </p>
	 * @param azimuth float <p>
	 * Defines the light source direction.
	 * </p>
	 * @param elevation float <p>
	 * Defines the light source direction.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function shadeimage ($gray, $azimuth, $elevation) {}

	/**
	 * Returns the size offset
	 * @link http://www.php.net/manual/en/function.imagick-getsizeoffset.php
	 * @return int the size offset associated with the Imagick object.
	 * &imagick.imagickexception.throw;
	 */
	public function getsizeoffset () {}

	/**
	 * Sets the size and offset of the Imagick object
	 * @link http://www.php.net/manual/en/function.imagick-setsizeoffset.php
	 * @param columns int <p>
	 * The width in pixels.
	 * </p>
	 * @param rows int <p>
	 * The height in pixels.
	 * </p>
	 * @param offset int <p>
	 * The image offset.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setsizeoffset ($columns, $rows, $offset) {}

	/**
	 * Adds adaptive blur filter to image
	 * @link http://www.php.net/manual/en/function.imagick-adaptiveblurimage.php
	 * @param radius float <p>
	 * The radius of the Gaussian, in pixels, not counting the center pixel.
	 * Provide a value of 0 and the radius will be chosen automagically.
	 * </p>
	 * @param sigma float <p>
	 * The standard deviation of the Gaussian, in pixels.
	 * </p>
	 * @param channel int[optional] <p>
	 * &imagick.parameter.channel;
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function adaptiveblurimage ($radius, $sigma, $channel = null) {}

	/**
	 * Enhances the contrast of a color image
	 * @link http://www.php.net/manual/en/function.imagick-contraststretchimage.php
	 * @param black_point float <p>
	 * The black point.
	 * </p>
	 * @param white_point float <p>
	 * The white point.
	 * </p>
	 * @param channel int[optional] <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Imagick::CHANNEL_ALL. Refer to this
	 * list of channel constants.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function contraststretchimage ($black_point, $white_point, $channel = null) {}

	/**
	 * Adaptively sharpen the image
	 * @link http://www.php.net/manual/en/function.imagick-adaptivesharpenimage.php
	 * @param radius float <p>
	 * The radius of the Gaussian, in pixels, not counting the center pixel. Use 0 for auto-select.
	 * </p>
	 * @param sigma float <p>
	 * The standard deviation of the Gaussian, in pixels.
	 * </p>
	 * @param channel int[optional] <p>
	 * &imagick.parameter.channel;
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function adaptivesharpenimage ($radius, $sigma, $channel = null) {}

	/**
	 * Creates a high-contrast, two-color image
	 * @link http://www.php.net/manual/en/function.imagick-randomthresholdimage.php
	 * @param low float <p>
	 * The low point
	 * </p>
	 * @param high float <p>
	 * The high point
	 * </p>
	 * @param channel int[optional] <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function randomthresholdimage ($low, $high, $channel = null) {}

	/**
	 * Rounds image corners
	 * @link http://www.php.net/manual/en/function.imagick-roundcorners.php
	 * @param x_rounding float <p>
	 * x rounding
	 * </p>
	 * @param y_rounding float <p>
	 * y rounding
	 * </p>
	 * @param stroke_width float[optional] <p>
	 * stroke width
	 * </p>
	 * @param displace float[optional] <p>
	 * image displace
	 * </p>
	 * @param size_correction float[optional] <p>
	 * size correction
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function roundcorners ($x_rounding, $y_rounding, $stroke_width = null, $displace = null, $size_correction = null) {}

	/**
	 * Set the iterator position
	 * @link http://www.php.net/manual/en/function.imagick-setiteratorindex.php
	 * @param index int <p>
	 * The position to set the iterator to
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setiteratorindex ($index) {}

	/**
	 * Gets the index of the current active image
	 * @link http://www.php.net/manual/en/function.imagick-getiteratorindex.php
	 * @return int an integer containing the index of the image in the stack.
	 * &imagick.imagickexception.throw;
	 */
	public function getiteratorindex () {}

	/**
	 * Convenience method for setting crop size and the image geometry
	 * @link http://www.php.net/manual/en/function.imagick-transformimage.php
	 * @param crop string <p>
	 * A crop geometry string. This geometry defines a subregion of the image to crop.
	 * </p>
	 * @param geometry string <p>
	 * An image geometry string. This geometry defines the final size of the image.
	 * </p>
	 * @return Imagick &imagick.return.success;
	 */
	public function transformimage ($crop, $geometry) {}

	/**
	 * Sets the image opacity level
	 * @link http://www.php.net/manual/en/function.imagick-setimageopacity.php
	 * @param opacity float <p>
	 * The level of transparency: 1.0 is fully opaque and 0.0 is fully
	 * transparent.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimageopacity ($opacity) {}

	/**
	 * Performs an ordered dither
	 * @link http://www.php.net/manual/en/function.imagick-orderedposterizeimage.php
	 * @param threshold_map string <p>
	 * A string containing the name of the threshold dither map to use
	 * </p>
	 * @param channel int[optional] <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function orderedposterizeimage ($threshold_map, $channel = null) {}

	/**
	 * Simulates a Polaroid picture
	 * @link http://www.php.net/manual/en/function.imagick-polaroidimage.php
	 * @param properties ImagickDraw <p>
	 * The polaroid properties
	 * </p>
	 * @param angle float <p>
	 * The polaroid angle
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function polaroidimage (ImagickDraw $properties, $angle) {}

	/**
	 * Returns the named image property
	 * @link http://www.php.net/manual/en/function.imagick-getimageproperty.php
	 * @param name string <p>
	 * name of the property (for example Exif:DateTime) 
	 * </p>
	 * @return string a string containing the image property, false if a 
	 * property with the given name does not exist.
	 */
	public function getimageproperty ($name) {}

	/**
	 * Sets an image property
	 * @link http://www.php.net/manual/en/function.imagick-setimageproperty.php
	 * @param name string <p>
	 * </p>
	 * @param value string <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimageproperty ($name, $value) {}

	/**
	 * Sets the image interpolate pixel method
	 * @link http://www.php.net/manual/en/function.imagick-setimageinterpolatemethod.php
	 * @param method int <p>
	 * The method is one of the Imagick::INTERPOLATE_* constants
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimageinterpolatemethod ($method) {}

	/**
	 * Returns the interpolation method
	 * @link http://www.php.net/manual/en/function.imagick-getimageinterpolatemethod.php
	 * @return int the interpolate method on success.
	 * &imagick.imagickexception.throw;
	 */
	public function getimageinterpolatemethod () {}

	/**
	 * Stretches with saturation the image intensity
	 * @link http://www.php.net/manual/en/function.imagick-linearstretchimage.php
	 * @param blackPoint float <p>
	 * The image black point
	 * </p>
	 * @param whitePoint float <p>
	 * The image white point
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function linearstretchimage ($blackPoint, $whitePoint) {}

	/**
	 * Returns the image length in bytes
	 * @link http://www.php.net/manual/en/function.imagick-getimagelength.php
	 * @return int an int containing the current image size.
	 */
	public function getimagelength () {}

	/**
	 * Set image size
	 * @link http://www.php.net/manual/en/function.imagick-extentimage.php
	 * @param width int <p>
	 * The new width
	 * </p>
	 * @param height int <p>
	 * The new height
	 * </p>
	 * @param x int <p>
	 * X position for the new size
	 * </p>
	 * @param y int <p>
	 * Y position for the new size
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function extentimage ($width, $height, $x, $y) {}

	/**
	 * Gets the image orientation
	 * @link http://www.php.net/manual/en/function.imagick-getimageorientation.php
	 * @return int an int on success.
	 * &imagick.imagickexception.throw;
	 */
	public function getimageorientation () {}

	/**
	 * Sets the image orientation
	 * @link http://www.php.net/manual/en/function.imagick-setimageorientation.php
	 * @param orientation int <p>
	 * One of the orientation constants
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimageorientation ($orientation) {}

	/**
	 * Changes the color value of any pixel that matches target
	 * @link http://www.php.net/manual/en/function.imagick-paintfloodfillimage.php
	 * @param fill mixed <p>
	 * ImagickPixel object or a string containing the fill color
	 * </p>
	 * @param fuzz float <p>
	 * The amount of fuzz. For example, set fuzz to 10 and the color red at
	 * intensities of 100 and 102 respectively are now interpreted as the
	 * same color for the purposes of the floodfill.
	 * </p>
	 * @param bordercolor mixed <p>
	 * ImagickPixel object or a string containing the border color
	 * </p>
	 * @param x int <p>
	 * X start position of the floodfill
	 * </p>
	 * @param y int <p>
	 * Y start position of the floodfill
	 * </p>
	 * @param channel int[optional] <p>
	 * &imagick.parameter.channel;
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function paintfloodfillimage ($fill, $fuzz, $bordercolor, $x, $y, $channel = null) {}

	/**
	 * Replaces colors in the image
	 * @link http://www.php.net/manual/en/function.imagick-clutimage.php
	 * @param lookup_table Imagick <p>
	 * Imagick object containing the color lookup table
	 * </p>
	 * @param channel float[optional] <p>
	 * The Channeltype
	 * constant. When not supplied, default channels are replaced.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function clutimage (Imagick $lookup_table, $channel = null) {}

	/**
	 * Returns the image properties
	 * @link http://www.php.net/manual/en/function.imagick-getimageproperties.php
	 * @param pattern string[optional] <p>
	 * The pattern for property names.
	 * </p>
	 * @param only_names bool[optional] <p>
	 * Whether to return only property names. If false then also the values are returned
	 * </p>
	 * @return array an array containing the image properties or property names.
	 */
	public function getimageproperties ($pattern = null, $only_names = null) {}

	/**
	 * Returns the image profiles
	 * @link http://www.php.net/manual/en/function.imagick-getimageprofiles.php
	 * @param pattern string[optional] <p>
	 * The pattern for profile names.
	 * </p>
	 * @param only_names bool[optional] <p>
	 * Whether to return only profile names. If false then values are returned as well
	 * </p>
	 * @return array an array containing the image profiles or profile names.
	 */
	public function getimageprofiles ($pattern = null, $only_names = null) {}

	/**
	 * Distorts an image using various distortion methods
	 * @link http://www.php.net/manual/en/function.imagick-distortimage.php
	 * @param method int <p>
	 * The method of image distortion. See distortion constants
	 * </p>
	 * @param arguments array <p>
	 * The arguments for this distortion method
	 * </p>
	 * @param bestfit bool <p>
	 * Attempt to resize destination to fit distorted source
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function distortimage ($method, array $arguments, $bestfit) {}

	/**
	 * Writes an image to a filehandle
	 * @link http://www.php.net/manual/en/function.imagick-writeimagefile.php
	 * @param filehandle resource <p>
	 * Filehandle where to write the image
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function writeimagefile ($filehandle) {}

	/**
	 * Writes frames to a filehandle
	 * @link http://www.php.net/manual/en/function.imagick-writeimagesfile.php
	 * @param filehandle resource <p>
	 * Filehandle where to write the images
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function writeimagesfile ($filehandle) {}

	/**
	 * Reset image page
	 * @link http://www.php.net/manual/en/function.imagick-resetimagepage.php
	 * @param page string <p>
	 * The page definition. For example 7168x5147+0+0
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function resetimagepage ($page) {}

	/**
	 * Sets image clip mask
	 * @link http://www.php.net/manual/en/function.imagick-setimageclipmask.php
	 * @param clip_mask Imagick <p>
	 * The Imagick object containing the clip mask
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimageclipmask (Imagick $clip_mask) {}

	/**
	 * Gets image clip mask
	 * @link http://www.php.net/manual/en/function.imagick-getimageclipmask.php
	 * @return Imagick an Imagick object containing the clip mask.
	 * &imagick.imagickexception.throw;
	 */
	public function getimageclipmask () {}

	/**
	 * Animates an image or images
	 * @link http://www.php.net/manual/en/function.imagick-animateimages.php
	 * @param x_server string <p>
	 * X server address
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function animateimages ($x_server) {}

	/**
	 * Recolors image
	 * @link http://www.php.net/manual/en/function.imagick-recolorimage.php
	 * @param matrix array <p>
	 * The matrix containing the color values
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function recolorimage (array $matrix) {}

	/**
	 * Sets font
	 * @link http://www.php.net/manual/en/function.imagick-setfont.php
	 * @param font string <p>
	 * Font name or a filename
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setfont ($font) {}

	/**
	 * Gets font
	 * @link http://www.php.net/manual/en/function.imagick-getfont.php
	 * @return string the string containing the font name or false if not font is set.
	 */
	public function getfont () {}

	/**
	 * Sets point size
	 * @link http://www.php.net/manual/en/function.imagick-setpointsize.php
	 * @param point_size float <p>
	 * Point size
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setpointsize ($point_size) {}

	/**
	 * Gets point size
	 * @link http://www.php.net/manual/en/function.imagick-getpointsize.php
	 * @return string a &float; containing the point size.
	 */
	public function getpointsize () {}

	/**
	 * Merges image layers
	 * @link http://www.php.net/manual/en/function.imagick-mergeimagelayers.php
	 * @param layer_method int <p>
	 * One of the Imagick::LAYERMETHOD_* constants
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function mergeimagelayers ($layer_method) {}

	/**
	 * The Imagick constructor
	 * @link http://www.php.net/manual/en/function.imagick-construct.php
	 * @param files mixed[optional] <p>
	 * The path to an image to load or array of paths
	 * </p>
	 * @return Imagick a new Imagick object on success.
	 * &imagick.imagickexception.throw;
	 */
	public function __construct ($files = null) {}

	public function __tostring () {}

	/**
	 * Returns a MagickPixelIterator
	 * @link http://www.php.net/manual/en/function.imagick-getpixeliterator.php
	 * @return ImagickPixelIterator an ImagickPixelIterator on success.
	 * &imagick.imagickexception.throw;
	 */
	public function getpixeliterator () {}

	/**
	 * Get an ImagickPixelIterator for an image section
	 * @link http://www.php.net/manual/en/function.imagick-getpixelregioniterator.php
	 * @param x int <p>
	 * The x-coordinate of the region.
	 * </p>
	 * @param y int <p>
	 * The y-coordinate of the region.
	 * </p>
	 * @param columns int <p>
	 * The width of the region.
	 * </p>
	 * @param rows int <p>
	 * The height of the region.
	 * </p>
	 * @return ImagickPixelIterator an ImagickPixelIterator for an image section.
	 * &imagick.imagickexception.throw;
	 */
	public function getpixelregioniterator ($x, $y, $columns, $rows) {}

	/**
	 * Reads image from filename
	 * @link http://www.php.net/manual/en/function.imagick-readimage.php
	 * @param filename string <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function readimage ($filename) {}

	/**
	 * @param filenames
	 */
	public function readimages ($filenames) {}

	/**
	 * Reads image from a binary string
	 * @link http://www.php.net/manual/en/function.imagick-readimageblob.php
	 * @param image string <p>
	 * </p>
	 * @param filename string[optional] 
	 * @return bool &imagick.return.success;
	 */
	public function readimageblob ($image, $filename = null) {}

	/**
	 * Sets the format of a particular image
	 * @link http://www.php.net/manual/en/function.imagick-setimageformat.php
	 * @param format string <p>
	 * String presentation of the image format. Format support
	 * depends on the ImageMagick installation.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimageformat ($format) {}

	/**
	 * Scales the size of an image
	 * @link http://www.php.net/manual/en/function.imagick-scaleimage.php
	 * @param cols int <p>
	 * </p>
	 * @param rows int <p>
	 * </p>
	 * @param bestfit bool[optional] <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function scaleimage ($cols, $rows, $bestfit = null) {}

	/**
	 * Writes an image to the specified filename
	 * @link http://www.php.net/manual/en/function.imagick-writeimage.php
	 * @param filename string[optional] <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function writeimage ($filename = null) {}

	/**
	 * Writes an image or image sequence
	 * @link http://www.php.net/manual/en/function.imagick-writeimages.php
	 * @param filename string <p>
	 * </p>
	 * @param adjoin bool <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function writeimages ($filename, $adjoin) {}

	/**
	 * Adds blur filter to image
	 * @link http://www.php.net/manual/en/function.imagick-blurimage.php
	 * @param radius float <p>
	 * Blur radius
	 * </p>
	 * @param sigma float <p>
	 * Standard deviation
	 * </p>
	 * @param channel int[optional] <p>
	 * The Channeltype
	 * constant. When not supplied, all channels are blurred.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function blurimage ($radius, $sigma, $channel = null) {}

	/**
	 * Changes the size of an image
	 * @link http://www.php.net/manual/en/function.imagick-thumbnailimage.php
	 * @param columns int <p>
	 * Image width
	 * </p>
	 * @param rows int <p>
	 * Image height
	 * </p>
	 * @param bestfit bool[optional] <p>
	 * Whether to force maximum values
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function thumbnailimage ($columns, $rows, $bestfit = null) {}

	/**
	 * Creates a crop thumbnail
	 * @link http://www.php.net/manual/en/function.imagick-cropthumbnailimage.php
	 * @param width int <p>
	 * The width of the thumbnail
	 * </p>
	 * @param height int <p>
	 * The Height of the thumbnail
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function cropthumbnailimage ($width, $height) {}

	/**
	 * Returns the filename of a particular image in a sequence
	 * @link http://www.php.net/manual/en/function.imagick-getimagefilename.php
	 * @return string a string with the filename of the image.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagefilename () {}

	/**
	 * Sets the filename of a particular image
	 * @link http://www.php.net/manual/en/function.imagick-setimagefilename.php
	 * @param filename string <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagefilename ($filename) {}

	/**
	 * Returns the format of a particular image in a sequence
	 * @link http://www.php.net/manual/en/function.imagick-getimageformat.php
	 * @return string a string containing the image format on success.
	 * &imagick.imagickexception.throw;
	 */
	public function getimageformat () {}

	public function getimagemimetype () {}

	/**
	 * Removes an image from the image list
	 * @link http://www.php.net/manual/en/function.imagick-removeimage.php
	 * @return bool &imagick.return.success;
	 */
	public function removeimage () {}

	/**
	 * Destroys the Imagick object
	 * @link http://www.php.net/manual/en/function.imagick-destroy.php
	 * @return bool &imagick.return.success;
	 */
	public function destroy () {}

	/**
	 * Clears all resources associated to Imagick object
	 * @link http://www.php.net/manual/en/function.imagick-clear.php
	 * @return bool &imagick.return.success;
	 */
	public function clear () {}

	/**
	 * Makes an exact copy of the Imagick object
	 * @link http://www.php.net/manual/en/function.imagick-clone.php
	 * @return Imagick 
	 */
	public function clone () {}

	/**
	 * Returns the image length in bytes
	 * @link http://www.php.net/manual/en/function.imagick-getimagesize.php
	 * @return int an int containing the current image size.
	 */
	public function getimagesize () {}

	/**
	 * Returns the image sequence as a blob
	 * @link http://www.php.net/manual/en/function.imagick-getimageblob.php
	 * @return string a string containing the image.
	 * &imagick.imagickexception.throw;
	 */
	public function getimageblob () {}

	/**
	 * Returns all image sequences as a blob
	 * @link http://www.php.net/manual/en/function.imagick-getimagesblob.php
	 * @return string a string containing the images. On failure, throws
	 * ImagickException.
	 */
	public function getimagesblob () {}

	/**
	 * Sets the Imagick iterator to the first image
	 * @link http://www.php.net/manual/en/function.imagick-setfirstiterator.php
	 * @return bool &imagick.return.success;
	 */
	public function setfirstiterator () {}

	/**
	 * Sets the Imagick iterator to the last image
	 * @link http://www.php.net/manual/en/function.imagick-setlastiterator.php
	 * @return bool &imagick.return.success;
	 */
	public function setlastiterator () {}

	public function resetiterator () {}

	/**
	 * Move to the previous image in the object
	 * @link http://www.php.net/manual/en/function.imagick-previousimage.php
	 * @return bool &imagick.return.success;
	 */
	public function previousimage () {}

	/**
	 * Moves to the next image
	 * @link http://www.php.net/manual/en/function.imagick-nextimage.php
	 * @return bool &imagick.return.success;
	 */
	public function nextimage () {}

	/**
	 * Checks if the object has a previous image
	 * @link http://www.php.net/manual/en/function.imagick-haspreviousimage.php
	 * @return bool true if the object has more images when traversing the list in the
	 * reverse direction, returns false if there are none.
	 */
	public function haspreviousimage () {}

	/**
	 * Checks if the object has more images
	 * @link http://www.php.net/manual/en/function.imagick-hasnextimage.php
	 * @return bool true if the object has more images when traversing the list in the
	 * forward direction, returns false if there are none.
	 */
	public function hasnextimage () {}

	/**
	 * Set the iterator position
	 * @link http://www.php.net/manual/en/function.imagick-setimageindex.php
	 * @param index int <p>
	 * The position to set the iterator to
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimageindex ($index) {}

	/**
	 * Gets the index of the current active image
	 * @link http://www.php.net/manual/en/function.imagick-getimageindex.php
	 * @return int an integer containing the index of the image in the stack.
	 * &imagick.imagickexception.throw;
	 */
	public function getimageindex () {}

	/**
	 * Adds a comment to your image
	 * @link http://www.php.net/manual/en/function.imagick-commentimage.php
	 * @param comment string <p>
	 * The comment to add
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function commentimage ($comment) {}

	/**
	 * Extracts a region of the image
	 * @link http://www.php.net/manual/en/function.imagick-cropimage.php
	 * @param width int <p>
	 * The width of the crop
	 * </p>
	 * @param height int <p>
	 * The height of the crop
	 * </p>
	 * @param x int <p>
	 * The X coordinate of the cropped region's top left corner
	 * </p>
	 * @param y int <p>
	 * The Y coordinate of the cropped region's top left corner
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function cropimage ($width, $height, $x, $y) {}

	/**
	 * Adds a label to an image
	 * @link http://www.php.net/manual/en/function.imagick-labelimage.php
	 * @param label string <p>
	 * The label to add
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function labelimage ($label) {}

	/**
	 * Gets the width and height as an associative array
	 * @link http://www.php.net/manual/en/function.imagick-getimagegeometry.php
	 * @return array an array with the width/height of the image.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagegeometry () {}

	/**
	 * Renders the ImagickDraw object on the current image
	 * @link http://www.php.net/manual/en/function.imagick-drawimage.php
	 * @param draw ImagickDraw <p>
	 * The drawing operations to render on the image.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function drawimage (ImagickDraw $draw) {}

	/**
	 * Sets the image compression quality
	 * @link http://www.php.net/manual/en/function.imagick-setimagecompressionquality.php
	 * @param quality int <p>
	 * The image compression quality as an integer
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagecompressionquality ($quality) {}

	/**
	 * Gets the current image's compression quality
	 * @link http://www.php.net/manual/en/function.imagick-getimagecompressionquality.php
	 * @return int integer describing the images compression quality
	 */
	public function getimagecompressionquality () {}

	/**
	 * Annotates an image with text
	 * @link http://www.php.net/manual/en/function.imagick-annotateimage.php
	 * @param draw_settings ImagickDraw <p>
	 * The ImagickDraw object that contains settings for drawing the text
	 * </p>
	 * @param x float <p>
	 * Horizontal offset in pixels to the left of text
	 * </p>
	 * @param y float <p>
	 * Vertical offset in pixels to the baseline of text
	 * </p>
	 * @param angle float <p>
	 * The angle at which to write the text
	 * </p>
	 * @param text string <p>
	 * The string to draw
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function annotateimage (ImagickDraw $draw_settings, $x, $y, $angle, $text) {}

	/**
	 * Composite one image onto another
	 * @link http://www.php.net/manual/en/function.imagick-compositeimage.php
	 * @param composite_object Imagick <p>
	 * Imagick object which holds the composite image
	 * </p>
	 * @param composite int 
	 * @param x int <p>
	 * The column offset of the composited image
	 * </p>
	 * @param y int <p>
	 * The row offset of the composited image
	 * </p>
	 * @param channel int[optional] <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this list of channel constants.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function compositeimage (Imagick $composite_object, $composite, $x, $y, $channel = null) {}

	/**
	 * Control the brightness, saturation, and hue
	 * @link http://www.php.net/manual/en/function.imagick-modulateimage.php
	 * @param brightness float <p>
	 * </p>
	 * @param saturation float <p>
	 * </p>
	 * @param hue float <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function modulateimage ($brightness, $saturation, $hue) {}

	/**
	 * Gets the number of unique colors in the image
	 * @link http://www.php.net/manual/en/function.imagick-getimagecolors.php
	 * @return int &imagick.return.success;
	 */
	public function getimagecolors () {}

	/**
	 * Creates a composite image
	 * @link http://www.php.net/manual/en/function.imagick-montageimage.php
	 * @param draw ImagickDraw <p>
	 * The font name, size, and color are obtained from this object.
	 * </p>
	 * @param tile_geometry string <p>
	 * The number of tiles per row and page (e.g. 6x4+0+0).
	 * </p>
	 * @param thumbnail_geometry string <p>
	 * Preferred image size and border size of each thumbnail
	 * (e.g. 120x120+4+3>).
	 * </p>
	 * @param mode int <p>
	 * Thumbnail framing mode, see Montage Mode constants.
	 * </p>
	 * @param frame string <p>
	 * Surround the image with an ornamental border (e.g. 15x15+3+3). The
	 * frame color is that of the thumbnail's matte color.
	 * </p>
	 * @return Imagick &imagick.return.success;
	 */
	public function montageimage (ImagickDraw $draw, $tile_geometry, $thumbnail_geometry, $mode, $frame) {}

	/**
	 * Identifies an image and fetches attributes
	 * @link http://www.php.net/manual/en/function.imagick-identifyimage.php
	 * @param appendRawOutput bool[optional] <p>
	 * </p>
	 * @return array Identifies an image and returns the attributes. Attributes include
	 * the image width, height, size, and others.
	 * &imagick.imagickexception.throw;
	 */
	public function identifyimage ($appendRawOutput = null) {}

	/**
	 * Changes the value of individual pixels based on a threshold
	 * @link http://www.php.net/manual/en/function.imagick-thresholdimage.php
	 * @param threshold float <p>
	 * </p>
	 * @param channel int[optional] <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function thresholdimage ($threshold, $channel = null) {}

	/**
	 * Selects a threshold for each pixel based on a range of intensity
	 * @link http://www.php.net/manual/en/function.imagick-adaptivethresholdimage.php
	 * @param width int <p>
	 * Width of the local neighborhood.
	 * </p>
	 * @param height int <p>
	 * Height of the local neighborhood.
	 * </p>
	 * @param offset int <p>
	 * The mean offset
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function adaptivethresholdimage ($width, $height, $offset) {}

	/**
	 * Forces all pixels below the threshold into black
	 * @link http://www.php.net/manual/en/function.imagick-blackthresholdimage.php
	 * @param threshold mixed <p>
	 * The threshold below which everything turns black
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function blackthresholdimage ($threshold) {}

	/**
	 * Force all pixels above the threshold into white
	 * @link http://www.php.net/manual/en/function.imagick-whitethresholdimage.php
	 * @param threshold mixed <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function whitethresholdimage ($threshold) {}

	/**
	 * Append a set of images
	 * @link http://www.php.net/manual/en/function.imagick-appendimages.php
	 * @param stack bool <p>
	 * The direction of the stack (top to bottom or bottom to top)
	 * </p>
	 * @return Imagick Imagick instance on success.
	 * &imagick.imagickexception.throw;
	 */
	public function appendimages ($stack) {}

	/**
	 * Simulates a charcoal drawing
	 * @link http://www.php.net/manual/en/function.imagick-charcoalimage.php
	 * @param radius float <p>
	 * The radius of the Gaussian, in pixels, not counting the center pixel
	 * </p>
	 * @param sigma float <p>
	 * The standard deviation of the Gaussian, in pixels
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function charcoalimage ($radius, $sigma) {}

	/**
	 * Enhances the contrast of a color image
	 * @link http://www.php.net/manual/en/function.imagick-normalizeimage.php
	 * @param channel int[optional] <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function normalizeimage ($channel = null) {}

	/**
	 * Simulates an oil painting
	 * @link http://www.php.net/manual/en/function.imagick-oilpaintimage.php
	 * @param radius float <p>
	 * The radius of the circular neighborhood.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function oilpaintimage ($radius) {}

	/**
	 * Reduces the image to a limited number of color level
	 * @link http://www.php.net/manual/en/function.imagick-posterizeimage.php
	 * @param levels int <p>
	 * </p>
	 * @param dither bool <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function posterizeimage ($levels, $dither) {}

	/**
	 * Radial blurs an image
	 * @link http://www.php.net/manual/en/function.imagick-radialblurimage.php
	 * @param angle float <p>
	 * </p>
	 * @param channel int[optional] <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function radialblurimage ($angle, $channel = null) {}

	/**
	 * Creates a simulated 3d button-like effect
	 * @link http://www.php.net/manual/en/function.imagick-raiseimage.php
	 * @param width int <p>
	 * </p>
	 * @param height int <p>
	 * </p>
	 * @param x int <p>
	 * </p>
	 * @param y int <p>
	 * </p>
	 * @param raise bool <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function raiseimage ($width, $height, $x, $y, $raise) {}

	/**
	 * Resample image to desired resolution
	 * @link http://www.php.net/manual/en/function.imagick-resampleimage.php
	 * @param x_resolution float <p>
	 * </p>
	 * @param y_resolution float <p>
	 * </p>
	 * @param filter int <p>
	 * </p>
	 * @param blur float <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function resampleimage ($x_resolution, $y_resolution, $filter, $blur) {}

	/**
	 * Scales an image
	 * @link http://www.php.net/manual/en/function.imagick-resizeimage.php
	 * @param columns int <p>
	 * Width of the image
	 * </p>
	 * @param rows int <p>
	 * Height of the image
	 * </p>
	 * @param filter int <p>
	 * Refer to the list of filter constants.
	 * </p>
	 * @param blur float <p>
	 * The blur factor where &gt; 1 is blurry, &lt; 1 is sharp.
	 * </p>
	 * @param bestfit bool[optional] <p>
	 * Optional fit parameter.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function resizeimage ($columns, $rows, $filter, $blur, $bestfit = null) {}

	/**
	 * Offsets an image
	 * @link http://www.php.net/manual/en/function.imagick-rollimage.php
	 * @param x int <p>
	 * The X offset.
	 * </p>
	 * @param y int <p>
	 * The Y offset.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function rollimage ($x, $y) {}

	/**
	 * Rotates an image
	 * @link http://www.php.net/manual/en/function.imagick-rotateimage.php
	 * @param background mixed <p>
	 * The background color
	 * </p>
	 * @param degrees float <p>
	 * The number of degrees to rotate the image
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function rotateimage ($background, $degrees) {}

	/**
	 * Scales an image with pixel sampling
	 * @link http://www.php.net/manual/en/function.imagick-sampleimage.php
	 * @param columns int <p>
	 * </p>
	 * @param rows int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function sampleimage ($columns, $rows) {}

	/**
	 * Applies a solarizing effect to the image
	 * @link http://www.php.net/manual/en/function.imagick-solarizeimage.php
	 * @param threshold int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function solarizeimage ($threshold) {}

	/**
	 * Simulates an image shadow
	 * @link http://www.php.net/manual/en/function.imagick-shadowimage.php
	 * @param opacity float <p>
	 * </p>
	 * @param sigma float <p>
	 * </p>
	 * @param x int <p>
	 * </p>
	 * @param y int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function shadowimage ($opacity, $sigma, $x, $y) {}

	/**
	 * @param key
	 * @param value
	 */
	public function setimageattribute ($key, $value) {}

	/**
	 * Sets the image background color
	 * @link http://www.php.net/manual/en/function.imagick-setimagebackgroundcolor.php
	 * @param background mixed <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagebackgroundcolor ($background) {}

	/**
	 * Sets the image composite operator
	 * @link http://www.php.net/manual/en/function.imagick-setimagecompose.php
	 * @param compose int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagecompose ($compose) {}

	/**
	 * Sets the image compression
	 * @link http://www.php.net/manual/en/function.imagick-setimagecompression.php
	 * @param compression int <p>
	 * One of the COMPRESSION constants
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagecompression ($compression) {}

	/**
	 * Sets the image delay
	 * @link http://www.php.net/manual/en/function.imagick-setimagedelay.php
	 * @param delay int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagedelay ($delay) {}

	/**
	 * Sets the image depth
	 * @link http://www.php.net/manual/en/function.imagick-setimagedepth.php
	 * @param depth int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagedepth ($depth) {}

	/**
	 * Sets the image gamma
	 * @link http://www.php.net/manual/en/function.imagick-setimagegamma.php
	 * @param gamma float <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagegamma ($gamma) {}

	/**
	 * Sets the image iterations
	 * @link http://www.php.net/manual/en/function.imagick-setimageiterations.php
	 * @param iterations int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimageiterations ($iterations) {}

	/**
	 * Sets the image matte color
	 * @link http://www.php.net/manual/en/function.imagick-setimagemattecolor.php
	 * @param matte mixed <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagemattecolor ($matte) {}

	/**
	 * Sets the page geometry of the image
	 * @link http://www.php.net/manual/en/function.imagick-setimagepage.php
	 * @param width int <p>
	 * </p>
	 * @param height int <p>
	 * </p>
	 * @param x int <p>
	 * </p>
	 * @param y int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagepage ($width, $height, $x, $y) {}

	/**
	 * @param filename
	 */
	public function setimageprogressmonitor ($filename) {}

	/**
	 * Sets the image resolution
	 * @link http://www.php.net/manual/en/function.imagick-setimageresolution.php
	 * @param x_resolution float <p>
	 * </p>
	 * @param y_resolution float <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimageresolution ($x_resolution, $y_resolution) {}

	/**
	 * Sets the image scene
	 * @link http://www.php.net/manual/en/function.imagick-setimagescene.php
	 * @param scene int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagescene ($scene) {}

	/**
	 * Sets the image ticks-per-second
	 * @link http://www.php.net/manual/en/function.imagick-setimagetickspersecond.php
	 * @param ticks_per_second int 
	 * @return bool &imagick.return.success;
	 */
	public function setimagetickspersecond ($ticks_per_second) {}

	/**
	 * Sets the image type
	 * @link http://www.php.net/manual/en/function.imagick-setimagetype.php
	 * @param image_type int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagetype ($image_type) {}

	/**
	 * Sets the image units of resolution
	 * @link http://www.php.net/manual/en/function.imagick-setimageunits.php
	 * @param units int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimageunits ($units) {}

	/**
	 * Sharpens an image
	 * @link http://www.php.net/manual/en/function.imagick-sharpenimage.php
	 * @param radius float <p>
	 * </p>
	 * @param sigma float <p>
	 * </p>
	 * @param channel int[optional] <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function sharpenimage ($radius, $sigma, $channel = null) {}

	/**
	 * Shaves pixels from the image edges
	 * @link http://www.php.net/manual/en/function.imagick-shaveimage.php
	 * @param columns int <p>
	 * </p>
	 * @param rows int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function shaveimage ($columns, $rows) {}

	/**
	 * Creating a parallelogram
	 * @link http://www.php.net/manual/en/function.imagick-shearimage.php
	 * @param background mixed <p>
	 * The background color
	 * </p>
	 * @param x_shear float <p>
	 * The number of degrees to shear on the x axis
	 * </p>
	 * @param y_shear float <p>
	 * The number of degrees to shear on the y axis
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function shearimage ($background, $x_shear, $y_shear) {}

	/**
	 * Splices a solid color into the image
	 * @link http://www.php.net/manual/en/function.imagick-spliceimage.php
	 * @param width int <p>
	 * </p>
	 * @param height int <p>
	 * </p>
	 * @param x int <p>
	 * </p>
	 * @param y int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function spliceimage ($width, $height, $x, $y) {}

	/**
	 * Fetch basic attributes about the image
	 * @link http://www.php.net/manual/en/function.imagick-pingimage.php
	 * @param filename string <p>
	 * The filename to read the information from.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function pingimage ($filename) {}

	/**
	 * Reads image from open filehandle
	 * @link http://www.php.net/manual/en/function.imagick-readimagefile.php
	 * @param filehandle resource <p>
	 * </p>
	 * @param fileName string[optional] <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function readimagefile ($filehandle, $fileName = null) {}

	/**
	 * Displays an image
	 * @link http://www.php.net/manual/en/function.imagick-displayimage.php
	 * @param servername string <p>
	 * The X server name
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function displayimage ($servername) {}

	/**
	 * Displays an image or image sequence
	 * @link http://www.php.net/manual/en/function.imagick-displayimages.php
	 * @param servername string <p>
	 * The X server name
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function displayimages ($servername) {}

	/**
	 * Randomly displaces each pixel in a block
	 * @link http://www.php.net/manual/en/function.imagick-spreadimage.php
	 * @param radius float <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function spreadimage ($radius) {}

	/**
	 * Swirls the pixels about the center of the image
	 * @link http://www.php.net/manual/en/function.imagick-swirlimage.php
	 * @param degrees float <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function swirlimage ($degrees) {}

	/**
	 * Strips an image of all profiles and comments
	 * @link http://www.php.net/manual/en/function.imagick-stripimage.php
	 * @return bool &imagick.return.success;
	 */
	public function stripimage () {}

	/**
	 * Returns formats supported by Imagick
	 * @link http://www.php.net/manual/en/function.imagick-queryformats.php
	 * @param pattern string[optional] <p>
	 * </p>
	 * @return array an array containing the formats supported by Imagick.
	 * &imagick.imagickexception.throw;
	 */
	public function queryformats ($pattern = null) {}

	/**
	 * Returns the configured fonts
	 * @link http://www.php.net/manual/en/function.imagick-queryfonts.php
	 * @param pattern string[optional] <p>
	 * The query pattern
	 * </p>
	 * @return array an array containing the configured fonts.
	 * &imagick.imagickexception.throw;
	 */
	public function queryfonts ($pattern = null) {}

	/**
	 * Returns an array representing the font metrics
	 * @link http://www.php.net/manual/en/function.imagick-queryfontmetrics.php
	 * @param properties ImagickDraw <p>
	 * ImagickDraw object containing font properties
	 * </p>
	 * @param text string <p>
	 * The text
	 * </p>
	 * @param multiline bool[optional] <p>
	 * Multiline parameter. If left empty it is autodetected
	 * </p>
	 * @return array a multi-dimensional array representing the font metrics.
	 * &imagick.imagickexception.throw;
	 */
	public function queryfontmetrics (ImagickDraw $properties, $text, $multiline = null) {}

	/**
	 * Hides a digital watermark within the image
	 * @link http://www.php.net/manual/en/function.imagick-steganoimage.php
	 * @param watermark_wand Imagick <p>
	 * </p>
	 * @param offset int <p>
	 * </p>
	 * @return Imagick &imagick.return.success;
	 */
	public function steganoimage (Imagick $watermark_wand, $offset) {}

	/**
	 * Adds random noise to the image
	 * @link http://www.php.net/manual/en/function.imagick-addnoiseimage.php
	 * @param noise_type int <p>
	 * The type of the noise. Refer to this list of
	 * noise constants.
	 * </p>
	 * @param channel int[optional] <p>
	 * &imagick.parameter.channel;
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function addnoiseimage ($noise_type, $channel = null) {}

	/**
	 * Simulates motion blur
	 * @link http://www.php.net/manual/en/function.imagick-motionblurimage.php
	 * @param radius float <p>
	 * The radius of the Gaussian, in pixels, not counting the center pixel.
	 * </p>
	 * @param sigma float <p>
	 * The standard deviation of the Gaussian, in pixels.
	 * </p>
	 * @param angle float <p>
	 * Apply the effect along this angle.
	 * </p>
	 * @param channel int[optional] <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * The channel argument affects only if Imagick is compiled against ImageMagick version
	 * 6.4.4 or greater.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function motionblurimage ($radius, $sigma, $angle, $channel = null) {}

	/**
	 * Forms a mosaic from images
	 * @link http://www.php.net/manual/en/function.imagick-mosaicimages.php
	 * @return Imagick &imagick.return.success;
	 */
	public function mosaicimages () {}

	/**
	 * Method morphs a set of images
	 * @link http://www.php.net/manual/en/function.imagick-morphimages.php
	 * @param number_frames int <p>
	 * The number of in-between images to generate.
	 * </p>
	 * @return Imagick This method returns a new Imagick object on success.
	 * &imagick.imagickexception.throw;
	 */
	public function morphimages ($number_frames) {}

	/**
	 * Scales an image proportionally to half its size
	 * @link http://www.php.net/manual/en/function.imagick-minifyimage.php
	 * @return bool &imagick.return.success;
	 */
	public function minifyimage () {}

	/**
	 * Transforms an image
	 * @link http://www.php.net/manual/en/function.imagick-affinetransformimage.php
	 * @param matrix ImagickDraw <p>
	 * The affine matrix
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function affinetransformimage (ImagickDraw $matrix) {}

	/**
	 * Average a set of images
	 * @link http://www.php.net/manual/en/function.imagick-averageimages.php
	 * @return Imagick a new Imagick object on success.
	 * &imagick.imagickexception.throw;
	 */
	public function averageimages () {}

	/**
	 * Surrounds the image with a border
	 * @link http://www.php.net/manual/en/function.imagick-borderimage.php
	 * @param bordercolor mixed <p>
	 * ImagickPixel object or a string containing the border color
	 * </p>
	 * @param width int <p>
	 * Border width
	 * </p>
	 * @param height int <p>
	 * Border height
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function borderimage ($bordercolor, $width, $height) {}

	/**
	 * Removes a region of an image and trims
	 * @link http://www.php.net/manual/en/function.imagick-chopimage.php
	 * @param width int <p>
	 * Width of the chopped area
	 * </p>
	 * @param height int <p>
	 * Height of the chopped area
	 * </p>
	 * @param x int <p>
	 * X origo of the chopped area
	 * </p>
	 * @param y int <p>
	 * Y origo of the chopped area
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function chopimage ($width, $height, $x, $y) {}

	/**
	 * Clips along the first path from the 8BIM profile
	 * @link http://www.php.net/manual/en/function.imagick-clipimage.php
	 * @return bool &imagick.return.success;
	 */
	public function clipimage () {}

	/**
	 * Clips along the named paths from the 8BIM profile
	 * @link http://www.php.net/manual/en/function.imagick-clippathimage.php
	 * @param pathname string <p>
	 * The name of the path
	 * </p>
	 * @param inside bool <p>
	 * If true later operations take effect inside clipping path.
	 * Otherwise later operations take effect outside clipping path.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function clippathimage ($pathname, $inside) {}

	/**
	 * @param pathname
	 * @param inside
	 */
	public function clipimagepath ($pathname, $inside) {}

	/**
	 * Composites a set of images
	 * @link http://www.php.net/manual/en/function.imagick-coalesceimages.php
	 * @return Imagick a new Imagick object on success.
	 * &imagick.imagickexception.throw;
	 */
	public function coalesceimages () {}

	/**
	 * Changes the color value of any pixel that matches target
	 * @link http://www.php.net/manual/en/function.imagick-colorfloodfillimage.php
	 * @param fill mixed <p>
	 * ImagickPixel object containing the fill color
	 * </p>
	 * @param fuzz float <p>
	 * The amount of fuzz. For example, set fuzz to 10 and the color red at
	 * intensities of 100 and 102 respectively are now interpreted as the
	 * same color for the purposes of the floodfill.
	 * </p>
	 * @param bordercolor mixed <p>
	 * ImagickPixel object containing the border color
	 * </p>
	 * @param x int <p>
	 * X start position of the floodfill
	 * </p>
	 * @param y int <p>
	 * Y start position of the floodfill
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function colorfloodfillimage ($fill, $fuzz, $bordercolor, $x, $y) {}

	/**
	 * Blends the fill color with the image
	 * @link http://www.php.net/manual/en/function.imagick-colorizeimage.php
	 * @param colorize mixed <p>
	 * ImagickPixel object or a string containing the colorize color
	 * </p>
	 * @param opacity mixed <p>
	 * ImagickPixel object or an float containing the opacity value. 
	 * 1.0 is fully opaque and 0.0 is fully transparent.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function colorizeimage ($colorize, $opacity) {}

	/**
	 * Returns the difference in one or more images
	 * @link http://www.php.net/manual/en/function.imagick-compareimagechannels.php
	 * @param image Imagick <p>
	 * Imagick object containing the image to compare.
	 * </p>
	 * @param channelType int <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @param metricType int <p>
	 * One of the metric type constants.
	 * </p>
	 * @return array Array consisting of new_wand and
	 * distortion.
	 */
	public function compareimagechannels (Imagick $image, $channelType, $metricType) {}

	/**
	 * Compares an image to a reconstructed image
	 * @link http://www.php.net/manual/en/function.imagick-compareimages.php
	 * @param compare Imagick <p>
	 * An image to compare to.
	 * </p>
	 * @param metric int <p>
	 * Provide a valid metric type constant. Refer to this
	 * list of metric constants.
	 * </p>
	 * @return array &imagick.return.success;
	 */
	public function compareimages (Imagick $compare, $metric) {}

	/**
	 * Change the contrast of the image
	 * @link http://www.php.net/manual/en/function.imagick-contrastimage.php
	 * @param sharpen bool <p>
	 * The sharpen value
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function contrastimage ($sharpen) {}

	/**
	 * Combines one or more images into a single image
	 * @link http://www.php.net/manual/en/function.imagick-combineimages.php
	 * @param channelType int <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @return Imagick &imagick.return.success;
	 */
	public function combineimages ($channelType) {}

	/**
	 * Applies a custom convolution kernel to the image
	 * @link http://www.php.net/manual/en/function.imagick-convolveimage.php
	 * @param kernel array <p>
	 * The convolution kernel
	 * </p>
	 * @param channel int[optional] <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function convolveimage (array $kernel, $channel = null) {}

	/**
	 * Displaces an image's colormap
	 * @link http://www.php.net/manual/en/function.imagick-cyclecolormapimage.php
	 * @param displace int <p>
	 * The amount to displace the colormap.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function cyclecolormapimage ($displace) {}

	/**
	 * Returns certain pixel differences between images
	 * @link http://www.php.net/manual/en/function.imagick-deconstructimages.php
	 * @return Imagick a new Imagick object on success.
	 * &imagick.imagickexception.throw;
	 */
	public function deconstructimages () {}

	/**
	 * Reduces the speckle noise in an image
	 * @link http://www.php.net/manual/en/function.imagick-despeckleimage.php
	 * @return bool &imagick.return.success;
	 */
	public function despeckleimage () {}

	/**
	 * Enhance edges within the image
	 * @link http://www.php.net/manual/en/function.imagick-edgeimage.php
	 * @param radius float <p>
	 * The radius of the operation.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function edgeimage ($radius) {}

	/**
	 * Returns a grayscale image with a three-dimensional effect
	 * @link http://www.php.net/manual/en/function.imagick-embossimage.php
	 * @param radius float <p>
	 * The radius of the effect
	 * </p>
	 * @param sigma float <p>
	 * The sigma of the effect
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function embossimage ($radius, $sigma) {}

	/**
	 * Improves the quality of a noisy image
	 * @link http://www.php.net/manual/en/function.imagick-enhanceimage.php
	 * @return bool &imagick.return.success;
	 */
	public function enhanceimage () {}

	/**
	 * Equalizes the image histogram
	 * @link http://www.php.net/manual/en/function.imagick-equalizeimage.php
	 * @return bool &imagick.return.success;
	 */
	public function equalizeimage () {}

	/**
	 * Applies an expression to an image
	 * @link http://www.php.net/manual/en/function.imagick-evaluateimage.php
	 * @param op int <p>
	 * The operator
	 * </p>
	 * @param constant float <p>
	 * The value of the operator
	 * </p>
	 * @param channel int[optional] <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function evaluateimage ($op, $constant, $channel = null) {}

	/**
	 * Merges a sequence of images
	 * @link http://www.php.net/manual/en/function.imagick-flattenimages.php
	 * @return Imagick &imagick.return.success;
	 */
	public function flattenimages () {}

	/**
	 * Creates a vertical mirror image
	 * @link http://www.php.net/manual/en/function.imagick-flipimage.php
	 * @return bool &imagick.return.success;
	 */
	public function flipimage () {}

	/**
	 * Creates a horizontal mirror image
	 * @link http://www.php.net/manual/en/function.imagick-flopimage.php
	 * @return bool &imagick.return.success;
	 */
	public function flopimage () {}

	/**
	 * Adds a simulated three-dimensional border
	 * @link http://www.php.net/manual/en/function.imagick-frameimage.php
	 * @param matte_color mixed <p>
	 * ImagickPixel object or a string representing the matte color
	 * </p>
	 * @param width int <p>
	 * The width of the border
	 * </p>
	 * @param height int <p>
	 * The height of the border
	 * </p>
	 * @param inner_bevel int <p>
	 * The inner bevel width
	 * </p>
	 * @param outer_bevel int <p>
	 * The outer bevel width
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function frameimage ($matte_color, $width, $height, $inner_bevel, $outer_bevel) {}

	/**
	 * Evaluate expression for each pixel in the image
	 * @link http://www.php.net/manual/en/function.imagick-fximage.php
	 * @param expression string <p>
	 * The expression.
	 * </p>
	 * @param channel int[optional] <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @return Imagick &imagick.return.success;
	 */
	public function fximage ($expression, $channel = null) {}

	/**
	 * Gamma-corrects an image
	 * @link http://www.php.net/manual/en/function.imagick-gammaimage.php
	 * @param gamma float <p>
	 * The amount of gamma-correction.
	 * </p>
	 * @param channel int[optional] <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function gammaimage ($gamma, $channel = null) {}

	/**
	 * Blurs an image
	 * @link http://www.php.net/manual/en/function.imagick-gaussianblurimage.php
	 * @param radius float <p>
	 * The radius of the Gaussian, in pixels, not counting the center pixel.
	 * </p>
	 * @param sigma float <p>
	 * The standard deviation of the Gaussian, in pixels.
	 * </p>
	 * @param channel int[optional] <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function gaussianblurimage ($radius, $sigma, $channel = null) {}

	/**
	 * @param key
	 */
	public function getimageattribute ($key) {}

	/**
	 * Returns the image background color
	 * @link http://www.php.net/manual/en/function.imagick-getimagebackgroundcolor.php
	 * @return ImagickPixel an ImagickPixel set to the background color of the image.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagebackgroundcolor () {}

	/**
	 * Returns the chromaticy blue primary point
	 * @link http://www.php.net/manual/en/function.imagick-getimageblueprimary.php
	 * @param x float <p>
	 * The chromaticity blue primary x-point.
	 * </p>
	 * @param y float <p>
	 * The chromaticity blue primary y-point.
	 * </p>
	 * @return array Array consisting of "x" and "y" coordinates of point.
	 */
	public function getimageblueprimary ($x, $y) {}

	/**
	 * Returns the image border color
	 * @link http://www.php.net/manual/en/function.imagick-getimagebordercolor.php
	 * @return ImagickPixel &imagick.return.success;
	 */
	public function getimagebordercolor () {}

	/**
	 * Gets the depth for a particular image channel
	 * @link http://www.php.net/manual/en/function.imagick-getimagechanneldepth.php
	 * @param channel int <p>
	 * &imagick.parameter.channel;
	 * </p>
	 * @return int &imagick.return.success;
	 */
	public function getimagechanneldepth ($channel) {}

	/**
	 * Compares image channels of an image to a reconstructed image
	 * @link http://www.php.net/manual/en/function.imagick-getimagechanneldistortion.php
	 * @param reference Imagick <p>
	 * Imagick object to compare to.
	 * </p>
	 * @param channel int <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @param metric int <p>
	 * One of the metric type constants.
	 * </p>
	 * @return float &imagick.return.success;
	 */
	public function getimagechanneldistortion (Imagick $reference, $channel, $metric) {}

	/**
	 * Gets the extrema for one or more image channels
	 * @link http://www.php.net/manual/en/function.imagick-getimagechannelextrema.php
	 * @param channel int <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @return array &imagick.return.success;
	 */
	public function getimagechannelextrema ($channel) {}

	/**
	 * Gets the mean and standard deviation
	 * @link http://www.php.net/manual/en/function.imagick-getimagechannelmean.php
	 * @param channel int <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @return array &imagick.return.success;
	 */
	public function getimagechannelmean ($channel) {}

	/**
	 * Returns statistics for each channel in the image
	 * @link http://www.php.net/manual/en/function.imagick-getimagechannelstatistics.php
	 * @return array &imagick.return.success;
	 */
	public function getimagechannelstatistics () {}

	/**
	 * Returns the color of the specified colormap index
	 * @link http://www.php.net/manual/en/function.imagick-getimagecolormapcolor.php
	 * @param index int <p>
	 * The offset into the image colormap.
	 * </p>
	 * @return ImagickPixel &imagick.return.success;
	 */
	public function getimagecolormapcolor ($index) {}

	/**
	 * Gets the image colorspace
	 * @link http://www.php.net/manual/en/function.imagick-getimagecolorspace.php
	 * @return int &imagick.return.success;
	 */
	public function getimagecolorspace () {}

	/**
	 * Returns the composite operator associated with the image
	 * @link http://www.php.net/manual/en/function.imagick-getimagecompose.php
	 * @return int &imagick.return.success;
	 */
	public function getimagecompose () {}

	/**
	 * Gets the image delay
	 * @link http://www.php.net/manual/en/function.imagick-getimagedelay.php
	 * @return int the image delay.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagedelay () {}

	/**
	 * Gets the image depth
	 * @link http://www.php.net/manual/en/function.imagick-getimagedepth.php
	 * @return int The image depth.
	 */
	public function getimagedepth () {}

	/**
	 * Compares an image to a reconstructed image
	 * @link http://www.php.net/manual/en/function.imagick-getimagedistortion.php
	 * @param reference MagickWand <p>
	 * Imagick object to compare to.
	 * </p>
	 * @param metric int <p>
	 * One of the metric type constants.
	 * </p>
	 * @return float the distortion metric used on the image (or the best guess
	 * thereof).
	 * &imagick.imagickexception.throw;
	 */
	public function getimagedistortion ($reference, $metric) {}

	/**
	 * Gets the extrema for the image
	 * @link http://www.php.net/manual/en/function.imagick-getimageextrema.php
	 * @return array an associative array with the keys "min" and "max".
	 * &imagick.imagickexception.throw;
	 */
	public function getimageextrema () {}

	/**
	 * Gets the image disposal method
	 * @link http://www.php.net/manual/en/function.imagick-getimagedispose.php
	 * @return int the dispose method on success.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagedispose () {}

	/**
	 * Gets the image gamma
	 * @link http://www.php.net/manual/en/function.imagick-getimagegamma.php
	 * @return float the image gamma on success.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagegamma () {}

	/**
	 * Returns the chromaticy green primary point
	 * @link http://www.php.net/manual/en/function.imagick-getimagegreenprimary.php
	 * @return array an array with the keys "x" and "y" on success, throws an
	 * ImagickException on failure.
	 */
	public function getimagegreenprimary () {}

	/**
	 * Returns the image height
	 * @link http://www.php.net/manual/en/function.imagick-getimageheight.php
	 * @return int the image height in pixels.
	 * &imagick.imagickexception.throw;
	 */
	public function getimageheight () {}

	/**
	 * Gets the image histogram
	 * @link http://www.php.net/manual/en/function.imagick-getimagehistogram.php
	 * @return array the image histogram as an array of ImagickPixel objects.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagehistogram () {}

	/**
	 * Gets the image interlace scheme
	 * @link http://www.php.net/manual/en/function.imagick-getimageinterlacescheme.php
	 * @return int the interlace scheme as an integer on success.
	 * &imagick.imagickexception.throw;
	 */
	public function getimageinterlacescheme () {}

	/**
	 * Gets the image iterations
	 * @link http://www.php.net/manual/en/function.imagick-getimageiterations.php
	 * @return int the image iterations as an integer.
	 * &imagick.imagickexception.throw;
	 */
	public function getimageiterations () {}

	/**
	 * Returns the image matte color
	 * @link http://www.php.net/manual/en/function.imagick-getimagemattecolor.php
	 * @return ImagickPixel ImagickPixel object on success.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagemattecolor () {}

	/**
	 * Returns the page geometry
	 * @link http://www.php.net/manual/en/function.imagick-getimagepage.php
	 * @return array the page geometry associated with the image in an array with the
	 * keys "width", "height", "x", and "y".
	 */
	public function getimagepage () {}

	/**
	 * Returns the color of the specified pixel
	 * @link http://www.php.net/manual/en/function.imagick-getimagepixelcolor.php
	 * @param x int <p>
	 * The x-coordinate of the pixel
	 * </p>
	 * @param y int <p>
	 * The y-coordinate of the pixel
	 * </p>
	 * @return ImagickPixel an ImagickPixel instance for the color at the coordinates given.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagepixelcolor ($x, $y) {}

	/**
	 * Returns the named image profile
	 * @link http://www.php.net/manual/en/function.imagick-getimageprofile.php
	 * @param name string <p>
	 * The name of the profile to return.
	 * </p>
	 * @return string a string containing the image profile.
	 * &imagick.imagickexception.throw;
	 */
	public function getimageprofile ($name) {}

	/**
	 * Returns the chromaticity red primary point
	 * @link http://www.php.net/manual/en/function.imagick-getimageredprimary.php
	 * @return array the chromaticity red primary point as an array with the keys "x"
	 * and "y".
	 * &imagick.imagickexception.throw;
	 */
	public function getimageredprimary () {}

	/**
	 * Gets the image rendering intent
	 * @link http://www.php.net/manual/en/function.imagick-getimagerenderingintent.php
	 * @return int the image rendering intent.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagerenderingintent () {}

	/**
	 * Gets the image X and Y resolution
	 * @link http://www.php.net/manual/en/function.imagick-getimageresolution.php
	 * @return array the resolution as an array.
	 * &imagick.imagickexception.throw;
	 */
	public function getimageresolution () {}

	/**
	 * Gets the image scene
	 * @link http://www.php.net/manual/en/function.imagick-getimagescene.php
	 * @return int the image scene.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagescene () {}

	/**
	 * Generates an SHA-256 message digest
	 * @link http://www.php.net/manual/en/function.imagick-getimagesignature.php
	 * @return string a string containing the SHA-256 hash of the file.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagesignature () {}

	/**
	 * Gets the image ticks-per-second
	 * @link http://www.php.net/manual/en/function.imagick-getimagetickspersecond.php
	 * @return int the image ticks-per-second.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagetickspersecond () {}

	/**
	 * Gets the potential image type
	 * @link http://www.php.net/manual/en/function.imagick-getimagetype.php
	 * @return int the potential image type.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagetype () {}

	/**
	 * Gets the image units of resolution
	 * @link http://www.php.net/manual/en/function.imagick-getimageunits.php
	 * @return int the image units of resolution.
	 * &imagick.imagickexception.throw;
	 */
	public function getimageunits () {}

	/**
	 * Returns the virtual pixel method
	 * @link http://www.php.net/manual/en/function.imagick-getimagevirtualpixelmethod.php
	 * @return int the virtual pixel method on success.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagevirtualpixelmethod () {}

	/**
	 * Returns the chromaticity white point
	 * @link http://www.php.net/manual/en/function.imagick-getimagewhitepoint.php
	 * @return array the chromaticity white point as an associative array with the keys
	 * "x" and "y".
	 * &imagick.imagickexception.throw;
	 */
	public function getimagewhitepoint () {}

	/**
	 * Returns the image width
	 * @link http://www.php.net/manual/en/function.imagick-getimagewidth.php
	 * @return int the image width.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagewidth () {}

	/**
	 * Returns the number of images in the object
	 * @link http://www.php.net/manual/en/function.imagick-getnumberimages.php
	 * @return int the number of images associated with Imagick object.
	 * &imagick.imagickexception.throw;
	 */
	public function getnumberimages () {}

	/**
	 * Gets the image total ink density
	 * @link http://www.php.net/manual/en/function.imagick-getimagetotalinkdensity.php
	 * @return float the image total ink density of the image.
	 * &imagick.imagickexception.throw;
	 */
	public function getimagetotalinkdensity () {}

	/**
	 * Extracts a region of the image
	 * @link http://www.php.net/manual/en/function.imagick-getimageregion.php
	 * @param width int <p>
	 * The width of the extracted region.
	 * </p>
	 * @param height int <p>
	 * The height of the extracted region.
	 * </p>
	 * @param x int <p>
	 * X-coordinate of the top-left corner of the extracted region.
	 * </p>
	 * @param y int <p>
	 * Y-coordinate of the top-left corner of the extracted region.
	 * </p>
	 * @return Imagick Extracts a region of the image and returns it as a new wand.
	 * &imagick.imagickexception.throw;
	 */
	public function getimageregion ($width, $height, $x, $y) {}

	/**
	 * Creates a new image as a copy
	 * @link http://www.php.net/manual/en/function.imagick-implodeimage.php
	 * @param radius float <p>
	 * The radius of the implode
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function implodeimage ($radius) {}

	/**
	 * Adjusts the levels of an image
	 * @link http://www.php.net/manual/en/function.imagick-levelimage.php
	 * @param blackPoint float <p>
	 * The image black point
	 * </p>
	 * @param gamma float <p>
	 * The gamma value
	 * </p>
	 * @param whitePoint float <p>
	 * The image white point
	 * </p>
	 * @param channel int[optional] <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function levelimage ($blackPoint, $gamma, $whitePoint, $channel = null) {}

	/**
	 * Scales an image proportionally 2x
	 * @link http://www.php.net/manual/en/function.imagick-magnifyimage.php
	 * @return bool &imagick.return.success;
	 */
	public function magnifyimage () {}

	/**
	 * Replaces the colors of an image with the closest color from a reference image.
	 * @link http://www.php.net/manual/en/function.imagick-mapimage.php
	 * @param map Imagick <p>
	 * </p>
	 * @param dither bool <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function mapimage (Imagick $map, $dither) {}

	/**
	 * Changes the transparency value of a color
	 * @link http://www.php.net/manual/en/function.imagick-mattefloodfillimage.php
	 * @param alpha float <p>
	 * The level of transparency: 1.0 is fully opaque and 0.0 is fully
	 * transparent.
	 * </p>
	 * @param fuzz float <p>
	 * The fuzz member of image defines how much tolerance is acceptable to
	 * consider two colors as the same.
	 * </p>
	 * @param bordercolor mixed <p>
	 * An ImagickPixel object or string representing the border color.
	 * </p>
	 * @param x int <p>
	 * The starting x coordinate of the operation.
	 * </p>
	 * @param y int <p>
	 * The starting y coordinate of the operation.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function mattefloodfillimage ($alpha, $fuzz, $bordercolor, $x, $y) {}

	/**
	 * Applies a digital filter
	 * @link http://www.php.net/manual/en/function.imagick-medianfilterimage.php
	 * @param radius float <p>
	 * The radius of the pixel neighborhood.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function medianfilterimage ($radius) {}

	/**
	 * Negates the colors in the reference image
	 * @link http://www.php.net/manual/en/function.imagick-negateimage.php
	 * @param gray bool <p>
	 * Whether to only negate grayscale pixels within the image.
	 * </p>
	 * @param channel int[optional] <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function negateimage ($gray, $channel = null) {}

	/**
	 * Change any pixel that matches color
	 * @link http://www.php.net/manual/en/function.imagick-paintopaqueimage.php
	 * @param target mixed <p>
	 * Change this target color to the fill color within the image. An
	 * ImagickPixel object or a string representing the target color.
	 * </p>
	 * @param fill mixed <p>
	 * An ImagickPixel object or a string representing the fill color.
	 * </p>
	 * @param fuzz float <p>
	 * The fuzz member of image defines how much tolerance is acceptable to
	 * consider two colors as the same.
	 * </p>
	 * @param channel int[optional] <p>
	 * Provide any channel constant that is valid for your channel mode. To
	 * apply to more than one channel, combine channeltype constants using
	 * bitwise operators. Refer to this
	 * list of channel constants.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function paintopaqueimage ($target, $fill, $fuzz, $channel = null) {}

	/**
	 * Changes any pixel that matches color with the color defined by fill
	 * @link http://www.php.net/manual/en/function.imagick-painttransparentimage.php
	 * @param target mixed <p>
	 * Change this target color to specified opacity value within the image.
	 * </p>
	 * @param alpha float <p>
	 * The level of transparency: 1.0 is fully opaque and 0.0 is fully 
	 * transparent.
	 * </p>
	 * @param fuzz float <p>
	 * The fuzz member of image defines how much tolerance is acceptable to
	 * consider two colors as the same.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function painttransparentimage ($target, $alpha, $fuzz) {}

	/**
	 * Quickly pin-point appropriate parameters for image processing
	 * @link http://www.php.net/manual/en/function.imagick-previewimages.php
	 * @param preview int <p>
	 * Preview type. See Preview type constants
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function previewimages ($preview) {}

	/**
	 * Adds or removes a profile from an image
	 * @link http://www.php.net/manual/en/function.imagick-profileimage.php
	 * @param name string <p>
	 * </p>
	 * @param profile string <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function profileimage ($name, $profile) {}

	/**
	 * Analyzes the colors within a reference image
	 * @link http://www.php.net/manual/en/function.imagick-quantizeimage.php
	 * @param numberColors int <p>
	 * </p>
	 * @param colorspace int <p>
	 * </p>
	 * @param treedepth int <p>
	 * </p>
	 * @param dither bool <p>
	 * </p>
	 * @param measureError bool <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function quantizeimage ($numberColors, $colorspace, $treedepth, $dither, $measureError) {}

	/**
	 * Analyzes the colors within a sequence of images
	 * @link http://www.php.net/manual/en/function.imagick-quantizeimages.php
	 * @param numberColors int <p>
	 * </p>
	 * @param colorspace int <p>
	 * </p>
	 * @param treedepth int <p>
	 * </p>
	 * @param dither bool <p>
	 * </p>
	 * @param measureError bool <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function quantizeimages ($numberColors, $colorspace, $treedepth, $dither, $measureError) {}

	/**
	 * Smooths the contours of an image
	 * @link http://www.php.net/manual/en/function.imagick-reducenoiseimage.php
	 * @param radius float <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function reducenoiseimage ($radius) {}

	/**
	 * Removes the named image profile and returns it
	 * @link http://www.php.net/manual/en/function.imagick-removeimageprofile.php
	 * @param name string <p>
	 * </p>
	 * @return string a string containing the profile of the image.
	 * &imagick.imagickexception.throw;
	 */
	public function removeimageprofile ($name) {}

	/**
	 * Separates a channel from the image
	 * @link http://www.php.net/manual/en/function.imagick-separateimagechannel.php
	 * @param channel int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function separateimagechannel ($channel) {}

	/**
	 * Sepia tones an image
	 * @link http://www.php.net/manual/en/function.imagick-sepiatoneimage.php
	 * @param threshold float <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function sepiatoneimage ($threshold) {}

	/**
	 * Sets the image bias for any method that convolves an image
	 * @link http://www.php.net/manual/en/function.imagick-setimagebias.php
	 * @param bias float <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagebias ($bias) {}

	/**
	 * Sets the image chromaticity blue primary point
	 * @link http://www.php.net/manual/en/function.imagick-setimageblueprimary.php
	 * @param x float <p>
	 * </p>
	 * @param y float <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimageblueprimary ($x, $y) {}

	/**
	 * Sets the image border color
	 * @link http://www.php.net/manual/en/function.imagick-setimagebordercolor.php
	 * @param border mixed <p>
	 * The border color
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagebordercolor ($border) {}

	/**
	 * Sets the depth of a particular image channel
	 * @link http://www.php.net/manual/en/function.imagick-setimagechanneldepth.php
	 * @param channel int <p>
	 * </p>
	 * @param depth int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagechanneldepth ($channel, $depth) {}

	/**
	 * Sets the color of the specified colormap index
	 * @link http://www.php.net/manual/en/function.imagick-setimagecolormapcolor.php
	 * @param index int <p>
	 * </p>
	 * @param color ImagickPixel <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagecolormapcolor ($index, ImagickPixel $color) {}

	/**
	 * Sets the image colorspace
	 * @link http://www.php.net/manual/en/function.imagick-setimagecolorspace.php
	 * @param colorspace int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagecolorspace ($colorspace) {}

	/**
	 * Sets the image disposal method
	 * @link http://www.php.net/manual/en/function.imagick-setimagedispose.php
	 * @param dispose int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagedispose ($dispose) {}

	/**
	 * Sets the image size
	 * @link http://www.php.net/manual/en/function.imagick-setimageextent.php
	 * @param columns int <p>
	 * </p>
	 * @param rows int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimageextent ($columns, $rows) {}

	/**
	 * Sets the image chromaticity green primary point
	 * @link http://www.php.net/manual/en/function.imagick-setimagegreenprimary.php
	 * @param x float <p>
	 * </p>
	 * @param y float <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagegreenprimary ($x, $y) {}

	/**
	 * Sets the image compression
	 * @link http://www.php.net/manual/en/function.imagick-setimageinterlacescheme.php
	 * @param interlace_scheme int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimageinterlacescheme ($interlace_scheme) {}

	/**
	 * Adds a named profile to the Imagick object
	 * @link http://www.php.net/manual/en/function.imagick-setimageprofile.php
	 * @param name string <p>
	 * </p>
	 * @param profile string <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimageprofile ($name, $profile) {}

	/**
	 * Sets the image chromaticity red primary point
	 * @link http://www.php.net/manual/en/function.imagick-setimageredprimary.php
	 * @param x float <p>
	 * </p>
	 * @param y float <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimageredprimary ($x, $y) {}

	/**
	 * Sets the image rendering intent
	 * @link http://www.php.net/manual/en/function.imagick-setimagerenderingintent.php
	 * @param rendering_intent int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagerenderingintent ($rendering_intent) {}

	/**
	 * Sets the image virtual pixel method
	 * @link http://www.php.net/manual/en/function.imagick-setimagevirtualpixelmethod.php
	 * @param method int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagevirtualpixelmethod ($method) {}

	/**
	 * Sets the image chromaticity white point
	 * @link http://www.php.net/manual/en/function.imagick-setimagewhitepoint.php
	 * @param x float <p>
	 * </p>
	 * @param y float <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimagewhitepoint ($x, $y) {}

	/**
	 * Adjusts the contrast of an image
	 * @link http://www.php.net/manual/en/function.imagick-sigmoidalcontrastimage.php
	 * @param sharpen bool <p>
	 * </p>
	 * @param alpha float <p>
	 * </p>
	 * @param beta float <p>
	 * </p>
	 * @param channel int[optional] <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function sigmoidalcontrastimage ($sharpen, $alpha, $beta, $channel = null) {}

	/**
	 * Composites two images
	 * @link http://www.php.net/manual/en/function.imagick-stereoimage.php
	 * @param offset_wand Imagick <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function stereoimage (Imagick $offset_wand) {}

	/**
	 * Repeatedly tiles the texture image
	 * @link http://www.php.net/manual/en/function.imagick-textureimage.php
	 * @param texture_wand Imagick <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function textureimage (Imagick $texture_wand) {}

	/**
	 * Applies a color vector to each pixel in the image
	 * @link http://www.php.net/manual/en/function.imagick-tintimage.php
	 * @param tint mixed <p>
	 * </p>
	 * @param opacity mixed <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function tintimage ($tint, $opacity) {}

	/**
	 * Sharpens an image
	 * @link http://www.php.net/manual/en/function.imagick-unsharpmaskimage.php
	 * @param radius float <p>
	 * </p>
	 * @param sigma float <p>
	 * </p>
	 * @param amount float <p>
	 * </p>
	 * @param threshold float <p>
	 * </p>
	 * @param channel int[optional] <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function unsharpmaskimage ($radius, $sigma, $amount, $threshold, $channel = null) {}

	/**
	 * Returns a new Imagick object
	 * @link http://www.php.net/manual/en/function.imagick-getimage.php
	 * @return Imagick a new Imagick object with the current image sequence.
	 * &imagick.imagickexception.throw;
	 */
	public function getimage () {}

	/**
	 * Adds new image to Imagick object image list
	 * @link http://www.php.net/manual/en/function.imagick-addimage.php
	 * @param source Imagick <p>
	 * The source Imagick object
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function addimage (Imagick $source) {}

	/**
	 * Replaces image in the object
	 * @link http://www.php.net/manual/en/function.imagick-setimage.php
	 * @param replace Imagick <p>
	 * The replace Imagick object
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setimage (Imagick $replace) {}

	/**
	 * Creates a new image
	 * @link http://www.php.net/manual/en/function.imagick-newimage.php
	 * @param cols int <p>
	 * Columns in the new image
	 * </p>
	 * @param rows int <p>
	 * Rows in the new image
	 * </p>
	 * @param background mixed <p>
	 * The background color used for this image
	 * </p>
	 * @param format string[optional] <p>
	 * Image format. This parameter was added in Imagick version 2.0.1.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function newimage ($cols, $rows, $background, $format = null) {}

	/**
	 * Creates a new image
	 * @link http://www.php.net/manual/en/function.imagick-newpseudoimage.php
	 * @param columns int <p>
	 * columns in the new image
	 * </p>
	 * @param rows int <p>
	 * rows in the new image
	 * </p>
	 * @param pseudoString string <p>
	 * string containing pseudo image definition.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function newpseudoimage ($columns, $rows, $pseudoString) {}

	/**
	 * Gets the object compression type
	 * @link http://www.php.net/manual/en/function.imagick-getcompression.php
	 * @return int the compression constant
	 */
	public function getcompression () {}

	/**
	 * Gets the object compression quality
	 * @link http://www.php.net/manual/en/function.imagick-getcompressionquality.php
	 * @return int integer describing the compression quality
	 */
	public function getcompressionquality () {}

	/**
	 * Returns the ImageMagick API copyright as a string
	 * @link http://www.php.net/manual/en/function.imagick-getcopyright.php
	 * @return string a string containing the copyright notice of Imagemagick and
	 * Magickwand C API.
	 */
	public function getcopyright () {}

	/**
	 * The filename associated with an image sequence
	 * @link http://www.php.net/manual/en/function.imagick-getfilename.php
	 * @return string a string on success.
	 * &imagick.imagickexception.throw;
	 */
	public function getfilename () {}

	/**
	 * Returns the format of the Imagick object
	 * @link http://www.php.net/manual/en/function.imagick-getformat.php
	 * @return string the format of the image.
	 * &imagick.imagickexception.throw;
	 */
	public function getformat () {}

	/**
	 * Returns the ImageMagick home URL
	 * @link http://www.php.net/manual/en/function.imagick-gethomeurl.php
	 * @return string a link to the imagemagick homepage.
	 */
	public function gethomeurl () {}

	/**
	 * Gets the object interlace scheme
	 * @link http://www.php.net/manual/en/function.imagick-getinterlacescheme.php
	 * @return int Gets the wand interlace
	 * scheme.
	 * &imagick.imagickexception.throw;
	 */
	public function getinterlacescheme () {}

	/**
	 * Returns a value associated with the specified key
	 * @link http://www.php.net/manual/en/function.imagick-getoption.php
	 * @param key string <p>
	 * The name of the option
	 * </p>
	 * @return string a value associated with a wand and the specified key.
	 * &imagick.imagickexception.throw;
	 */
	public function getoption ($key) {}

	/**
	 * Returns the ImageMagick package name
	 * @link http://www.php.net/manual/en/function.imagick-getpackagename.php
	 * @return string the ImageMagick package name as a string.
	 * &imagick.imagickexception.throw;
	 */
	public function getpackagename () {}

	/**
	 * Returns the page geometry
	 * @link http://www.php.net/manual/en/function.imagick-getpage.php
	 * @return array the page geometry associated with the Imagick object in
	 * an associative array with the keys "width", "height", "x", and "y",
	 * throwing ImagickException on error.
	 */
	public function getpage () {}

	/**
	 * Gets the quantum depth
	 * @link http://www.php.net/manual/en/function.imagick-getquantumdepth.php
	 * @return array the Imagick quantum depth as a string.
	 * &imagick.imagickexception.throw;
	 */
	public function getquantumdepth () {}

	/**
	 * Returns the Imagick quantum range
	 * @link http://www.php.net/manual/en/function.imagick-getquantumrange.php
	 * @return array the Imagick quantum range as a string.
	 * &imagick.imagickexception.throw;
	 */
	public function getquantumrange () {}

	/**
	 * Returns the ImageMagick release date
	 * @link http://www.php.net/manual/en/function.imagick-getreleasedate.php
	 * @return string the ImageMagick release date as a string.
	 * &imagick.imagickexception.throw;
	 */
	public function getreleasedate () {}

	/**
	 * Returns the specified resource's memory usage
	 * @link http://www.php.net/manual/en/function.imagick-getresource.php
	 * @param type int <p>
	 * Refer to the list of resourcetype constants.
	 * </p>
	 * @return int the specified resource's memory usage in megabytes.
	 * &imagick.imagickexception.throw;
	 */
	public function getresource ($type) {}

	/**
	 * Returns the specified resource limit
	 * @link http://www.php.net/manual/en/function.imagick-getresourcelimit.php
	 * @param type int <p>
	 * Refer to the list of resourcetype constants.
	 * </p>
	 * @return int the specified resource limit in megabytes.
	 * &imagick.imagickexception.throw;
	 */
	public function getresourcelimit ($type) {}

	/**
	 * Gets the horizontal and vertical sampling factor
	 * @link http://www.php.net/manual/en/function.imagick-getsamplingfactors.php
	 * @return array an associative array with the horizontal and vertical sampling
	 * factors of the image.
	 * &imagick.imagickexception.throw;
	 */
	public function getsamplingfactors () {}

	/**
	 * Returns the size associated with the Imagick object
	 * @link http://www.php.net/manual/en/function.imagick-getsize.php
	 * @return array the size associated with the Imagick object as an array with the
	 * keys "columns" and "rows".
	 */
	public function getsize () {}

	/**
	 * Returns the ImageMagick API version
	 * @link http://www.php.net/manual/en/function.imagick-getversion.php
	 * @return array the ImageMagick API version as a string and as a number.
	 * &imagick.imagickexception.throw;
	 */
	public function getversion () {}

	/**
	 * Sets the object's default background color
	 * @link http://www.php.net/manual/en/function.imagick-setbackgroundcolor.php
	 * @param background mixed <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setbackgroundcolor ($background) {}

	/**
	 * Sets the object's default compression type
	 * @link http://www.php.net/manual/en/function.imagick-setcompression.php
	 * @param compression int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setcompression ($compression) {}

	/**
	 * Sets the object's default compression quality
	 * @link http://www.php.net/manual/en/function.imagick-setcompressionquality.php
	 * @param quality int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setcompressionquality ($quality) {}

	/**
	 * Sets the filename before you read or write the image
	 * @link http://www.php.net/manual/en/function.imagick-setfilename.php
	 * @param filename string <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setfilename ($filename) {}

	/**
	 * Sets the format of the Imagick object
	 * @link http://www.php.net/manual/en/function.imagick-setformat.php
	 * @param format string <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setformat ($format) {}

	/**
	 * Sets the image compression
	 * @link http://www.php.net/manual/en/function.imagick-setinterlacescheme.php
	 * @param interlace_scheme int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setinterlacescheme ($interlace_scheme) {}

	/**
	 * Set an option
	 * @link http://www.php.net/manual/en/function.imagick-setoption.php
	 * @param key string <p>
	 * </p>
	 * @param value string <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setoption ($key, $value) {}

	/**
	 * Sets the page geometry of the Imagick object
	 * @link http://www.php.net/manual/en/function.imagick-setpage.php
	 * @param width int <p>
	 * </p>
	 * @param height int <p>
	 * </p>
	 * @param x int <p>
	 * </p>
	 * @param y int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setpage ($width, $height, $x, $y) {}

	/**
	 * Sets the limit for a particular resource in megabytes
	 * @link http://www.php.net/manual/en/function.imagick-setresourcelimit.php
	 * @param type int <p>
	 * </p>
	 * @param limit int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setresourcelimit ($type, $limit) {}

	/**
	 * Sets the image resolution
	 * @link http://www.php.net/manual/en/function.imagick-setresolution.php
	 * @param x_resolution float <p>
	 * </p>
	 * @param y_resolution float <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setresolution ($x_resolution, $y_resolution) {}

	/**
	 * Sets the image sampling factors
	 * @link http://www.php.net/manual/en/function.imagick-setsamplingfactors.php
	 * @param factors array <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setsamplingfactors (array $factors) {}

	/**
	 * Sets the size of the Imagick object
	 * @link http://www.php.net/manual/en/function.imagick-setsize.php
	 * @param columns int <p>
	 * </p>
	 * @param rows int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setsize ($columns, $rows) {}

	/**
	 * Sets the image type attribute
	 * @link http://www.php.net/manual/en/function.imagick-settype.php
	 * @param image_type int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function settype ($image_type) {}

	public function key () {}

	public function next () {}

	public function rewind () {}

	/**
	 * Checks if the current item is valid
	 * @link http://www.php.net/manual/en/function.imagick-valid.php
	 * @return bool &imagick.return.success;
	 */
	public function valid () {}

	/**
	 * Returns a reference to the current Imagick object
	 * @link http://www.php.net/manual/en/function.imagick-current.php
	 * @return Imagick self on success.
	 * &imagick.imagickexception.throw;
	 */
	public function current () {}

}

/** @jms-builtin */
class ImagickDraw  {

	public function resetvectorgraphics () {}

	/**
	 * The ImagickDraw constructor
	 * @link http://www.php.net/manual/en/function.imagickdraw-construct.php
	 * @return ImagickDraw 
	 */
	public function __construct () {}

	/**
	 * Sets the fill color to be used for drawing filled objects
	 * @link http://www.php.net/manual/en/function.imagickdraw-setfillcolor.php
	 * @param fill_pixel ImagickPixel <p>
	 * ImagickPixel to use to set the color
	 * </p>
	 * @return bool 
	 */
	public function setfillcolor (ImagickPixel $fill_pixel) {}

	/**
	 * Sets the opacity to use when drawing using the fill color or fill texture
	 * @link http://www.php.net/manual/en/function.imagickdraw-setfillalpha.php
	 * @param opacity float <p>
	 * fill alpha
	 * </p>
	 * @return bool 
	 */
	public function setfillalpha ($opacity) {}

	/**
	 * Sets the color used for stroking object outlines
	 * @link http://www.php.net/manual/en/function.imagickdraw-setstrokecolor.php
	 * @param stroke_pixel ImagickPixel <p>
	 * the stroke color
	 * </p>
	 * @return bool 
	 */
	public function setstrokecolor (ImagickPixel $stroke_pixel) {}

	/**
	 * Specifies the opacity of stroked object outlines
	 * @link http://www.php.net/manual/en/function.imagickdraw-setstrokealpha.php
	 * @param opacity float <p>
	 * opacity
	 * </p>
	 * @return bool 
	 */
	public function setstrokealpha ($opacity) {}

	/**
	 * Sets the width of the stroke used to draw object outlines
	 * @link http://www.php.net/manual/en/function.imagickdraw-setstrokewidth.php
	 * @param stroke_width float <p>
	 * stroke width
	 * </p>
	 * @return bool 
	 */
	public function setstrokewidth ($stroke_width) {}

	/**
	 * Clears the ImagickDraw
	 * @link http://www.php.net/manual/en/function.imagickdraw-clear.php
	 * @return bool an ImagickDraw object.
	 */
	public function clear () {}

	/**
	 * Draws a circle
	 * @link http://www.php.net/manual/en/function.imagickdraw-circle.php
	 * @param ox float <p>
	 * origin x coordinate
	 * </p>
	 * @param oy float <p>
	 * origin y coordinate
	 * </p>
	 * @param px float <p>
	 * perimeter x coordinate
	 * </p>
	 * @param py float <p>
	 * perimeter y coordinate
	 * </p>
	 * @return bool 
	 */
	public function circle ($ox, $oy, $px, $py) {}

	/**
	 * Draws text on the image
	 * @link http://www.php.net/manual/en/function.imagickdraw-annotation.php
	 * @param x float <p>
	 * The x coordinate where text is drawn
	 * </p>
	 * @param y float <p>
	 * The y coordinate where text is drawn
	 * </p>
	 * @param text string <p>
	 * The text to draw on the image
	 * </p>
	 * @return bool 
	 */
	public function annotation ($x, $y, $text) {}

	/**
	 * Controls whether text is antialiased
	 * @link http://www.php.net/manual/en/function.imagickdraw-settextantialias.php
	 * @param antiAlias bool <p>
	 * </p>
	 * @return bool 
	 */
	public function settextantialias ($antiAlias) {}

	/**
	 * Specifies specifies the text code set
	 * @link http://www.php.net/manual/en/function.imagickdraw-settextencoding.php
	 * @param encoding string <p>
	 * the encoding name
	 * </p>
	 * @return bool 
	 */
	public function settextencoding ($encoding) {}

	/**
	 * Sets the fully-specified font to use when annotating with text
	 * @link http://www.php.net/manual/en/function.imagickdraw-setfont.php
	 * @param font_name string <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setfont ($font_name) {}

	/**
	 * Sets the font family to use when annotating with text
	 * @link http://www.php.net/manual/en/function.imagickdraw-setfontfamily.php
	 * @param font_family string <p>
	 * the font family
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setfontfamily ($font_family) {}

	/**
	 * Sets the font pointsize to use when annotating with text
	 * @link http://www.php.net/manual/en/function.imagickdraw-setfontsize.php
	 * @param pointsize float <p>
	 * the point size
	 * </p>
	 * @return bool 
	 */
	public function setfontsize ($pointsize) {}

	/**
	 * Sets the font style to use when annotating with text
	 * @link http://www.php.net/manual/en/function.imagickdraw-setfontstyle.php
	 * @param style int <p>
	 * STYLETYPE_ constant
	 * </p>
	 * @return bool 
	 */
	public function setfontstyle ($style) {}

	/**
	 * Sets the font weight
	 * @link http://www.php.net/manual/en/function.imagickdraw-setfontweight.php
	 * @param font_weight int <p>
	 * </p>
	 * @return bool 
	 */
	public function setfontweight ($font_weight) {}

	/**
	 * Returns the font
	 * @link http://www.php.net/manual/en/function.imagickdraw-getfont.php
	 * @return string a string on success and false if no font is set.
	 */
	public function getfont () {}

	/**
	 * Returns the font family
	 * @link http://www.php.net/manual/en/function.imagickdraw-getfontfamily.php
	 * @return string the font family currently selected or false if font family is not set.
	 */
	public function getfontfamily () {}

	/**
	 * Returns the font pointsize
	 * @link http://www.php.net/manual/en/function.imagickdraw-getfontsize.php
	 * @return float the font size associated with the current ImagickDraw object.
	 */
	public function getfontsize () {}

	/**
	 * Returns the font style
	 * @link http://www.php.net/manual/en/function.imagickdraw-getfontstyle.php
	 * @return int the font style constant (STYLE_) associated with the ImagickDraw object 
	 * or 0 if no style is set.
	 */
	public function getfontstyle () {}

	/**
	 * Returns the font weight
	 * @link http://www.php.net/manual/en/function.imagickdraw-getfontweight.php
	 * @return int an int on success and 0 if no weight is set.
	 */
	public function getfontweight () {}

	/**
	 * Frees all associated resources
	 * @link http://www.php.net/manual/en/function.imagickdraw-destroy.php
	 * @return bool 
	 */
	public function destroy () {}

	/**
	 * Draws a rectangle
	 * @link http://www.php.net/manual/en/function.imagickdraw-rectangle.php
	 * @param x1 float <p>
	 * x coordinate of the top left corner
	 * </p>
	 * @param y1 float <p>
	 * y coordinate of the top left corner
	 * </p>
	 * @param x2 float <p>
	 * x coordinate of the bottom right corner
	 * </p>
	 * @param y2 float <p>
	 * y coordinate of the bottom right corner
	 * </p>
	 * @return bool 
	 */
	public function rectangle ($x1, $y1, $x2, $y2) {}

	/**
	 * Draws a rounded rectangle
	 * @link http://www.php.net/manual/en/function.imagickdraw-roundrectangle.php
	 * @param x1 float <p>
	 * x coordinate of the top left corner
	 * </p>
	 * @param y1 float <p>
	 * y coordinate of the top left corner
	 * </p>
	 * @param x2 float <p>
	 * x coordinate of the bottom right 
	 * </p>
	 * @param y2 float <p>
	 * y coordinate of the bottom right 
	 * </p>
	 * @param rx float <p>
	 * x rounding
	 * </p>
	 * @param ry float <p>
	 * y rounding
	 * </p>
	 * @return bool 
	 */
	public function roundrectangle ($x1, $y1, $x2, $y2, $rx, $ry) {}

	/**
	 * Draws an ellipse on the image
	 * @link http://www.php.net/manual/en/function.imagickdraw-ellipse.php
	 * @param ox float <p>
	 * </p>
	 * @param oy float <p>
	 * </p>
	 * @param rx float <p>
	 * </p>
	 * @param ry float <p>
	 * </p>
	 * @param start float <p>
	 * </p>
	 * @param end float <p>
	 * </p>
	 * @return bool 
	 */
	public function ellipse ($ox, $oy, $rx, $ry, $start, $end) {}

	/**
	 * Skews the current coordinate system in the horizontal direction
	 * @link http://www.php.net/manual/en/function.imagickdraw-skewx.php
	 * @param degrees float <p>
	 * degrees to skew
	 * </p>
	 * @return bool 
	 */
	public function skewx ($degrees) {}

	/**
	 * Skews the current coordinate system in the vertical direction
	 * @link http://www.php.net/manual/en/function.imagickdraw-skewy.php
	 * @param degrees float <p>
	 * degrees to skew
	 * </p>
	 * @return bool 
	 */
	public function skewy ($degrees) {}

	/**
	 * Applies a translation to the current coordinate system
	 * @link http://www.php.net/manual/en/function.imagickdraw-translate.php
	 * @param x float <p>
	 * horizontal translation
	 * </p>
	 * @param y float <p>
	 * vertical translation
	 * </p>
	 * @return bool 
	 */
	public function translate ($x, $y) {}

	/**
	 * Draws a line
	 * @link http://www.php.net/manual/en/function.imagickdraw-line.php
	 * @param sx float <p>
	 * starting x coordinate
	 * </p>
	 * @param sy float <p>
	 * starting y coordinate
	 * </p>
	 * @param ex float <p>
	 * ending x coordinate
	 * </p>
	 * @param ey float <p>
	 * ending y coordinate
	 * </p>
	 * @return bool 
	 */
	public function line ($sx, $sy, $ex, $ey) {}

	/**
	 * Draws an arc
	 * @link http://www.php.net/manual/en/function.imagickdraw-arc.php
	 * @param sx float <p>
	 * Starting x ordinate of bounding rectangle
	 * </p>
	 * @param sy float <p>
	 * starting y ordinate of bounding rectangle
	 * </p>
	 * @param ex float <p>
	 * ending x ordinate of bounding rectangle
	 * </p>
	 * @param ey float <p>
	 * ending y ordinate of bounding rectangle
	 * </p>
	 * @param sd float <p>
	 * starting degrees of rotation
	 * </p>
	 * @param ed float <p>
	 * ending degrees of rotation
	 * </p>
	 * @return bool 
	 */
	public function arc ($sx, $sy, $ex, $ey, $sd, $ed) {}

	/**
	 * Paints on the image's opacity channel
	 * @link http://www.php.net/manual/en/function.imagickdraw-matte.php
	 * @param x float <p>
	 * x coordinate of the matte
	 * </p>
	 * @param y float <p>
	 * y coordinate of the matte
	 * </p>
	 * @param paintMethod int <p>
	 * PAINT_ constant
	 * </p>
	 * @return bool Returns true on success or false on failure.
	 */
	public function matte ($x, $y, $paintMethod) {}

	/**
	 * Draws a polygon
	 * @link http://www.php.net/manual/en/function.imagickdraw-polygon.php
	 * @param coordinates array <p>
	 * multidimensional array like array( array( 'x' => 3, 'y' => 4 ), array( 'x' => 2, 'y' => 6 ) );
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function polygon (array $coordinates) {}

	/**
	 * Draws a point
	 * @link http://www.php.net/manual/en/function.imagickdraw-point.php
	 * @param x float <p>
	 * point's x coordinate
	 * </p>
	 * @param y float <p>
	 * point's y coordinate
	 * </p>
	 * @return bool 
	 */
	public function point ($x, $y) {}

	/**
	 * Returns the text decoration
	 * @link http://www.php.net/manual/en/function.imagickdraw-gettextdecoration.php
	 * @return int one of the DECORATION_ constants
	 * and 0 if no decoration is set.
	 */
	public function gettextdecoration () {}

	/**
	 * Returns the code set used for text annotations
	 * @link http://www.php.net/manual/en/function.imagickdraw-gettextencoding.php
	 * @return string a string specifying the code set
	 * or false if text encoding is not set.
	 */
	public function gettextencoding () {}

	public function getfontstretch () {}

	/**
	 * Sets the font stretch to use when annotating with text
	 * @link http://www.php.net/manual/en/function.imagickdraw-setfontstretch.php
	 * @param fontStretch int <p>
	 * STRETCH_ constant
	 * </p>
	 * @return bool 
	 */
	public function setfontstretch ($fontStretch) {}

	/**
	 * Controls whether stroked outlines are antialiased
	 * @link http://www.php.net/manual/en/function.imagickdraw-setstrokeantialias.php
	 * @param stroke_antialias bool <p>
	 * the antialias setting
	 * </p>
	 * @return bool 
	 */
	public function setstrokeantialias ($stroke_antialias) {}

	/**
	 * Specifies a text alignment
	 * @link http://www.php.net/manual/en/function.imagickdraw-settextalignment.php
	 * @param alignment int <p>
	 * ALIGN_ constant
	 * </p>
	 * @return bool 
	 */
	public function settextalignment ($alignment) {}

	/**
	 * Specifies a decoration
	 * @link http://www.php.net/manual/en/function.imagickdraw-settextdecoration.php
	 * @param decoration int <p>
	 * DECORATION_ constant
	 * </p>
	 * @return bool 
	 */
	public function settextdecoration ($decoration) {}

	/**
	 * Specifies the color of a background rectangle
	 * @link http://www.php.net/manual/en/function.imagickdraw-settextundercolor.php
	 * @param under_color ImagickPixel <p>
	 * the under color
	 * </p>
	 * @return bool 
	 */
	public function settextundercolor (ImagickPixel $under_color) {}

	/**
	 * Sets the overall canvas size
	 * @link http://www.php.net/manual/en/function.imagickdraw-setviewbox.php
	 * @param x1 int <p>
	 * left x coordinate
	 * </p>
	 * @param y1 int <p>
	 * left y coordinate
	 * </p>
	 * @param x2 int <p>
	 * right x coordinate
	 * </p>
	 * @param y2 int <p>
	 * right y coordinate
	 * </p>
	 * @return bool 
	 */
	public function setviewbox ($x1, $y1, $x2, $y2) {}

	/**
	 * Makes an exact copy of the specified ImagickDraw object
	 * @link http://www.php.net/manual/en/function.imagickdraw-clone.php
	 * @return ImagickDraw What the function returns, first on success, then on failure. See
	 * also the &amp;return.success; entity
	 */
	public function clone () {}

	/**
	 * Adjusts the current affine transformation matrix
	 * @link http://www.php.net/manual/en/function.imagickdraw-affine.php
	 * @param affine array <p>
	 * Affine matrix parameters
	 * </p>
	 * @return bool 
	 */
	public function affine (array $affine) {}

	/**
	 * Draws a bezier curve
	 * @link http://www.php.net/manual/en/function.imagickdraw-bezier.php
	 * @param coordinates array <p>
	 * Multidimensional array like array( array( 'x' => 1, 'y' => 2 ), 
	 * array( 'x' => 3, 'y' => 4 ) )
	 * </p>
	 * @return bool 
	 */
	public function bezier (array $coordinates) {}

	/**
	 * Composites an image onto the current image
	 * @link http://www.php.net/manual/en/function.imagickdraw-composite.php
	 * @param compose int <p>
	 * composition operator. One of COMPOSITE_ constants
	 * </p>
	 * @param x float <p>
	 * x coordinate of the top left corner
	 * </p>
	 * @param y float <p>
	 * y coordinate of the top left corner
	 * </p>
	 * @param width float <p>
	 * width of the composition image
	 * </p>
	 * @param height float <p>
	 * height of the composition image
	 * </p>
	 * @param compositeWand Imagick <p>
	 * the Imagick object where composition image is taken from
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function composite ($compose, $x, $y, $width, $height, Imagick $compositeWand) {}

	/**
	 * Draws color on image
	 * @link http://www.php.net/manual/en/function.imagickdraw-color.php
	 * @param x float <p>
	 * x coordinate of the paint
	 * </p>
	 * @param y float <p>
	 * y coordinate of the paint
	 * </p>
	 * @param paintMethod int <p>
	 * one of the PAINT_ constants
	 * </p>
	 * @return bool 
	 */
	public function color ($x, $y, $paintMethod) {}

	/**
	 * Adds a comment
	 * @link http://www.php.net/manual/en/function.imagickdraw-comment.php
	 * @param comment string <p>
	 * The comment string to add to vector output stream
	 * </p>
	 * @return bool 
	 */
	public function comment ($comment) {}

	/**
	 * Obtains the current clipping path ID
	 * @link http://www.php.net/manual/en/function.imagickdraw-getclippath.php
	 * @return string a string containing the clip path ID or false if no clip path exists.
	 */
	public function getclippath () {}

	/**
	 * Returns the current polygon fill rule
	 * @link http://www.php.net/manual/en/function.imagickdraw-getcliprule.php
	 * @return int one of the FILLRULE_ constants.
	 */
	public function getcliprule () {}

	/**
	 * Returns the interpretation of clip path units
	 * @link http://www.php.net/manual/en/function.imagickdraw-getclipunits.php
	 * @return int an int on success.
	 */
	public function getclipunits () {}

	/**
	 * Returns the fill color
	 * @link http://www.php.net/manual/en/function.imagickdraw-getfillcolor.php
	 * @return ImagickPixel an ImagickPixel object.
	 */
	public function getfillcolor () {}

	/**
	 * Returns the opacity used when drawing
	 * @link http://www.php.net/manual/en/function.imagickdraw-getfillopacity.php
	 * @return float The opacity.
	 */
	public function getfillopacity () {}

	/**
	 * Returns the fill rule
	 * @link http://www.php.net/manual/en/function.imagickdraw-getfillrule.php
	 * @return int a FILLRULE_ constant
	 */
	public function getfillrule () {}

	/**
	 * Returns the text placement gravity
	 * @link http://www.php.net/manual/en/function.imagickdraw-getgravity.php
	 * @return int a GRAVITY_ constant on success and 0 if no gravity is set.
	 */
	public function getgravity () {}

	/**
	 * Returns the current stroke antialias setting
	 * @link http://www.php.net/manual/en/function.imagickdraw-getstrokeantialias.php
	 * @return bool true if antialiasing is on and false if it is off.
	 */
	public function getstrokeantialias () {}

	/**
	 * Returns the color used for stroking object outlines
	 * @link http://www.php.net/manual/en/function.imagickdraw-getstrokecolor.php
	 * @param stroke_color ImagickPixel <p>
	 * </p>
	 * @return ImagickPixel an ImagickPixel object which describes the color.
	 */
	public function getstrokecolor (ImagickPixel $stroke_color) {}

	/**
	 * Returns an array representing the pattern of dashes and gaps used to stroke paths
	 * @link http://www.php.net/manual/en/function.imagickdraw-getstrokedasharray.php
	 * @return array an array on success and empty array if not set.
	 */
	public function getstrokedasharray () {}

	/**
	 * Returns the offset into the dash pattern to start the dash
	 * @link http://www.php.net/manual/en/function.imagickdraw-getstrokedashoffset.php
	 * @return float a float representing the offset and 0 if it's not set.
	 */
	public function getstrokedashoffset () {}

	/**
	 * Returns the shape to be used at the end of open subpaths when they are stroked
	 * @link http://www.php.net/manual/en/function.imagickdraw-getstrokelinecap.php
	 * @return int one of the LINECAP_ constants or 0 if stroke linecap is not set.
	 */
	public function getstrokelinecap () {}

	/**
	 * Returns the shape to be used at the corners of paths when they are stroked
	 * @link http://www.php.net/manual/en/function.imagickdraw-getstrokelinejoin.php
	 * @return int one of the LINEJOIN_ constants or 0 if stroke line join is not set.
	 */
	public function getstrokelinejoin () {}

	/**
	 * Returns the stroke miter limit
	 * @link http://www.php.net/manual/en/function.imagickdraw-getstrokemiterlimit.php
	 * @return int an int describing the miter limit
	 * and 0 if no miter limit is set.
	 */
	public function getstrokemiterlimit () {}

	/**
	 * Returns the opacity of stroked object outlines
	 * @link http://www.php.net/manual/en/function.imagickdraw-getstrokeopacity.php
	 * @return float a double describing the opacity.
	 */
	public function getstrokeopacity () {}

	/**
	 * Returns the width of the stroke used to draw object outlines
	 * @link http://www.php.net/manual/en/function.imagickdraw-getstrokewidth.php
	 * @return float a double describing the stroke width.
	 */
	public function getstrokewidth () {}

	/**
	 * Returns the text alignment
	 * @link http://www.php.net/manual/en/function.imagickdraw-gettextalignment.php
	 * @return int one of the ALIGN_ constants and 0 if no align is set.
	 */
	public function gettextalignment () {}

	/**
	 * Returns the current text antialias setting
	 * @link http://www.php.net/manual/en/function.imagickdraw-gettextantialias.php
	 * @return bool true if text is antialiased and false if not.
	 */
	public function gettextantialias () {}

	/**
	 * Returns a string containing vector graphics
	 * @link http://www.php.net/manual/en/function.imagickdraw-getvectorgraphics.php
	 * @return string a string containing the vector graphics.
	 */
	public function getvectorgraphics () {}

	/**
	 * Returns the text under color
	 * @link http://www.php.net/manual/en/function.imagickdraw-gettextundercolor.php
	 * @return ImagickPixel an ImagickPixel object describing the color.
	 */
	public function gettextundercolor () {}

	/**
	 * Adds a path element to the current path
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathclose.php
	 * @return bool 
	 */
	public function pathclose () {}

	/**
	 * Draws a cubic Bezier curve
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathcurvetoabsolute.php
	 * @param x1 float <p>
	 * x coordinate of the first control point
	 * </p>
	 * @param y1 float <p>
	 * y coordinate of the first control point
	 * </p>
	 * @param x2 float <p>
	 * x coordinate of the second control point
	 * </p>
	 * @param y2 float <p>
	 * y coordinate of the first control point
	 * </p>
	 * @param x float <p>
	 * x coordinate of the curve end
	 * </p>
	 * @param y float <p>
	 * y coordinate of the curve end
	 * </p>
	 * @return bool 
	 */
	public function pathcurvetoabsolute ($x1, $y1, $x2, $y2, $x, $y) {}

	/**
	 * Draws a cubic Bezier curve
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathcurvetorelative.php
	 * @param x1 float <p>
	 * x coordinate of starting control point
	 * </p>
	 * @param y1 float <p>
	 * y coordinate of starting control point
	 * </p>
	 * @param x2 float <p>
	 * x coordinate of ending control point
	 * </p>
	 * @param y2 float <p>
	 * y coordinate of ending control point
	 * </p>
	 * @param x float <p>
	 * ending x coordinate
	 * </p>
	 * @param y float <p>
	 * ending y coordinate
	 * </p>
	 * @return bool 
	 */
	public function pathcurvetorelative ($x1, $y1, $x2, $y2, $x, $y) {}

	/**
	 * Draws a quadratic Bezier curve
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathcurvetoquadraticbezierabsolute.php
	 * @param x1 float <p>
	 * x coordinate of the control point
	 * </p>
	 * @param y1 float <p>
	 * y coordinate of the control point
	 * </p>
	 * @param x float <p>
	 * x coordinate of the end point
	 * </p>
	 * @param y float <p>
	 * y coordinate of the end point
	 * </p>
	 * @return bool 
	 */
	public function pathcurvetoquadraticbezierabsolute ($x1, $y1, $x, $y) {}

	/**
	 * Draws a quadratic Bezier curve
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathcurvetoquadraticbezierrelative.php
	 * @param x1 float <p>
	 * starting x coordinate
	 * </p>
	 * @param y1 float <p>
	 * starting y coordinate
	 * </p>
	 * @param x float <p>
	 * ending x coordinate
	 * </p>
	 * @param y float <p>
	 * ending y coordinate
	 * </p>
	 * @return bool 
	 */
	public function pathcurvetoquadraticbezierrelative ($x1, $y1, $x, $y) {}

	/**
	 * Draws a quadratic Bezier curve
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathcurvetoquadraticbeziersmoothabsolute.php
	 * @param x float <p>
	 * ending x coordinate
	 * </p>
	 * @param y float <p>
	 * ending y coordinate
	 * </p>
	 * @return bool 
	 */
	public function pathcurvetoquadraticbeziersmoothabsolute ($x, $y) {}

	/**
	 * Draws a quadratic Bezier curve
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathcurvetoquadraticbeziersmoothrelative.php
	 * @param x float <p>
	 * ending x coordinate
	 * </p>
	 * @param y float <p>
	 * ending y coordinate
	 * </p>
	 * @return bool 
	 */
	public function pathcurvetoquadraticbeziersmoothrelative ($x, $y) {}

	/**
	 * Draws a cubic Bezier curve
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathcurvetosmoothabsolute.php
	 * @param x2 float <p>
	 * x coordinate of the second control point
	 * </p>
	 * @param y2 float <p>
	 * y coordinate of the second control point
	 * </p>
	 * @param x float <p>
	 * x coordinate of the ending point
	 * </p>
	 * @param y float <p>
	 * y coordinate of the ending point
	 * </p>
	 * @return bool 
	 */
	public function pathcurvetosmoothabsolute ($x2, $y2, $x, $y) {}

	/**
	 * Draws a cubic Bezier curve
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathcurvetosmoothrelative.php
	 * @param x2 float <p>
	 * x coordinate of the second control point
	 * </p>
	 * @param y2 float <p>
	 * y coordinate of the second control point
	 * </p>
	 * @param x float <p>
	 * x coordinate of the ending point
	 * </p>
	 * @param y float <p>
	 * y coordinate of the ending point
	 * </p>
	 * @return bool 
	 */
	public function pathcurvetosmoothrelative ($x2, $y2, $x, $y) {}

	/**
	 * Draws an elliptical arc
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathellipticarcabsolute.php
	 * @param rx float <p>
	 * x radius
	 * </p>
	 * @param ry float <p>
	 * y radius
	 * </p>
	 * @param x_axis_rotation float <p>
	 * x axis rotation
	 * </p>
	 * @param large_arc_flag bool <p>
	 * large arc flag
	 * </p>
	 * @param sweep_flag bool <p>
	 * sweep flag
	 * </p>
	 * @param x float <p>
	 * x coordinate
	 * </p>
	 * @param y float <p>
	 * y coordinate
	 * </p>
	 * @return bool 
	 */
	public function pathellipticarcabsolute ($rx, $ry, $x_axis_rotation, $large_arc_flag, $sweep_flag, $x, $y) {}

	/**
	 * Draws an elliptical arc
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathellipticarcrelative.php
	 * @param rx float <p>
	 * x radius
	 * </p>
	 * @param ry float <p>
	 * y radius
	 * </p>
	 * @param x_axis_rotation float <p>
	 * x axis rotation
	 * </p>
	 * @param large_arc_flag bool <p>
	 * large arc flag
	 * </p>
	 * @param sweep_flag bool <p>
	 * sweep flag
	 * </p>
	 * @param x float <p>
	 * x coordinate
	 * </p>
	 * @param y float <p>
	 * y coordinate
	 * </p>
	 * @return bool 
	 */
	public function pathellipticarcrelative ($rx, $ry, $x_axis_rotation, $large_arc_flag, $sweep_flag, $x, $y) {}

	/**
	 * Terminates the current path
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathfinish.php
	 * @return bool 
	 */
	public function pathfinish () {}

	/**
	 * Draws a line path
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathlinetoabsolute.php
	 * @param x float <p>
	 * starting x coordinate
	 * </p>
	 * @param y float <p>
	 * ending x coordinate
	 * </p>
	 * @return bool 
	 */
	public function pathlinetoabsolute ($x, $y) {}

	/**
	 * Draws a line path
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathlinetorelative.php
	 * @param x float <p>
	 * starting x coordinate
	 * </p>
	 * @param y float <p>
	 * starting y coordinate
	 * </p>
	 * @return bool 
	 */
	public function pathlinetorelative ($x, $y) {}

	/**
	 * Draws a horizontal line path
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathlinetohorizontalabsolute.php
	 * @param x float <p>
	 * x coordinate
	 * </p>
	 * @return bool 
	 */
	public function pathlinetohorizontalabsolute ($x) {}

	/**
	 * Draws a horizontal line
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathlinetohorizontalrelative.php
	 * @param x float <p>
	 * x coordinate
	 * </p>
	 * @return bool 
	 */
	public function pathlinetohorizontalrelative ($x) {}

	/**
	 * Draws a vertical line
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathlinetoverticalabsolute.php
	 * @param y float <p>
	 * y coordinate
	 * </p>
	 * @return bool 
	 */
	public function pathlinetoverticalabsolute ($y) {}

	/**
	 * Draws a vertical line path
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathlinetoverticalrelative.php
	 * @param y float <p>
	 * y coordinate
	 * </p>
	 * @return bool 
	 */
	public function pathlinetoverticalrelative ($y) {}

	/**
	 * Starts a new sub-path
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathmovetoabsolute.php
	 * @param x float <p>
	 * x coordinate of the starting point
	 * </p>
	 * @param y float <p>
	 * y coordinate of the starting point
	 * </p>
	 * @return bool 
	 */
	public function pathmovetoabsolute ($x, $y) {}

	/**
	 * Starts a new sub-path
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathmovetorelative.php
	 * @param x float <p>
	 * target x coordinate
	 * </p>
	 * @param y float <p>
	 * target y coordinate
	 * </p>
	 * @return bool 
	 */
	public function pathmovetorelative ($x, $y) {}

	/**
	 * Declares the start of a path drawing list
	 * @link http://www.php.net/manual/en/function.imagickdraw-pathstart.php
	 * @return bool 
	 */
	public function pathstart () {}

	/**
	 * Draws a polyline
	 * @link http://www.php.net/manual/en/function.imagickdraw-polyline.php
	 * @param coordinates array <p>
	 * array of x and y coordinates: array( array( 'x' => 4, 'y' => 6 ), array( 'x' => 8, 'y' => 10 ) ) 
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function polyline (array $coordinates) {}

	/**
	 * Terminates a clip path definition
	 * @link http://www.php.net/manual/en/function.imagickdraw-popclippath.php
	 * @return bool 
	 */
	public function popclippath () {}

	/**
	 * Terminates a definition list
	 * @link http://www.php.net/manual/en/function.imagickdraw-popdefs.php
	 * @return bool 
	 */
	public function popdefs () {}

	/**
	 * Terminates a pattern definition
	 * @link http://www.php.net/manual/en/function.imagickdraw-poppattern.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function poppattern () {}

	/**
	 * Starts a clip path definition
	 * @link http://www.php.net/manual/en/function.imagickdraw-pushclippath.php
	 * @param clip_mask_id string <p>
	 * Clip mask Id
	 * </p>
	 * @return bool 
	 */
	public function pushclippath ($clip_mask_id) {}

	/**
	 * Indicates that following commands create named elements for early processing
	 * @link http://www.php.net/manual/en/function.imagickdraw-pushdefs.php
	 * @return bool 
	 */
	public function pushdefs () {}

	/**
	 * Indicates that subsequent commands up to a ImagickDraw::opPattern() command comprise the definition of a named pattern
	 * @link http://www.php.net/manual/en/function.imagickdraw-pushpattern.php
	 * @param pattern_id string <p>
	 * the pattern Id
	 * </p>
	 * @param x float <p>
	 * x coordinate of the top-left corner
	 * </p>
	 * @param y float <p>
	 * y coordinate of the top-left corner
	 * </p>
	 * @param width float <p>
	 * width of the pattern
	 * </p>
	 * @param height float <p>
	 * height of the pattern
	 * </p>
	 * @return bool Returns true on success or false on failure.
	 */
	public function pushpattern ($pattern_id, $x, $y, $width, $height) {}

	/**
	 * Renders all preceding drawing commands onto the image
	 * @link http://www.php.net/manual/en/function.imagickdraw-render.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function render () {}

	/**
	 * Applies the specified rotation to the current coordinate space
	 * @link http://www.php.net/manual/en/function.imagickdraw-rotate.php
	 * @param degrees float <p>
	 * degrees to rotate
	 * </p>
	 * @return bool 
	 */
	public function rotate ($degrees) {}

	/**
	 * Adjusts the scaling factor
	 * @link http://www.php.net/manual/en/function.imagickdraw-scale.php
	 * @param x float <p>
	 * horizontal factor
	 * </p>
	 * @param y float <p>
	 * vertical factor
	 * </p>
	 * @return bool 
	 */
	public function scale ($x, $y) {}

	/**
	 * Associates a named clipping path with the image
	 * @link http://www.php.net/manual/en/function.imagickdraw-setclippath.php
	 * @param clip_mask string <p>
	 * the clipping path name
	 * </p>
	 * @return bool 
	 */
	public function setclippath ($clip_mask) {}

	/**
	 * Set the polygon fill rule to be used by the clipping path
	 * @link http://www.php.net/manual/en/function.imagickdraw-setcliprule.php
	 * @param fill_rule int <p>
	 * FILLRULE_ constant
	 * </p>
	 * @return bool 
	 */
	public function setcliprule ($fill_rule) {}

	/**
	 * Sets the interpretation of clip path units
	 * @link http://www.php.net/manual/en/function.imagickdraw-setclipunits.php
	 * @param clip_units int <p>
	 * the number of clip units
	 * </p>
	 * @return bool 
	 */
	public function setclipunits ($clip_units) {}

	/**
	 * Sets the opacity to use when drawing using the fill color or fill texture
	 * @link http://www.php.net/manual/en/function.imagickdraw-setfillopacity.php
	 * @param fillOpacity float <p>
	 * the fill opacity
	 * </p>
	 * @return bool 
	 */
	public function setfillopacity ($fillOpacity) {}

	/**
	 * Sets the URL to use as a fill pattern for filling objects
	 * @link http://www.php.net/manual/en/function.imagickdraw-setfillpatternurl.php
	 * @param fill_url string <p>
	 * URL to use to obtain fill pattern.
	 * </p>
	 * @return bool Returns true on success or false on failure.
	 */
	public function setfillpatternurl ($fill_url) {}

	/**
	 * Sets the fill rule to use while drawing polygons
	 * @link http://www.php.net/manual/en/function.imagickdraw-setfillrule.php
	 * @param fill_rule int <p>
	 * FILLRULE_ constant
	 * </p>
	 * @return bool 
	 */
	public function setfillrule ($fill_rule) {}

	/**
	 * Sets the text placement gravity
	 * @link http://www.php.net/manual/en/function.imagickdraw-setgravity.php
	 * @param gravity int <p>
	 * GRAVITY_ constant
	 * </p>
	 * @return bool 
	 */
	public function setgravity ($gravity) {}

	/**
	 * Sets the pattern used for stroking object outlines
	 * @link http://www.php.net/manual/en/function.imagickdraw-setstrokepatternurl.php
	 * @param stroke_url string <p>
	 * stroke URL
	 * </p>
	 * @return bool imagick.imagickdraw.return.success;
	 */
	public function setstrokepatternurl ($stroke_url) {}

	/**
	 * Specifies the offset into the dash pattern to start the dash
	 * @link http://www.php.net/manual/en/function.imagickdraw-setstrokedashoffset.php
	 * @param dash_offset float <p>
	 * dash offset
	 * </p>
	 * @return bool 
	 */
	public function setstrokedashoffset ($dash_offset) {}

	/**
	 * Specifies the shape to be used at the end of open subpaths when they are stroked
	 * @link http://www.php.net/manual/en/function.imagickdraw-setstrokelinecap.php
	 * @param linecap int <p>
	 * LINECAP_ constant
	 * </p>
	 * @return bool 
	 */
	public function setstrokelinecap ($linecap) {}

	/**
	 * Specifies the shape to be used at the corners of paths when they are stroked
	 * @link http://www.php.net/manual/en/function.imagickdraw-setstrokelinejoin.php
	 * @param linejoin int <p>
	 * LINEJOIN_ constant
	 * </p>
	 * @return bool 
	 */
	public function setstrokelinejoin ($linejoin) {}

	/**
	 * Specifies the miter limit
	 * @link http://www.php.net/manual/en/function.imagickdraw-setstrokemiterlimit.php
	 * @param miterlimit int <p>
	 * the miter limit
	 * </p>
	 * @return bool 
	 */
	public function setstrokemiterlimit ($miterlimit) {}

	/**
	 * Specifies the opacity of stroked object outlines
	 * @link http://www.php.net/manual/en/function.imagickdraw-setstrokeopacity.php
	 * @param stroke_opacity float <p>
	 * stroke opacity. 1.0 is fully opaque
	 * </p>
	 * @return bool 
	 */
	public function setstrokeopacity ($stroke_opacity) {}

	/**
	 * Sets the vector graphics
	 * @link http://www.php.net/manual/en/function.imagickdraw-setvectorgraphics.php
	 * @param xml string <p>
	 * xml containing the vector graphics
	 * </p>
	 * @return bool Returns true on success or false on failure.
	 */
	public function setvectorgraphics ($xml) {}

	/**
	 * Destroys the current ImagickDraw in the stack, and returns to the previously pushed ImagickDraw
	 * @link http://www.php.net/manual/en/function.imagickdraw-pop.php
	 * @return bool true on success and false on failure.
	 */
	public function pop () {}

	/**
	 * Clones the current ImagickDraw and pushes it to the stack
	 * @link http://www.php.net/manual/en/function.imagickdraw-push.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function push () {}

	/**
	 * Specifies the pattern of dashes and gaps used to stroke paths
	 * @link http://www.php.net/manual/en/function.imagickdraw-setstrokedasharray.php
	 * @param dashArray array <p>
	 * array of floats
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setstrokedasharray (array $dashArray) {}

}

/** @jms-builtin */
class ImagickPixelIterator implements Iterator, Traversable {

	/**
	 * The ImagickPixelIterator constructor
	 * @link http://www.php.net/manual/en/function.imagickpixeliterator-construct.php
	 * @param wand Imagick 
	 * @return ImagickPixelIterator &imagick.return.success;
	 */
	public function __construct (Imagick $wand) {}

	/**
	 * Returns a new pixel iterator
	 * @link http://www.php.net/manual/en/function.imagickpixeliterator-newpixeliterator.php
	 * @param wand Imagick 
	 * @return bool &imagick.return.success; Throwing ImagickPixelIteratorException.
	 */
	public function newpixeliterator (Imagick $wand) {}

	/**
	 * Returns a new pixel iterator
	 * @link http://www.php.net/manual/en/function.imagickpixeliterator-newpixelregioniterator.php
	 * @param wand Imagick <p>
	 * </p>
	 * @param x int <p>
	 * </p>
	 * @param y int <p>
	 * </p>
	 * @param columns int <p>
	 * </p>
	 * @param rows int <p>
	 * </p>
	 * @return bool a new ImagickPixelIterator on success; on failure, throws
	 * ImagickPixelIteratorException.
	 */
	public function newpixelregioniterator (Imagick $wand, $x, $y, $columns, $rows) {}

	/**
	 * Returns the current pixel iterator row
	 * @link http://www.php.net/manual/en/function.imagickpixeliterator-getiteratorrow.php
	 * @return int the integer offset of the row, throwing
	 * ImagickPixelIteratorException on error.
	 */
	public function getiteratorrow () {}

	/**
	 * Set the pixel iterator row
	 * @link http://www.php.net/manual/en/function.imagickpixeliterator-setiteratorrow.php
	 * @param row int <p>
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setiteratorrow ($row) {}

	/**
	 * Sets the pixel iterator to the first pixel row
	 * @link http://www.php.net/manual/en/function.imagickpixeliterator-setiteratorfirstrow.php
	 * @return bool &imagick.return.success;
	 */
	public function setiteratorfirstrow () {}

	/**
	 * Sets the pixel iterator to the last pixel row
	 * @link http://www.php.net/manual/en/function.imagickpixeliterator-setiteratorlastrow.php
	 * @return bool &imagick.return.success;
	 */
	public function setiteratorlastrow () {}

	/**
	 * Returns the previous row
	 * @link http://www.php.net/manual/en/function.imagickpixeliterator-getpreviousiteratorrow.php
	 * @return array the previous row as an array of ImagickPixelWand objects from the
	 * ImagickPixelIterator, throwing ImagickPixelIteratorException on error.
	 */
	public function getpreviousiteratorrow () {}

	/**
	 * Returns the current row of ImagickPixel objects
	 * @link http://www.php.net/manual/en/function.imagickpixeliterator-getcurrentiteratorrow.php
	 * @return array a row as an array of ImagickPixel objects that can themselves be iterated.
	 */
	public function getcurrentiteratorrow () {}

	/**
	 * Returns the next row of the pixel iterator
	 * @link http://www.php.net/manual/en/function.imagickpixeliterator-getnextiteratorrow.php
	 * @return array the next row as an array of ImagickPixel objects, throwing
	 * ImagickPixelIteratorException on error.
	 */
	public function getnextiteratorrow () {}

	/**
	 * Resets the pixel iterator
	 * @link http://www.php.net/manual/en/function.imagickpixeliterator-resetiterator.php
	 * @return bool &imagick.return.success;
	 */
	public function resetiterator () {}

	/**
	 * Syncs the pixel iterator
	 * @link http://www.php.net/manual/en/function.imagickpixeliterator-synciterator.php
	 * @return bool &imagick.return.success;
	 */
	public function synciterator () {}

	/**
	 * Deallocates resources associated with a PixelIterator
	 * @link http://www.php.net/manual/en/function.imagickpixeliterator-destroy.php
	 * @return bool &imagick.return.success;
	 */
	public function destroy () {}

	/**
	 * Clear resources associated with a PixelIterator
	 * @link http://www.php.net/manual/en/function.imagickpixeliterator-clear.php
	 * @return bool &imagick.return.success;
	 */
	public function clear () {}

	public function key () {}

	public function next () {}

	public function rewind () {}

	public function current () {}

	public function valid () {}

}

/** @jms-builtin */
class ImagickPixel  {

	/**
	 * Returns the normalized HSL color of the ImagickPixel object
	 * @link http://www.php.net/manual/en/function.imagickpixel-gethsl.php
	 * @return array the HSL value in an array with the keys "hue",
	 * "saturation", and "luminosity". Throws ImagickPixelException on failure.
	 */
	public function gethsl () {}

	/**
	 * Sets the normalized HSL color
	 * @link http://www.php.net/manual/en/function.imagickpixel-sethsl.php
	 * @param hue float <p>
	 * The normalized value for hue, described as a fractional arc
	 * (between 0 and 1) of the hue circle, where the zero value is
	 * red.
	 * </p>
	 * @param saturation float <p>
	 * The normalized value for saturation, with 1 as full saturation.
	 * </p>
	 * @param luminosity float <p>
	 * The normalized value for luminosity, on a scale from black at
	 * 0 to white at 1, with the full HS value at 0.5 luminosity.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function sethsl ($hue, $saturation, $luminosity) {}

	public function getcolorvaluequantum () {}

	/**
	 * @param color_value
	 */
	public function setcolorvaluequantum ($color_value) {}

	public function getindex () {}

	/**
	 * @param index
	 */
	public function setindex ($index) {}

	/**
	 * The ImagickPixel constructor
	 * @link http://www.php.net/manual/en/function.imagickpixel-construct.php
	 * @param color string[optional] <p>
	 * The optional color string to use as the initial value of this object.
	 * </p>
	 * @return ImagickPixel an ImagickPixel object on success, throwing ImagickPixelException on
	 * failure.
	 */
	public function __construct ($color = null) {}

	/**
	 * Sets the color
	 * @link http://www.php.net/manual/en/function.imagickpixel-setcolor.php
	 * @param color string <p>
	 * The color definition to use in order to initialise the
	 * ImagickPixel object.
	 * </p>
	 * @return bool true if the specified color was set, false otherwise.
	 */
	public function setcolor ($color) {}

	/**
	 * Sets the normalized value of one of the channels
	 * @link http://www.php.net/manual/en/function.imagickpixel-setcolorvalue.php
	 * @param color int <p>
	 * One of the Imagick channel color constants.
	 * </p>
	 * @param value float <p>
	 * The value to set this channel to, ranging from 0 to 1.
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function setcolorvalue ($color, $value) {}

	/**
	 * Gets the normalized value of the provided color channel
	 * @link http://www.php.net/manual/en/function.imagickpixel-getcolorvalue.php
	 * @param color int <p>
	 * The channel to check, specified as one of the Imagick channel constants.
	 * </p>
	 * @return float The value of the channel, as a normalized floating-point number, throwing
	 * ImagickPixelException on error.
	 */
	public function getcolorvalue ($color) {}

	/**
	 * Clears resources associated with this object
	 * @link http://www.php.net/manual/en/function.imagickpixel-clear.php
	 * @return bool &imagick.return.success;
	 */
	public function clear () {}

	/**
	 * Deallocates resources associated with this object
	 * @link http://www.php.net/manual/en/function.imagickpixel-destroy.php
	 * @return bool &imagick.return.success;
	 */
	public function destroy () {}

	/**
	 * Check the distance between this color and another
	 * @link http://www.php.net/manual/en/function.imagickpixel-issimilar.php
	 * @param color ImagickPixel <p>
	 * The ImagickPixel object to compare this object against.
	 * </p>
	 * @param fuzz float <p>
	 * The maximum distance within which to consider these colors as similar.
	 * The theoretical maximum for this value is the square root of three
	 * (1.732).
	 * </p>
	 * @return bool &imagick.return.success;
	 */
	public function issimilar (ImagickPixel $color, $fuzz) {}

	/**
	 * Returns the color
	 * @link http://www.php.net/manual/en/function.imagickpixel-getcolor.php
	 * @param normalized bool[optional] <p>
	 * Normalize the color values
	 * </p>
	 * @return array An array of channel values, each normalized if true is given as param. Throws
	 * ImagickPixelException on error.
	 */
	public function getcolor ($normalized = null) {}

	/**
	 * Returns the color as a string
	 * @link http://www.php.net/manual/en/function.imagickpixel-getcolorasstring.php
	 * @return string the color of the ImagickPixel object as a string.
	 */
	public function getcolorasstring () {}

	/**
	 * Returns the color count associated with this color
	 * @link http://www.php.net/manual/en/function.imagickpixel-getcolorcount.php
	 * @return int the color count as an integer on success, throws
	 * ImagickPixelException on failure.
	 */
	public function getcolorcount () {}

	/**
	 * @param colorCount
	 */
	public function setcolorcount ($colorCount) {}

	public function clone () {}

}
// End of imagick v.2.3.0
?>
