"use strict";
if (typeof body === 'undefined') {
    var body = $('body');
}
var e = {
    init: function () {
        e.choicesSelect(),
            e.dataTables(),
            e.dropzone(),
            e.glightbox()
    },

    enablePopovers: function () {
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
        const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
    },
    isVariableDefined: function (el) {
        return typeof !!el && (el) != 'undefined' && el != null;
    },
    getParents: function (el, selector, filter) {
        const result = [];
        const matchesSelector = el.matches || el.webkitMatchesSelector || el.mozMatchesSelector || el.msMatchesSelector;

        // match start from parent
        el = el.parentElement;
        while (el && !matchesSelector.call(el, selector)) {
            if (!filter) {
                if (selector) {
                    if (matchesSelector.call(el, selector)) {
                        return result.push(el);
                    }
                } else {
                    result.push(el);
                }
            } else {
                if (matchesSelector.call(el, filter)) {
                    result.push(el);
                }
            }
            el = el.parentElement;
            if (e.isVariableDefined(el)) {
                if (matchesSelector.call(el, selector)) {
                    return el;
                }
            }

        }
        return result;
    },
    getNextSiblings: function (el, selector, filter) {
        let sibs = [];
        let nextElem = el.parentNode.firstChild;
        const matchesSelector = el.matches || el.webkitMatchesSelector || el.mozMatchesSelector || el.msMatchesSelector;
        do {
            if (nextElem.nodeType === 3) continue; // ignore text nodes
            if (nextElem === el) continue; // ignore elem of target
            if (nextElem === el.nextElementSibling) {
                if ((!filter || filter(el))) {
                    if (selector) {
                        if (matchesSelector.call(nextElem, selector)) {
                            return nextElem;
                        }
                    } else {
                        sibs.push(nextElem);
                    }
                    el = nextElem;

                }
            }
        } while (nextElem = nextElem.nextSibling)
        return sibs;
    },
    on: function (selectors, type, listener) {
        document.addEventListener("DOMContentLoaded", () => {
            if (!(selectors instanceof HTMLElement) && selectors !== null) {
                selectors = document.querySelector(selectors);
            }
            selectors.addEventListener(type, listener);
        });
    },
    onAll: function (selectors, type, listener) {
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(selectors).forEach((element) => {
                if (type.indexOf(',') > -1) {
                    let types = type.split(',');
                    types.forEach((type) => {
                        element.addEventListener(type, listener);
                    });
                } else {
                    element.addEventListener(type, listener);
                }


            });
        });
    },
    removeClass: function (selectors, className) {
        if (!(selectors instanceof HTMLElement) && selectors !== null) {
            selectors = document.querySelector(selectors);
        }
        if (e.isVariableDefined(selectors)) {
            selectors.removeClass(className);
        }
    },
    removeAllClass: function (selectors, className) {
        if (e.isVariableDefined(selectors) && (selectors instanceof HTMLElement)) {
            document.querySelectorAll(selectors).forEach((element) => {
                element.removeClass(className);
            });
        }

    },
    toggleClass: function (selectors, className) {
        if (!(selectors instanceof HTMLElement) && selectors !== null) {
            selectors = document.querySelector(selectors);
        }
        if (e.isVariableDefined(selectors)) {
            selectors.toggleClass(className);
        }
    },
    toggleAllClass: function (selectors, className) {
        if (e.isVariableDefined(selectors) && (selectors instanceof HTMLElement)) {
            document.querySelectorAll(selectors).forEach((element) => {
                element.toggleClass(className);
            });
        }
    },
    addClass: function (selectors, className) {
        if (!(selectors instanceof HTMLElement) && selectors !== null) {
            selectors = document.querySelector(selectors);
        }
        if (e.isVariableDefined(selectors)) {
            selectors.addClass(className);
        }
    },
    select: function (selectors) {
        return document.querySelector(selectors);
    },
    selectAll: function (selectors) {
        return document.querySelectorAll(selectors);
    },

    // START: 1 Choices
    choicesSelect: function () {
        var choice = e.select('.js-choice');

        if (e.isVariableDefined(choice)) {
            var element = document.querySelectorAll('.js-choice');

            element.forEach(function (item) {
                var removeItemBtn = item.getAttribute('data-remove-item-button') == 'true' ? true : false;
                var placeHolder = item.getAttribute('data-placeholder') == 'false' ? false : true;
                var placeHolderVal = item.getAttribute('data-placeholder-val') ? item.getAttribute('data-placeholder-val') : 'Type and hit enter';
                var maxItemCount = item.getAttribute('data-max-item-count') ? item.getAttribute('data-max-item-count') : 20;
                var searchEnabled = item.getAttribute('data-search-enabled') == 'false' ? false : true;

                var choices = new Choices(item, {
                    removeItemButton: removeItemBtn,
                    placeholder: placeHolder,
                    placeholderValue: placeHolderVal,
                    maxItemCount: maxItemCount,
                    searchEnabled: searchEnabled
                });

            });
        }
    },
    // END: Choices

    // START: 2 Datatables
    dataTables: function () {
        var table = e.select('.datatable');

        if (e.isVariableDefined(table)) {
            var element = document.querySelectorAll('.datatable');
            element.forEach(function (item) {
                var responsive = item.getAttribute('data-responsive') == 'true' ? true : false;
                var paging = item.getAttribute('data-paging') == 'false' ? false : true;
                var searching = item.getAttribute('data-searching') == 'true' ? true : false;
                var pagingType = item.getAttribute('data-paging-type') ? item.getAttribute('data-paging-type') : 'full_numbers';
                var scrollX = item.getAttribute('scroll-x') == 'false' ? false : true;

                // var removeItemBtn = item.getAttribute('data-remove-item-button') == 'true' ? true : false;
                // var placeHolder = item.getAttribute('data-placeholder') == 'false' ? false : true;
                // var placeHolderVal = item.getAttribute('data-placeholder-val') ? item.getAttribute('data-placeholder-val') : 'Type and hit enter';
                // var maxItemCount = item.getAttribute('data-max-item-count') ? item.getAttribute('data-max-item-count') : 3;
                // var searchEnabled = item.getAttribute('data-search-enabled') == 'false' ? false : true;

                var tables = new DataTable('.datatable', {
                    responsive: responsive,
                    paging: paging,
                    searching: searching,
                    pagingType: pagingType,
                    scrollX: scrollX
                });

            });
        }
    },
    // END: Choices


    // START: 3 Dropzone
    dropzone: function () {
        var table = e.select('.dropzone');

        if (e.isVariableDefined(table)) {
            Dropzone.autoDiscover = false;
            var element = document.querySelectorAll('.dropzone');
            element.forEach(function (item) {
                var addRemoveLinks = item.getAttribute('data-add-remove-links') == 'false' ? false : true;
                var acceptedFiles = item.getAttribute('data-accepted-files') ? item.getAttribute('data-accepted-files') : null;
                var autoProcessQueue = item.getAttribute('data-auto-process-queue') == 'true' ? true : false;
                var maxFiles = item.getAttribute('data-max-files') ? item.getAttribute('data-max-files') : 1;
                // var scrollX = item.getAttribute('scroll-x') == 'false' ? false : true;

                var myDropzone = new Dropzone(item, {
                    addRemoveLinks: addRemoveLinks,
                    autoProcessQueue: autoProcessQueue,
                    maxFiles: maxFiles,
                    acceptedFiles: acceptedFiles
                });

            });
        }
    },
    // END: Dropzone

    // START: 4 Glightbox
    glightbox: function () {
        var lightbox = e.select('.glightbox');

        if (e.isVariableDefined(lightbox)) {
            var element = document.querySelectorAll('.glightbox');

            element.forEach(function (item) {
                var triggerElement = item.getAttribute('data-trigger-element') ? item.getAttribute('data-trigger-element') : false;
                var box = e.glightboxFromElement(item);


                if (triggerElement) {
                    document.querySelector(triggerElement).addEventListener('click', function () {
                        box.open();
                    });
                }
            });
        }
    },

    glightboxFromElement: function (item) {
        let lightboxElements = new Array();
        var child = item.querySelector('a');
        var triggerElement = item.getAttribute('data-trigger-element') ? item.getAttribute('data-trigger-element') : false;

        var autoPlayVideos = item.getAttribute('data-auto-play-videos') == 'false' ? false : true;
        var descriptionPosition = (new Array(['right', 'left', 'center'])).includes(item.getAttribute('data-description-position')) ? box.getAttribute('data-description-position') : 'right';

        if (e.isVariableDefined(child)) {
            var children = item.querySelectorAll('a');
            children.forEach(function (box) {


                var href = box.getAttribute('href') ? box.getAttribute('href') : "#";
                var title = box.getAttribute('data-title') ? box.getAttribute('data-title') : "";
                var description = box.getAttribute('data-description') ? box.getAttribute('data-description') : "";
                var type = (new Array(['image', 'video'])).includes(box.getAttribute('data-type')) ? box.getAttribute('data-type') : 'image';
                var effect = (new Array(['zoom', 'fade', 'none'])).includes(box.getAttribute('data-effect')) ? box.getAttribute('data-effect') : 'none';
                var width = box.getAttribute('data-width') ? box.getAttribute('data-width') : 'auto';
                var height = box.getAttribute('data-height') ? box.getAttribute('data-height') : 'auto';
                var zoomable = box.getAttribute('data-zoomable') == 'false' ? false : true;
                var draggable = box.getAttribute('data-draggable') == 'false' ? false : true;

                lightboxElements.push({
                    href: href,
                    title: title,
                    description: description,
                    type: type,
                    effect: effect,
                    width: width,
                    height: height,
                    zoomable: zoomable,
                    draggable: draggable
                })
            });
        }

        var myGallery = new GLightbox({
            elements: lightboxElements,
            autoPlayVideos: autoPlayVideos,
            descriptionPosition: descriptionPosition
        });

        return lightboxElements.length > 0 ? myGallery : false;
    }
};
e.init();

$(document).ready(function () {
    body.on('click', '.open-dynamic-glightbox', function () {
        var target = $(this).attr('data-glightbox-container');
        const lightbox = e.glightboxFromElement(document.querySelector(target));
        lightbox.open();
    });
});