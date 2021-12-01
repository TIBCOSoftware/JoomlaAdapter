/**
 * Created by Kevin Li<huali@tibco-support.com> on 1/26/15.
 *
 * Variable prefixes' meanings:
 * -------------------------------------------------------------------------
 * --- The prefix of a variable's name reveals the type of data it holds ---
 * -------------------------------------------------------------------------
 *
 * a: Array
 * b: Boolean
 * d: DOM
 * f: Function
 * l: List(an array-like object)
 * n: Number
 * o: Object
 * r: Regular expression
 * s: String
 * x: More than one type
 *  : Special case or NOT my code
 *
 * *** These prefixes can be concatenated to indicate that the variable can
 *         hold the specified types of data ***
 */

/**
 * The name space for Policy Viewer related stuff.
 *
 * @type {Object}
 */
var policyViewer = {};

(function ($) {
    'use strict';

    var jqMask = $('<div id="policy_viewer_mask" class="policy-viewer-position"></div>'),
        jqOverlay = $('<div id="policy_viewer_overlay" class="policy-viewer-position"></div>'),
        jqHeader = $('<div id="policy_viewer_header" class="header-height"></div>'),
        jqHTTPMethod = $('<div id="policy_viewer_http_method" class="inline-block pull-left header-height"></div>'),
        jqTitles = $('<div id="policy_viewer_titles" class="inline-block header-height"><h1></h1><p></p></div>'),
        jqCloseBtn = $('<div id="policy_viewer_close_btn" class="inline-block pull-right header-height"><a></a></div>'),
        jqContent = $('<div id="policy_viewer_content" class="border-bottom-radius"></div>'),
        jqContentMask = $('<div id="policy_viewer_content_mask" class="border-bottom-radius"></div>'),
        oConstants = {
            API_FIELD_KEY: 'keae98b085de51e6fc65fab708c329e5b',
            CONTENT_WRAPPER_HTML: '<div class="policy-viewer-content-wrapper"></div>',
            HTTP_METHOD_FIELD_KEY: 'kadc32b63e05baafc80e21f82c9c2818d',
            OPERATION_TITLE_CLASS: 'page-header',
            POST_DATA_URL: GLOBAL_CONTEXT_PATH + '/index.php?option=com_cobalt&task=ajaxmore.savePolicy',
            TEMPLATE_FILE_SUFFIX: '.tpl.php',
            TEMPLATE_URL_PREFIX: 'components/com_cobalt/fields/textarea/tmpl/output/partials/',
            WRAPPER_ID_PREFIX: 'content_wrapper_'
        },
        aTplsInOrder = ['overview', 'policies', 'policy_types', 'wizard'],
        oViewCache = {},
        oWizardCache = {},
        aData = [],
        oSelectedNode = null,
        oSelectedPolicyType = null,
        sCurrentTplName = '';

    /**
     * Initialize the overlay for the policy viewer upon document ready.
     *
     * @author Kevin Li<huali@tibco-support.com>
     */
    $(function () {

        $('body')
            .append(jqMask)
            .append(jqOverlay
                .append(jqHeader
                    .append(jqHTTPMethod)
                    .append(jqTitles)
                    .append(jqCloseBtn
                        .on('click', function () {

                            policyViewer.oUI.close();
                        })))
                .append(jqContent)
                .append(jqContentMask));

        setHTTPMethod($('#dd-' + oConstants.HTTP_METHOD_FIELD_KEY).text());
        setTitle($('.' + oConstants.OPERATION_TITLE_CLASS).text());
        setSubTitle($('#dd-' + oConstants.API_FIELD_KEY + ' .record-title').text());

        policyViewer.EVENT_TYPE_TEMPLATE_LOADED = 'policy_viewer_template_loaded';
        policyViewer.EVENT_TYPE_TEMPLATE_SHOW = 'policy_viewer_template_show';
        policyViewer.EVENT_TYPE_TEMPLATE_READY = 'policy_viewer_template_ready';
        policyViewer.initialize = initialize;
    });

    /**
     * Set the currently selected node to null.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @returns {Object} The {@link policyViewer.oData} object for method chaining.
     */
    function clearNodeSelection() {

        oSelectedNode = null;
        return this;
    }

    /**
     * Set the currently selected policy type to null.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @returns {Object} The {@link policyViewer.oData} object for method chaining.
     */
    function clearPolicyTypeSelection() {

        oSelectedPolicyType = null;
        return this;
    }

    /**
     * Close the policy viewer. If a jQuery's Promise object is provided as the first argument the policy viewer will
     * not be closed until the Promise object is resolved. Otherwise, the policy viewer will be closed right away.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Promise} oPromise Optional. jQuery's Promise object. If this argument is omitted the policy viewer will
     * be closed right away. Otherwise, the policy viewer won't close until the promise is resolved.
     * @returns {Object} The {@link policyViewer.oUI} object for method chaining.
     */
    function close(oPromise) {

        if (isOpen()) {

            if (typeof oPromise !== 'undefined' &&
                $.isFunction(oPromise.done)) {

                showLoadingIcon();

                oPromise.done(function () {

                    hideLoadingIcon();
                    sCurrentTplName = undefined;
                    hideOverlay();
                });
            } else {

                sCurrentTplName = undefined;
                hideOverlay();
            }
        }
        return this;
    }

    /**
     * Get the name of the template of the next view in the policy viewer.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     * @returns {string} The name of the template of the next view in the policy viewer if the current template is found
     * and it's not the last template. Otherwise, an undefined value is returned.
     */
    function getNextTplName() {

        var nCurrentIndex = aTplsInOrder.indexOf(sCurrentTplName),
            ret;

        if (nCurrentIndex > -1 && nCurrentIndex < aTplsInOrder.length - 1) {

            ret = aTplsInOrder[nCurrentIndex + 1];
        }
        return ret;
    }

    /**
     * Get the data of one of the nodes in the overview.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {string} sNodeName The name of the node to be returned.
     * @returns {Object} A JSON object containing the data about the node denoted by the xNodeId.
     */
    function getNode(sNodeName) {

        var ret = {};

        $.each(aData, function (nIndex, oItem) {

            if (oItem.name === sNodeName) {

                ret = oItem;
            }
        });
        return ret;
    }

    /**
     * Get the overview of the topology of an operation.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @returns {Array} An array containing the information about the 4 nodes in the overview.
     */
    function getOverview() {

        return aData;
    }

    /**
     * Get the policy types that can be applied to currently selected node. If the argument is passed a truthy value,
     * all available policy types will be returned.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {boolean} bAll True if all available policy types should be returned. False if only the policy types that
     * can be applied to currently selected node should be returned.
     * @returns {Array} An array containing the information of policy types.
     */
    function getPolicyTypes(bAll) {

        return [];
    }

    /**
     * Get the index of the name of the template of the previous view in the templates array.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     * @returns {string} The name of the template of the next view in the policy viewer if the current template is found
     * and it's not the first template. Otherwise, an undefined value is returned.
     */
    function getPrevTplName() {

        var nCurrentIndex = aTplsInOrder.indexOf(sCurrentTplName),
            ret;

        if (nCurrentIndex > 0) {

            ret = aTplsInOrder[nCurrentIndex - 1];
        }
        return ret;
    }

    /**
     * Get the currently selected node.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @returns {Object} An object containing the information of the currently selected node.
     */
    function getSelectedNode() {

        return oSelectedNode;
    }

    /**
     * Get the currently selected policy type.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @returns {Object} An object containing the information of the currently selected policy type.
     */
    function getSelectedPolicyType() {

        return oSelectedPolicyType;
    }

    /**
     * Get a template over an HTTP request.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     * @param {string} sTplName Required. The name of the template. If this argument is omitted an undefined will
     * be returned.
     * @returns {jqXHR} jQuery's jqXHR object which is a super set of XMLHTTPRequest.
     */
    function getTemplate(sTplName) {

        if (typeof sTplName === 'string') {

            return $.get(GLOBAL_CONTEXT_PATH + oConstants.TEMPLATE_URL_PREFIX + sTplName + oConstants.TEMPLATE_FILE_SUFFIX);
        }
    }

    /**
     * Hide all templates in the policy viewer.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     */
    function hideAllTemplates() {

        jqContent
            .find('.policy-viewer-content-wrapper')
            .hide(0);
    }

    /**
     * Hide the loading icon.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     */
    function hideLoadingIcon() {

        jqContentMask.fadeOut(0);
    }

    /**
     * Hide the policy viewer overlay and the mask layer.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     */
    function hideOverlay() {

        jqOverlay.hide(200, function () {

            hideAllTemplates();
        });
        jqMask.fadeOut(200);
    }

    /**
     * Initialize the policy viewer with data and utility methods.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Array} aPolicyData Required. The initial policy data which is an array. If this argument is omitted
     * nothing will be done.
     * @returns {Object} The {@link policyViewer} object for method chaining.
     */
    function initialize(aPolicyData) {

        if ($.isArray(aPolicyData)) {

            aData = aPolicyData;

            // The name space for data related stuff.
            policyViewer.oData = {
                clearNodeSelection: clearNodeSelection,
                clearPolicyTypeSelection: clearPolicyTypeSelection,
                getNode: getNode,
                getOverview: getOverview,
                getPolicyTypes: getPolicyTypes,
                getSelectedNode: getSelectedNode,
                getSelectedPolicyType: getSelectedPolicyType,
                save: save,
                setSelectedNode: setSelectedNode,
                setSelectedPolicyType: setSelectedPolicyType
            };

            // The name space for UI related stuff.
            policyViewer.oUI = {
                close: close,
                loadWizard: loadWizard,
                next: next,
                open: open,
                prev: prev
            };

            $.extend(policyViewer.oUI, EventEmitter.newInstance());

            policyViewer.oUI.on(policyViewer.EVENT_TYPE_TEMPLATE_READY, function (sReadyTplName) {

                if (sReadyTplName === sCurrentTplName) {

                    hideLoadingIcon();
                }
            });
        }
        return this;
    }

    /**
     * Tell if the policy viewer is open.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     * @returns {boolean} True if the policy viewer is open. False otherwise.
     */
    function isOpen() {

        return jqOverlay.css('display') === '' || jqOverlay.css('display') === 'block';
    }

    /**
     * Tell whether the template by the name sTplName is being displayed in the policy viewer.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     * @param {string} sTplName Required. The name of the template to check. If this argument is not a name of any
     * of the templates in the registry, a false value will always be returned.
     * @returns {boolean} True if the template by the name sTplName is being displayed in the policy viewer. False
     * otherwise.
     */
    function isShowingTemplate(sTplName) {

        var ret = false;

        if (typeof sTplName === 'string' &&
            oViewCache[sTplName] instanceof jQuery) {

            ret = oViewCache[sTplName].css('display') === '' || oViewCache[sTplName].css('display') === 'block';
        }
        return ret;
    }

    /**
     * Load a template into the policy viewer container. If the template by the name sTplName has already been
     * loaded before it simply toggles the display. Otherwise, an HTTP request will be sent out to get the content of
     * the template. At the same time the jQuery wrapped DOM element of the template will be saved in a registry with
     * the template name as the key for later use.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     * @param {string} sTplName Required. The name of the template. If this argument is omitted nothing will be
     * done.
     * @returns {Object} The {@link policyViewer.oUI} object for method chaining.
     */
    function loadTemplate(sTplName) {

        if (isOpen() && typeof sTplName === 'string') {

            sCurrentTplName = sTplName;

            showLoadingIcon();

            if (sTplName in oViewCache) {

                hideAllTemplates();

                oViewCache[sTplName].show(0);

                window.setTimeout(function () {

                    policyViewer.oUI.emit(policyViewer.EVENT_TYPE_TEMPLATE_SHOW, sTplName);
                });

            } else {

                var jqXHR = getTemplate(sTplName);

                if (typeof jqXHR !== 'undefined') {

                    jqXHR.done(function (data) {

                        var sWrapperId = oConstants.WRAPPER_ID_PREFIX + sTplName;

                        oViewCache[sTplName] = $(oConstants.CONTENT_WRAPPER_HTML)
                            .attr('id', sWrapperId)
                            .html(data);

                        hideAllTemplates();

                        jqContent.append(oViewCache[sTplName]);

                        window.setTimeout(function () {

                            policyViewer.oUI.emit(policyViewer.EVENT_TYPE_TEMPLATE_LOADED, sTplName);
                            policyViewer.oUI.emit(policyViewer.EVENT_TYPE_TEMPLATE_SHOW, sTplName);
                        });

                    }).fail(function (jqXHR, textStatus) {

                        console.error(textStatus);
                        hideLoadingIcon();
                    });
                }
            }
        }
    }

    /**
     * Get the wizard for a specific type of policy.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {string} sWizardName The name of the wizard.
     * @param {Function} fDone A callback function for the successful response. It'll be called with the data returned
     * from the server.
     * @returns {Promise|undefined} A Promise object if the fDone argument is omitted or undefined if the fDone is
     * provided.
     */
    function loadWizard(sWizardName, fDone) {

        var ret,
            oD;

        if (typeof sWizardName === 'string') {

            if(typeof fDone !== 'function') {

                oD = $.Deferred();
                ret = oD.promise();
            }

            if (sWizardName in oWizardCache) {

                if(oD) {

                    oD.resolve(oWizardCache[sWizardName]);

                } else {

                    fDone(oWizardCache[sWizardName]);
                }
            } else {

                getTemplate(sWizardName)
                    .done(function (oData) {

                        oWizardCache[sWizardName] = oData;

                        if(oD) {

                            oD.resolve(oData);

                        } else {

                            fDone(oData);
                        }

                    })
                    .fail(function (jqXHR, textStatus) {

                        oD.reject('Error occurred while loading wizard. ' + textStatus);

                        throw 'Error occurred while loading wizard. ' + textStatus;
                    });
            }
        }
        return ret;
    }

    /**
     * Navigate away from the current view, either to the next or the previous view in the policy viewer depending on
     * the value of the only argument.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     * @param {boolean} bNext True if the next view is the destination. Otherwise, the previous view is the destination.
     */
    function navigateAway(bNext) {

        if (isOpen()) {

            var sDestTplName = bNext ? getNextTplName() : getPrevTplName();

            if (typeof sDestTplName === 'string') {

                loadTemplate(sDestTplName);
            }
        }
    }

    /**
     * Navigate to the next view in the policy viewer.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @returns {Object} The {@link policyViewer.oUI} object for method chaining.
     */
    function next() {

        navigateAway(true);

        return this;
    }

    /**
     * Open up the policy viewer overlay with an optional template if the template name is provided as the first
     * argument.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {string} sTplName Optional. The name of the template that will be loaded initially. If this argument
     * is omitted an empty dialog will be opened up.
     * @returns {Object} The {@link policyViewer.oUI} object for method chaining.
     */
    function open(sTplName) {

        if (!isOpen()) {

            showOverlay(function () {

                if (typeof sTplName === 'string') {

                    // MUST be called with "this" otherwise the "this" inside "loadTemplate" will be wrong.
                    loadTemplate(sTplName);
                }
            });
        }
        return this;
    }

    /**
     * Post policy data to the back-end server to save into the database.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     * @returns {jqXHR} jQuery's jqXHR object which is a super set of XMLHTTPRequest.
     */
    function postData() {

        return $.post(oConstants.POST_DATA_URL, aData, 'json');
    }

    /**
     * Navigate to the previous view in the policy viewer.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @returns {Object} The {@link policyViewer.oUI} object for method chaining.
     */
    function prev() {

        navigateAway(false);

        return this;
    }

    /**
     * Save the data back to the database and optionally close the policy viewer.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {boolean} bClosePolicyViewer True to close the policy viewer after saving the data. False to leave the
     * policy viewer open.
     * @returns {Object} The {@link policyViewer.oData} object for method chaining.
     */
    function save(bClosePolicyViewer) {

        postData().done(function () {

            if (bClosePolicyViewer) {

                policyViewer.oUI.close();
            }
        }).fail(function (jqXHR, textStatus) {

            console.error(textStatus);
        });
        return this;
    }

    /**
     * Set the HTTP method of the policy viewer.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     * @param {string} sHTTPMethod The HTTP method of the policy viewer.
     */
    function setHTTPMethod(sHTTPMethod) {

        var sMethod = sHTTPMethod;

        if (typeof sMethod !== 'string' ||
            sMethod.length === 0) {

            sMethod = 'POST';
        }
        jqHTTPMethod.text(sMethod);
    }

    /**
     * Set the selected node.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Object} oNode Required. An object containing the information of a node. If this argument is omitted
     * nothing will be done.
     * @returns {Object} The {@link policyViewer.oData} object for method chaining.
     */
    function setSelectedNode(oNode) {

        if (typeof oNode === 'object' &&
            oNode.constructor === Object) {

            oSelectedNode = oNode;
        }
        return this;
    }

    /**
     * Set the selected policy type.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Object} oPolicyType Required. An object containing the information of a policy type. If this argument is
     * omitted nothing will be done.
     * @returns {Object} The {@link policyViewer.oData} object for method chaining.
     */
    function setSelectedPolicyType(oPolicyType) {

        if (typeof oPolicyType === 'object' &&
            oPolicyType.constructor === Object) {

            oSelectedPolicyType = oPolicyType;
        }
        return this;
    }

    /**
     * Set the sub-title of the policy viewer.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     * @param {string} sSubTitle The sub-title of the policy viewer.
     */
    function setSubTitle(sSubTitle) {

        if (typeof sSubTitle === 'string' &&
            sSubTitle.length > 0) {

            jqTitles.find('p').text('API: ' + sSubTitle);
        }
    }

    /**
     * Set the title of the policy viewer.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     * @param {string} sTitle The title of the policy viewer.
     */
    function setTitle(sTitle) {

        if (typeof sTitle === 'string' &&
            sTitle.length > 0) {

            jqTitles.find('h1').text(sTitle);
        }
    }

    /**
     * Show the loading icon.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     */
    function showLoadingIcon() {

        jqContentMask.fadeIn(0);
    }

    /**
     * Show the policy viewer overlay and the mask layer.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Function} fAnimCb A callback function for the jQuery animation.
     * @private
     */
    function showOverlay(fAnimCb) {

        jqMask.fadeIn(200);
        jqOverlay.show(200, fAnimCb);
    }

}(jQuery));