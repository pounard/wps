/*jslint browser: true, devel: true, todo: true, indent: 2 */
/*global jQuery, Dispatcher, Template, Folder */
(function ($, document) {
  "use strict";
  $(document).ready(function () {
    $(".actions").each(function () {
      var
        displayed = false,
        parent = $(this),
        trigger = parent.find('> a'),
        menu = parent.find('> ul');
      if (menu.length) {
        trigger.on("click", function () {
          if (displayed) {
            menu.hide();
            displayed = false;
          } else {
            menu.show();
            displayed = true;
          }
        });
      }
    });
  });
}(jQuery, document));