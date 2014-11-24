/*jslint browser: true, nomen: true, plusplus: true, todo: true, white: true */
/*globals $, escape, unescape */

(function() {
    "use strict";

    var imgGrepConfig;

    imgGrepConfig = {};

    function file2url(_file) {
        return imgGrepConfig.galleriesDir + "/" + imgGrepConfig.galleries[imgGrepConfig.params.gallery] + "/" + _file;
    }

    function readConfig() {
        $.extend(true, imgGrepConfig, $("#search-result").data("config"));
    }

    $(document).ready (function() {
        var $loadList, $progressbar, $progressbarLabel, imagesCnt, items, params, results;

        readConfig();

        $loadList = $("#loadList");
        if ($loadList.length) {
            imagesCnt = {
                all: $loadList.find("li").length,
                checked: 0,
                compared: 0
            };

            $progressbarLabel = $("<span />", {id: "progressbar-label"});
            $progressbar = $("#progressbar");
            $progressbar.
                addClass("working").
                append($progressbarLabel).
                progressbar({
                    change: function() {
                        $progressbarLabel.text(imagesCnt.compared + " porównano / " + imagesCnt.checked + " sprawdzono / " + imagesCnt.all + " wszystkich");
                    }
                });

            items        = [];
            params       = imgGrepConfig.params;
            results      = [];

            $loadList.find("li").each(function() {
                var $item, errorCallback, file;

                $item         = $(this);
                file          = $item.text();
                errorCallback = function() {
                    $item.removeClass("loading").addClass("error");
                };

                items.push({
                    url: "./compute",
                    cache: false,
                    type: "POST",
                    timeout: 25000,
                    data: {
                        grey: params.grey ? 1 : 0,
                        resize: params.resize ? 1 : 0,
                        fileA: params.file,
                        fileB: params.gallery + "/" + file,
                        algorithm: params.algorithm
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $item.addClass("loading");
                    },
                    complete: function() {
                        ++imagesCnt.checked;
                        $progressbar.progressbar("value", $progressbar.progressbar("value") + 100 / items.length);
                    },
                    success: function(_data) {
                        if (_data.error) {
                            errorCallback();
                            return;
                        }

                        results.push({
                            file: file,
                            wasCompared: _data.result.wasCompared,
                            isIdentical: _data.result.isIdentical,
                            ratio: _data.result.ratio
                        });

                        if (_data.result.wasCompared) {
                            ++imagesCnt.compared;
                        }

                        $item.removeClass("loading");
                        $item.remove();
                    },
                    error: errorCallback
                });
            });

            $.ajaxPool({
                limit: 4,
                tasks: items,
                success: undefined,
                error: undefined,
                complete: function() {
                    var $results, i, result;

                    results = $.grep(results, function(item) {
                        return item.wasCompared;
                    });

                    if (params.identical === "Y") {
                        results = $.grep(results, function(item) {
                            return item.isIdentical;
                        });
                    }
                    else {
                        results.sort(function(a, b) {
                            if (a.isIdentical) {
                                return -1;
                            }

                            if (b.isIdentical) {
                                return 1;
                            }

                            return (parseFloat(a.ratio) - parseFloat(b.ratio));
                        });
                    }

                    if (params.limit) {
                        results = results.splice(0, params.limit);
                    }

                    $results = $("#results");
                    if (results.length === 0) {
                        $results.append("<li class='centered'>Brak wyników</li>");
                    }
                    else {
                        for (i = 0; i < results.length; ++i) {
                            result = results[i];
                            $results.append(
                                "<li>" +
                                    "<div>" +
                                        "<div class='FL'>" + result.file + "</div>" +
                                        "<div class='FR'>" + (
                                            result.isIdentical
                                                ? "IDENTYCZNY"
                                                : (
                                                    result.ratio !== null
                                                        ? ("współczynnik: " + result.ratio)
                                                        : "-"
                                                )
                                        ) + "</div>" +
                                    "</div>" +
                                    "<div class='CB centered'>" +
                                        "<img src='" + file2url(result.file) + "' alt='" + result.file + "' />" +
                                    "</div>" +
                                "</li>"
                            );
                        }
                    }

                    $progressbar.removeClass("working");
                }
            });
        }
    });
}());