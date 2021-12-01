/**
 * Created by Kevin Li<huali@tibco-support.com> on 11/17/14.
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
 * Anything that emits events.
 *
 * @class
 */
var EventEmitter = (function ($) {
    'use strict';

    /**
     * An array containing all instances of EventEmitter.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @private
     * @type {Array}
     */
    var aInstances = [],
        /**
         * An object containing all text constant values.
         *
         * @type {Object}
         */
        oTextConstants = {
            ERROR_LIMIT_REACHED: 'Max listener count reached.',
            ERROR_EVENT_EMITTER_EXPECTED: 'An instance of EventEmitter is expected for the first argument.',
            ERROR_EVENT_TYPE_NOT_STRING: 'Event type is expected to be a string.',
            ERROR_LISTENER_NOT_FUNCTION: 'Listener is expected to be a function.',
            ERROR_MAX_LISTENER_COUNT_NOT_NUMBER: 'The max listener count is expected to be a number.'
        },
        /**
         * The constructor function of the EventEmitter.
         *
         * @author Kevin Li<huali@tibco-support.com>
         * @private
         * @constructor
         * @type {Function}
         */
        factory = function () {

            /**
             * An object containing all instances of EventEmitter.
             *
             * @author Kevin Li<huali@tibco-support.com>
             * @private
             * @type {Object}
             */
            var oInstance = {},
                /**
                 * An object containing all event/listeners pairs.
                 *
                 * @author Kevin Li<huali@tibco-support.com>
                 * @private
                 * @type {Object}
                 */
                oRegistry = {},
                /**
                 * The number of maximum listeners for a specific event type.
                 *
                 * @author Kevin Li<huali@tibco-support.com>
                 * @private
                 * @type {number}
                 * @default Number.POSITIVE_INFINITY
                 */
                nLimit = Number.POSITIVE_INFINITY;

            $.extend(oInstance, {
                addListener: addListener,
                emit: emit,
                hasListener: hasListener,
                isListenedOn: isListenedOn,
                /**
                 * A shortcut for the method removeListener.
                 *
                 * @type {Function}
                 */
                off: removeListener,
                /**
                 * A shortcut for the method addListener.
                 *
                 * @type {Function}
                 */
                on: addListener,
                once: once,
                removeAllListeners: removeAllListeners,
                setMaxListeners: setMaxListeners,
                listeners: listeners,
                removeListener: removeListener
            });

            /**
             * Add a listener for a specific event type.
             *
             * @author Kevin Li<huali@tibco-support.com>
             * @param {string} sEventType The event type for which a listener is to be added.
             * @param {Function} fListener The listener that is added for a specific event type.
             * @returns {EventEmitter} The instance of EventEmitter so that method calls can be chained.
             * @throws Error will be thrown if the limit of listeners count is 0.
             * @throws Error will be thrown if the event type argument is not a string.
             * @throws Error will be thrown if the listener argument is not a function.
             * @throws Error will be thrown if the number of listeners has reached the limit of listeners count.
             */
            function addListener(sEventType, fListener) {

                if (nLimit <= 0) {

                    throw oTextConstants.ERROR_LIMIT_REACHED;

                } else if (typeof sEventType !== 'string') {

                    throw oTextConstants.ERROR_EVENT_TYPE_NOT_STRING;

                } else if (typeof fListener !== 'function') {

                    throw oTextConstants.ERROR_LISTENER_NOT_FUNCTION;

                } else {

                    if (typeof oRegistry[sEventType] === 'undefined') {

                        oRegistry[sEventType] = [];
                    }

                    if (oRegistry[sEventType].length < nLimit) {

                        oRegistry[sEventType].push(fListener);

                    } else {

                        throw oTextConstants.ERROR_LIMIT_REACHED;
                    }
                }
                return this;
            }

            /**
             * Emit an event for a specific event type.
             *
             * @author Kevin Li<huali@tibco-support.com>
             * @param {string} sEventType The event type for which a listener is to be removed.
             * @param {...} args Any number of arguments to be passed to the listeners.
             * @returns {EventEmitter} The instance of EventEmitter so that method calls can be chained.
             * @throws Error will be thrown if the event type argument is not a string.
             */
            function emit(sEventType) {

                if (typeof sEventType !== 'string') {

                    throw oTextConstants.ERROR_EVENT_TYPE_NOT_STRING;

                } else {

                    if (typeof oRegistry[sEventType] !== 'undefined') {

                        var args = Array.prototype.slice.call(arguments, 1),
                            aListeners = oRegistry[sEventType],
                            i;

                        for (i = 0; i < aListeners.length; i++) {

                            aListeners[i].apply(this, args);
                        }
                    }
                }
                return this;
            }

            /**
             * Check whether a listener function hsa been registered for a specific event type.
             *
             * @author Kevin Li<huali@tibco-support.com>
             * @param {string} sEventType The event type to check for.
             * @param {Function} fListener The listener function to be checked against.
             * @returns {boolean} True if the listener has been registered for the specific event type. False otherwise.
             * @throws Error will be thrown if the event type argument is not a string.
             * @throws Error will be thrown if the listener argument is not a function.
             */
            function hasListener(sEventType, fListener) {

                if (typeof sEventType !== 'string') {

                    throw oTextConstants.ERROR_EVENT_TYPE_NOT_STRING;

                } else if (typeof fListener !== 'function') {

                    throw oTextConstants.ERROR_LISTENER_NOT_FUNCTION;

                } else {

                    var aListeners = oRegistry[sEventType],
                        ret,
                        i;

                    if (typeof aListeners === 'undefined' ||
                        aListeners.length === 0) {

                        ret = false;

                    } else {

                        if(aListeners.indexOf(fListener) > -1) {

                            ret = true;

                        } else {

                            for(i = 0; i < aListeners.length; i += 1) {

                                if(isWrapped(sEventType, fListener, aListeners[i])) {

                                    ret = true;
                                    break;
                                }
                            }
                        }
                    }
                    return ret;
                }
            }

            /**
             * Tell whether a specific event type has been listened on.
             *
             * @author Kevin Li<huali@tibco-support.com>
             * @param {string} sEventType The event type to be checked against.
             * @returns {boolean} True if the event type has been listened on. False otherwise.
             * @throws Error will be thrown if the event type argument is not a string.
             */
            function isListenedOn(sEventType) {

                if (typeof sEventType !== 'string') {

                    throw oTextConstants.ERROR_EVENT_TYPE_NOT_STRING;

                } else {

                    var aListeners = oRegistry[sEventType];

                    return typeof aListeners !== 'undefined' && aListeners.length > 0;
                }
            }

            /**
             * Tell whether the wrapper function is wrapping around the original function.
             *
             * @author Kevin Li<huali@tibco-support.com>
             * @private
             * @param {string} sEventType The event type for which the listeners are checked.
             * @param {Function} fListener The original function.
             * @param {Function} fWrapped The wrapper function
             * @returns {boolean} True if the wrapper function is wrapping around the original function. False
             * otherwise.
             */
            function isWrapped(sEventType, fListener, fWrapped) {

                var sListener = serialize(wrapForOnce(sEventType, fListener)),
                    sWrapped = serialize(fWrapped);

                return sListener === sWrapped;
            }

            /**
             * Get the number of listeners for a specific event type. If the event type is not specified, the
             * returned value will be 0.
             *
             * @author Kevin Li<huali@tibco-support.com>
             * @param {string} sEventType The event type for which a listener is to be counted.
             * @returns {number} The number of listeners for the specified event type. If the event type is
             * missing, 0 will be returned.
             * @throws Error will be thrown if the event type argument is not a string.
             */
            function listeners(sEventType) {

                var ret = 0;

                if (typeof sEventType !== 'string') {

                    throw oTextConstants.ERROR_EVENT_TYPE_NOT_STRING;

                } else {

                    if (typeof oRegistry[sEventType] !== 'undefined') {

                        ret = oRegistry[sEventType].length;
                    }
                }
                return ret;
            }

            /**
             * Add a listener which runs only once for a specific event type.
             *
             * @author Kevin Li<huali@tibco-support.com>
             * @param {string} sEventType The event type for which a listener that runs only once is to be
             * added.
             * @param {Function} fListener The listener that is added for a specific event type and will run
             * only once.
             * @returns {EventEmitter} The instance of EventEmitter so that method calls can be chained.
             */
            function once(sEventType, fListener) {

                return addListener(sEventType, wrapForOnce(sEventType, fListener));
            }

            /**
             * Remove all listeners for a specific event type. If the event type is not specified all listeners
             * for all event types will be purged.
             *
             * @author Kevin Li<huali@tibco-support.com>
             * @param {string} sEventType The event type for which all listeners are to be removed. This
             * parameter is optional. If it's omitted all listeners for all event types will be purged.
             * @returns {EventEmitter} The instance of EventEmitter so that method calls can be chained.
             */
            function removeAllListeners(sEventType) {

                if (typeof sEventType === 'string') {

                    if (sEventType in oRegistry) {

                        delete oRegistry[sEventType];
                    }
                } else if (typeof sEventType === 'undefined') {

                    oRegistry = {};
                }
                return this;
            }

            /**
             * Remove a listener for a specific event type.
             *
             * @author Kevin Li<huali@tibco-support.com>
             * @param {string} sEventType The event type for which a listener is to be removed.
             * @param {Function} fListener The listener that is removed for a specific event type.
             * @returns {EventEmitter} The instance of EventEmitter so that method calls can be chained.
             * @throws Error will be thrown if the event type argument is not a string.
             * @throws Error will be thrown if the listener argument is not a function.
             */
            function removeListener(sEventType, fListener) {

                if (typeof sEventType !== 'string') {

                    throw oTextConstants.ERROR_EVENT_TYPE_NOT_STRING;

                } else if (typeof fListener !== 'function') {

                    throw oTextConstants.ERROR_LISTENER_NOT_FUNCTION;

                } else {

                    var aListeners = oRegistry[sEventType],
                        nIndex = -1,
                        i;

                    if ($.isArray(aListeners)) {

                        nIndex = aListeners.indexOf(fListener);

                        // The unwrapped fListener is not found in the listeners array.
                        // Try wrapped fListener.
                        if (nIndex < 0) {

                            for (i = 0; i < aListeners.length; i += 1) {

                                if (isWrapped(sEventType, fListener, aListeners[i])) {

                                    nIndex = i;
                                    break;
                                }
                            }
                        }

                        // Either an unwrapped or a wrapped fListener has been found.
                        if (nIndex >= 0) {

                            aListeners.splice(nIndex, 1);
                        }

                        // When the array is empty just remove it.
                        if (aListeners.length === 0) {

                            delete oRegistry[sEventType];
                        }
                    }
                }
                return this;
            }

            /**
             * Convert a JavaScript object to a string by making use of its toString() method. So as long as the
             * object has a toString() method it can be converted to a string with all the spaces, tabs, carriage
             * returns and line feeds removed.
             *
             * @author Kevin Li<huali@tibco-support.com>
             * @private
             * @param {Function} fTarget A JavaScript function.
             * @returns {string} A string that is returned by the toString() method and got all spaces, tabs,
             * carriage returns and line feeds removed.
             */
            function serialize(fTarget) {

                var ret = typeof fTarget !== 'undefined' &&
                    typeof fTarget.toString === 'function' &&
                    fTarget.toString();

                if (ret) {

                    ret = ret.replace(/\s+/g, '');
                }
                return ret || '';
            }

            /**
             * Set the maximum number of listeners that can be registered for one event.
             *
             * @author Kevin Li<huali@tibco-support.com>
             * @param {number} nMaxListeners
             * @returns {EventEmitter} The instance of EventEmitter so that method calls can be chained.
             * @throws Error will be thrown if the only argument is not a number.
             */
            function setMaxListeners(nMaxListeners) {

                if (typeof nMaxListeners !== 'number') {

                    throw oTextConstants.ERROR_MAX_LISTENER_COUNT_NOT_NUMBER;

                } else {

                    if (nMaxListeners < nLimit) {

                        setTimeout(trimExtraListeners);
                    }
                    nLimit = nMaxListeners;
                }
                return this;
            }

            /**
             * Cut out extra listeners for every event type based on the value of max listener count. Older ones are
             * kept and newer ones are removed.
             *
             * @author Kevin Li<huali@tibco-support.com>
             * @private
             */
            function trimExtraListeners() {

                var p,
                    aListeners;

                for (p in oRegistry) {

                    if (oRegistry.hasOwnProperty(p)) {

                        aListeners = oRegistry[p];

                        if (aListeners.length > nLimit) {

                            aListeners.splice(nLimit);
                        }
                    }
                }
            }

            /**
             * Wrap the original listener function for a specific event and return the wrapper function so that
             * after this listener is invoked it'll be removed automatically.
             *
             * @author Kevin Li<huali@tibco-support.com>
             * @private
             * @param {string} sEventType A specific event type.
             * @param {Function} fListener The listener function which is to be wrapped.
             * @returns {Function} The wrapper function.
             */
            function wrapForOnce(sEventType, fListener) {

                var fWrapped = function () {

                    fListener.apply(this, Array.prototype.slice.apply(arguments));
                    this.removeListener(sEventType, fListener);
                };
                // This is just a flag to tell apart the wrapped listener and the original listener.
                // Assigning it to "fWrapped" as an attribute is to keep it untouched even if the code is obfuscated.
                fWrapped.sFlag = 'ThisIsAOnceWrapper';

                return fWrapped;
            }

            return oInstance;
        };

    return {
        hasInstance: hasInstance,
        listenerCount: listenerCount,
        newInstance: newInstance
    };
    /**
     * Tell whether the object is an instance of EventEmitter.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @static
     * @param {Object} oInstance An object to test whether it's an instance of EventEmitter or not.
     * @returns {boolean} True if the object is an instance of EventEmitter. False otherwise.
     */
    function hasInstance(oInstance) {

        return aInstances.indexOf(oInstance) > -1;
    }

    /**
     * Get the number of listeners for a specific event type. If the event type is not specified, the returned
     * value will be 0.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @static
     * @param {EventEmitter} oEventEmitter An instance of EventEmitter.
     * @param {string} sEventType The event type for which a listener is to be counted.
     * @returns {number} The number of listeners for the specified event type. If the event type is missing, 0 will
     * be returned.
     * @throws Error will be thrown if the first argument is not an instance of EventEmitter.
     */
    function listenerCount(oEventEmitter, sEventType) {

        if (!hasInstance(oEventEmitter)) {

            throw oTextConstants.ERROR_EVENT_EMITTER_EXPECTED;

        } else {

            return oEventEmitter.listeners(sEventType);
        }
    }

    /**
     * Create and return a new instance of EventEmitter. This instance will be remembered so that we can easily tell
     * whether a given object is an instance of EventEmitter or not.
     *
     * @author Kevin Li<huali@tibco-support.com>
     * @static
     * @returns {EventEmitter} An instance of EventEmitter.
     */
    function newInstance() {

        var instance = factory.apply(null, Array.prototype.slice.call(arguments, 0));

        return aInstances[aInstances.length] = instance;
    }

}(jQuery));

