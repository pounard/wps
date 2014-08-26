/*jslint browser: true, devel: true, todo: true, indent: 2, plusplus: true */
/*global jQuery, Dispatcher, Template, Folder */
(function ($, document) {
  "use strict";
  $(document).ready(function () {

    var
      // FIXME: Hardcoded list of sizes
      sizes = [ 100, 200, 230, 300, 600, 900, 1200, "full" ],
      overlay = $("<div id=\"overlay\" style=\"display:none;\"></div>"),
      overlayToolbar = $("<div id=\"overlay-toolbar\"></div>"),
      overlayContent = $("<div id=\"overlay-content\"></div>"),
      overlayPrevButton = $("<div class=\"media-prev\" style=\"display:none;\"><a href=\"#\">Prevous</a></div>"),
      overlayNextButton = $("<div class=\"media-next\" style=\"display:none;\"><a href=\"#\">Next</a></div>"),
      overlayCloseButton = $("<a href=\"#\" id=\"overlay-close\">Close</a>"),

      closeOverlay = function () {
        overlay.hide();
        overlayContent.html("");
        overlayPrevButton.hide();
        overlayNextButton.hide();
      },

      imageClicked = function (event) {

        var
          $this = $(this),
          href = $this.attr("href"),
          image,
          index = 0,
          viewportWidth = $(window).width(),
          viewportHeight = $(window).height(),
          currentSize = "";

        event.stopPropagation();
        event.preventDefault();

        $.ajax({
          async: true,
          method: "get",
          contentType: "json",
          dataType: "json",
          url: href,
          cache: true,
          success: function (data) {

            // Build overlay with media data
            overlayContent.html("");
            overlay.show();

            // Load image and display into
            // Select size depending on view port
            for (index; index < sizes.length; ++index) {
              currentSize = sizes[index];
              if ("full" === currentSize) {
                break;
              }
              if (viewportWidth < currentSize) {
                currentSize = "w" + currentSize;
                break;
              }
            }

            // Handle prev and next buttons
            if (data.data.prev && data.data.prev !== null) {
              overlayPrevButton.find("a").attr("href", data.data.base + "app/media/" + data.data.prev.id);
              overlayPrevButton.show();
            } else {
              overlayPrevButton.hide();
            }
            if (data.data.next && data.data.next !== null) {
              overlayNextButton.find("a").attr("href", data.data.base + "app/media/" + data.data.next.id);
              overlayNextButton.show();
            } else {
              overlayNextButton.hide();
            }

            image = $("<img src=\"" + data.data.cdn + "/" + currentSize + "/" + data.data.media.realPath + "\"/>");
            image.css({"max-width": viewportWidth, "max-height": viewportHeight});
            overlayContent.append(image);
          },
          error: function () {
            closeOverlay();
          }
        });
      };

    // Build and populate overlay stuff
    overlayPrevButton.find("a").on("click", imageClicked);
    overlayNextButton.find("a").on("click", imageClicked);
    overlayToolbar
      .append(overlayNextButton)
      .append(overlayPrevButton)
      .append(overlayCloseButton);
    overlay
      .append(overlayToolbar)
      .append(overlayContent);
    overlayCloseButton.on("click", closeOverlay);
    $(document.body).append(overlay);

    $("#album .media-link").on("click", imageClicked);
  });
}(jQuery, document));