/* global async:true */
/* global jQuery:true */
/* global Ajax:true */
/* global requestUrl:true */
/* global concurrency:true */

"use strict";

var $j = jQuery.noConflict();

$j(document).ready(function () {
    var overlayElem = document.createElement("div");
    overlayElem.setAttribute("id", "kraken-overlay");
    document.body.appendChild(overlayElem);

    var modalElem = document.getElementById("kraken-modal");
    document.body.appendChild(modalElem.parentNode.removeChild(modalElem));

    $j("#kraken-modal-close, #kraken-success-close").on("click", function () {
        window.location.reload();
    });
});

window.optimizeImages = {
    optimize: function (type, images, total) {
        var originalHeader = $j("#kraken-modal-head").text();

        if (type === "media") {
            $j("#kraken-modal-head").text(originalHeader.replace("__type__", "media"));
        } else {
            $j("#kraken-modal-head").text(originalHeader.replace("__type__", "skin"));
        }

        $j("#kraken-modal").show();
        $j("#kraken-overlay").addClass("show");
        $j("#kraken-total").text(total);

        var j = 0;

        var queue = async.queue(function (task, callback) {
            new Ajax.Request(requestUrl, {
                method : "get",
                parameters: {
                    type: type,
                    image: JSON.stringify(task.file)
                },
                onComplete: function (response) {
                    j++;

                    var json = response.responseJSON;

                    if (json.success) {
                        if (!queue.paused) {
                            $j("#kraken-current").text(j);
                            $j("#kraken-number-progress")
                                .addClass("show")
                                .text(Math.round(j / total * 100) + "%");

                            $j("#kraken-progress-bar").css({
                                width: Math.round(j / total * 100) + "%"
                            });
                        }
                    } else {
                        var errorText = "";

                        if (json.statusCode === 429) {
                            errorText = "Unnknown API Key. Please check your Kraken API key and try again.";
                        } else if (json.statusCode === 401) {
                            errorText = "Your free quota (50 MB) has reached its limit.<br>Request more testing quota (support@kraken.io) or please upgrade to a paid plan.";
                        } else {
                            errorText = json.message;
                        }

                        if (json.statusCode === 401 || json.statusCode === 429) {
                            $j("#kraken-modal-error")
                                .html(json.message)
                                .show();

                            queue.pause();

                            $j("#kraken-modal").css({
                                marginTop: -(Math.round($j("#kraken-modal").height()/2)) + "px"
                            });
                        }
                    }

                    callback();
                }
            });
        }, concurrency);

        for (var i = 0, ii = images.length; i < ii; i++) {
            queue.push({
                file: images[i]
            });
        }

        queue.drain = function () {
            setTimeout(function () {
                var successMessage = "";

                if (type === "media") {
                    successMessage = "Congratulations! Your skin images have been optimized.";
                } else {
                    successMessage = "Congratulations! Your media images have been optimized.";
                }

                $j("#kraken-modal-head").text(successMessage);
                $j("#kraken-success-close").show();
                $j("#kraken-progress").hide();
                $j("#kraken-modal-footer").hide();
                $j("#kraken-count").hide();
                $j("#kraken-modal-content")
                    .addClass("with-success")
                    .find("p")
                    .addClass("with-success");
            }, 500);
        };
    }
};