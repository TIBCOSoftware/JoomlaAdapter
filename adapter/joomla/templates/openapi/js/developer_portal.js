/*
 * @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

/**
 * @author Huan Li
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
 * *** These prefixes can be concatenated to indicate that the variable can hold the specified types of data ***
 */

'use strict';

var DeveloperPortal = {};

(function($) {
    DeveloperPortal.DEFAULT_FORM_ID = 'adminForm';
    DeveloperPortal.REQUEST_KEY_URI = 'apiKey';
    DeveloperPortal.PORTAL_EVENT_URI = 'portalEvent';
    DeveloperPortal.PORTAL_EVENT_TYPE_CREATE = 'Create';
    DeveloperPortal.PORTAL_EVENT_TYPE_UPDATE = 'Update';
    DeveloperPortal.PORTAL_EVENT_TYPE_DELETE = 'Delete';
    DeveloperPortal.PORTAL_EVENT_TYPE_REQUEST_KEY = 'RequestKey';
    DeveloperPortal.PORTAL_OBJECT_TYPE_API = 'API';
    DeveloperPortal.PORTAL_OBJECT_TYPE_APPLICATION = 'Application';
    DeveloperPortal.PORTAL_OBJECT_TYPE_ENVIRONMENT = 'Environment';
    DeveloperPortal.PORTAL_OBJECT_TYPE_GATEWAY = 'Gateway';
    DeveloperPortal.PORTAL_OBJECT_TYPE_KEY = 'Key';
    DeveloperPortal.PORTAL_OBJECT_TYPE_OPERATION = 'Operation';
    DeveloperPortal.PORTAL_OBJECT_TYPE_ORGANIZATION = 'Organization';
    DeveloperPortal.PORTAL_OBJECT_TYPE_PLAN = 'Plan';
    DeveloperPortal.PORTAL_OBJECT_TYPE_PRODUCT = 'Product';
    DeveloperPortal.PORTAL_OBJECT_TYPE_SUBSCRIPTION = 'Subscription';
    DeveloperPortal.PORTAL_OBJECT_TYPE_USER_PROFILE = 'UserProfile';
    DeveloperPortal.PORTAL_OBJECT_TYPE_MAPPING = 'Mapping';
    DeveloperPortal.PORTAL_OBJECT_TYPE_SCHEMA = 'Schema';
    DeveloperPortal.SERVER_STATUS_SUCCESS = 'Success';
    DeveloperPortal.SERVER_STATUS_ERROR = 'Error';
    DeveloperPortal.SERVER_STATUS_COMPLETED = 'Completed';
    DeveloperPortal.SERVER_STATUS_PARTIALLY_COMPLETED = 'Partially Completed';
    DeveloperPortal.FIELD_TYPE_TEXT = 'TEXT';
    DeveloperPortal.FIELD_TYPE_RADIO = 'RADIO';
    DeveloperPortal.FIELD_TYPE_CHECK_BOX = 'CHECK_BOX';
    DeveloperPortal.FIELD_TYPE_TEXTAREA = 'TEXTAREA';
    DeveloperPortal.FIELD_TYPE_RELATION = 'RELATION';
    DeveloperPortal.INGORED_FIELD_LIST = ['jform[access]', 'jform[alias]', 'jform[archive]', 'jform[ctime]', 'jform[extime]', 'jform[ftime]', 'Itemid', 'langs'];
    DeveloperPortal.REGEXP_NAME = /([a-zA-Z0-9_]+)/;
    DeveloperPortal.REGEXP_JFORM_NAME = /jform\[([a-zA-Z0-9_]+)\]/;
    DeveloperPortal.REGEXP_JFORM_FIELDS_ID = /jform\[fields\]\[([0-9]+)\]/;
    DeveloperPortal.REGEXP_JFORM_FIELDS_ID_ARRAY = /jform\[fields\]\[([0-9]+)\]\[\]/;
    DeveloperPortal.REGEXP_JFORM_FIELDS_ID_OTHERS = /jform\[fields\]\[([0-9]+)\]\[[^\]].+/;
    DeveloperPortal.PARENT_CONTENT_TYPES = [1, 2, 4, 5, 9, 10];
    DeveloperPortal.NOTIFYING_CONTENT_TYPES = [1, 2, 3, 4, 5, 6, 7, 9, 10, 20, 21];
    DeveloperPortal.ERROR_TYPE_PORTAL_EVENT = 'portal_event';
    DeveloperPortal.ERROR_TYPE_API_KEY = 'api_key';
    DeveloperPortal.KEY_HAS_ERRORS = 'has_errors';
    DeveloperPortal.KEY_ERROR_MESSAGES = 'error_messages';
    DeveloperPortal.ERROR_MESSAGES_ARRAY = [];
    DeveloperPortal.KEY_HAS_WARNINGS = 'has_warnings';
    DeveloperPortal.KEY_WARNING_MESSAGES = 'warning_messages';
    DeveloperPortal.WARNING_MESSAGES_ARRAY = [];
    DeveloperPortal.KEY_HAS_SUCCESS = 'has_success';
    DeveloperPortal.KEY_SUCCESS_MESSAGES = 'success_messages';
    DeveloperPortal.SUCCESS_MESSAGES_ARRAY = [];
     DeveloperPortal.CONTENT_TYPE_MAP = [
        '',
        DeveloperPortal.PORTAL_OBJECT_TYPE_PRODUCT,
        DeveloperPortal.PORTAL_OBJECT_TYPE_API,
        DeveloperPortal.PORTAL_OBJECT_TYPE_GATEWAY,
        DeveloperPortal.PORTAL_OBJECT_TYPE_ENVIRONMENT,
        DeveloperPortal.PORTAL_OBJECT_TYPE_ORGANIZATION,
        DeveloperPortal.PORTAL_OBJECT_TYPE_OPERATION,
        DeveloperPortal.PORTAL_OBJECT_TYPE_PLAN,
        DeveloperPortal.PORTAL_OBJECT_TYPE_USER_PROFILE,
        DeveloperPortal.PORTAL_OBJECT_TYPE_APPLICATION,
        DeveloperPortal.PORTAL_OBJECT_TYPE_SUBSCRIPTION,
        DeveloperPortal.PORTAL_OBJECT_TYPE_KEY
    ];
    DeveloperPortal.CONTENT_TYPE_MAP[20] = DeveloperPortal.PORTAL_OBJECT_TYPE_MAPPING;
    DeveloperPortal.CONTENT_TYPE_MAP[21] = DeveloperPortal.PORTAL_OBJECT_TYPE_SCHEMA;
    DeveloperPortal.DELETION_REDIRECT_URI = [
        '',
        '/products',
        '/apis',
        '/environments',
        '/environments',
        '/organizations',
        '/apis',
        '/products',
        '/organizations',
        '/applications',
        '/subscriptions',
        '/applications'
    ];
    DeveloperPortal.DELETION_REDIRECT_URI[20] = '/mappings';
    DeveloperPortal.DELETION_REDIRECT_URI[21] = '/mappings/schemas';
    DeveloperPortal.REGEXP_EXTRA_ENCODED = /[~!()']/g;
    DeveloperPortal.REGEXP_EXTRA_DECODED = /%7E|%21|%28|%29|%27/g;


    /**
     * Grabbing a Config Variable to send in headers.
     * @returns {string} a string to be used as a header
     */
    DeveloperPortal._encodingGrabber = function() {
   
            var result = null;
              $.ajax(
              {
                url: GLOBAL_CONTEXT_PATH+"index.php?option=com_cobalt&task=ajaxMore.returnData",
                type: 'get',
                dataType: 'json',
                async: false,
                cache: false,
                success: function(data) 
                {
                    result = data.result;
                }
              });

          return result;
    };




    /**
     * Count how many properties one plain object owns.
     *
     * @param {Object} oPlainObject The plain object of which the number of properties is to be counted. If this
     * parameter is omitted the return value will be 0.
     * @returns {Number} The number of properties the plain object owns excluding the inherited properties.
     */
    DeveloperPortal._countPlainObjectSize = function(oPlainObject) {
        var p, size = 0;
        if (oPlainObject) {
            if ( p in oPlainObject) {
                if (oPlainObject.hasOwnProperty(p)) {
                    size++;
                }
            }
        }
        return size;
    };
    
    /**
     * Post an event to the portal engine with certain information.
     *
     * @param {Object} oData The JSON data to submit.
     * @param {Function} fCallback The callback function when the notification is sent out successfully. It will receive
     * one JSON argument which contains the data returned from the server.
     * @param {Function} fErrorback The callback function when the notification is not sent out successfully. It will
     * receive one string argument which contains the error messages.
     * @returns {jqXHR|String} Either a jQuery XMLHttpRequest object when the oData is provided or an error message
     * string otherwise.
     */
    DeveloperPortal._portalEvent = function(oData, fCallback, fErrorback) {
        if (oData) {
            DeveloperPortal._makeAllValuesArray(oData.updatedFields);
            var val = DeveloperPortal._encodingGrabber();
            oData.userId = _USER_ID;
            return $.ajax({
                url: GLOBAL_CONTEXT_PATH + DeveloperPortal.PORTAL_EVENT_URI,
                contentType: 'application/json',
                data: JSON.stringify(oData),
                dataType: 'text',
                headers: {
                    sessionId: _SESSION_ID,
                    "Authorization": "Basic " + btoa(val + ":" + val)
                },
                type: 'post',
                success: function(data, textStatus, jqXHR) {
                    var oJSON = {}, aErrMsg = [], aWarningMsg = [], sSummary = '';
                    try {
                        oJSON = $.parseJSON(data);
                        if(oJSON.status === DeveloperPortal.SERVER_STATUS_SUCCESS || oJSON.status === DeveloperPortal.SERVER_STATUS_COMPLETED) {
                            if(typeof fCallback === 'function') {
                                fCallback(oJSON);
                            }
                        } else if(oJSON.status === DeveloperPortal.SERVER_STATUS_PARTIALLY_COMPLETED || oJSON.status === DeveloperPortal.SERVER_STATUS_ERROR) {
                            sSummary = DeveloperPortal._parsePortalRespSummary(oJSON);
                            if(sSummary !== '') {
                              sSummary += (DeveloperPortal._urlifySupport(oJSON.uuid, PORTAL_RESP_SUMMARY_POSTFIX_UUID) + oJSON.uuid);
                            }
                            DeveloperPortal.storeErrMsgInCookie(sSummary);
                            if(typeof fCallback === 'function') {
                                fCallback(oJSON);
                            }
                        } else {
                          if(typeof fCallback === 'function') {
                              fCallback(oJSON);
                          }
                        }
                    } catch(oE) {
                        aErrMsg.push(oE.message);
                        DeveloperPortal.storeErrMsgInCookie(aErrMsg);
                        if ( typeof fErrorback === 'function') {
                            fErrorback(aErrMsg.join('<br />'));
                        } else {
                            window.location.reload();
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var sUUID = UUID.generate(),
                        sErrMsg = errorThrown + DeveloperPortal._urlifySupport(sUUID, PORTAL_RESP_SUMMARY_POSTFIX_UUID) + sUUID;
                    if(textStatus === 'timeout') {
                        sErrMsg = PORTAL_TIMEOUT_ERROR_MESSAGE;
                    } else if(jqXHR.status === 404 || jqXHR.status === 503 || jqXHR.status=== 0) {
                        sErrMsg = PORTAL_UNREACHABLE_ERROR_MESSAGE;
                    }
                    DeveloperPortal._saveLogInDatabase({
                        log_type: (textStatus ? textStatus : ''),
                        status: jqXHR.status,
                        statusText: jqXHR.statusText,
                        content: jqXHR.responseText,
                        summary: sErrMsg,
                        entity_type: oData.objectType,
                        entity_id: oData.id,
                        event: oData.eventType,
                        event_status: 'error',
                        uuid: sUUID
                    }, function(sErrMsg) {
                        DeveloperPortal.storeErrMsgInCookie([sErrMsg]);
                        if ( typeof fErrorback === 'function') {
                            fErrorback(sErrMsg);
                        } else {
                            window.location.reload();
                        }
                    }, [DeveloperPortal._urlifySupport(sUUID, sErrMsg) + sUUID]);
                }
            });
        } else {
            return 'The record id must be provided.';
        }
    };

    /**
     * Urlify the word "support" in the summary's postfix string.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {String} sUUID The UUID to be used to identify an error message.
     * @param {String} sTemplateStr The template string which contains the place holders to get replaced.
     * @returns {String} A new postfix string with the word "support" being urlified.
     * @private
     */
    DeveloperPortal._urlifySupport = function(sUUID, sTemplateStr) {
      return sTemplateStr.replace(/support/, function(match) {
        return DeveloperPortal._urlify(match, SUPPORT_PAGE_URL + '?prepopulate=1&uuid=' + sUUID, true);
      });
    };

    DeveloperPortal._parseFieldId = function(sFieldName) {
        var rv = '', matches = sFieldName.match(DeveloperPortal.REGEXP_JFORM_FIELDS_ID_OTHERS) || sFieldName.match(DeveloperPortal.REGEXP_JFORM_FIELDS_ID_ARRAY) || sFieldName.match(DeveloperPortal.REGEXP_JFORM_FIELDS_ID) || sFieldName.match(DeveloperPortal.REGEXP_JFORM_NAME) || [];
        if (matches.length) {
            rv = matches[1];
        }
        return rv;
    };
    
    /**
     * Save the error messages when having problem reaching the portal engine into the database.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Object} oData An object of data to be sent out.
     * @param {Function} fCallback The callback function when the server responds.
     * @param {Array} aCallcackArgs An array of parameters to be passed to the callback function.
     */
    DeveloperPortal._saveLogInDatabase = function(oData, fCallback, aCallcackArgs){
        $.ajax({
            url: GLOBAL_CONTEXT_PATH + 'index.php?option=com_cobalt&task=ajaxmore.asgLogs',
            data: oData,
            dataType: 'json',
            type: 'POST',
            complete: function(jqXHR, textStatus) {
                fCallback.apply(null, aCallcackArgs);
            }
        });
    };
    
    /**
     * Get all the error messages out of the portal response including the error messages inside the nested results.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Object} oPortalResp - The portal response which is a JSON object.
     * @returns {Array} An array of error message strings.
     */
    DeveloperPortal._parsePortalRespErrors = function(oPortalResp) {
        var rv = [];
        if(oPortalResp) {
            if(oPortalResp.errors && oPortalResp.errors.length > 0) {
                rv = rv.concat(oPortalResp.errors);
            }
            if(typeof oPortalResp.nestedResults !== 'undefined' && oPortalResp.nestedResults.length) {
                for(i = 0; i < oPortalResp.nestedResults.length; i++) {
                    rv = rv.concat(DeveloperPortal._parsePortalRespErrors(oPortalResp.nestedResults[i]));
                }
            }
        }
        return rv;
    };

    /**
     * Get the summary out of the portal response.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Object} oPortalResp - The portal response which is a JSON object.
     * @returns {string} The summary string if the summary is found. Otherwise, an empty string will be returned.
     * @private
     */
    DeveloperPortal._parsePortalRespSummary = function(oPortalResp) {
        var rv = '';
        if(oPortalResp) {
          if(oPortalResp.summary && oPortalResp.summary.length > 0) {
              rv = oPortalResp.summary;
          }
        }
        return rv;
    };
    
    /**
     * Get all the warning messages out of the portal response including the warning messages inside the nested results.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Object} oPortalResp - The portal response which is a JSON object.
     * @returns {Array} An array of warning message strings. 
     */
    DeveloperPortal._parsePortalRespWarnings = function(oPortalResp) {
        var rv = [], i;
        if(oPortalResp) {
            if(oPortalResp.warnings && oPortalResp.warnings.length > 0) {
                rv = rv.concat(oPortalResp.warnings);
            }
            if(typeof oPortalResp.nestedResults !== 'undefined' && oPortalResp.nestedResults.length) {
                for(i = 0; i < oPortalResp.nestedResults.length; i++) {
                    rv = rv.concat(DeveloperPortal._parsePortalRespWarnings(oPortalResp.nestedResults[i]));
                }
            }
        }
        return rv;
    };

    /**
     * Tell if the paths, which both include the host and the pathname of a URL, are identical. The search strings are not taken into account.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Object} oLocation1 The first location to be compared.
     * @param {Object} oLocation2 The second location to be compared.
     * @returns {Boolean} True if the two paths are identical, false otherwise.
     */
    DeveloperPortal._pathsMatch = function(oLocation1, oLocation2) {
        return (oLocation1.host + oLocation1.pathname) === (oLocation2.host + oLocation2.pathname);
    };

    /**
     * Parse the id of an object out of the given URL.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {String} sUrl The URL string in which the id of an object can be found.
     * @returns {Number} The id of an object.
     */
    DeveloperPortal._parseObjectId = function(sUrl) {
        var rIdTitle = /\/([0-9]+)-[^\/?]+(\?|$)/, rId = /&id=([0-9]+):/,
            aResults = sUrl.match(rIdTitle) || sUrl.match(rId) || [0, 0];
        return parseInt(aResults[1], 10);
    };

    DeveloperPortal._getFieldValues = function(sFormId) {
        sFormId = sFormId || DeveloperPortal.DEFAULT_FORM_ID;

        var oFieldValues = {
            title: $('#' + sFormId + ' input[name="jform[title]"]').val()
        }, aHiddenFields = $('#' + sFormId + ' > input'), aFieldDivs = $('#' + sFormId + ' div[id^="fld-"]'), aFieldsInDiv, sFieldId = '', aFieldValueArray = [], i;

        aHiddenFields.each(function(index, hiddenField) {
            sFieldId = DeveloperPortal._parseFieldId(hiddenField.name);
            if (sFieldId !== '') {
                oFieldValues[sFieldId] = $(hiddenField).val();
            }
        });

        aFieldDivs.each(function(index, div) {
            aFieldsInDiv = $(div).find('input, select, textarea, radio');
            sFieldId = $(div).attr('id').substring($(div).attr('id').indexOf('fld-') + 'fld-'.length + 1);
            aFieldsInDiv.each(function(index, field) {
                if (aFieldsInDiv.length === 1) {
                    oFieldValues[sFieldId] = $(field).val();
                } else if (aFieldsInDiv.length > 1) {
                    if (!( sFieldId in oFieldValues)) {
                        oFieldValues[sFieldId] = [$(field).val()];
                    } else {
                        oFieldValues[sFieldId].push($(field).val());
                    }
                } else {
                    oFieldValues[sFieldId] = null;
                }
            });
        });
        return oFieldValues;
    };

    /**
     * Request for a regular key or an OAuth key for the application specified by the first parameter.
     *
     * @param {Number} nApplicationId The id of the application for which the key is requested.
     * @param {Function} fCallback The callback function when the notification is sent out successfully. It will receive
     * one JSON argument which contains the data returned from the server.
     * @param {Function} fErrorback The callback function when the notification is not sent out successfully. It will
     * receive one string argument which contains the error messages.
     * @returns {jqXHR|String} Either a jQuery XMLHttpRequest object when the record id is provided or an error message
     * string otherwise.
     */
    DeveloperPortal._requestKey = function(nApplicationId, fCallback, fErrorback) {
        var val = DeveloperPortal._encodingGrabber();
        if (nApplicationId) {
            return $.ajax({
                url: GLOBAL_CONTEXT_PATH + DeveloperPortal.REQUEST_KEY_URI,
                data: {
                    "applicationId": nApplicationId
                },
                type: 'get',
                dataType: 'text',
                headers: {
                    sessionId: _SESSION_ID,
                    "Authorization": "Basic " + btoa(val + ":" + val)
                },
                timeout: 30000,
                success: function(data, testStatus, jqXHR) {
                    var oJSON = {}, aErrMsg = [];
                    try {
                        oJSON = $.parseJSON(data);
                        if(oJSON.success) {
                            if(typeof fCallback === 'function') {
                                fCallback(oJSON);
                            }
                        } else {
                            aErrMsg.push(oJSON.message);
                        }
                    } catch(oE) {
                        aErrMsg.push(ERROR_GETTING_API_KEY);
                    }
                    if(aErrMsg.length > 0) {
                        DeveloperPortal.storeErrMsgInCookie(aErrMsg);
                        if ( typeof fErrorback === 'function') {
                            fErrorback(aErrMsg.join('<br />'));
                        } else {
                            window.location.reload();
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var sUUID = UUID.generate(),
                        sErrMsg = errorThrown + DeveloperPortal._urlifySupport(sUUID, PORTAL_RESP_SUMMARY_POSTFIX_UUID) + sUUID;
                    if(textStatus === 'timeout') {
                        sErrMsg = PORTAL_TIMEOUT_ERROR_MESSAGE;
                    } else if(jqXHR.status === 404 || jqXHR.status === 503) {
                        sErrMsg = PORTAL_UNREACHABLE_ERROR_MESSAGE;
                    }
                    DeveloperPortal._saveLogInDatabase({
                        log_type: (textStatus ? textStatus : ''),
                        status: jqXHR.status,
                        statusText: jqXHR.statusText,
                        content: jqXHR.responseText,
                        summary: sErrMsg,
                        entity_type: DeveloperPortal.PORTAL_OBJECT_TYPE_APPLICATION,
                        entity_id: nApplicationId,
                        event: DeveloperPortal.PORTAL_EVENT_TYPE_REQUEST_KEY,
                        event_status: 'error',
                        uuid: sUUID
                    }, function(sErrMsg) {
                        DeveloperPortal.storeErrMsgInCookie([sErrMsg]);
                        if ( typeof fErrorback === 'function') {
                            fErrorback(sErrMsg);
                        } else {
                            window.location.reload();
                        }
                    }, [DeveloperPortal._urlifySupport(sUUID, sErrMsg) + sUUID]);
                }
            });
        } else {
            return 'The application id must be provided.';
        }
    };

    /**
     * Get the form token for the forms on either the front-end or the back-end of the CMS.
     *
     * @param {Object} bBackEnd True if the token to be retrieved is for the form on the back-end. False if the
     * token to be retrieved is for the form on the front-end.
     */
    DeveloperPortal._getFormToken = function(bBackEnd) {
        return _FORM_TOKEN;
    };

    /**
     * Update the status of the related keys
     * @param {Array} aKeyList a list of keys' id
     * @param {Function} fCallback The callback function when the notification is sent out successfully. It will receive
     * one JSON argument which contains the data returned from the server.
     * @param {Function} fErrorback The callback function when the notification is not sent out successfully. It will
     * receive one string argument which contains the error messages.
     */
    DeveloperPortal._disableKeys = function(nApplicationId, aKeyList, fCallback, fErrorback) {
        $.ajax({
            type: 'post',
            data: {
                keyList: aKeyList
            },
            url: GLOBAL_CONTEXT_PATH + 'index.php?option=com_cobalt&task=ajaxmore.updateStatusOfKey',
            success: function(data, textStatus, jqXHR) {
                if ( typeof fCallback === 'function') {
                    fCallback(data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                var sErrMsg = 'The old keys of application ' + nApplicationId + ' are not successfully disabled.';
                DeveloperPortal.storeErrMsgInCookie([sErrMsg]);
                if ( typeof fErrorback === 'function') {
                    fErrorback([sErrMsg, errorThrown].join('<br />'));
                }
                window.location.reload();
            }
        });
    };

    /**
     * Store the requested API key into the database and send a notification to the portal engine.
     *
     * @param {Number} nApplicationId The id of the application for which the API key is requested.
     * @param {String} aOldKeys The API key that the application currently owns.
     * @param {String} sKey The requested API key.
     * @param {String} sSecret The secret of the key if the key is an OAuth key.
     * @param {Function} fCallback The callback function when the notification is sent out successfully. It will receive
     * one JSON argument which contains the data returned from the server.
     * @param {Function} fErrorback The callback function when the notification is not sent out successfully. It will
     * receive one string argument which contains the error messages.
     */
    DeveloperPortal._storeKey = function(nApplicationId, aOldKeys, sKey, sSecret, fCallback, fErrorback) {
        var dForm = $('<form id="keyForm" name="keyForm" enctype="multipart/form-data" method="post"></form>').appendTo('body'),
            sAction = GLOBAL_CONTEXT_PATH + 'index.php/applications/submit/5-applications/11?fand=' + nApplicationId + '&field_id=86',
            sIFrameId = 'iframe_key_form_submission',
            dIFrame = $('<iframe id="' + sIFrameId + '" name="' + sIFrameId + '" style="display: none;" />').appendTo('body'),
            dWindow, sRedirectUrl;

        dForm.attr('action', sAction);
        dForm.attr('target', sIFrameId);
        dForm.append('<input type="hidden" name="task" value="form.save" />');
        dForm.append('<input type="hidden" name="' + DeveloperPortal._getFormToken() + '" value="1" />');
        dForm.append('<input type="hidden" name="jform[title]" value="' + nApplicationId + '_api_key_' + Math.round(Math.random() * 1000) + '" />');
        dForm.append('<input type="hidden" name="jform[fields][82]" value="' + sKey + '" />');
        dForm.append('<input type="hidden" name="jform[fields][107]" value="' + PORTAL_UUID + '" />');

        if(sSecret) {
            dForm.append('<input type="hidden" name="jform[fields][81]" value="oauth" />');
            dForm.append('<input type="hidden" name="jform[fields][83]" value="' + sSecret + '" />');
        } else {
            dForm.append('<input type="hidden" name="jform[fields][81]" value="regular" />');
        }
        dForm.append('<input type="hidden" name="jform[fields][85]" value="Active" />');
        dForm.append('<input type="hidden" name="jform[fields][86]" value="' + nApplicationId + '" />');
        dForm.append('<input type="hidden" name="jform[ucatid]" value="0" />');
        dForm.append('<input type="hidden" name="jform[id]" value="0" />');
        dForm.append('<input type="hidden" name="jform[section_id]" value="5" />');
        dForm.append('<input type="hidden" name="jform[type_id]" value="11" />');
        dForm.append('<input type="hidden" name="jform[published]" value="1" />');

        dIFrame.on('load', function(oEvent) {

            dWindow = dIFrame[0].contentWindow;
            sRedirectUrl = dWindow.location.href;

            if(dWindow.location.href == window.location.href) {
                // The path of the iframe being identical to the one of the main window means the page was not redirected due to some errors occurred.
                var sErrMsg = 'API key ' + sKey + (sSecret ? ' with the secret "' + sSecret + '"' : '') + ' of application ' + nApplicationId + ' is not successfully stored in the database.';
                DeveloperPortal.storeErrMsgInCookie([sErrMsg]);
                if ( typeof fErrorback === 'function') {
                    fErrorback([sErrMsg, GENERIC_ERROR_MESSAGE].join('<br />'));
                }
                window.location.reload();
            } else {
                DeveloperPortal.sendUpdateNotification(nApplicationId, DeveloperPortal.CONTENT_TYPE_MAP[9], {
                    87: aOldKeys
                }, function() {
                    window.location.reload();
                }, fErrorback);
            }

        });

        dForm.submit();
    };

    /**
     * Tell if the two arrays passed in as the first two arguments are equal or not.
     *
     * @param {Array} aOne One array to be compared.
     * @param {Array} aTwo The other array to be compared to.
     * @returns {Boolean} True if the two arrays are equal which means they have the same number and order of the
     * same set of elements. False otherwise.
     */
    DeveloperPortal.arrayEqual = function(aOne, aTwo) {
        var rv = false, i;
        if ($.isArray(aOne) && $.isArray(aTwo)) {
            if (aOne.length === aTwo.length) {
                if(aOne.length === 0) {
                    rv = true;
                } else {
                    aOne.sort();
                    aTwo.sort();
                    for ( i = 0; i < aOne.length; i++) {
                        if (aOne[i] === aTwo[i]) {
                            rv = true;
                        } else {
                            rv = false;
                            break;
                        }
                    }
                }
            }
        }
        return rv;
    };

    /**
     * Make sure all the values in the object are arrays. If not put them in an array with them as the only element.
     *
     * @param {Object} oUpdatedFields The object that's to be checked.
     * @returns {Object} A object whose values are all arrays.
     */
    DeveloperPortal._makeAllValuesArray = function(oUpdatedFields) {
        if (oUpdatedFields) {
            var p;
            for (p in oUpdatedFields) {
                if (oUpdatedFields.hasOwnProperty(p)) {
                    if (oUpdatedFields[p] === undefined || oUpdatedFields[p] === null) {
                        oUpdatedFields[p] = [];
                    } else if (!$.isArray(oUpdatedFields[p])) {
                        oUpdatedFields[p] = [oUpdatedFields[p]];
                    }
                }
            }
        }
    };

    /**
     * Make an HTML hyper link out of a plain text.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {String} sText The text to be used as the label of the hyper link.
     * @param {String} sUrl The URL of the hyper link.
     * @param {String} bSameWindow True to open the hyper link in the same window. False to open it in a new window.
     * @returns {String} The HTML hyper link.
     * @private
     */
    DeveloperPortal._urlify = function(sText, sUrl, bSameWindow) {
      if(sText && sUrl) {
          return '<a href="' + sUrl + '"' + (bSameWindow ? '' : ' target="_blank"') + '>' + sText + '</a>';
      } else {
          return '';
      }
    };

    /**
     * Remember the initial values of all the fields in a form for the sake of a later dirty state check.
     *
     * @param {String} sFormId The id of the form of which the fields' values are to be remembered. If this is
     * omitted the default form id will be used.
     * @returns {Object} An key-value object containing all the fields and their values in the form specified by the
     * parameter.
     */
    DeveloperPortal.getFieldValues = function(sFormId) {
        sFormId = sFormId || DeveloperPortal.DEFAULT_FORM_ID;
        var oFieldValues = {
            title: {
                type: 'input',
                value: $('#' + sFormId + ' #jform_title').val()
            }
        };
        $('#' + sFormId + ' *[id^="fld-"]').each(function(index, fieldDiv) {
            //@formatter:off
            var fields = $(fieldDiv).find("input, textarea, select, radio, checkbox"),
            isParent = fields.parents('div[id^="parent_list"]').length,
            fieldId = $(fieldDiv).attr('id'), o, i;
            //@formatter:on

            oFieldValues[fieldId] = {
                label: $(fieldDiv).find('label').text().trim()
            };

            if (fields.length) {
                if (isParent) {
                    if ($(fieldDiv).find('div[id^="parent_list"]').length) {
                        oFieldValues[fieldId].type = DeveloperPortal.FIELD_TYPE_RELATION;
                        oFieldValues[fieldId].value = {};
                        $(fieldDiv).find('div[id^="parent_list"]').find('div.list-item').each(function(id2, listItem) {
                            oFieldValues[fieldId].value[$(listItem).attr('rel')] = 1;
                        });
                    }
                } else {
                    oFieldValues[fieldId].type = fields.get(0).tagName;
                    if (fields.length > 1) {
                        o = {};
                        for ( i = 0; i < fields.length; i++) {
                            o[fields[i].id] = fields[i].value;
                        }
                        oFieldValues[fieldId].value = o;
                    } else {
                        oFieldValues[fieldId].value = fields.get(0).value;
                    }
                }
            } else {
                oFieldValues[fieldId].type = DeveloperPortal.FIELD_TYPE_RELATION;
                oFieldValues[fieldId].value = {};
            }
        });
        return oFieldValues;
    };
    
    /**
     * Verify the form token in order to tell whether the session is still valid.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Function} fCallback The callback function invoked when the verification succeeded.
     * @param {Function} fErrorback The callback function invoked when the verification failed.
     */
    DeveloperPortal.verifyFormToken = function(fCallback, fErrorback) {
        var sOldToken = DeveloperPortal._getFormToken();
        $.get(GLOBAL_CONTEXT_PATH + 'index.php/?option=com_cobalt&task=ajaxmore.getFormToken', null, function(data, textStatus, jqXHR) {
            if(sOldToken === data.result) {
                fCallback(data);
            } else {
                fErrorback(FORM_TOKENS_DIFFERENT);
            }           
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            fErrorback(jqXHR.status + ', ' + jqXHR.statusText);
        });
    };

    /**
     * Request an API key for an application.
     *
     * @param {Number} nApplicationId The id of the application for which the API key is requested.
     * @param {Array} aOldKeys The API key that the application currently owns.
     * @param {Number} nActiveKeyCount The number of active keys of the application.
     * @param {Function} fCallback The callback function when the notification is sent out successfully. It will receive
     * one JSON argument which contains the data returned from the server.
     * @param {Function} fErrorback The callback function when the notification is not sent out successfully. It will
     * receive one string argument which contains the error messages.
     */
    DeveloperPortal.requestKey = function(nApplicationId, aOldKeys, nActiveKeyCount, fCallback, fErrorback) {
        DeveloperPortal.verifyFormToken(function(data) {
            if (nActiveKeyCount === 0 || confirm('If you continue, the old key will be disabled. Are you sure?', 'Are you sure?')) {
                DeveloperPortal._requestKey(nApplicationId, function(data) {
                    if (data) {
                        DeveloperPortal._disableKeys(nApplicationId, aOldKeys, function() {
                            DeveloperPortal._storeKey(nApplicationId, aOldKeys, data.apiKey.key, data.apiKey.isOauth ? data.apiKey.secret : '', function(result) {
                                if(typeof fCallback === 'function') {
                                    fCallback();
                                }
                            }, fErrorback);
                        }, fErrorback);
                    } else {
                        DeveloperPortal.storeErrMsgInCookie([NO_VALID_JSON_DATA]);
                        if ( typeof fErrorback === 'function') {
                            fErrorback(NO_VALID_JSON_DATA);
                        }
                        window.location.reload();
                    }
                }, fErrorback);
            }
        }, function(errorThrown) {
            Joomla.showError([errorThrown]);
        });
    };

    /**
     * Send a create notification to the portal engine.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Number} nId The id of the object that was created.
     * @param {String} sObjectType The content type of the object that was created.
     * @param {Function} fCallback The callback function when the notification is sent out successfully. It will receive
     * one JSON argument which contains the data returned from the server.
     * @param {Function} fErrorback The callback function when the notification is not sent out successfully. It will
     * receive one string argument which contains the error messages.
     */
    DeveloperPortal.sendCreateNotification = function(nId, sObjectType, fCallback, fErrorback) {
        if (nId && sObjectType) {
            var rv = DeveloperPortal._portalEvent({
                id: nId,
                eventType: DeveloperPortal.PORTAL_EVENT_TYPE_CREATE,
                objectType: sObjectType
            }, fCallback, fErrorback);
            if ( typeof rv === 'string') {
                if ( typeof fErrorback === 'function') {
                    fErrorback(rv);
                }
            }
        } else {
            if ( typeof fErrorback === 'function') {
                fErrorback('nId or sObjectType was not provided.');
            }
        }
    };

    /**
     * Send an update notification to the portal engine.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Number} nId The id of the object that was updated.
     * @param {String} sObjectType The content type of the object that was updated.
     * @param {Object} oUpdatedFields Some of the fields that were updated in the object.
     * @param {Function} fCallback The callback function when the notification is sent out successfully. It will receive
     * one JSON argument which contains the data returned from the server.
     * @param {Function} fErrorback The callback function when the notification is not sent out successfully. It will
     * receive one string argument which contains the error messages.
     */
    DeveloperPortal.sendUpdateNotification = function(nId, sObjectType, oUpdatedFields, fCallback, fErrorback) {
        if (nId && sObjectType && oUpdatedFields) {
            var rv = DeveloperPortal._portalEvent({
                id: nId,
                eventType: DeveloperPortal.PORTAL_EVENT_TYPE_UPDATE,
                objectType: sObjectType,
                updatedFields: oUpdatedFields
            }, fCallback, fErrorback);
            if ( typeof rv === 'string') {
                if ( typeof fErrorback === 'function') {
                    fErrorback(rv);
                }
            }
        } else {
            if ( typeof fErrorback === 'function') {
                fErrorback('nId or sObjectType was not provided.');
            }
        }
    };

    /**
     * Send a delete notification to the portal engine.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Number} nId The id of the object that was deleted.
     * @param {String} sObjectType The content type of the object that was deleted.
     * @param {Function} fCallback The callback function when the notification is sent out successfully. It will receive
     * one JSON argument which contains the data returned from the server.
     * @param {Function} fErrorback The callback function when the notification is not sent out successfully. It will
     * receive one string argument which contains the error messages.
     */
    DeveloperPortal.sendDeleteNotification = function(nId, sObjectType, fCallback, fErrorback) {
        if (nId && sObjectType) {
            var rv = DeveloperPortal._portalEvent({
                id: nId,
                eventType: DeveloperPortal.PORTAL_EVENT_TYPE_DELETE,
                objectType: sObjectType
            }, fCallback, fErrorback);
            if ( typeof rv === 'string') {
                if ( typeof fErrorback === 'function') {
                    fErrorback(rv);
                }
            }
        } else {
            if ( typeof fErrorback === 'function') {
                fErrorback('nId or sObjectType was not provided.');
            }
        }
    };

    /**
     * Get all the fields in the form specified by sFormId and put all its data into one FormData object and return the
     * object.
     *
     * @param {String} sFormId Optional. The id of the form of which the fields are to be gone through. If this is
     * omitted the default form id will be used.
     * @param {Function} fInspect Optional. A function which will be run for every field of the form. It will be given
     * two arguments. The first argument is the name of a field and the second argument is the value of a field.
     * @returns {Object} A FormData object containing all data in the form specified by sFormId.
     */
    DeveloperPortal.getFormData = function(sFormId, fInspect) {
        if (!sFormId) {
            sFormId = DeveloperPortal.DEFAULT_FORM_ID;
        } else if ( typeof sFormId === 'function') {
            fInspect = sFormId;
            sFormId = DeveloperPortal.DEFAULT_FORM_ID;
        } else if ( typeof sFormId !== 'string') {
            sFormId = DeveloperPortal.DEFAULT_FORM_ID;
        }
        var formData = new FormData();
        $('#' + sFormId + ' input[type!="button"], #' + sFormId + ' textarea, #' + sFormId + ' select, #' + sFormId + ' radio, #' + sFormId + ' checkbox').each(function(index, item) {
            if (item.name === 'task') {
                formData.append(item.name, 'form.save');
            } else {
                formData.append(item.name, $(item).val());
            }
            if ( typeof fInspect === 'function') {
                fInspect(item.name, $(item).val());
            }
        });
        return formData;
    };

    /**
     * Submit the form by Ajax instead of simple form submission.
     *
     * @param {String} sTask Required. The task parameter which will be used by Joomla for form submission.
     * @param {Function} fCallback Optional. The callback function when the form is successfully submitted. It'll receive the id of the object and a redirect URL as its arguments.
     * @param {Function} fErrorback Optional. The callback function when the form is not successfully submitted. It'll receive the error that's thrown as its only argument.
     */
    DeveloperPortal.submitForm = function(sTask, fCallback, fErrorback) {
        var bsValid = Joomla.validate(), sIFrameId = 'iframe_form_submission', dForm = $('#adminForm'),
            dIFrame = $('<iframe id="' + sIFrameId + '" name="' + sIFrameId + '" style="display: none;" />').appendTo('body'),
            nTypeId = parseInt(dForm.find('input[name="jform[type_id]"]').val(), 10),
            bCreation = dForm.find('input[name="jform[id]"][value="0"]').length === 1,
            dWindow, sRedirectUrl, nRecordId, sUserGroupUrl, aOldKeys = typeof ApplicationForm !== 'undefined' ? ApplicationForm.aOldKeys : [],
            nActiveKeyCount = typeof ApplicationForm !== 'undefined' ? ApplicationForm.nActiveKeyCount : 0,
            oldOAuth = typeof ApplicationForm !== 'undefined' ? ApplicationForm.oauth : '',
            newOAuth = DeveloperPortal.getRadioButtonsValue('jform[fields][64]');

        if (bsValid === true) {
            if(typeof dForm.onsubmit === 'function') {
                dform.onsubmit();
            }
            if(typeof dForm.fireEvent === 'function') {
                dForm.fireEvent('submit');
            }
            dForm.find('input[name="task"]').val(sTask);
            dForm.attr('target', sIFrameId);
            dIFrame.on('load', function(oEvent) {

                dWindow = oEvent.target.contentWindow;
                sRedirectUrl = dWindow.location.href;

                if(DeveloperPortal._pathsMatch(dWindow.location, window.location) || !dWindow.RecordTemplate || dWindow.RecordTemplate.nRecordId === undefined) {
                    // The path of the iframe being identical to the one of the main window means the page was not redirected due to some errors occurred.
                    Joomla.showError(DeveloperPortal.getIFrameErrMsgArray(oEvent.target));
                } else {
                    nRecordId = dWindow.RecordTemplate.nRecordId;

                    if($.inArray(nTypeId, DeveloperPortal.NOTIFYING_CONTENT_TYPES) > -1) {
                        if(bCreation) {
                          if(nTypeId === 5) {
                            sUserGroupUrl = GLOBAL_CONTEXT_PATH + 'index.php?option=com_cobalt&task=ajaxmore.createUserGroups&org_id=' + nRecordId;
                            $.ajax({
                              url: sUserGroupUrl,
                              complete: function(jqXHR) {
                                DeveloperPortal.sendCreateNotification(nRecordId, DeveloperPortal.CONTENT_TYPE_MAP[nTypeId], function(data) {
                                  if(fCallback) {
                                    fCallback(nRecordId, sRedirectUrl);
                                  } else {
                                      window.location.href = sRedirectUrl;
                                  }
                                }, function(errorThrown) {
                                  if(fErrorback) {
                                      fErrorback(sRedirectUrl, errorThrown);
                                  } else {
                                     window.location.href = sRedirectUrl;
                                  }
                                });
                              }
                            });
                          } else if(nTypeId === 6) {
                            DeveloperPortal.sendUpdateNotification(parent_api_id, DeveloperPortal.PORTAL_OBJECT_TYPE_API, {
                                31: []
                            }, function(data) {
                                if(fCallback) {
                                    fCallback(nRecordId, sRedirectUrl);
                                } else {
                                    window.location.href = sRedirectUrl;
                                }
                            }, function(errorThrown) {
                                if(fErrorback) {
                                    fErrorback(sRedirectUrl);
                                } else {
                                    window.location.href = sRedirectUrl;
                                }
                            });
                          } else {
                            DeveloperPortal.sendCreateNotification(nRecordId, DeveloperPortal.CONTENT_TYPE_MAP[nTypeId], function(data) {
                              if(fCallback) {
                                fCallback(nRecordId, sRedirectUrl);
                              } else {
                                window.location.href = sRedirectUrl;
                              }
                            }, function(errorThrown) {
                              if(fErrorback) {
                                fErrorback(sRedirectUrl, errorThrown);
                              } else {
                                window.location.href = sRedirectUrl;
                              }
                            });
                          }
                        } else {
                            if(nTypeId === 9 && oldOAuth !== newOAuth && nActiveKeyCount > 0) {
                                DeveloperPortal._disableKeys(nRecordId, aOldKeys, function() {
                                    if(typeof oUpdatedFields === 'undefined') {
                                        if(fCallback) {
                                            fCallback(nRecordId, sRedirectUrl);
                                        } else {
                                            window.location.href = sRedirectUrl;
                                        }
                                    } else {
                                        DeveloperPortal.sendUpdateNotification(nRecordId, DeveloperPortal.CONTENT_TYPE_MAP[nTypeId], oUpdatedFields, function(data) {
                                            if(fCallback) {
                                                fCallback(nRecordId, sRedirectUrl);
                                            } else {
                                                window.location.href = sRedirectUrl;
                                            }
                                        }, function(errorThrown) {
                                            if(fErrorback) {
                                                fErrorback(sRedirectUrl, errorThrown);
                                            } else {
                                                window.location.href = sRedirectUrl;
                                            }
                                        });
                                    }
                                }, function() {
                                    if(typeof oUpdatedFields === 'undefined') {
                                        if(fCallback) {
                                            fCallback(nRecordId, sRedirectUrl);
                                        } else {
                                            window.location.href = sRedirectUrl;
                                        }
                                    } else {
                                        DeveloperPortal.sendUpdateNotification(nRecordId, DeveloperPortal.CONTENT_TYPE_MAP[nTypeId], oUpdatedFields, function(data) {
                                            if(fCallback) {
                                                fCallback(nRecordId, sRedirectUrl);
                                            } else {
                                                window.location.href = sRedirectUrl;
                                            }
                                        }, function(errorThrown) {
                                            if(fErrorback) {
                                                fErrorback(sRedirectUrl, errorThrown);
                                            } else {
                                                window.location.href = sRedirectUrl;
                                            }
                                        });
                                    }
                                });
                            } else {
                                if(typeof oUpdatedFields === 'undefined') {
                                    if(fCallback) {
                                        fCallback(nRecordId, sRedirectUrl);
                                    } else {
                                        window.location.href = sRedirectUrl;
                                    }
                                } else {
                                    DeveloperPortal.sendUpdateNotification(nRecordId, DeveloperPortal.CONTENT_TYPE_MAP[nTypeId], oUpdatedFields, function(data) {
                                        if(fCallback) {
                                            fCallback(nRecordId, sRedirectUrl);
                                        } else {
                                            window.location.href = sRedirectUrl;
                                        }
                                    }, function(errorThrown) {
                                        if(fErrorback) {
                                            fErrorback(sRedirectUrl, errorThrown);
                                        } else {
                                            window.location.href = sRedirectUrl;
                                        }
                                    });
                                }
                            }
                        }
                    } else {
                        if(fCallback) {
                            fCallback(nRecordId, sRedirectUrl);
                        } else {
                            window.location.href = sRedirectUrl;
                        }
                    }
                }
            });

            dForm.submit();
        } else {
            Joomla.showError(bsValid);
        }
    };

    /**
     * Archive a record and send delete notification to the portal engine after successful deletion.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Number} nObjId The id of the object to be deleted.
     * @param {Number} nTypeId The id of the type of the object to be deleted.
     */
    DeveloperPortal.archiveRecord = function(nRecId, nTypeId) {
        // confirm dialog box
        if (confirm('This will permanently delete the record, are you sure?') == true) {
            $.ajax({
                url: GLOBAL_CONTEXT_PATH + 'index.php',
                data: {
                    option: 'com_cobalt',
                    task: 'ajaxmore.archiveRecord',
                    rec_id: nRecId,
                    type_id: nTypeId
                },
                dataType: 'json',
                success: function(data, textStatus, jqXHR) {
                    if(nTypeId === 8) {
                        $.post('index.php?options=com_cobalt&task=ajaxmore.disabledUser', {
                            userfile_id: nRecId
                        }, function(res){
                            if(!res.success){
                                DeveloperPortal.storeErrMsgInCookie([FAILED_TO_DEACTIVATE_USER_AFTER_ARCHIVE_USERPROFILE]);
                            }
                            window.location.href = GLOBAL_CONTEXT_PATH + 'index.php' + DeveloperPortal.DELETION_REDIRECT_URI[nTypeId];
                        }, 'json');
                    } else if (nTypeId === 6){
                        DeveloperPortal.sendUpdateNotification(parent_api_id,DeveloperPortal.PORTAL_OBJECT_TYPE_API,{'31':[]}, function(data) {
                            window.location.href = GLOBAL_CONTEXT_PATH + 'index.php' + DeveloperPortal.DELETION_REDIRECT_URI[nTypeId];
                        }, function(errorThrown) {
                            window.location.href = GLOBAL_CONTEXT_PATH + 'index.php' + DeveloperPortal.DELETION_REDIRECT_URI[nTypeId];
                        } );
                    } else if(nTypeId === 3) {
                        DeveloperPortal.sendUpdateNotification(parent_env_id, DeveloperPortal.PORTAL_OBJECT_TYPE_ENVIRONMENT, {'15': [nRecId]}, function(data) {
                            window.location.href = GLOBAL_CONTEXT_PATH + 'index.php' + DeveloperPortal.DELETION_REDIRECT_URI[nTypeId];
                        }, function(errorThrown) {
                            window.location.href = GLOBAL_CONTEXT_PATH + 'index.php' + DeveloperPortal.DELETION_REDIRECT_URI[nTypeId];
                        });
                    } else {
                        DeveloperPortal.sendDeleteNotification(nRecId, DeveloperPortal.CONTENT_TYPE_MAP[nTypeId], function(data) {
                            window.location.href = GLOBAL_CONTEXT_PATH + 'index.php' + DeveloperPortal.DELETION_REDIRECT_URI[nTypeId];
                        }, function(errorThrown) {
                            window.location.href = GLOBAL_CONTEXT_PATH + 'index.php' + DeveloperPortal.DELETION_REDIRECT_URI[nTypeId];
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrow) {
                    Joomla.showError([ARCHIVE_FAILED]);
                }
            });
        };
    };


    /**
     * Get the value of a group of radio buttons of which share the value of the "name" attribute.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     * @param {String} sRadioButtonsName The name that's shared by all the radio buttons.
     * @returns {String} The value of the radio button that's checked.
     */
    DeveloperPortal.getRadioButtonsValue = function(sRadioButtonsName) {
        var rv = '';
        $('input[name="' + sRadioButtonsName + '"][type="radio"]').each(function(index, item) {
            if(item.checked === true) {
                rv = item.value;
            }
        });
        return rv;
    };
    
    /**
     * Get out the data which are delimited by semicolons from the cookie of the browser and put them into a plain JavaScript object as key-value pairs so that they can be easily accessed by keys. 
     * 
     * @author Kevin Li<huali@tibco-support.com>
     * @returns {Object} A plain JavaScript object containing key-value pairs of the data extracted from the browser's cookie.
     */
    DeveloperPortal.getCookieValues = function() {
        var rv = {}, sCookie = document.cookie, aCookies, i, sCookieData;
        if(sCookie.length > 0) {
            aCookies = sCookie.split(/\s*;\s*/);
            for(i = 0; i < aCookies.length; i++) {
                sCookieData = aCookies[i];
                if(sCookieData.indexOf('=') > -1) {
                    rv[sCookieData.substring(0, sCookieData.indexOf('='))] = decodeURIComponent(sCookieData.substring(sCookieData.indexOf('=') + 1));
                } else {
                    rv[sCookieData] = '';
                }
            }
        }
        return rv;
    };
    
    /**
     * Set a key-value pair into the cookie of the browser.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     * @param {String} sKey - The key of the key-value pair which is to be set.
     * @param {String} sValue - The value of the key-value pair which is to be set.
     * @param {Number} [nDaysToLive=1] - The max age of this key-value pair. The default value is 1.
     */
    DeveloperPortal.setCookieValue = function(sKey, sValue, nDaysToLive) {
        if(sKey !== undefined && sKey !== null && sValue !== undefined && sValue !== null) {
            if(typeof nDaysToLive !== 'number') {
                nDaysToLive = 1;
            }
            document.cookie = sKey + '=' + encodeURIComponent(sValue) + '; max-age=' + (nDaysToLive * 24 * 60 * 60) + '; path=/';
        }
    };
    
    /**
     * Remove the key-value pair whose "key" is specified by sKey.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     * @param {String} sKey - The key of the key-value pair which is to be removed.
     */
    DeveloperPortal.removeCookieValue = function(sKey) {
        if(sKey !== undefined && sKey !== null) {
            DeveloperPortal.setCookieValue(sKey, '', 0);
        }
    };
    
    /**
     * Store the error messages in the cookie of the browser.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     * @param {Array} aErrMsg - An array of error messages to be saved in the cookie of the browser.
     */
    DeveloperPortal.storeErrMsgInCookie = function(aErrMsg) {
        if(aErrMsg !== undefined && aErrMsg !== null && aErrMsg.length > 0) {
            DeveloperPortal.ERROR_MESSAGES_ARRAY = DeveloperPortal.ERROR_MESSAGES_ARRAY.concat(aErrMsg);
            DeveloperPortal.setCookieValue(DeveloperPortal.KEY_HAS_ERRORS, 'true');
            DeveloperPortal.setCookieValue(DeveloperPortal.KEY_ERROR_MESSAGES, DeveloperPortal.ERROR_MESSAGES_ARRAY.join('<br />'));
        }
    };
    
    /**
     * Store the warning messages in the cookie of the browser.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     * @param {String} aWarningMsg - An array of warning messages to be saved in the cookie of the browser.
     */
    DeveloperPortal.storeWarningMsgInCookie = function(aWarningMsg) {
        if(aWarningMsg !== undefined && aWarningMsg !== null && aWarningMsg.length > 0) {
            DeveloperPortal.WARNING_MESSAGES_ARRAY = DeveloperPortal.WARNING_MESSAGES_ARRAY.concat(aWarningMsg);
            DeveloperPortal.setCookieValue(DeveloperPortal.KEY_HAS_WARNINGS, 'true');
            DeveloperPortal.setCookieValue(DeveloperPortal.KEY_WARNING_MESSAGES, DeveloperPortal.WARNING_MESSAGES_ARRAY.join('<br />'));
        }
    };
    
    /**
     * Store the success messages in the cookie of the browser.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     * @param {String} aSuccessMsg - An array of success messages to be saved in the cookie of the browser.
     */
    DeveloperPortal.storeSuccessMsgInCookie = function(aSuccessMsg) {
        if(aSuccessMsg !== undefined && aSuccessMsg !== null && aSuccessMsg.length > 0) {
            DeveloperPortal.SUCCESS_MESSAGES_ARRAY = DeveloperPortal.SUCCESS_MESSAGES_ARRAY.concat(aSuccessMsg);
            DeveloperPortal.setCookieValue(DeveloperPortal.KEY_HAS_SUCCESS, 'true');
            DeveloperPortal.setCookieValue(DeveloperPortal.KEY_SUCCESS_MESSAGES, DeveloperPortal.SUCCESS_MESSAGES_ARRAY.join('<br />'));
        }
    };
    
   /**
     * Remove the error messages from the cookie of the browser.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     */
    DeveloperPortal.removeErrMsgFromCookie = function() {
        DeveloperPortal.setCookieValue(DeveloperPortal.KEY_HAS_ERRORS, 'false');
        DeveloperPortal.removeCookieValue(DeveloperPortal.KEY_ERROR_MESSAGES);
        DeveloperPortal.ERROR_MESSAGES_ARRAY = [];
    };
    
    /**
     * Remove the warning messages from the cookie of the browser.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     */
    DeveloperPortal.removeWarningMsgFromCookie = function() {
        DeveloperPortal.setCookieValue(DeveloperPortal.KEY_HAS_WARNINGS, 'false');
        DeveloperPortal.removeCookieValue(DeveloperPortal.KEY_WARNING_MESSAGES);
        DeveloperPortal.WARNING_MESSAGES_ARRAY = [];
    };
    
    /**
     * Remove the success messages from the cookie of the browser.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     */
    DeveloperPortal.removeSuccessMsgFromCookie = function() {
        DeveloperPortal.setCookieValue(DeveloperPortal.KEY_HAS_SUCCESS, 'false');
        DeveloperPortal.removeCookieValue(DeveloperPortal.KEY_SUCCESS_MESSAGES);
        DeveloperPortal.SUCCESS_MESSAGES_ARRAY = [];
    };
    
   /**
     * Get all the error messages that are shown on the top of the page inside an iframe element.
     * 
     * @author Kevin Li<huali@tibco-support.com>
     * @param {DOM} dIFrame The iframe element from which the error messages are to be extracted.
     * @returns {Array} An array of strings of error messages.
     */
    DeveloperPortal.getIFrameErrMsgArray = function(dIFrame) {
        var rv = [], dParagraphs;
        if(dIFrame.contentWindow) {
            dParagraphs = dIFrame.contentWindow.jQuery('div#system-message-container div.alert > div > p');
            dParagraphs.each(function() {
                rv.push($(this).text());
            });
        }
        return rv;
    };

    DeveloperPortal.approvePublish = function(record_id,is_to_show) {

        $.post('index.php?option=com_cobalt&task=ajaxmore.approvepublish',{"record_id":record_id,"is_to_show":is_to_show},
          function(res){
              if(res.success == 1){
                window.location.reload();
              }else{
                window.location.reload();
              }
          },'json');
    };

    DeveloperPortal.resync = function(id, objectType) {
      var oData = {
          'id': id,
          'eventType': "Resync",
          'objectType': objectType
        },
        alert = jQuery('.btn.btn-resync').next().html();
      DeveloperPortal._portalEvent(oData, 
        function(){
          jQuery("#system-message-container").html(alert).fadeIn();
        }, function(){
          jQuery("#system-message-container").html(alert).fadeIn();
        }
      );
    };
    
    /**
     * Initialize the Cobalt's boolean field with IE9 support.
     * 
     * @param {String} sYes The id of the "Yes" button.
     * @param {String} sNo The id of the "No" button.
     */
    DeveloperPortal.yesno = function(sYes, sNo) {
        var y = $(sYes), n = $(sNo);
        y.on('click', function() {
            y.addClass('btn-success');
            n.removeClass('btn-danger');
            n.next('input[type="radio"]').removeAttr('checked');
            y.prev('input[type="radio"]').attr('checked', true);
        });
        n.on('click', function() {
            n.addClass('btn-danger');
            y.removeClass('btn-success');
            y.prev('input[type="radio"]').removeAttr('checked');
            n.next('input[type="radio"]').attr('checked', true);
        });
        
        if(y.prev('input:checked[type="radio"]').length > 0) {
            y.addClass('btn-success');
        }
        if(n.next('input:checked[type="radio"]').length > 0) {
            n.addClass('btn-danger');
        }
    };


    /**
     * Encode the input string in the same way as the java.net.URLEncoder.encode() of Java SE 7 does.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {String} sInput The input string which is to be encoded. If this is not a string an empty string
     * will be returned.
     * @returns {String} The encoded string.
     */
    DeveloperPortal.java7EncodeURIComponent = function(sInput) {
        var ret = '';

        if(typeof sInput === 'string') {

            ret = encodeURIComponent(sInput)
                .replace(DeveloperPortal.REGEXP_EXTRA_ENCODED, function(char) {

                    return '%' + char.charCodeAt(0).toString(16).toUpperCase();
                })
                .replace(/%20/g, '+');
        }

        return ret;
    };

    /**
     * Decode the input string in the same way as the java.net.URLDecoder.decode() of Java SE 7 does.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @param {String} sInput The input string which is to be decoded. If this is not a string an empty string
     * will be returned.
     * @returns {String} The decoded string.
     */
    DeveloperPortal.java7DecodeURIComponent = function(sInput) {
        var ret = '';

        if(typeof sInput === 'string') {
            ret = decodeURIComponent(sInput
                .replace(DeveloperPortal.REGEXP_EXTRA_DECODED, function(char) {
                    return String.fromCharCode(parseInt(char.substr(1), 16));
                })
                .replace(/\+/g, ' '));
        }

        return ret;
    }




    /**
     * Delete a record and send delete notification to the portal engine after successful deletion.
     *
     * @author Vivian Ma<xima@tibco-support.com>
     * @param {Number} nObjId The id of the object to be deleted.
     * @param {Number} nTypeId The id of the type of the object to be deleted.
     */
    DeveloperPortal.deleteRecord = function(nRecId, nTypeId) {
        // confirm dialog box
        if (confirm('This will permanently delete the record, are you sure?') == true) {
           $.ajax({
                url: GLOBAL_CONTEXT_PATH + 'index.php',
                data: {
                    option: 'com_cobalt',
                    task: 'ajaxmore.deleteRecord',
                    id: nRecId,
                    nTypeId:nTypeId
                },
                dataType: 'json',
                success: function(data, textStatus, jqXHR) {
                    if(data.success===1){
                        DeveloperPortal.storeSuccessMsgInCookie([DELETE_SUCCESS]);
                        if (nTypeId === 6){
                            DeveloperPortal.sendUpdateNotification(parent_api_id,DeveloperPortal.PORTAL_OBJECT_TYPE_API,{'31':[]}, function(data) {
                                window.location.href = GLOBAL_CONTEXT_PATH + 'index.php' + DeveloperPortal.DELETION_REDIRECT_URI[nTypeId];
                            }, function(errorThrown) {
                                window.location.href = GLOBAL_CONTEXT_PATH + 'index.php' + DeveloperPortal.DELETION_REDIRECT_URI[nTypeId];
                            } );
                        } else if(nTypeId === 3) {
                            DeveloperPortal.sendUpdateNotification(parent_env_id, DeveloperPortal.PORTAL_OBJECT_TYPE_ENVIRONMENT, {'15': [nRecId]}, function(data) {
                                window.location.href = GLOBAL_CONTEXT_PATH + 'index.php' + DeveloperPortal.DELETION_REDIRECT_URI[nTypeId];
                            }, function(errorThrown) {
                                window.location.href = GLOBAL_CONTEXT_PATH + 'index.php' + DeveloperPortal.DELETION_REDIRECT_URI[nTypeId];
                            });
                        } else {
                            DeveloperPortal.sendDeleteNotification(nRecId, DeveloperPortal.CONTENT_TYPE_MAP[nTypeId], function(data) {
                                window.location.href = GLOBAL_CONTEXT_PATH + 'index.php' + DeveloperPortal.DELETION_REDIRECT_URI[nTypeId];
                            }, function(errorThrown) {
                                window.location.href = GLOBAL_CONTEXT_PATH + 'index.php' + DeveloperPortal.DELETION_REDIRECT_URI[nTypeId];
                            });
                        }
                    }else{
                        Joomla.showError([DELETE_FAILED]);
                    }
                },
                error: function(jqXHR, textStatus, errorThrow) {
                    Joomla.showError([DELETE_FAILED]);
                }
            });
        };
    };    
}(jQuery));