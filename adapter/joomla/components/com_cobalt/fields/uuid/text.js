/*
 * @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

function initMask(id, mask, type)
 {
	oTextMask = new Mask(mask, type);
	oTextMask.attach($("field_" + id));
}