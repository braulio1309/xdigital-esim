export const recentSalesTable = {
    "options": {
        "cols": [{
            "name": "Invoice ID",
            "key": "id",
            "class": "text-primary",
            "type": "modal"
        }, {"name": "Customer", "key": "customer", "class": "text-primary"}, {
            "name": "Grand total",
            "key": "total",
            "alignment": "right"
        }, {"name": "pain", "key": "paid", "alignment": "right"}, {
            "name": "Due",
            "key": "due",
            "alignment": "right"
        }, {"name": "Order status", "key": "status"}]
    },
    "data": [{
        "id": "POS-7-sales",
        "invoice_id": 6,
        "customer": "Reilly Stoltenberg",
        "customer_id": 7,
        "total": "$230",
        "paid": "$0",
        "due": "$0",
        "status": "<span class=\"badge badge-warning badge-pill\">Hold</span>"
    }, {
        "id": "POS-5-sales",
        "invoice_id": 5,
        "customer": "Alfonso Koelpin",
        "customer_id": 5,
        "total": "$970",
        "paid": "$0",
        "due": "$0",
        "status": "<span class=\"badge badge-warning badge-pill\">Hold</span>"
    }, {
        "id": "POS-4-sales",
        "invoice_id": 4,
        "customer": "Florence Becker",
        "customer_id": 4,
        "total": "$950",
        "paid": "$0",
        "due": "$0",
        "status": "<span class=\"badge badge-primary badge-pill\">Pending</span>"
    }, {
        "id": "POS-3-sales",
        "invoice_id": 3,
        "customer": "Amely Brekke",
        "customer_id": 3,
        "total": "$350",
        "paid": "$0",
        "due": "$0",
        "status": "<span class=\"badge badge-warning badge-pill\">Hold</span>"
    }, {
        "id": "POS-2-sales",
        "invoice_id": 2,
        "customer": "Marlin Macejkovic",
        "customer_id": 2,
        "total": "$1250",
        "paid": "$0",
        "due": "$0",
        "status": "<span class=\"badge badge-primary badge-pill\">Pending</span>"
    }, {
        "id": "POS-1-sales",
        "invoice_id": 1,
        "customer": "Walk-in -customer",
        "customer_id": 1,
        "total": "$1500",
        "paid": "$0",
        "due": "$0",
        "status": "<span class=\"badge badge-warning badge-pill\">Hold</span>"
    }]
}

export const salesPurchaseData = {
    "labels": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"], "dataSet": [{
        "label": "Sales",
        "barPercentage": 0.5,
        "barThickness": 30,
        "borderWidth": 1,
        "backgroundColor": "#3874ff",
        "data": [0, 0, 0, 5250, 0, 0, 0],
        "_meta": {
            "31": {
                "type": "bar",
                "data": [{
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 0,
                    "_index": 0,
                    "hidden": false,
                    "_xScale": {
                        "id": "x-axis-0",
                        "type": "category",
                        "options": {
                            "display": true,
                            "position": "bottom",
                            "offset": true,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": true,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 12,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                        "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                        "_reviveId": 2
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0"
                            },
                            "type": "category",
                            "id": "x-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "bottom",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 3
                            }
                        },
                        "maxWidth": 962.8788931396484,
                        "maxHeight": 180,
                        "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                        "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                        "_labelSizes": {
                            "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 25.582687377929688,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "Sun": 21.02239990234375,
                                    "Mon": 24.107635498046875,
                                    "Tue": 20.27923583984375,
                                    "Wed": 25.582687377929688,
                                    "Thu": 20.504440307617188,
                                    "Fri": 12.64495849609375,
                                    "Sat": 18.32000732421875
                                }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.5,
                            "ty1": 325,
                            "tx2": 49.5,
                            "ty2": 324.3498,
                            "x1": 49.5,
                            "y1": 8.4465,
                            "x2": 49.5,
                            "y2": 324,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 186.5,
                            "ty1": 325,
                            "tx2": 186.5,
                            "ty2": 324.3498,
                            "x1": 186.5,
                            "y1": 8.4465,
                            "x2": 186.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 323.5,
                            "ty1": 325,
                            "tx2": 323.5,
                            "ty2": 324.3498,
                            "x1": 323.5,
                            "y1": 8.4465,
                            "x2": 323.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 460.5,
                            "ty1": 325,
                            "tx2": 460.5,
                            "ty2": 324.3498,
                            "x1": 460.5,
                            "y1": 8.4465,
                            "x2": 460.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 597.5,
                            "ty1": 325,
                            "tx2": 597.5,
                            "ty2": 324.3498,
                            "x1": 597.5,
                            "y1": 8.4465,
                            "x2": 597.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 734.5,
                            "ty1": 325,
                            "tx2": 734.5,
                            "ty2": 324.3498,
                            "x1": 734.5,
                            "y1": 8.4465,
                            "x2": 734.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 871.5,
                            "ty1": 325,
                            "tx2": 871.5,
                            "ty2": 324.3498,
                            "x1": 871.5,
                            "y1": 8.4465,
                            "x2": 871.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 1009.5,
                            "ty1": 325,
                            "tx2": 1009.5,
                            "ty2": 324.3498,
                            "x1": 1009.5,
                            "y1": 8.4465,
                            "x2": 1009.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 117.68388494175501,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sun",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 254.80944110456193,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Mon",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 391.9349972673689,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Tue",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 529.0605534301758,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Wed",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 666.1861095929828,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Thu",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 803.3116657557896,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Fri",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 940.4372219185966,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sat",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }],
                        "width": 959.8788931396484,
                        "left": 49.12110686035156,
                        "right": 1009,
                        "paddingLeft": 3,
                        "paddingTop": 0,
                        "paddingRight": 3,
                        "paddingBottom": 0,
                        "minIndex": 0,
                        "maxIndex": 6,
                        "min": "Sun",
                        "max": "Sat",
                        "_startPixel": 49.12110686035156,
                        "_endPixel": 1009,
                        "_reversePixels": false,
                        "_length": 959.8788931396484,
                        "_startValue": -0.5,
                        "_valueRange": 7,
                        "labelRotation": 0,
                        "minSize": {"width": 962.8788931396484, "height": 35.6502},
                        "height": 35.6502,
                        "_ticksToDraw": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "top": 324.3498,
                        "bottom": 360
                    },
                    "_yScale": {
                        "id": "y-axis-0",
                        "type": "linear",
                        "options": {
                            "display": true,
                            "position": "left",
                            "offset": false,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": false,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 14,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> linear(tickValue, index, ticks)",
                                        "tooltip": "<pre>function(tickValue, index, ticks) {\r\n\t\t\t// If we have lots of ticks, don't use the ones\r\n\t\t\tvar delta = ticks.length > 3 ? ticks[2] - ticks[1] : ticks[1] - ticks[0];\r\n\r\n\t\t\t// If we have a number like 2.5 as the delta, figure out how many decimal places we need\r\n\t\t\tif (Math.abs(delta) > 1) {\r\n\t\t\t\tif (tickValue !== Math.floor(tickValue)) {\r\n\t\t\t\t\t// not an integer\r\n\t\t\t\t\tdelta = tickValue - Math.floor(tickValue);\r\n\t\t\t\t}\r\n\t\t\t}\r\n\r\n\t\t\tvar logDelta = helpers$1.log10(Math.abs(delta));\r\n\t\t\tvar tickString = '';\r\n\r\n\t\t\tif (tickValue !== 0) {\r\n\t\t\t\tvar maxTick = Math.max(Math.abs(ticks[0]), Math.abs(ticks[ticks.length - 1]));\r\n\t\t\t\tif (maxTick < 1e-4) { // all ticks are small numbers; use scientific notation\r\n\t\t\t\t\tvar logTick = helpers$1.log10(Math.abs(tickValue));\r\n\t\t\t\t\tvar numExponential = Math.floor(logTick) - Math.floor(logDelta);\r\n\t\t\t\t\tnumExponential = Math.max(Math.min(numExponential, 20), 0);\r\n\t\t\t\t\ttickString = tickValue.toExponential(numExponential);\r\n\t\t\t\t} else {\r\n\t\t\t\t\tvar numDecimal = -1 * Math.floor(logDelta);\r\n\t\t\t\t\tnumDecimal = Math.max(Math.min(numDecimal, 20), 0); // toFixed has a max of 20 decimal places\r\n\t\t\t\t\ttickString = tickValue.toFixed(numDecimal);\r\n\t\t\t\t}\r\n\t\t\t} else {\r\n\t\t\t\ttickString = '0'; // never show decimal places for 0\r\n\t\t\t}\r\n\r\n\t\t\treturn tickString;\r\n\t\t}</pre>",
                                        "_reviveId": 4
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0",
                                "precision": 2
                            },
                            "type": "linear",
                            "id": "y-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "left",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 5
                            }
                        },
                        "maxWidth": 506,
                        "maxHeight": 360,
                        "margins": {"left": 0, "right": 0, "top": 8.4465, "bottom": 8.4465},
                        "_ticks": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "ticks": ["6000", "5000", "4000", "3000", "2000", "1000", "0"],
                        "_labelSizes": {
                            "first": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 7.0712738037109375, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 28.363906860351562,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "0": 7.0712738037109375,
                                    "1000": 24.8170166015625,
                                    "2000": 27.688308715820312,
                                    "3000": 27.845947265625,
                                    "4000": 28.296356201171875,
                                    "5000": 28.28509521484375,
                                    "6000": 28.363906860351562
                                }, "gc": ["6000", "5000", "4000", "3000", "2000", "1000", "0"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.12110686035156,
                            "ty1": 8.5,
                            "tx2": 49,
                            "ty2": 8.5,
                            "x1": 50,
                            "y1": 8.5,
                            "x2": 1009,
                            "y2": 8.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 61.5,
                            "tx2": 49,
                            "ty2": 61.5,
                            "x1": 50,
                            "y1": 61.5,
                            "x2": 1009,
                            "y2": 61.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 113.5,
                            "tx2": 49,
                            "ty2": 113.5,
                            "x1": 50,
                            "y1": 113.5,
                            "x2": 1009,
                            "y2": 113.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 166.5,
                            "tx2": 49,
                            "ty2": 166.5,
                            "x1": 50,
                            "y1": 166.5,
                            "x2": 1009,
                            "y2": 166.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 219.5,
                            "tx2": 49,
                            "ty2": 219.5,
                            "x1": 50,
                            "y1": 219.5,
                            "x2": 1009,
                            "y2": 219.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 271.5,
                            "tx2": 49,
                            "ty2": 271.5,
                            "x1": 50,
                            "y1": 271.5,
                            "x2": 1009,
                            "y2": 271.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 324.5,
                            "tx2": 49,
                            "ty2": 324.5,
                            "x1": 50,
                            "y1": 324.5,
                            "x2": 1009,
                            "y2": 324.5,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 35.12110686035156,
                            "y": 8.4465,
                            "rotation": 0,
                            "label": "6000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 61.09704999999999,
                            "rotation": 0,
                            "label": "5000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 113.7476,
                            "rotation": 0,
                            "label": "4000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 166.39815,
                            "rotation": 0,
                            "label": "3000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 219.0487,
                            "rotation": 0,
                            "label": "2000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 271.69925,
                            "rotation": 0,
                            "label": "1000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 324.3498,
                            "rotation": 0,
                            "label": "0",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }],
                        "height": 315.9033,
                        "top": 8.4465,
                        "bottom": 324.3498,
                        "paddingLeft": 0,
                        "paddingTop": 8.4465,
                        "paddingRight": 0,
                        "paddingBottom": 8.4465,
                        "min": 0,
                        "max": 6000,
                        "start": 0,
                        "end": 6000,
                        "ticksAsNumbers": [6000, 5000, 4000, 3000, 2000, 1000, 0],
                        "zeroLineIndex": 6,
                        "_startPixel": 8.4465,
                        "_endPixel": 324.3498,
                        "_reversePixels": true,
                        "_length": 315.9033,
                        "_startValue": 0,
                        "_endValue": 6000,
                        "_valueRange": 6000,
                        "labelRotation": 0,
                        "minSize": {"width": 49.12110686035156, "height": 360},
                        "width": 49.12110686035156,
                        "_ticksToDraw": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "left": 0,
                        "right": 49.12110686035156
                    },
                    "_model": {
                        "backgroundColor": "#3874ff",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "Sales",
                        "label": "Sun",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 102.68388494175501,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_view": {
                        "backgroundColor": "#3874ff",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "Sales",
                        "label": "Sun",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 102.68388494175501,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 0,
                    "_index": 1,
                    "hidden": false,
                    "_xScale": {
                        "id": "x-axis-0",
                        "type": "category",
                        "options": {
                            "display": true,
                            "position": "bottom",
                            "offset": true,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": true,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 12,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                        "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                        "_reviveId": 6
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0"
                            },
                            "type": "category",
                            "id": "x-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "bottom",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 7
                            }
                        },
                        "maxWidth": 962.8788931396484,
                        "maxHeight": 180,
                        "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                        "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                        "_labelSizes": {
                            "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 25.582687377929688,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "Sun": 21.02239990234375,
                                    "Mon": 24.107635498046875,
                                    "Tue": 20.27923583984375,
                                    "Wed": 25.582687377929688,
                                    "Thu": 20.504440307617188,
                                    "Fri": 12.64495849609375,
                                    "Sat": 18.32000732421875
                                }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.5,
                            "ty1": 325,
                            "tx2": 49.5,
                            "ty2": 324.3498,
                            "x1": 49.5,
                            "y1": 8.4465,
                            "x2": 49.5,
                            "y2": 324,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 186.5,
                            "ty1": 325,
                            "tx2": 186.5,
                            "ty2": 324.3498,
                            "x1": 186.5,
                            "y1": 8.4465,
                            "x2": 186.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 323.5,
                            "ty1": 325,
                            "tx2": 323.5,
                            "ty2": 324.3498,
                            "x1": 323.5,
                            "y1": 8.4465,
                            "x2": 323.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 460.5,
                            "ty1": 325,
                            "tx2": 460.5,
                            "ty2": 324.3498,
                            "x1": 460.5,
                            "y1": 8.4465,
                            "x2": 460.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 597.5,
                            "ty1": 325,
                            "tx2": 597.5,
                            "ty2": 324.3498,
                            "x1": 597.5,
                            "y1": 8.4465,
                            "x2": 597.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 734.5,
                            "ty1": 325,
                            "tx2": 734.5,
                            "ty2": 324.3498,
                            "x1": 734.5,
                            "y1": 8.4465,
                            "x2": 734.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 871.5,
                            "ty1": 325,
                            "tx2": 871.5,
                            "ty2": 324.3498,
                            "x1": 871.5,
                            "y1": 8.4465,
                            "x2": 871.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 1009.5,
                            "ty1": 325,
                            "tx2": 1009.5,
                            "ty2": 324.3498,
                            "x1": 1009.5,
                            "y1": 8.4465,
                            "x2": 1009.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 117.68388494175501,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sun",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 254.80944110456193,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Mon",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 391.9349972673689,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Tue",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 529.0605534301758,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Wed",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 666.1861095929828,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Thu",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 803.3116657557896,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Fri",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 940.4372219185966,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sat",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }],
                        "width": 959.8788931396484,
                        "left": 49.12110686035156,
                        "right": 1009,
                        "paddingLeft": 3,
                        "paddingTop": 0,
                        "paddingRight": 3,
                        "paddingBottom": 0,
                        "minIndex": 0,
                        "maxIndex": 6,
                        "min": "Sun",
                        "max": "Sat",
                        "_startPixel": 49.12110686035156,
                        "_endPixel": 1009,
                        "_reversePixels": false,
                        "_length": 959.8788931396484,
                        "_startValue": -0.5,
                        "_valueRange": 7,
                        "labelRotation": 0,
                        "minSize": {"width": 962.8788931396484, "height": 35.6502},
                        "height": 35.6502,
                        "_ticksToDraw": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "top": 324.3498,
                        "bottom": 360
                    },
                    "_yScale": {
                        "id": "y-axis-0",
                        "type": "linear",
                        "options": {
                            "display": true,
                            "position": "left",
                            "offset": false,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": false,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 14,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> linear(tickValue, index, ticks)",
                                        "tooltip": "<pre>function(tickValue, index, ticks) {\r\n\t\t\t// If we have lots of ticks, don't use the ones\r\n\t\t\tvar delta = ticks.length > 3 ? ticks[2] - ticks[1] : ticks[1] - ticks[0];\r\n\r\n\t\t\t// If we have a number like 2.5 as the delta, figure out how many decimal places we need\r\n\t\t\tif (Math.abs(delta) > 1) {\r\n\t\t\t\tif (tickValue !== Math.floor(tickValue)) {\r\n\t\t\t\t\t// not an integer\r\n\t\t\t\t\tdelta = tickValue - Math.floor(tickValue);\r\n\t\t\t\t}\r\n\t\t\t}\r\n\r\n\t\t\tvar logDelta = helpers$1.log10(Math.abs(delta));\r\n\t\t\tvar tickString = '';\r\n\r\n\t\t\tif (tickValue !== 0) {\r\n\t\t\t\tvar maxTick = Math.max(Math.abs(ticks[0]), Math.abs(ticks[ticks.length - 1]));\r\n\t\t\t\tif (maxTick < 1e-4) { // all ticks are small numbers; use scientific notation\r\n\t\t\t\t\tvar logTick = helpers$1.log10(Math.abs(tickValue));\r\n\t\t\t\t\tvar numExponential = Math.floor(logTick) - Math.floor(logDelta);\r\n\t\t\t\t\tnumExponential = Math.max(Math.min(numExponential, 20), 0);\r\n\t\t\t\t\ttickString = tickValue.toExponential(numExponential);\r\n\t\t\t\t} else {\r\n\t\t\t\t\tvar numDecimal = -1 * Math.floor(logDelta);\r\n\t\t\t\t\tnumDecimal = Math.max(Math.min(numDecimal, 20), 0); // toFixed has a max of 20 decimal places\r\n\t\t\t\t\ttickString = tickValue.toFixed(numDecimal);\r\n\t\t\t\t}\r\n\t\t\t} else {\r\n\t\t\t\ttickString = '0'; // never show decimal places for 0\r\n\t\t\t}\r\n\r\n\t\t\treturn tickString;\r\n\t\t}</pre>",
                                        "_reviveId": 8
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0",
                                "precision": 2
                            },
                            "type": "linear",
                            "id": "y-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "left",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 9
                            }
                        },
                        "maxWidth": 506,
                        "maxHeight": 360,
                        "margins": {"left": 0, "right": 0, "top": 8.4465, "bottom": 8.4465},
                        "_ticks": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "ticks": ["6000", "5000", "4000", "3000", "2000", "1000", "0"],
                        "_labelSizes": {
                            "first": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 7.0712738037109375, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 28.363906860351562,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "0": 7.0712738037109375,
                                    "1000": 24.8170166015625,
                                    "2000": 27.688308715820312,
                                    "3000": 27.845947265625,
                                    "4000": 28.296356201171875,
                                    "5000": 28.28509521484375,
                                    "6000": 28.363906860351562
                                }, "gc": ["6000", "5000", "4000", "3000", "2000", "1000", "0"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.12110686035156,
                            "ty1": 8.5,
                            "tx2": 49,
                            "ty2": 8.5,
                            "x1": 50,
                            "y1": 8.5,
                            "x2": 1009,
                            "y2": 8.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 61.5,
                            "tx2": 49,
                            "ty2": 61.5,
                            "x1": 50,
                            "y1": 61.5,
                            "x2": 1009,
                            "y2": 61.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 113.5,
                            "tx2": 49,
                            "ty2": 113.5,
                            "x1": 50,
                            "y1": 113.5,
                            "x2": 1009,
                            "y2": 113.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 166.5,
                            "tx2": 49,
                            "ty2": 166.5,
                            "x1": 50,
                            "y1": 166.5,
                            "x2": 1009,
                            "y2": 166.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 219.5,
                            "tx2": 49,
                            "ty2": 219.5,
                            "x1": 50,
                            "y1": 219.5,
                            "x2": 1009,
                            "y2": 219.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 271.5,
                            "tx2": 49,
                            "ty2": 271.5,
                            "x1": 50,
                            "y1": 271.5,
                            "x2": 1009,
                            "y2": 271.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 324.5,
                            "tx2": 49,
                            "ty2": 324.5,
                            "x1": 50,
                            "y1": 324.5,
                            "x2": 1009,
                            "y2": 324.5,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 35.12110686035156,
                            "y": 8.4465,
                            "rotation": 0,
                            "label": "6000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 61.09704999999999,
                            "rotation": 0,
                            "label": "5000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 113.7476,
                            "rotation": 0,
                            "label": "4000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 166.39815,
                            "rotation": 0,
                            "label": "3000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 219.0487,
                            "rotation": 0,
                            "label": "2000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 271.69925,
                            "rotation": 0,
                            "label": "1000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 324.3498,
                            "rotation": 0,
                            "label": "0",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }],
                        "height": 315.9033,
                        "top": 8.4465,
                        "bottom": 324.3498,
                        "paddingLeft": 0,
                        "paddingTop": 8.4465,
                        "paddingRight": 0,
                        "paddingBottom": 8.4465,
                        "min": 0,
                        "max": 6000,
                        "start": 0,
                        "end": 6000,
                        "ticksAsNumbers": [6000, 5000, 4000, 3000, 2000, 1000, 0],
                        "zeroLineIndex": 6,
                        "_startPixel": 8.4465,
                        "_endPixel": 324.3498,
                        "_reversePixels": true,
                        "_length": 315.9033,
                        "_startValue": 0,
                        "_endValue": 6000,
                        "_valueRange": 6000,
                        "labelRotation": 0,
                        "minSize": {"width": 49.12110686035156, "height": 360},
                        "width": 49.12110686035156,
                        "_ticksToDraw": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "left": 0,
                        "right": 49.12110686035156
                    },
                    "_model": {
                        "backgroundColor": "#3874ff",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "Sales",
                        "label": "Mon",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 239.80944110456193,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_view": {
                        "backgroundColor": "#3874ff",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "Sales",
                        "label": "Mon",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 239.80944110456193,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 0,
                    "_index": 2,
                    "hidden": false,
                    "_xScale": {
                        "id": "x-axis-0",
                        "type": "category",
                        "options": {
                            "display": true,
                            "position": "bottom",
                            "offset": true,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": true,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 12,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                        "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                        "_reviveId": 10
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0"
                            },
                            "type": "category",
                            "id": "x-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "bottom",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 11
                            }
                        },
                        "maxWidth": 962.8788931396484,
                        "maxHeight": 180,
                        "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                        "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                        "_labelSizes": {
                            "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 25.582687377929688,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "Sun": 21.02239990234375,
                                    "Mon": 24.107635498046875,
                                    "Tue": 20.27923583984375,
                                    "Wed": 25.582687377929688,
                                    "Thu": 20.504440307617188,
                                    "Fri": 12.64495849609375,
                                    "Sat": 18.32000732421875
                                }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.5,
                            "ty1": 325,
                            "tx2": 49.5,
                            "ty2": 324.3498,
                            "x1": 49.5,
                            "y1": 8.4465,
                            "x2": 49.5,
                            "y2": 324,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 186.5,
                            "ty1": 325,
                            "tx2": 186.5,
                            "ty2": 324.3498,
                            "x1": 186.5,
                            "y1": 8.4465,
                            "x2": 186.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 323.5,
                            "ty1": 325,
                            "tx2": 323.5,
                            "ty2": 324.3498,
                            "x1": 323.5,
                            "y1": 8.4465,
                            "x2": 323.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 460.5,
                            "ty1": 325,
                            "tx2": 460.5,
                            "ty2": 324.3498,
                            "x1": 460.5,
                            "y1": 8.4465,
                            "x2": 460.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 597.5,
                            "ty1": 325,
                            "tx2": 597.5,
                            "ty2": 324.3498,
                            "x1": 597.5,
                            "y1": 8.4465,
                            "x2": 597.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 734.5,
                            "ty1": 325,
                            "tx2": 734.5,
                            "ty2": 324.3498,
                            "x1": 734.5,
                            "y1": 8.4465,
                            "x2": 734.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 871.5,
                            "ty1": 325,
                            "tx2": 871.5,
                            "ty2": 324.3498,
                            "x1": 871.5,
                            "y1": 8.4465,
                            "x2": 871.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 1009.5,
                            "ty1": 325,
                            "tx2": 1009.5,
                            "ty2": 324.3498,
                            "x1": 1009.5,
                            "y1": 8.4465,
                            "x2": 1009.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 117.68388494175501,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sun",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 254.80944110456193,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Mon",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 391.9349972673689,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Tue",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 529.0605534301758,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Wed",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 666.1861095929828,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Thu",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 803.3116657557896,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Fri",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 940.4372219185966,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sat",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }],
                        "width": 959.8788931396484,
                        "left": 49.12110686035156,
                        "right": 1009,
                        "paddingLeft": 3,
                        "paddingTop": 0,
                        "paddingRight": 3,
                        "paddingBottom": 0,
                        "minIndex": 0,
                        "maxIndex": 6,
                        "min": "Sun",
                        "max": "Sat",
                        "_startPixel": 49.12110686035156,
                        "_endPixel": 1009,
                        "_reversePixels": false,
                        "_length": 959.8788931396484,
                        "_startValue": -0.5,
                        "_valueRange": 7,
                        "labelRotation": 0,
                        "minSize": {"width": 962.8788931396484, "height": 35.6502},
                        "height": 35.6502,
                        "_ticksToDraw": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "top": 324.3498,
                        "bottom": 360
                    },
                    "_yScale": {
                        "id": "y-axis-0",
                        "type": "linear",
                        "options": {
                            "display": true,
                            "position": "left",
                            "offset": false,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": false,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 14,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> linear(tickValue, index, ticks)",
                                        "tooltip": "<pre>function(tickValue, index, ticks) {\r\n\t\t\t// If we have lots of ticks, don't use the ones\r\n\t\t\tvar delta = ticks.length > 3 ? ticks[2] - ticks[1] : ticks[1] - ticks[0];\r\n\r\n\t\t\t// If we have a number like 2.5 as the delta, figure out how many decimal places we need\r\n\t\t\tif (Math.abs(delta) > 1) {\r\n\t\t\t\tif (tickValue !== Math.floor(tickValue)) {\r\n\t\t\t\t\t// not an integer\r\n\t\t\t\t\tdelta = tickValue - Math.floor(tickValue);\r\n\t\t\t\t}\r\n\t\t\t}\r\n\r\n\t\t\tvar logDelta = helpers$1.log10(Math.abs(delta));\r\n\t\t\tvar tickString = '';\r\n\r\n\t\t\tif (tickValue !== 0) {\r\n\t\t\t\tvar maxTick = Math.max(Math.abs(ticks[0]), Math.abs(ticks[ticks.length - 1]));\r\n\t\t\t\tif (maxTick < 1e-4) { // all ticks are small numbers; use scientific notation\r\n\t\t\t\t\tvar logTick = helpers$1.log10(Math.abs(tickValue));\r\n\t\t\t\t\tvar numExponential = Math.floor(logTick) - Math.floor(logDelta);\r\n\t\t\t\t\tnumExponential = Math.max(Math.min(numExponential, 20), 0);\r\n\t\t\t\t\ttickString = tickValue.toExponential(numExponential);\r\n\t\t\t\t} else {\r\n\t\t\t\t\tvar numDecimal = -1 * Math.floor(logDelta);\r\n\t\t\t\t\tnumDecimal = Math.max(Math.min(numDecimal, 20), 0); // toFixed has a max of 20 decimal places\r\n\t\t\t\t\ttickString = tickValue.toFixed(numDecimal);\r\n\t\t\t\t}\r\n\t\t\t} else {\r\n\t\t\t\ttickString = '0'; // never show decimal places for 0\r\n\t\t\t}\r\n\r\n\t\t\treturn tickString;\r\n\t\t}</pre>",
                                        "_reviveId": 12
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0",
                                "precision": 2
                            },
                            "type": "linear",
                            "id": "y-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "left",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 13
                            }
                        },
                        "maxWidth": 506,
                        "maxHeight": 360,
                        "margins": {"left": 0, "right": 0, "top": 8.4465, "bottom": 8.4465},
                        "_ticks": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "ticks": ["6000", "5000", "4000", "3000", "2000", "1000", "0"],
                        "_labelSizes": {
                            "first": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 7.0712738037109375, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 28.363906860351562,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "0": 7.0712738037109375,
                                    "1000": 24.8170166015625,
                                    "2000": 27.688308715820312,
                                    "3000": 27.845947265625,
                                    "4000": 28.296356201171875,
                                    "5000": 28.28509521484375,
                                    "6000": 28.363906860351562
                                }, "gc": ["6000", "5000", "4000", "3000", "2000", "1000", "0"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.12110686035156,
                            "ty1": 8.5,
                            "tx2": 49,
                            "ty2": 8.5,
                            "x1": 50,
                            "y1": 8.5,
                            "x2": 1009,
                            "y2": 8.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 61.5,
                            "tx2": 49,
                            "ty2": 61.5,
                            "x1": 50,
                            "y1": 61.5,
                            "x2": 1009,
                            "y2": 61.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 113.5,
                            "tx2": 49,
                            "ty2": 113.5,
                            "x1": 50,
                            "y1": 113.5,
                            "x2": 1009,
                            "y2": 113.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 166.5,
                            "tx2": 49,
                            "ty2": 166.5,
                            "x1": 50,
                            "y1": 166.5,
                            "x2": 1009,
                            "y2": 166.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 219.5,
                            "tx2": 49,
                            "ty2": 219.5,
                            "x1": 50,
                            "y1": 219.5,
                            "x2": 1009,
                            "y2": 219.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 271.5,
                            "tx2": 49,
                            "ty2": 271.5,
                            "x1": 50,
                            "y1": 271.5,
                            "x2": 1009,
                            "y2": 271.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 324.5,
                            "tx2": 49,
                            "ty2": 324.5,
                            "x1": 50,
                            "y1": 324.5,
                            "x2": 1009,
                            "y2": 324.5,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 35.12110686035156,
                            "y": 8.4465,
                            "rotation": 0,
                            "label": "6000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 61.09704999999999,
                            "rotation": 0,
                            "label": "5000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 113.7476,
                            "rotation": 0,
                            "label": "4000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 166.39815,
                            "rotation": 0,
                            "label": "3000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 219.0487,
                            "rotation": 0,
                            "label": "2000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 271.69925,
                            "rotation": 0,
                            "label": "1000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 324.3498,
                            "rotation": 0,
                            "label": "0",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }],
                        "height": 315.9033,
                        "top": 8.4465,
                        "bottom": 324.3498,
                        "paddingLeft": 0,
                        "paddingTop": 8.4465,
                        "paddingRight": 0,
                        "paddingBottom": 8.4465,
                        "min": 0,
                        "max": 6000,
                        "start": 0,
                        "end": 6000,
                        "ticksAsNumbers": [6000, 5000, 4000, 3000, 2000, 1000, 0],
                        "zeroLineIndex": 6,
                        "_startPixel": 8.4465,
                        "_endPixel": 324.3498,
                        "_reversePixels": true,
                        "_length": 315.9033,
                        "_startValue": 0,
                        "_endValue": 6000,
                        "_valueRange": 6000,
                        "labelRotation": 0,
                        "minSize": {"width": 49.12110686035156, "height": 360},
                        "width": 49.12110686035156,
                        "_ticksToDraw": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "left": 0,
                        "right": 49.12110686035156
                    },
                    "_model": {
                        "backgroundColor": "#3874ff",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "Sales",
                        "label": "Tue",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 376.9349972673689,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_view": {
                        "backgroundColor": "#3874ff",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "Sales",
                        "label": "Tue",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 376.9349972673689,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 0,
                    "_index": 3,
                    "hidden": false,
                    "_xScale": {
                        "id": "x-axis-0",
                        "type": "category",
                        "options": {
                            "display": true,
                            "position": "bottom",
                            "offset": true,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": true,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 12,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                        "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                        "_reviveId": 14
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0"
                            },
                            "type": "category",
                            "id": "x-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "bottom",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 15
                            }
                        },
                        "maxWidth": 962.8788931396484,
                        "maxHeight": 180,
                        "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                        "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                        "_labelSizes": {
                            "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 25.582687377929688,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "Sun": 21.02239990234375,
                                    "Mon": 24.107635498046875,
                                    "Tue": 20.27923583984375,
                                    "Wed": 25.582687377929688,
                                    "Thu": 20.504440307617188,
                                    "Fri": 12.64495849609375,
                                    "Sat": 18.32000732421875
                                }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.5,
                            "ty1": 325,
                            "tx2": 49.5,
                            "ty2": 324.3498,
                            "x1": 49.5,
                            "y1": 8.4465,
                            "x2": 49.5,
                            "y2": 324,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 186.5,
                            "ty1": 325,
                            "tx2": 186.5,
                            "ty2": 324.3498,
                            "x1": 186.5,
                            "y1": 8.4465,
                            "x2": 186.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 323.5,
                            "ty1": 325,
                            "tx2": 323.5,
                            "ty2": 324.3498,
                            "x1": 323.5,
                            "y1": 8.4465,
                            "x2": 323.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 460.5,
                            "ty1": 325,
                            "tx2": 460.5,
                            "ty2": 324.3498,
                            "x1": 460.5,
                            "y1": 8.4465,
                            "x2": 460.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 597.5,
                            "ty1": 325,
                            "tx2": 597.5,
                            "ty2": 324.3498,
                            "x1": 597.5,
                            "y1": 8.4465,
                            "x2": 597.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 734.5,
                            "ty1": 325,
                            "tx2": 734.5,
                            "ty2": 324.3498,
                            "x1": 734.5,
                            "y1": 8.4465,
                            "x2": 734.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 871.5,
                            "ty1": 325,
                            "tx2": 871.5,
                            "ty2": 324.3498,
                            "x1": 871.5,
                            "y1": 8.4465,
                            "x2": 871.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 1009.5,
                            "ty1": 325,
                            "tx2": 1009.5,
                            "ty2": 324.3498,
                            "x1": 1009.5,
                            "y1": 8.4465,
                            "x2": 1009.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 117.68388494175501,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sun",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 254.80944110456193,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Mon",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 391.9349972673689,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Tue",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 529.0605534301758,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Wed",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 666.1861095929828,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Thu",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 803.3116657557896,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Fri",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 940.4372219185966,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sat",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }],
                        "width": 959.8788931396484,
                        "left": 49.12110686035156,
                        "right": 1009,
                        "paddingLeft": 3,
                        "paddingTop": 0,
                        "paddingRight": 3,
                        "paddingBottom": 0,
                        "minIndex": 0,
                        "maxIndex": 6,
                        "min": "Sun",
                        "max": "Sat",
                        "_startPixel": 49.12110686035156,
                        "_endPixel": 1009,
                        "_reversePixels": false,
                        "_length": 959.8788931396484,
                        "_startValue": -0.5,
                        "_valueRange": 7,
                        "labelRotation": 0,
                        "minSize": {"width": 962.8788931396484, "height": 35.6502},
                        "height": 35.6502,
                        "_ticksToDraw": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "top": 324.3498,
                        "bottom": 360
                    },
                    "_yScale": {
                        "id": "y-axis-0",
                        "type": "linear",
                        "options": {
                            "display": true,
                            "position": "left",
                            "offset": false,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": false,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 14,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> linear(tickValue, index, ticks)",
                                        "tooltip": "<pre>function(tickValue, index, ticks) {\r\n\t\t\t// If we have lots of ticks, don't use the ones\r\n\t\t\tvar delta = ticks.length > 3 ? ticks[2] - ticks[1] : ticks[1] - ticks[0];\r\n\r\n\t\t\t// If we have a number like 2.5 as the delta, figure out how many decimal places we need\r\n\t\t\tif (Math.abs(delta) > 1) {\r\n\t\t\t\tif (tickValue !== Math.floor(tickValue)) {\r\n\t\t\t\t\t// not an integer\r\n\t\t\t\t\tdelta = tickValue - Math.floor(tickValue);\r\n\t\t\t\t}\r\n\t\t\t}\r\n\r\n\t\t\tvar logDelta = helpers$1.log10(Math.abs(delta));\r\n\t\t\tvar tickString = '';\r\n\r\n\t\t\tif (tickValue !== 0) {\r\n\t\t\t\tvar maxTick = Math.max(Math.abs(ticks[0]), Math.abs(ticks[ticks.length - 1]));\r\n\t\t\t\tif (maxTick < 1e-4) { // all ticks are small numbers; use scientific notation\r\n\t\t\t\t\tvar logTick = helpers$1.log10(Math.abs(tickValue));\r\n\t\t\t\t\tvar numExponential = Math.floor(logTick) - Math.floor(logDelta);\r\n\t\t\t\t\tnumExponential = Math.max(Math.min(numExponential, 20), 0);\r\n\t\t\t\t\ttickString = tickValue.toExponential(numExponential);\r\n\t\t\t\t} else {\r\n\t\t\t\t\tvar numDecimal = -1 * Math.floor(logDelta);\r\n\t\t\t\t\tnumDecimal = Math.max(Math.min(numDecimal, 20), 0); // toFixed has a max of 20 decimal places\r\n\t\t\t\t\ttickString = tickValue.toFixed(numDecimal);\r\n\t\t\t\t}\r\n\t\t\t} else {\r\n\t\t\t\ttickString = '0'; // never show decimal places for 0\r\n\t\t\t}\r\n\r\n\t\t\treturn tickString;\r\n\t\t}</pre>",
                                        "_reviveId": 16
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0",
                                "precision": 2
                            },
                            "type": "linear",
                            "id": "y-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "left",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 17
                            }
                        },
                        "maxWidth": 506,
                        "maxHeight": 360,
                        "margins": {"left": 0, "right": 0, "top": 8.4465, "bottom": 8.4465},
                        "_ticks": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "ticks": ["6000", "5000", "4000", "3000", "2000", "1000", "0"],
                        "_labelSizes": {
                            "first": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 7.0712738037109375, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 28.363906860351562,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "0": 7.0712738037109375,
                                    "1000": 24.8170166015625,
                                    "2000": 27.688308715820312,
                                    "3000": 27.845947265625,
                                    "4000": 28.296356201171875,
                                    "5000": 28.28509521484375,
                                    "6000": 28.363906860351562
                                }, "gc": ["6000", "5000", "4000", "3000", "2000", "1000", "0"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.12110686035156,
                            "ty1": 8.5,
                            "tx2": 49,
                            "ty2": 8.5,
                            "x1": 50,
                            "y1": 8.5,
                            "x2": 1009,
                            "y2": 8.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 61.5,
                            "tx2": 49,
                            "ty2": 61.5,
                            "x1": 50,
                            "y1": 61.5,
                            "x2": 1009,
                            "y2": 61.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 113.5,
                            "tx2": 49,
                            "ty2": 113.5,
                            "x1": 50,
                            "y1": 113.5,
                            "x2": 1009,
                            "y2": 113.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 166.5,
                            "tx2": 49,
                            "ty2": 166.5,
                            "x1": 50,
                            "y1": 166.5,
                            "x2": 1009,
                            "y2": 166.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 219.5,
                            "tx2": 49,
                            "ty2": 219.5,
                            "x1": 50,
                            "y1": 219.5,
                            "x2": 1009,
                            "y2": 219.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 271.5,
                            "tx2": 49,
                            "ty2": 271.5,
                            "x1": 50,
                            "y1": 271.5,
                            "x2": 1009,
                            "y2": 271.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 324.5,
                            "tx2": 49,
                            "ty2": 324.5,
                            "x1": 50,
                            "y1": 324.5,
                            "x2": 1009,
                            "y2": 324.5,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 35.12110686035156,
                            "y": 8.4465,
                            "rotation": 0,
                            "label": "6000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 61.09704999999999,
                            "rotation": 0,
                            "label": "5000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 113.7476,
                            "rotation": 0,
                            "label": "4000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 166.39815,
                            "rotation": 0,
                            "label": "3000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 219.0487,
                            "rotation": 0,
                            "label": "2000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 271.69925,
                            "rotation": 0,
                            "label": "1000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 324.3498,
                            "rotation": 0,
                            "label": "0",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }],
                        "height": 315.9033,
                        "top": 8.4465,
                        "bottom": 324.3498,
                        "paddingLeft": 0,
                        "paddingTop": 8.4465,
                        "paddingRight": 0,
                        "paddingBottom": 8.4465,
                        "min": 0,
                        "max": 6000,
                        "start": 0,
                        "end": 6000,
                        "ticksAsNumbers": [6000, 5000, 4000, 3000, 2000, 1000, 0],
                        "zeroLineIndex": 6,
                        "_startPixel": 8.4465,
                        "_endPixel": 324.3498,
                        "_reversePixels": true,
                        "_length": 315.9033,
                        "_startValue": 0,
                        "_endValue": 6000,
                        "_valueRange": 6000,
                        "labelRotation": 0,
                        "minSize": {"width": 49.12110686035156, "height": 360},
                        "width": 49.12110686035156,
                        "_ticksToDraw": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "left": 0,
                        "right": 49.12110686035156
                    },
                    "_model": {
                        "backgroundColor": "#3874ff",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "Sales",
                        "label": "Wed",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 514.0605534301758,
                        "y": 47.9344125,
                        "width": 30
                    },
                    "_view": {
                        "backgroundColor": "#3874ff",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "Sales",
                        "label": "Wed",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 514.0605534301758,
                        "y": 47.9344125,
                        "width": 30
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 0,
                    "_index": 4,
                    "hidden": false,
                    "_xScale": {
                        "id": "x-axis-0",
                        "type": "category",
                        "options": {
                            "display": true,
                            "position": "bottom",
                            "offset": true,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": true,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 12,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                        "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                        "_reviveId": 18
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0"
                            },
                            "type": "category",
                            "id": "x-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "bottom",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 19
                            }
                        },
                        "maxWidth": 962.8788931396484,
                        "maxHeight": 180,
                        "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                        "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                        "_labelSizes": {
                            "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 25.582687377929688,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "Sun": 21.02239990234375,
                                    "Mon": 24.107635498046875,
                                    "Tue": 20.27923583984375,
                                    "Wed": 25.582687377929688,
                                    "Thu": 20.504440307617188,
                                    "Fri": 12.64495849609375,
                                    "Sat": 18.32000732421875
                                }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.5,
                            "ty1": 325,
                            "tx2": 49.5,
                            "ty2": 324.3498,
                            "x1": 49.5,
                            "y1": 8.4465,
                            "x2": 49.5,
                            "y2": 324,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 186.5,
                            "ty1": 325,
                            "tx2": 186.5,
                            "ty2": 324.3498,
                            "x1": 186.5,
                            "y1": 8.4465,
                            "x2": 186.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 323.5,
                            "ty1": 325,
                            "tx2": 323.5,
                            "ty2": 324.3498,
                            "x1": 323.5,
                            "y1": 8.4465,
                            "x2": 323.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 460.5,
                            "ty1": 325,
                            "tx2": 460.5,
                            "ty2": 324.3498,
                            "x1": 460.5,
                            "y1": 8.4465,
                            "x2": 460.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 597.5,
                            "ty1": 325,
                            "tx2": 597.5,
                            "ty2": 324.3498,
                            "x1": 597.5,
                            "y1": 8.4465,
                            "x2": 597.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 734.5,
                            "ty1": 325,
                            "tx2": 734.5,
                            "ty2": 324.3498,
                            "x1": 734.5,
                            "y1": 8.4465,
                            "x2": 734.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 871.5,
                            "ty1": 325,
                            "tx2": 871.5,
                            "ty2": 324.3498,
                            "x1": 871.5,
                            "y1": 8.4465,
                            "x2": 871.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 1009.5,
                            "ty1": 325,
                            "tx2": 1009.5,
                            "ty2": 324.3498,
                            "x1": 1009.5,
                            "y1": 8.4465,
                            "x2": 1009.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 117.68388494175501,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sun",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 254.80944110456193,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Mon",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 391.9349972673689,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Tue",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 529.0605534301758,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Wed",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 666.1861095929828,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Thu",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 803.3116657557896,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Fri",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 940.4372219185966,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sat",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }],
                        "width": 959.8788931396484,
                        "left": 49.12110686035156,
                        "right": 1009,
                        "paddingLeft": 3,
                        "paddingTop": 0,
                        "paddingRight": 3,
                        "paddingBottom": 0,
                        "minIndex": 0,
                        "maxIndex": 6,
                        "min": "Sun",
                        "max": "Sat",
                        "_startPixel": 49.12110686035156,
                        "_endPixel": 1009,
                        "_reversePixels": false,
                        "_length": 959.8788931396484,
                        "_startValue": -0.5,
                        "_valueRange": 7,
                        "labelRotation": 0,
                        "minSize": {"width": 962.8788931396484, "height": 35.6502},
                        "height": 35.6502,
                        "_ticksToDraw": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "top": 324.3498,
                        "bottom": 360
                    },
                    "_yScale": {
                        "id": "y-axis-0",
                        "type": "linear",
                        "options": {
                            "display": true,
                            "position": "left",
                            "offset": false,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": false,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 14,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> linear(tickValue, index, ticks)",
                                        "tooltip": "<pre>function(tickValue, index, ticks) {\r\n\t\t\t// If we have lots of ticks, don't use the ones\r\n\t\t\tvar delta = ticks.length > 3 ? ticks[2] - ticks[1] : ticks[1] - ticks[0];\r\n\r\n\t\t\t// If we have a number like 2.5 as the delta, figure out how many decimal places we need\r\n\t\t\tif (Math.abs(delta) > 1) {\r\n\t\t\t\tif (tickValue !== Math.floor(tickValue)) {\r\n\t\t\t\t\t// not an integer\r\n\t\t\t\t\tdelta = tickValue - Math.floor(tickValue);\r\n\t\t\t\t}\r\n\t\t\t}\r\n\r\n\t\t\tvar logDelta = helpers$1.log10(Math.abs(delta));\r\n\t\t\tvar tickString = '';\r\n\r\n\t\t\tif (tickValue !== 0) {\r\n\t\t\t\tvar maxTick = Math.max(Math.abs(ticks[0]), Math.abs(ticks[ticks.length - 1]));\r\n\t\t\t\tif (maxTick < 1e-4) { // all ticks are small numbers; use scientific notation\r\n\t\t\t\t\tvar logTick = helpers$1.log10(Math.abs(tickValue));\r\n\t\t\t\t\tvar numExponential = Math.floor(logTick) - Math.floor(logDelta);\r\n\t\t\t\t\tnumExponential = Math.max(Math.min(numExponential, 20), 0);\r\n\t\t\t\t\ttickString = tickValue.toExponential(numExponential);\r\n\t\t\t\t} else {\r\n\t\t\t\t\tvar numDecimal = -1 * Math.floor(logDelta);\r\n\t\t\t\t\tnumDecimal = Math.max(Math.min(numDecimal, 20), 0); // toFixed has a max of 20 decimal places\r\n\t\t\t\t\ttickString = tickValue.toFixed(numDecimal);\r\n\t\t\t\t}\r\n\t\t\t} else {\r\n\t\t\t\ttickString = '0'; // never show decimal places for 0\r\n\t\t\t}\r\n\r\n\t\t\treturn tickString;\r\n\t\t}</pre>",
                                        "_reviveId": 20
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0",
                                "precision": 2
                            },
                            "type": "linear",
                            "id": "y-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "left",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 21
                            }
                        },
                        "maxWidth": 506,
                        "maxHeight": 360,
                        "margins": {"left": 0, "right": 0, "top": 8.4465, "bottom": 8.4465},
                        "_ticks": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "ticks": ["6000", "5000", "4000", "3000", "2000", "1000", "0"],
                        "_labelSizes": {
                            "first": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 7.0712738037109375, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 28.363906860351562,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "0": 7.0712738037109375,
                                    "1000": 24.8170166015625,
                                    "2000": 27.688308715820312,
                                    "3000": 27.845947265625,
                                    "4000": 28.296356201171875,
                                    "5000": 28.28509521484375,
                                    "6000": 28.363906860351562
                                }, "gc": ["6000", "5000", "4000", "3000", "2000", "1000", "0"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.12110686035156,
                            "ty1": 8.5,
                            "tx2": 49,
                            "ty2": 8.5,
                            "x1": 50,
                            "y1": 8.5,
                            "x2": 1009,
                            "y2": 8.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 61.5,
                            "tx2": 49,
                            "ty2": 61.5,
                            "x1": 50,
                            "y1": 61.5,
                            "x2": 1009,
                            "y2": 61.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 113.5,
                            "tx2": 49,
                            "ty2": 113.5,
                            "x1": 50,
                            "y1": 113.5,
                            "x2": 1009,
                            "y2": 113.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 166.5,
                            "tx2": 49,
                            "ty2": 166.5,
                            "x1": 50,
                            "y1": 166.5,
                            "x2": 1009,
                            "y2": 166.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 219.5,
                            "tx2": 49,
                            "ty2": 219.5,
                            "x1": 50,
                            "y1": 219.5,
                            "x2": 1009,
                            "y2": 219.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 271.5,
                            "tx2": 49,
                            "ty2": 271.5,
                            "x1": 50,
                            "y1": 271.5,
                            "x2": 1009,
                            "y2": 271.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 324.5,
                            "tx2": 49,
                            "ty2": 324.5,
                            "x1": 50,
                            "y1": 324.5,
                            "x2": 1009,
                            "y2": 324.5,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 35.12110686035156,
                            "y": 8.4465,
                            "rotation": 0,
                            "label": "6000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 61.09704999999999,
                            "rotation": 0,
                            "label": "5000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 113.7476,
                            "rotation": 0,
                            "label": "4000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 166.39815,
                            "rotation": 0,
                            "label": "3000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 219.0487,
                            "rotation": 0,
                            "label": "2000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 271.69925,
                            "rotation": 0,
                            "label": "1000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 324.3498,
                            "rotation": 0,
                            "label": "0",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }],
                        "height": 315.9033,
                        "top": 8.4465,
                        "bottom": 324.3498,
                        "paddingLeft": 0,
                        "paddingTop": 8.4465,
                        "paddingRight": 0,
                        "paddingBottom": 8.4465,
                        "min": 0,
                        "max": 6000,
                        "start": 0,
                        "end": 6000,
                        "ticksAsNumbers": [6000, 5000, 4000, 3000, 2000, 1000, 0],
                        "zeroLineIndex": 6,
                        "_startPixel": 8.4465,
                        "_endPixel": 324.3498,
                        "_reversePixels": true,
                        "_length": 315.9033,
                        "_startValue": 0,
                        "_endValue": 6000,
                        "_valueRange": 6000,
                        "labelRotation": 0,
                        "minSize": {"width": 49.12110686035156, "height": 360},
                        "width": 49.12110686035156,
                        "_ticksToDraw": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "left": 0,
                        "right": 49.12110686035156
                    },
                    "_model": {
                        "backgroundColor": "#3874ff",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "Sales",
                        "label": "Thu",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 651.1861095929828,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_view": {
                        "backgroundColor": "#3874ff",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "Sales",
                        "label": "Thu",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 651.1861095929828,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 0,
                    "_index": 5,
                    "hidden": false,
                    "_xScale": {
                        "id": "x-axis-0",
                        "type": "category",
                        "options": {
                            "display": true,
                            "position": "bottom",
                            "offset": true,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": true,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 12,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                        "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                        "_reviveId": 22
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0"
                            },
                            "type": "category",
                            "id": "x-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "bottom",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 23
                            }
                        },
                        "maxWidth": 962.8788931396484,
                        "maxHeight": 180,
                        "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                        "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                        "_labelSizes": {
                            "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 25.582687377929688,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "Sun": 21.02239990234375,
                                    "Mon": 24.107635498046875,
                                    "Tue": 20.27923583984375,
                                    "Wed": 25.582687377929688,
                                    "Thu": 20.504440307617188,
                                    "Fri": 12.64495849609375,
                                    "Sat": 18.32000732421875
                                }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.5,
                            "ty1": 325,
                            "tx2": 49.5,
                            "ty2": 324.3498,
                            "x1": 49.5,
                            "y1": 8.4465,
                            "x2": 49.5,
                            "y2": 324,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 186.5,
                            "ty1": 325,
                            "tx2": 186.5,
                            "ty2": 324.3498,
                            "x1": 186.5,
                            "y1": 8.4465,
                            "x2": 186.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 323.5,
                            "ty1": 325,
                            "tx2": 323.5,
                            "ty2": 324.3498,
                            "x1": 323.5,
                            "y1": 8.4465,
                            "x2": 323.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 460.5,
                            "ty1": 325,
                            "tx2": 460.5,
                            "ty2": 324.3498,
                            "x1": 460.5,
                            "y1": 8.4465,
                            "x2": 460.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 597.5,
                            "ty1": 325,
                            "tx2": 597.5,
                            "ty2": 324.3498,
                            "x1": 597.5,
                            "y1": 8.4465,
                            "x2": 597.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 734.5,
                            "ty1": 325,
                            "tx2": 734.5,
                            "ty2": 324.3498,
                            "x1": 734.5,
                            "y1": 8.4465,
                            "x2": 734.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 871.5,
                            "ty1": 325,
                            "tx2": 871.5,
                            "ty2": 324.3498,
                            "x1": 871.5,
                            "y1": 8.4465,
                            "x2": 871.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 1009.5,
                            "ty1": 325,
                            "tx2": 1009.5,
                            "ty2": 324.3498,
                            "x1": 1009.5,
                            "y1": 8.4465,
                            "x2": 1009.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 117.68388494175501,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sun",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 254.80944110456193,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Mon",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 391.9349972673689,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Tue",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 529.0605534301758,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Wed",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 666.1861095929828,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Thu",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 803.3116657557896,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Fri",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 940.4372219185966,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sat",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }],
                        "width": 959.8788931396484,
                        "left": 49.12110686035156,
                        "right": 1009,
                        "paddingLeft": 3,
                        "paddingTop": 0,
                        "paddingRight": 3,
                        "paddingBottom": 0,
                        "minIndex": 0,
                        "maxIndex": 6,
                        "min": "Sun",
                        "max": "Sat",
                        "_startPixel": 49.12110686035156,
                        "_endPixel": 1009,
                        "_reversePixels": false,
                        "_length": 959.8788931396484,
                        "_startValue": -0.5,
                        "_valueRange": 7,
                        "labelRotation": 0,
                        "minSize": {"width": 962.8788931396484, "height": 35.6502},
                        "height": 35.6502,
                        "_ticksToDraw": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "top": 324.3498,
                        "bottom": 360
                    },
                    "_yScale": {
                        "id": "y-axis-0",
                        "type": "linear",
                        "options": {
                            "display": true,
                            "position": "left",
                            "offset": false,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": false,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 14,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> linear(tickValue, index, ticks)",
                                        "tooltip": "<pre>function(tickValue, index, ticks) {\r\n\t\t\t// If we have lots of ticks, don't use the ones\r\n\t\t\tvar delta = ticks.length > 3 ? ticks[2] - ticks[1] : ticks[1] - ticks[0];\r\n\r\n\t\t\t// If we have a number like 2.5 as the delta, figure out how many decimal places we need\r\n\t\t\tif (Math.abs(delta) > 1) {\r\n\t\t\t\tif (tickValue !== Math.floor(tickValue)) {\r\n\t\t\t\t\t// not an integer\r\n\t\t\t\t\tdelta = tickValue - Math.floor(tickValue);\r\n\t\t\t\t}\r\n\t\t\t}\r\n\r\n\t\t\tvar logDelta = helpers$1.log10(Math.abs(delta));\r\n\t\t\tvar tickString = '';\r\n\r\n\t\t\tif (tickValue !== 0) {\r\n\t\t\t\tvar maxTick = Math.max(Math.abs(ticks[0]), Math.abs(ticks[ticks.length - 1]));\r\n\t\t\t\tif (maxTick < 1e-4) { // all ticks are small numbers; use scientific notation\r\n\t\t\t\t\tvar logTick = helpers$1.log10(Math.abs(tickValue));\r\n\t\t\t\t\tvar numExponential = Math.floor(logTick) - Math.floor(logDelta);\r\n\t\t\t\t\tnumExponential = Math.max(Math.min(numExponential, 20), 0);\r\n\t\t\t\t\ttickString = tickValue.toExponential(numExponential);\r\n\t\t\t\t} else {\r\n\t\t\t\t\tvar numDecimal = -1 * Math.floor(logDelta);\r\n\t\t\t\t\tnumDecimal = Math.max(Math.min(numDecimal, 20), 0); // toFixed has a max of 20 decimal places\r\n\t\t\t\t\ttickString = tickValue.toFixed(numDecimal);\r\n\t\t\t\t}\r\n\t\t\t} else {\r\n\t\t\t\ttickString = '0'; // never show decimal places for 0\r\n\t\t\t}\r\n\r\n\t\t\treturn tickString;\r\n\t\t}</pre>",
                                        "_reviveId": 24
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0",
                                "precision": 2
                            },
                            "type": "linear",
                            "id": "y-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "left",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 25
                            }
                        },
                        "maxWidth": 506,
                        "maxHeight": 360,
                        "margins": {"left": 0, "right": 0, "top": 8.4465, "bottom": 8.4465},
                        "_ticks": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "ticks": ["6000", "5000", "4000", "3000", "2000", "1000", "0"],
                        "_labelSizes": {
                            "first": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 7.0712738037109375, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 28.363906860351562,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "0": 7.0712738037109375,
                                    "1000": 24.8170166015625,
                                    "2000": 27.688308715820312,
                                    "3000": 27.845947265625,
                                    "4000": 28.296356201171875,
                                    "5000": 28.28509521484375,
                                    "6000": 28.363906860351562
                                }, "gc": ["6000", "5000", "4000", "3000", "2000", "1000", "0"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.12110686035156,
                            "ty1": 8.5,
                            "tx2": 49,
                            "ty2": 8.5,
                            "x1": 50,
                            "y1": 8.5,
                            "x2": 1009,
                            "y2": 8.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 61.5,
                            "tx2": 49,
                            "ty2": 61.5,
                            "x1": 50,
                            "y1": 61.5,
                            "x2": 1009,
                            "y2": 61.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 113.5,
                            "tx2": 49,
                            "ty2": 113.5,
                            "x1": 50,
                            "y1": 113.5,
                            "x2": 1009,
                            "y2": 113.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 166.5,
                            "tx2": 49,
                            "ty2": 166.5,
                            "x1": 50,
                            "y1": 166.5,
                            "x2": 1009,
                            "y2": 166.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 219.5,
                            "tx2": 49,
                            "ty2": 219.5,
                            "x1": 50,
                            "y1": 219.5,
                            "x2": 1009,
                            "y2": 219.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 271.5,
                            "tx2": 49,
                            "ty2": 271.5,
                            "x1": 50,
                            "y1": 271.5,
                            "x2": 1009,
                            "y2": 271.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 324.5,
                            "tx2": 49,
                            "ty2": 324.5,
                            "x1": 50,
                            "y1": 324.5,
                            "x2": 1009,
                            "y2": 324.5,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 35.12110686035156,
                            "y": 8.4465,
                            "rotation": 0,
                            "label": "6000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 61.09704999999999,
                            "rotation": 0,
                            "label": "5000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 113.7476,
                            "rotation": 0,
                            "label": "4000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 166.39815,
                            "rotation": 0,
                            "label": "3000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 219.0487,
                            "rotation": 0,
                            "label": "2000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 271.69925,
                            "rotation": 0,
                            "label": "1000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 324.3498,
                            "rotation": 0,
                            "label": "0",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }],
                        "height": 315.9033,
                        "top": 8.4465,
                        "bottom": 324.3498,
                        "paddingLeft": 0,
                        "paddingTop": 8.4465,
                        "paddingRight": 0,
                        "paddingBottom": 8.4465,
                        "min": 0,
                        "max": 6000,
                        "start": 0,
                        "end": 6000,
                        "ticksAsNumbers": [6000, 5000, 4000, 3000, 2000, 1000, 0],
                        "zeroLineIndex": 6,
                        "_startPixel": 8.4465,
                        "_endPixel": 324.3498,
                        "_reversePixels": true,
                        "_length": 315.9033,
                        "_startValue": 0,
                        "_endValue": 6000,
                        "_valueRange": 6000,
                        "labelRotation": 0,
                        "minSize": {"width": 49.12110686035156, "height": 360},
                        "width": 49.12110686035156,
                        "_ticksToDraw": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "left": 0,
                        "right": 49.12110686035156
                    },
                    "_model": {
                        "backgroundColor": "#3874ff",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "Sales",
                        "label": "Fri",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 788.3116657557896,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_view": {
                        "backgroundColor": "#3874ff",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "Sales",
                        "label": "Fri",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 788.3116657557896,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 0,
                    "_index": 6,
                    "hidden": false,
                    "_xScale": {
                        "id": "x-axis-0",
                        "type": "category",
                        "options": {
                            "display": true,
                            "position": "bottom",
                            "offset": true,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": true,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 12,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                        "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                        "_reviveId": 26
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0"
                            },
                            "type": "category",
                            "id": "x-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "bottom",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 27
                            }
                        },
                        "maxWidth": 962.8788931396484,
                        "maxHeight": 180,
                        "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                        "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                        "_labelSizes": {
                            "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 25.582687377929688,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "Sun": 21.02239990234375,
                                    "Mon": 24.107635498046875,
                                    "Tue": 20.27923583984375,
                                    "Wed": 25.582687377929688,
                                    "Thu": 20.504440307617188,
                                    "Fri": 12.64495849609375,
                                    "Sat": 18.32000732421875
                                }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.5,
                            "ty1": 325,
                            "tx2": 49.5,
                            "ty2": 324.3498,
                            "x1": 49.5,
                            "y1": 8.4465,
                            "x2": 49.5,
                            "y2": 324,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 186.5,
                            "ty1": 325,
                            "tx2": 186.5,
                            "ty2": 324.3498,
                            "x1": 186.5,
                            "y1": 8.4465,
                            "x2": 186.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 323.5,
                            "ty1": 325,
                            "tx2": 323.5,
                            "ty2": 324.3498,
                            "x1": 323.5,
                            "y1": 8.4465,
                            "x2": 323.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 460.5,
                            "ty1": 325,
                            "tx2": 460.5,
                            "ty2": 324.3498,
                            "x1": 460.5,
                            "y1": 8.4465,
                            "x2": 460.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 597.5,
                            "ty1": 325,
                            "tx2": 597.5,
                            "ty2": 324.3498,
                            "x1": 597.5,
                            "y1": 8.4465,
                            "x2": 597.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 734.5,
                            "ty1": 325,
                            "tx2": 734.5,
                            "ty2": 324.3498,
                            "x1": 734.5,
                            "y1": 8.4465,
                            "x2": 734.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 871.5,
                            "ty1": 325,
                            "tx2": 871.5,
                            "ty2": 324.3498,
                            "x1": 871.5,
                            "y1": 8.4465,
                            "x2": 871.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 1009.5,
                            "ty1": 325,
                            "tx2": 1009.5,
                            "ty2": 324.3498,
                            "x1": 1009.5,
                            "y1": 8.4465,
                            "x2": 1009.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 117.68388494175501,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sun",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 254.80944110456193,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Mon",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 391.9349972673689,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Tue",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 529.0605534301758,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Wed",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 666.1861095929828,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Thu",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 803.3116657557896,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Fri",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 940.4372219185966,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sat",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }],
                        "width": 959.8788931396484,
                        "left": 49.12110686035156,
                        "right": 1009,
                        "paddingLeft": 3,
                        "paddingTop": 0,
                        "paddingRight": 3,
                        "paddingBottom": 0,
                        "minIndex": 0,
                        "maxIndex": 6,
                        "min": "Sun",
                        "max": "Sat",
                        "_startPixel": 49.12110686035156,
                        "_endPixel": 1009,
                        "_reversePixels": false,
                        "_length": 959.8788931396484,
                        "_startValue": -0.5,
                        "_valueRange": 7,
                        "labelRotation": 0,
                        "minSize": {"width": 962.8788931396484, "height": 35.6502},
                        "height": 35.6502,
                        "_ticksToDraw": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "top": 324.3498,
                        "bottom": 360
                    },
                    "_yScale": {
                        "id": "y-axis-0",
                        "type": "linear",
                        "options": {
                            "display": true,
                            "position": "left",
                            "offset": false,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": false,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 14,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> linear(tickValue, index, ticks)",
                                        "tooltip": "<pre>function(tickValue, index, ticks) {\r\n\t\t\t// If we have lots of ticks, don't use the ones\r\n\t\t\tvar delta = ticks.length > 3 ? ticks[2] - ticks[1] : ticks[1] - ticks[0];\r\n\r\n\t\t\t// If we have a number like 2.5 as the delta, figure out how many decimal places we need\r\n\t\t\tif (Math.abs(delta) > 1) {\r\n\t\t\t\tif (tickValue !== Math.floor(tickValue)) {\r\n\t\t\t\t\t// not an integer\r\n\t\t\t\t\tdelta = tickValue - Math.floor(tickValue);\r\n\t\t\t\t}\r\n\t\t\t}\r\n\r\n\t\t\tvar logDelta = helpers$1.log10(Math.abs(delta));\r\n\t\t\tvar tickString = '';\r\n\r\n\t\t\tif (tickValue !== 0) {\r\n\t\t\t\tvar maxTick = Math.max(Math.abs(ticks[0]), Math.abs(ticks[ticks.length - 1]));\r\n\t\t\t\tif (maxTick < 1e-4) { // all ticks are small numbers; use scientific notation\r\n\t\t\t\t\tvar logTick = helpers$1.log10(Math.abs(tickValue));\r\n\t\t\t\t\tvar numExponential = Math.floor(logTick) - Math.floor(logDelta);\r\n\t\t\t\t\tnumExponential = Math.max(Math.min(numExponential, 20), 0);\r\n\t\t\t\t\ttickString = tickValue.toExponential(numExponential);\r\n\t\t\t\t} else {\r\n\t\t\t\t\tvar numDecimal = -1 * Math.floor(logDelta);\r\n\t\t\t\t\tnumDecimal = Math.max(Math.min(numDecimal, 20), 0); // toFixed has a max of 20 decimal places\r\n\t\t\t\t\ttickString = tickValue.toFixed(numDecimal);\r\n\t\t\t\t}\r\n\t\t\t} else {\r\n\t\t\t\ttickString = '0'; // never show decimal places for 0\r\n\t\t\t}\r\n\r\n\t\t\treturn tickString;\r\n\t\t}</pre>",
                                        "_reviveId": 28
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0",
                                "precision": 2
                            },
                            "type": "linear",
                            "id": "y-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "left",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 29
                            }
                        },
                        "maxWidth": 506,
                        "maxHeight": 360,
                        "margins": {"left": 0, "right": 0, "top": 8.4465, "bottom": 8.4465},
                        "_ticks": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "ticks": ["6000", "5000", "4000", "3000", "2000", "1000", "0"],
                        "_labelSizes": {
                            "first": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 7.0712738037109375, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 28.363906860351562,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "0": 7.0712738037109375,
                                    "1000": 24.8170166015625,
                                    "2000": 27.688308715820312,
                                    "3000": 27.845947265625,
                                    "4000": 28.296356201171875,
                                    "5000": 28.28509521484375,
                                    "6000": 28.363906860351562
                                }, "gc": ["6000", "5000", "4000", "3000", "2000", "1000", "0"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.12110686035156,
                            "ty1": 8.5,
                            "tx2": 49,
                            "ty2": 8.5,
                            "x1": 50,
                            "y1": 8.5,
                            "x2": 1009,
                            "y2": 8.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 61.5,
                            "tx2": 49,
                            "ty2": 61.5,
                            "x1": 50,
                            "y1": 61.5,
                            "x2": 1009,
                            "y2": 61.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 113.5,
                            "tx2": 49,
                            "ty2": 113.5,
                            "x1": 50,
                            "y1": 113.5,
                            "x2": 1009,
                            "y2": 113.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 166.5,
                            "tx2": 49,
                            "ty2": 166.5,
                            "x1": 50,
                            "y1": 166.5,
                            "x2": 1009,
                            "y2": 166.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 219.5,
                            "tx2": 49,
                            "ty2": 219.5,
                            "x1": 50,
                            "y1": 219.5,
                            "x2": 1009,
                            "y2": 219.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 271.5,
                            "tx2": 49,
                            "ty2": 271.5,
                            "x1": 50,
                            "y1": 271.5,
                            "x2": 1009,
                            "y2": 271.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 324.5,
                            "tx2": 49,
                            "ty2": 324.5,
                            "x1": 50,
                            "y1": 324.5,
                            "x2": 1009,
                            "y2": 324.5,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 35.12110686035156,
                            "y": 8.4465,
                            "rotation": 0,
                            "label": "6000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 61.09704999999999,
                            "rotation": 0,
                            "label": "5000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 113.7476,
                            "rotation": 0,
                            "label": "4000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 166.39815,
                            "rotation": 0,
                            "label": "3000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 219.0487,
                            "rotation": 0,
                            "label": "2000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 271.69925,
                            "rotation": 0,
                            "label": "1000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 324.3498,
                            "rotation": 0,
                            "label": "0",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }],
                        "height": 315.9033,
                        "top": 8.4465,
                        "bottom": 324.3498,
                        "paddingLeft": 0,
                        "paddingTop": 8.4465,
                        "paddingRight": 0,
                        "paddingBottom": 8.4465,
                        "min": 0,
                        "max": 6000,
                        "start": 0,
                        "end": 6000,
                        "ticksAsNumbers": [6000, 5000, 4000, 3000, 2000, 1000, 0],
                        "zeroLineIndex": 6,
                        "_startPixel": 8.4465,
                        "_endPixel": 324.3498,
                        "_reversePixels": true,
                        "_length": 315.9033,
                        "_startValue": 0,
                        "_endValue": 6000,
                        "_valueRange": 6000,
                        "labelRotation": 0,
                        "minSize": {"width": 49.12110686035156, "height": 360},
                        "width": 49.12110686035156,
                        "_ticksToDraw": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "left": 0,
                        "right": 49.12110686035156
                    },
                    "_model": {
                        "backgroundColor": "#3874ff",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "Sales",
                        "label": "Sat",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 925.4372219185966,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_view": {
                        "backgroundColor": "#3874ff",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "Sales",
                        "label": "Sat",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 925.4372219185966,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_start": null
                }],
                "dataset": null,
                "controller": {
                    "chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "index": 0,
                    "_type": "bar",
                    "_data": [0, 0, 0, 5250, 0, 0, 0],
                    "_config": {
                        "categoryPercentage": 0.8,
                        "barPercentage": 0.5,
                        "label": "Sales",
                        "barThickness": 30,
                        "borderWidth": 1,
                        "backgroundColor": "#3874ff"
                    },
                    "_cachedDataOpts": {
                        "backgroundColor": "#3874ff",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "barPercentage": 0.5,
                        "barThickness": 30,
                        "categoryPercentage": 0.8
                    },
                    "_ruler": {
                        "pixels": [117.68388494175501, 254.80944110456193, 391.9349972673689, 529.0605534301758, 666.1861095929828, 803.3116657557896, 940.4372219185966],
                        "start": 49.12110686035156,
                        "end": 1009,
                        "stackCount": 2,
                        "scale": {
                            "id": "x-axis-0",
                            "type": "category",
                            "options": {
                                "display": true,
                                "position": "bottom",
                                "offset": true,
                                "gridLines": {
                                    "display": true,
                                    "color": "#F0F2F5",
                                    "lineWidth": 1,
                                    "drawBorder": true,
                                    "drawOnChartArea": true,
                                    "drawTicks": false,
                                    "tickMarkLength": 10,
                                    "zeroLineWidth": 1,
                                    "zeroLineColor": "rgba(0,0,0,0.25)",
                                    "zeroLineBorderDash": [8, 4],
                                    "zeroLineBorderDashOffset": 0,
                                    "offsetGridLines": true,
                                    "borderDash": [8, 4],
                                    "borderDashOffset": 0
                                },
                                "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                                "ticks": {
                                    "beginAtZero": true,
                                    "minRotation": 0,
                                    "maxRotation": 50,
                                    "mirror": false,
                                    "padding": 12,
                                    "reverse": false,
                                    "display": true,
                                    "autoSkip": true,
                                    "autoSkipPadding": 0,
                                    "labelOffset": 0,
                                    "callback": {
                                        "_custom": {
                                            "type": "function",
                                            "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                            "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                            "_reviveId": 30
                                        }
                                    },
                                    "minor": {},
                                    "major": {},
                                    "lineHeight": 1.5,
                                    "fontFamily": "'Poppins', sans-serif",
                                    "fontSize": 11.262,
                                    "fontColor": "#9397A0"
                                },
                                "type": "category",
                                "id": "x-axis-0"
                            },
                            "ctx": "[object CanvasRenderingContext2D]",
                            "chart": {
                                "_custom": {
                                    "type": "component-definition",
                                    "display": "<i>Unknown Component</i>",
                                    "tooltip": "Component definition"
                                }
                            },
                            "hidden": false,
                            "fullWidth": false,
                            "position": "bottom",
                            "weight": 0,
                            "_layers": {
                                "_custom": {
                                    "type": "function",
                                    "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                    "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                    "_reviveId": 31
                                }
                            },
                            "maxWidth": 962.8788931396484,
                            "maxHeight": 180,
                            "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                            "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                                "value": "Mon",
                                "major": false,
                                "label": "Mon",
                                "_index": 1
                            }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                                "value": "Wed",
                                "major": false,
                                "label": "Wed",
                                "_index": 3
                            }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                                "value": "Fri",
                                "major": false,
                                "label": "Fri",
                                "_index": 5
                            }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                            "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                            "_labelSizes": {
                                "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                                "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                                "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                                "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                            },
                            "_maxLabelLines": 0,
                            "longestLabelWidth": 25.582687377929688,
                            "longestTextCache": {
                                "normal 11.262px 'Poppins', sans-serif": {
                                    "data": {
                                        "Sun": 21.02239990234375,
                                        "Mon": 24.107635498046875,
                                        "Tue": 20.27923583984375,
                                        "Wed": 25.582687377929688,
                                        "Thu": 20.504440307617188,
                                        "Fri": 12.64495849609375,
                                        "Sat": 18.32000732421875
                                    }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                                }
                            },
                            "_gridLineItems": [{
                                "tx1": 49.5,
                                "ty1": 325,
                                "tx2": 49.5,
                                "ty2": 324.3498,
                                "x1": 49.5,
                                "y1": 8.4465,
                                "x2": 49.5,
                                "y2": 324,
                                "width": 1,
                                "color": "rgba(0,0,0,0.25)",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }, {
                                "tx1": 186.5,
                                "ty1": 325,
                                "tx2": 186.5,
                                "ty2": 324.3498,
                                "x1": 186.5,
                                "y1": 8.4465,
                                "x2": 186.5,
                                "y2": 324,
                                "width": 1,
                                "color": "#F0F2F5",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }, {
                                "tx1": 323.5,
                                "ty1": 325,
                                "tx2": 323.5,
                                "ty2": 324.3498,
                                "x1": 323.5,
                                "y1": 8.4465,
                                "x2": 323.5,
                                "y2": 324,
                                "width": 1,
                                "color": "#F0F2F5",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }, {
                                "tx1": 460.5,
                                "ty1": 325,
                                "tx2": 460.5,
                                "ty2": 324.3498,
                                "x1": 460.5,
                                "y1": 8.4465,
                                "x2": 460.5,
                                "y2": 324,
                                "width": 1,
                                "color": "#F0F2F5",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }, {
                                "tx1": 597.5,
                                "ty1": 325,
                                "tx2": 597.5,
                                "ty2": 324.3498,
                                "x1": 597.5,
                                "y1": 8.4465,
                                "x2": 597.5,
                                "y2": 324,
                                "width": 1,
                                "color": "#F0F2F5",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }, {
                                "tx1": 734.5,
                                "ty1": 325,
                                "tx2": 734.5,
                                "ty2": 324.3498,
                                "x1": 734.5,
                                "y1": 8.4465,
                                "x2": 734.5,
                                "y2": 324,
                                "width": 1,
                                "color": "#F0F2F5",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }, {
                                "tx1": 871.5,
                                "ty1": 325,
                                "tx2": 871.5,
                                "ty2": 324.3498,
                                "x1": 871.5,
                                "y1": 8.4465,
                                "x2": 871.5,
                                "y2": 324,
                                "width": 1,
                                "color": "#F0F2F5",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }, {
                                "tx1": 1009.5,
                                "ty1": 325,
                                "tx2": 1009.5,
                                "ty2": 324.3498,
                                "x1": 1009.5,
                                "y1": 8.4465,
                                "x2": 1009.5,
                                "y2": 324,
                                "width": 1,
                                "color": "#F0F2F5",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }],
                            "_labelItems": [{
                                "x": 117.68388494175501,
                                "y": 336.3498,
                                "rotation": 0,
                                "label": "Sun",
                                "font": {
                                    "family": "'Poppins', sans-serif",
                                    "lineHeight": 16.893,
                                    "size": 11.262,
                                    "style": "normal",
                                    "weight": null,
                                    "string": "normal 11.262px 'Poppins', sans-serif",
                                    "color": "#9397A0"
                                },
                                "textOffset": 8.4465,
                                "textAlign": "center"
                            }, {
                                "x": 254.80944110456193,
                                "y": 336.3498,
                                "rotation": 0,
                                "label": "Mon",
                                "font": {
                                    "family": "'Poppins', sans-serif",
                                    "lineHeight": 16.893,
                                    "size": 11.262,
                                    "style": "normal",
                                    "weight": null,
                                    "string": "normal 11.262px 'Poppins', sans-serif",
                                    "color": "#9397A0"
                                },
                                "textOffset": 8.4465,
                                "textAlign": "center"
                            }, {
                                "x": 391.9349972673689,
                                "y": 336.3498,
                                "rotation": 0,
                                "label": "Tue",
                                "font": {
                                    "family": "'Poppins', sans-serif",
                                    "lineHeight": 16.893,
                                    "size": 11.262,
                                    "style": "normal",
                                    "weight": null,
                                    "string": "normal 11.262px 'Poppins', sans-serif",
                                    "color": "#9397A0"
                                },
                                "textOffset": 8.4465,
                                "textAlign": "center"
                            }, {
                                "x": 529.0605534301758,
                                "y": 336.3498,
                                "rotation": 0,
                                "label": "Wed",
                                "font": {
                                    "family": "'Poppins', sans-serif",
                                    "lineHeight": 16.893,
                                    "size": 11.262,
                                    "style": "normal",
                                    "weight": null,
                                    "string": "normal 11.262px 'Poppins', sans-serif",
                                    "color": "#9397A0"
                                },
                                "textOffset": 8.4465,
                                "textAlign": "center"
                            }, {
                                "x": 666.1861095929828,
                                "y": 336.3498,
                                "rotation": 0,
                                "label": "Thu",
                                "font": {
                                    "family": "'Poppins', sans-serif",
                                    "lineHeight": 16.893,
                                    "size": 11.262,
                                    "style": "normal",
                                    "weight": null,
                                    "string": "normal 11.262px 'Poppins', sans-serif",
                                    "color": "#9397A0"
                                },
                                "textOffset": 8.4465,
                                "textAlign": "center"
                            }, {
                                "x": 803.3116657557896,
                                "y": 336.3498,
                                "rotation": 0,
                                "label": "Fri",
                                "font": {
                                    "family": "'Poppins', sans-serif",
                                    "lineHeight": 16.893,
                                    "size": 11.262,
                                    "style": "normal",
                                    "weight": null,
                                    "string": "normal 11.262px 'Poppins', sans-serif",
                                    "color": "#9397A0"
                                },
                                "textOffset": 8.4465,
                                "textAlign": "center"
                            }, {
                                "x": 940.4372219185966,
                                "y": 336.3498,
                                "rotation": 0,
                                "label": "Sat",
                                "font": {
                                    "family": "'Poppins', sans-serif",
                                    "lineHeight": 16.893,
                                    "size": 11.262,
                                    "style": "normal",
                                    "weight": null,
                                    "string": "normal 11.262px 'Poppins', sans-serif",
                                    "color": "#9397A0"
                                },
                                "textOffset": 8.4465,
                                "textAlign": "center"
                            }],
                            "width": 959.8788931396484,
                            "left": 49.12110686035156,
                            "right": 1009,
                            "paddingLeft": 3,
                            "paddingTop": 0,
                            "paddingRight": 3,
                            "paddingBottom": 0,
                            "minIndex": 0,
                            "maxIndex": 6,
                            "min": "Sun",
                            "max": "Sat",
                            "_startPixel": 49.12110686035156,
                            "_endPixel": 1009,
                            "_reversePixels": false,
                            "_length": 959.8788931396484,
                            "_startValue": -0.5,
                            "_valueRange": 7,
                            "labelRotation": 0,
                            "minSize": {"width": 962.8788931396484, "height": 35.6502},
                            "height": 35.6502,
                            "_ticksToDraw": [{
                                "value": "Sun",
                                "major": false,
                                "label": "Sun",
                                "_index": 0
                            }, {"value": "Mon", "major": false, "label": "Mon", "_index": 1}, {
                                "value": "Tue",
                                "major": false,
                                "label": "Tue",
                                "_index": 2
                            }, {"value": "Wed", "major": false, "label": "Wed", "_index": 3}, {
                                "value": "Thu",
                                "major": false,
                                "label": "Thu",
                                "_index": 4
                            }, {"value": "Fri", "major": false, "label": "Fri", "_index": 5}, {
                                "value": "Sat",
                                "major": false,
                                "label": "Sat",
                                "_index": 6
                            }],
                            "top": 324.3498,
                            "bottom": 360
                        }
                    }
                },
                "hidden": null,
                "xAxisID": "x-axis-0",
                "yAxisID": "y-axis-0",
                "order": 0,
                "index": 0,
                "bar": true,
                "$filler": null
            }
        }
    }, {
        "label": "purchase",
        "barPercentage": 0.5,
        "barThickness": 30,
        "borderWidth": 1,
        "backgroundColor": "#5bc5d5",
        "data": [0, 0, 0, 0, 0, 0, 0],
        "_meta": {
            "31": {
                "type": "bar",
                "data": [{
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 1,
                    "_index": 0,
                    "hidden": false,
                    "_xScale": {
                        "id": "x-axis-0",
                        "type": "category",
                        "options": {
                            "display": true,
                            "position": "bottom",
                            "offset": true,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": true,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 12,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                        "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                        "_reviveId": 32
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0"
                            },
                            "type": "category",
                            "id": "x-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "bottom",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 33
                            }
                        },
                        "maxWidth": 962.8788931396484,
                        "maxHeight": 180,
                        "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                        "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                        "_labelSizes": {
                            "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 25.582687377929688,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "Sun": 21.02239990234375,
                                    "Mon": 24.107635498046875,
                                    "Tue": 20.27923583984375,
                                    "Wed": 25.582687377929688,
                                    "Thu": 20.504440307617188,
                                    "Fri": 12.64495849609375,
                                    "Sat": 18.32000732421875
                                }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.5,
                            "ty1": 325,
                            "tx2": 49.5,
                            "ty2": 324.3498,
                            "x1": 49.5,
                            "y1": 8.4465,
                            "x2": 49.5,
                            "y2": 324,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 186.5,
                            "ty1": 325,
                            "tx2": 186.5,
                            "ty2": 324.3498,
                            "x1": 186.5,
                            "y1": 8.4465,
                            "x2": 186.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 323.5,
                            "ty1": 325,
                            "tx2": 323.5,
                            "ty2": 324.3498,
                            "x1": 323.5,
                            "y1": 8.4465,
                            "x2": 323.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 460.5,
                            "ty1": 325,
                            "tx2": 460.5,
                            "ty2": 324.3498,
                            "x1": 460.5,
                            "y1": 8.4465,
                            "x2": 460.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 597.5,
                            "ty1": 325,
                            "tx2": 597.5,
                            "ty2": 324.3498,
                            "x1": 597.5,
                            "y1": 8.4465,
                            "x2": 597.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 734.5,
                            "ty1": 325,
                            "tx2": 734.5,
                            "ty2": 324.3498,
                            "x1": 734.5,
                            "y1": 8.4465,
                            "x2": 734.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 871.5,
                            "ty1": 325,
                            "tx2": 871.5,
                            "ty2": 324.3498,
                            "x1": 871.5,
                            "y1": 8.4465,
                            "x2": 871.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 1009.5,
                            "ty1": 325,
                            "tx2": 1009.5,
                            "ty2": 324.3498,
                            "x1": 1009.5,
                            "y1": 8.4465,
                            "x2": 1009.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 117.68388494175501,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sun",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 254.80944110456193,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Mon",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 391.9349972673689,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Tue",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 529.0605534301758,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Wed",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 666.1861095929828,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Thu",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 803.3116657557896,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Fri",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 940.4372219185966,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sat",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }],
                        "width": 959.8788931396484,
                        "left": 49.12110686035156,
                        "right": 1009,
                        "paddingLeft": 3,
                        "paddingTop": 0,
                        "paddingRight": 3,
                        "paddingBottom": 0,
                        "minIndex": 0,
                        "maxIndex": 6,
                        "min": "Sun",
                        "max": "Sat",
                        "_startPixel": 49.12110686035156,
                        "_endPixel": 1009,
                        "_reversePixels": false,
                        "_length": 959.8788931396484,
                        "_startValue": -0.5,
                        "_valueRange": 7,
                        "labelRotation": 0,
                        "minSize": {"width": 962.8788931396484, "height": 35.6502},
                        "height": 35.6502,
                        "_ticksToDraw": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "top": 324.3498,
                        "bottom": 360
                    },
                    "_yScale": {
                        "id": "y-axis-0",
                        "type": "linear",
                        "options": {
                            "display": true,
                            "position": "left",
                            "offset": false,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": false,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 14,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> linear(tickValue, index, ticks)",
                                        "tooltip": "<pre>function(tickValue, index, ticks) {\r\n\t\t\t// If we have lots of ticks, don't use the ones\r\n\t\t\tvar delta = ticks.length > 3 ? ticks[2] - ticks[1] : ticks[1] - ticks[0];\r\n\r\n\t\t\t// If we have a number like 2.5 as the delta, figure out how many decimal places we need\r\n\t\t\tif (Math.abs(delta) > 1) {\r\n\t\t\t\tif (tickValue !== Math.floor(tickValue)) {\r\n\t\t\t\t\t// not an integer\r\n\t\t\t\t\tdelta = tickValue - Math.floor(tickValue);\r\n\t\t\t\t}\r\n\t\t\t}\r\n\r\n\t\t\tvar logDelta = helpers$1.log10(Math.abs(delta));\r\n\t\t\tvar tickString = '';\r\n\r\n\t\t\tif (tickValue !== 0) {\r\n\t\t\t\tvar maxTick = Math.max(Math.abs(ticks[0]), Math.abs(ticks[ticks.length - 1]));\r\n\t\t\t\tif (maxTick < 1e-4) { // all ticks are small numbers; use scientific notation\r\n\t\t\t\t\tvar logTick = helpers$1.log10(Math.abs(tickValue));\r\n\t\t\t\t\tvar numExponential = Math.floor(logTick) - Math.floor(logDelta);\r\n\t\t\t\t\tnumExponential = Math.max(Math.min(numExponential, 20), 0);\r\n\t\t\t\t\ttickString = tickValue.toExponential(numExponential);\r\n\t\t\t\t} else {\r\n\t\t\t\t\tvar numDecimal = -1 * Math.floor(logDelta);\r\n\t\t\t\t\tnumDecimal = Math.max(Math.min(numDecimal, 20), 0); // toFixed has a max of 20 decimal places\r\n\t\t\t\t\ttickString = tickValue.toFixed(numDecimal);\r\n\t\t\t\t}\r\n\t\t\t} else {\r\n\t\t\t\ttickString = '0'; // never show decimal places for 0\r\n\t\t\t}\r\n\r\n\t\t\treturn tickString;\r\n\t\t}</pre>",
                                        "_reviveId": 34
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0",
                                "precision": 2
                            },
                            "type": "linear",
                            "id": "y-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "left",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 35
                            }
                        },
                        "maxWidth": 506,
                        "maxHeight": 360,
                        "margins": {"left": 0, "right": 0, "top": 8.4465, "bottom": 8.4465},
                        "_ticks": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "ticks": ["6000", "5000", "4000", "3000", "2000", "1000", "0"],
                        "_labelSizes": {
                            "first": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 7.0712738037109375, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 28.363906860351562,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "0": 7.0712738037109375,
                                    "1000": 24.8170166015625,
                                    "2000": 27.688308715820312,
                                    "3000": 27.845947265625,
                                    "4000": 28.296356201171875,
                                    "5000": 28.28509521484375,
                                    "6000": 28.363906860351562
                                }, "gc": ["6000", "5000", "4000", "3000", "2000", "1000", "0"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.12110686035156,
                            "ty1": 8.5,
                            "tx2": 49,
                            "ty2": 8.5,
                            "x1": 50,
                            "y1": 8.5,
                            "x2": 1009,
                            "y2": 8.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 61.5,
                            "tx2": 49,
                            "ty2": 61.5,
                            "x1": 50,
                            "y1": 61.5,
                            "x2": 1009,
                            "y2": 61.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 113.5,
                            "tx2": 49,
                            "ty2": 113.5,
                            "x1": 50,
                            "y1": 113.5,
                            "x2": 1009,
                            "y2": 113.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 166.5,
                            "tx2": 49,
                            "ty2": 166.5,
                            "x1": 50,
                            "y1": 166.5,
                            "x2": 1009,
                            "y2": 166.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 219.5,
                            "tx2": 49,
                            "ty2": 219.5,
                            "x1": 50,
                            "y1": 219.5,
                            "x2": 1009,
                            "y2": 219.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 271.5,
                            "tx2": 49,
                            "ty2": 271.5,
                            "x1": 50,
                            "y1": 271.5,
                            "x2": 1009,
                            "y2": 271.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 324.5,
                            "tx2": 49,
                            "ty2": 324.5,
                            "x1": 50,
                            "y1": 324.5,
                            "x2": 1009,
                            "y2": 324.5,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 35.12110686035156,
                            "y": 8.4465,
                            "rotation": 0,
                            "label": "6000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 61.09704999999999,
                            "rotation": 0,
                            "label": "5000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 113.7476,
                            "rotation": 0,
                            "label": "4000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 166.39815,
                            "rotation": 0,
                            "label": "3000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 219.0487,
                            "rotation": 0,
                            "label": "2000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 271.69925,
                            "rotation": 0,
                            "label": "1000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 324.3498,
                            "rotation": 0,
                            "label": "0",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }],
                        "height": 315.9033,
                        "top": 8.4465,
                        "bottom": 324.3498,
                        "paddingLeft": 0,
                        "paddingTop": 8.4465,
                        "paddingRight": 0,
                        "paddingBottom": 8.4465,
                        "min": 0,
                        "max": 6000,
                        "start": 0,
                        "end": 6000,
                        "ticksAsNumbers": [6000, 5000, 4000, 3000, 2000, 1000, 0],
                        "zeroLineIndex": 6,
                        "_startPixel": 8.4465,
                        "_endPixel": 324.3498,
                        "_reversePixels": true,
                        "_length": 315.9033,
                        "_startValue": 0,
                        "_endValue": 6000,
                        "_valueRange": 6000,
                        "labelRotation": 0,
                        "minSize": {"width": 49.12110686035156, "height": 360},
                        "width": 49.12110686035156,
                        "_ticksToDraw": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "left": 0,
                        "right": 49.12110686035156
                    },
                    "_model": {
                        "backgroundColor": "#5bc5d5",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "purchase",
                        "label": "Sun",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 132.68388494175503,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_view": {
                        "backgroundColor": "#5bc5d5",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "purchase",
                        "label": "Sun",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 132.68388494175503,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 1,
                    "_index": 1,
                    "hidden": false,
                    "_xScale": {
                        "id": "x-axis-0",
                        "type": "category",
                        "options": {
                            "display": true,
                            "position": "bottom",
                            "offset": true,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": true,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 12,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                        "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                        "_reviveId": 36
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0"
                            },
                            "type": "category",
                            "id": "x-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "bottom",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 37
                            }
                        },
                        "maxWidth": 962.8788931396484,
                        "maxHeight": 180,
                        "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                        "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                        "_labelSizes": {
                            "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 25.582687377929688,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "Sun": 21.02239990234375,
                                    "Mon": 24.107635498046875,
                                    "Tue": 20.27923583984375,
                                    "Wed": 25.582687377929688,
                                    "Thu": 20.504440307617188,
                                    "Fri": 12.64495849609375,
                                    "Sat": 18.32000732421875
                                }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.5,
                            "ty1": 325,
                            "tx2": 49.5,
                            "ty2": 324.3498,
                            "x1": 49.5,
                            "y1": 8.4465,
                            "x2": 49.5,
                            "y2": 324,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 186.5,
                            "ty1": 325,
                            "tx2": 186.5,
                            "ty2": 324.3498,
                            "x1": 186.5,
                            "y1": 8.4465,
                            "x2": 186.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 323.5,
                            "ty1": 325,
                            "tx2": 323.5,
                            "ty2": 324.3498,
                            "x1": 323.5,
                            "y1": 8.4465,
                            "x2": 323.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 460.5,
                            "ty1": 325,
                            "tx2": 460.5,
                            "ty2": 324.3498,
                            "x1": 460.5,
                            "y1": 8.4465,
                            "x2": 460.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 597.5,
                            "ty1": 325,
                            "tx2": 597.5,
                            "ty2": 324.3498,
                            "x1": 597.5,
                            "y1": 8.4465,
                            "x2": 597.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 734.5,
                            "ty1": 325,
                            "tx2": 734.5,
                            "ty2": 324.3498,
                            "x1": 734.5,
                            "y1": 8.4465,
                            "x2": 734.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 871.5,
                            "ty1": 325,
                            "tx2": 871.5,
                            "ty2": 324.3498,
                            "x1": 871.5,
                            "y1": 8.4465,
                            "x2": 871.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 1009.5,
                            "ty1": 325,
                            "tx2": 1009.5,
                            "ty2": 324.3498,
                            "x1": 1009.5,
                            "y1": 8.4465,
                            "x2": 1009.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 117.68388494175501,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sun",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 254.80944110456193,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Mon",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 391.9349972673689,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Tue",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 529.0605534301758,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Wed",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 666.1861095929828,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Thu",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 803.3116657557896,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Fri",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 940.4372219185966,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sat",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }],
                        "width": 959.8788931396484,
                        "left": 49.12110686035156,
                        "right": 1009,
                        "paddingLeft": 3,
                        "paddingTop": 0,
                        "paddingRight": 3,
                        "paddingBottom": 0,
                        "minIndex": 0,
                        "maxIndex": 6,
                        "min": "Sun",
                        "max": "Sat",
                        "_startPixel": 49.12110686035156,
                        "_endPixel": 1009,
                        "_reversePixels": false,
                        "_length": 959.8788931396484,
                        "_startValue": -0.5,
                        "_valueRange": 7,
                        "labelRotation": 0,
                        "minSize": {"width": 962.8788931396484, "height": 35.6502},
                        "height": 35.6502,
                        "_ticksToDraw": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "top": 324.3498,
                        "bottom": 360
                    },
                    "_yScale": {
                        "id": "y-axis-0",
                        "type": "linear",
                        "options": {
                            "display": true,
                            "position": "left",
                            "offset": false,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": false,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 14,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> linear(tickValue, index, ticks)",
                                        "tooltip": "<pre>function(tickValue, index, ticks) {\r\n\t\t\t// If we have lots of ticks, don't use the ones\r\n\t\t\tvar delta = ticks.length > 3 ? ticks[2] - ticks[1] : ticks[1] - ticks[0];\r\n\r\n\t\t\t// If we have a number like 2.5 as the delta, figure out how many decimal places we need\r\n\t\t\tif (Math.abs(delta) > 1) {\r\n\t\t\t\tif (tickValue !== Math.floor(tickValue)) {\r\n\t\t\t\t\t// not an integer\r\n\t\t\t\t\tdelta = tickValue - Math.floor(tickValue);\r\n\t\t\t\t}\r\n\t\t\t}\r\n\r\n\t\t\tvar logDelta = helpers$1.log10(Math.abs(delta));\r\n\t\t\tvar tickString = '';\r\n\r\n\t\t\tif (tickValue !== 0) {\r\n\t\t\t\tvar maxTick = Math.max(Math.abs(ticks[0]), Math.abs(ticks[ticks.length - 1]));\r\n\t\t\t\tif (maxTick < 1e-4) { // all ticks are small numbers; use scientific notation\r\n\t\t\t\t\tvar logTick = helpers$1.log10(Math.abs(tickValue));\r\n\t\t\t\t\tvar numExponential = Math.floor(logTick) - Math.floor(logDelta);\r\n\t\t\t\t\tnumExponential = Math.max(Math.min(numExponential, 20), 0);\r\n\t\t\t\t\ttickString = tickValue.toExponential(numExponential);\r\n\t\t\t\t} else {\r\n\t\t\t\t\tvar numDecimal = -1 * Math.floor(logDelta);\r\n\t\t\t\t\tnumDecimal = Math.max(Math.min(numDecimal, 20), 0); // toFixed has a max of 20 decimal places\r\n\t\t\t\t\ttickString = tickValue.toFixed(numDecimal);\r\n\t\t\t\t}\r\n\t\t\t} else {\r\n\t\t\t\ttickString = '0'; // never show decimal places for 0\r\n\t\t\t}\r\n\r\n\t\t\treturn tickString;\r\n\t\t}</pre>",
                                        "_reviveId": 38
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0",
                                "precision": 2
                            },
                            "type": "linear",
                            "id": "y-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "left",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 39
                            }
                        },
                        "maxWidth": 506,
                        "maxHeight": 360,
                        "margins": {"left": 0, "right": 0, "top": 8.4465, "bottom": 8.4465},
                        "_ticks": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "ticks": ["6000", "5000", "4000", "3000", "2000", "1000", "0"],
                        "_labelSizes": {
                            "first": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 7.0712738037109375, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 28.363906860351562,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "0": 7.0712738037109375,
                                    "1000": 24.8170166015625,
                                    "2000": 27.688308715820312,
                                    "3000": 27.845947265625,
                                    "4000": 28.296356201171875,
                                    "5000": 28.28509521484375,
                                    "6000": 28.363906860351562
                                }, "gc": ["6000", "5000", "4000", "3000", "2000", "1000", "0"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.12110686035156,
                            "ty1": 8.5,
                            "tx2": 49,
                            "ty2": 8.5,
                            "x1": 50,
                            "y1": 8.5,
                            "x2": 1009,
                            "y2": 8.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 61.5,
                            "tx2": 49,
                            "ty2": 61.5,
                            "x1": 50,
                            "y1": 61.5,
                            "x2": 1009,
                            "y2": 61.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 113.5,
                            "tx2": 49,
                            "ty2": 113.5,
                            "x1": 50,
                            "y1": 113.5,
                            "x2": 1009,
                            "y2": 113.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 166.5,
                            "tx2": 49,
                            "ty2": 166.5,
                            "x1": 50,
                            "y1": 166.5,
                            "x2": 1009,
                            "y2": 166.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 219.5,
                            "tx2": 49,
                            "ty2": 219.5,
                            "x1": 50,
                            "y1": 219.5,
                            "x2": 1009,
                            "y2": 219.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 271.5,
                            "tx2": 49,
                            "ty2": 271.5,
                            "x1": 50,
                            "y1": 271.5,
                            "x2": 1009,
                            "y2": 271.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 324.5,
                            "tx2": 49,
                            "ty2": 324.5,
                            "x1": 50,
                            "y1": 324.5,
                            "x2": 1009,
                            "y2": 324.5,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 35.12110686035156,
                            "y": 8.4465,
                            "rotation": 0,
                            "label": "6000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 61.09704999999999,
                            "rotation": 0,
                            "label": "5000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 113.7476,
                            "rotation": 0,
                            "label": "4000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 166.39815,
                            "rotation": 0,
                            "label": "3000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 219.0487,
                            "rotation": 0,
                            "label": "2000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 271.69925,
                            "rotation": 0,
                            "label": "1000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 324.3498,
                            "rotation": 0,
                            "label": "0",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }],
                        "height": 315.9033,
                        "top": 8.4465,
                        "bottom": 324.3498,
                        "paddingLeft": 0,
                        "paddingTop": 8.4465,
                        "paddingRight": 0,
                        "paddingBottom": 8.4465,
                        "min": 0,
                        "max": 6000,
                        "start": 0,
                        "end": 6000,
                        "ticksAsNumbers": [6000, 5000, 4000, 3000, 2000, 1000, 0],
                        "zeroLineIndex": 6,
                        "_startPixel": 8.4465,
                        "_endPixel": 324.3498,
                        "_reversePixels": true,
                        "_length": 315.9033,
                        "_startValue": 0,
                        "_endValue": 6000,
                        "_valueRange": 6000,
                        "labelRotation": 0,
                        "minSize": {"width": 49.12110686035156, "height": 360},
                        "width": 49.12110686035156,
                        "_ticksToDraw": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "left": 0,
                        "right": 49.12110686035156
                    },
                    "_model": {
                        "backgroundColor": "#5bc5d5",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "purchase",
                        "label": "Mon",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 269.8094411045619,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_view": {
                        "backgroundColor": "#5bc5d5",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "purchase",
                        "label": "Mon",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 269.8094411045619,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 1,
                    "_index": 2,
                    "hidden": false,
                    "_xScale": {
                        "id": "x-axis-0",
                        "type": "category",
                        "options": {
                            "display": true,
                            "position": "bottom",
                            "offset": true,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": true,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 12,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                        "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                        "_reviveId": 40
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0"
                            },
                            "type": "category",
                            "id": "x-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "bottom",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 41
                            }
                        },
                        "maxWidth": 962.8788931396484,
                        "maxHeight": 180,
                        "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                        "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                        "_labelSizes": {
                            "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 25.582687377929688,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "Sun": 21.02239990234375,
                                    "Mon": 24.107635498046875,
                                    "Tue": 20.27923583984375,
                                    "Wed": 25.582687377929688,
                                    "Thu": 20.504440307617188,
                                    "Fri": 12.64495849609375,
                                    "Sat": 18.32000732421875
                                }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.5,
                            "ty1": 325,
                            "tx2": 49.5,
                            "ty2": 324.3498,
                            "x1": 49.5,
                            "y1": 8.4465,
                            "x2": 49.5,
                            "y2": 324,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 186.5,
                            "ty1": 325,
                            "tx2": 186.5,
                            "ty2": 324.3498,
                            "x1": 186.5,
                            "y1": 8.4465,
                            "x2": 186.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 323.5,
                            "ty1": 325,
                            "tx2": 323.5,
                            "ty2": 324.3498,
                            "x1": 323.5,
                            "y1": 8.4465,
                            "x2": 323.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 460.5,
                            "ty1": 325,
                            "tx2": 460.5,
                            "ty2": 324.3498,
                            "x1": 460.5,
                            "y1": 8.4465,
                            "x2": 460.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 597.5,
                            "ty1": 325,
                            "tx2": 597.5,
                            "ty2": 324.3498,
                            "x1": 597.5,
                            "y1": 8.4465,
                            "x2": 597.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 734.5,
                            "ty1": 325,
                            "tx2": 734.5,
                            "ty2": 324.3498,
                            "x1": 734.5,
                            "y1": 8.4465,
                            "x2": 734.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 871.5,
                            "ty1": 325,
                            "tx2": 871.5,
                            "ty2": 324.3498,
                            "x1": 871.5,
                            "y1": 8.4465,
                            "x2": 871.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 1009.5,
                            "ty1": 325,
                            "tx2": 1009.5,
                            "ty2": 324.3498,
                            "x1": 1009.5,
                            "y1": 8.4465,
                            "x2": 1009.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 117.68388494175501,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sun",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 254.80944110456193,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Mon",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 391.9349972673689,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Tue",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 529.0605534301758,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Wed",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 666.1861095929828,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Thu",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 803.3116657557896,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Fri",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 940.4372219185966,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sat",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }],
                        "width": 959.8788931396484,
                        "left": 49.12110686035156,
                        "right": 1009,
                        "paddingLeft": 3,
                        "paddingTop": 0,
                        "paddingRight": 3,
                        "paddingBottom": 0,
                        "minIndex": 0,
                        "maxIndex": 6,
                        "min": "Sun",
                        "max": "Sat",
                        "_startPixel": 49.12110686035156,
                        "_endPixel": 1009,
                        "_reversePixels": false,
                        "_length": 959.8788931396484,
                        "_startValue": -0.5,
                        "_valueRange": 7,
                        "labelRotation": 0,
                        "minSize": {"width": 962.8788931396484, "height": 35.6502},
                        "height": 35.6502,
                        "_ticksToDraw": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "top": 324.3498,
                        "bottom": 360
                    },
                    "_yScale": {
                        "id": "y-axis-0",
                        "type": "linear",
                        "options": {
                            "display": true,
                            "position": "left",
                            "offset": false,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": false,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 14,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> linear(tickValue, index, ticks)",
                                        "tooltip": "<pre>function(tickValue, index, ticks) {\r\n\t\t\t// If we have lots of ticks, don't use the ones\r\n\t\t\tvar delta = ticks.length > 3 ? ticks[2] - ticks[1] : ticks[1] - ticks[0];\r\n\r\n\t\t\t// If we have a number like 2.5 as the delta, figure out how many decimal places we need\r\n\t\t\tif (Math.abs(delta) > 1) {\r\n\t\t\t\tif (tickValue !== Math.floor(tickValue)) {\r\n\t\t\t\t\t// not an integer\r\n\t\t\t\t\tdelta = tickValue - Math.floor(tickValue);\r\n\t\t\t\t}\r\n\t\t\t}\r\n\r\n\t\t\tvar logDelta = helpers$1.log10(Math.abs(delta));\r\n\t\t\tvar tickString = '';\r\n\r\n\t\t\tif (tickValue !== 0) {\r\n\t\t\t\tvar maxTick = Math.max(Math.abs(ticks[0]), Math.abs(ticks[ticks.length - 1]));\r\n\t\t\t\tif (maxTick < 1e-4) { // all ticks are small numbers; use scientific notation\r\n\t\t\t\t\tvar logTick = helpers$1.log10(Math.abs(tickValue));\r\n\t\t\t\t\tvar numExponential = Math.floor(logTick) - Math.floor(logDelta);\r\n\t\t\t\t\tnumExponential = Math.max(Math.min(numExponential, 20), 0);\r\n\t\t\t\t\ttickString = tickValue.toExponential(numExponential);\r\n\t\t\t\t} else {\r\n\t\t\t\t\tvar numDecimal = -1 * Math.floor(logDelta);\r\n\t\t\t\t\tnumDecimal = Math.max(Math.min(numDecimal, 20), 0); // toFixed has a max of 20 decimal places\r\n\t\t\t\t\ttickString = tickValue.toFixed(numDecimal);\r\n\t\t\t\t}\r\n\t\t\t} else {\r\n\t\t\t\ttickString = '0'; // never show decimal places for 0\r\n\t\t\t}\r\n\r\n\t\t\treturn tickString;\r\n\t\t}</pre>",
                                        "_reviveId": 42
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0",
                                "precision": 2
                            },
                            "type": "linear",
                            "id": "y-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "left",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 43
                            }
                        },
                        "maxWidth": 506,
                        "maxHeight": 360,
                        "margins": {"left": 0, "right": 0, "top": 8.4465, "bottom": 8.4465},
                        "_ticks": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "ticks": ["6000", "5000", "4000", "3000", "2000", "1000", "0"],
                        "_labelSizes": {
                            "first": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 7.0712738037109375, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 28.363906860351562,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "0": 7.0712738037109375,
                                    "1000": 24.8170166015625,
                                    "2000": 27.688308715820312,
                                    "3000": 27.845947265625,
                                    "4000": 28.296356201171875,
                                    "5000": 28.28509521484375,
                                    "6000": 28.363906860351562
                                }, "gc": ["6000", "5000", "4000", "3000", "2000", "1000", "0"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.12110686035156,
                            "ty1": 8.5,
                            "tx2": 49,
                            "ty2": 8.5,
                            "x1": 50,
                            "y1": 8.5,
                            "x2": 1009,
                            "y2": 8.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 61.5,
                            "tx2": 49,
                            "ty2": 61.5,
                            "x1": 50,
                            "y1": 61.5,
                            "x2": 1009,
                            "y2": 61.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 113.5,
                            "tx2": 49,
                            "ty2": 113.5,
                            "x1": 50,
                            "y1": 113.5,
                            "x2": 1009,
                            "y2": 113.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 166.5,
                            "tx2": 49,
                            "ty2": 166.5,
                            "x1": 50,
                            "y1": 166.5,
                            "x2": 1009,
                            "y2": 166.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 219.5,
                            "tx2": 49,
                            "ty2": 219.5,
                            "x1": 50,
                            "y1": 219.5,
                            "x2": 1009,
                            "y2": 219.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 271.5,
                            "tx2": 49,
                            "ty2": 271.5,
                            "x1": 50,
                            "y1": 271.5,
                            "x2": 1009,
                            "y2": 271.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 324.5,
                            "tx2": 49,
                            "ty2": 324.5,
                            "x1": 50,
                            "y1": 324.5,
                            "x2": 1009,
                            "y2": 324.5,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 35.12110686035156,
                            "y": 8.4465,
                            "rotation": 0,
                            "label": "6000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 61.09704999999999,
                            "rotation": 0,
                            "label": "5000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 113.7476,
                            "rotation": 0,
                            "label": "4000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 166.39815,
                            "rotation": 0,
                            "label": "3000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 219.0487,
                            "rotation": 0,
                            "label": "2000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 271.69925,
                            "rotation": 0,
                            "label": "1000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 324.3498,
                            "rotation": 0,
                            "label": "0",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }],
                        "height": 315.9033,
                        "top": 8.4465,
                        "bottom": 324.3498,
                        "paddingLeft": 0,
                        "paddingTop": 8.4465,
                        "paddingRight": 0,
                        "paddingBottom": 8.4465,
                        "min": 0,
                        "max": 6000,
                        "start": 0,
                        "end": 6000,
                        "ticksAsNumbers": [6000, 5000, 4000, 3000, 2000, 1000, 0],
                        "zeroLineIndex": 6,
                        "_startPixel": 8.4465,
                        "_endPixel": 324.3498,
                        "_reversePixels": true,
                        "_length": 315.9033,
                        "_startValue": 0,
                        "_endValue": 6000,
                        "_valueRange": 6000,
                        "labelRotation": 0,
                        "minSize": {"width": 49.12110686035156, "height": 360},
                        "width": 49.12110686035156,
                        "_ticksToDraw": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "left": 0,
                        "right": 49.12110686035156
                    },
                    "_model": {
                        "backgroundColor": "#5bc5d5",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "purchase",
                        "label": "Tue",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 406.9349972673689,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_view": {
                        "backgroundColor": "#5bc5d5",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "purchase",
                        "label": "Tue",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 406.9349972673689,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 1,
                    "_index": 3,
                    "hidden": false,
                    "_xScale": {
                        "id": "x-axis-0",
                        "type": "category",
                        "options": {
                            "display": true,
                            "position": "bottom",
                            "offset": true,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": true,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 12,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                        "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                        "_reviveId": 44
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0"
                            },
                            "type": "category",
                            "id": "x-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "bottom",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 45
                            }
                        },
                        "maxWidth": 962.8788931396484,
                        "maxHeight": 180,
                        "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                        "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                        "_labelSizes": {
                            "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 25.582687377929688,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "Sun": 21.02239990234375,
                                    "Mon": 24.107635498046875,
                                    "Tue": 20.27923583984375,
                                    "Wed": 25.582687377929688,
                                    "Thu": 20.504440307617188,
                                    "Fri": 12.64495849609375,
                                    "Sat": 18.32000732421875
                                }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.5,
                            "ty1": 325,
                            "tx2": 49.5,
                            "ty2": 324.3498,
                            "x1": 49.5,
                            "y1": 8.4465,
                            "x2": 49.5,
                            "y2": 324,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 186.5,
                            "ty1": 325,
                            "tx2": 186.5,
                            "ty2": 324.3498,
                            "x1": 186.5,
                            "y1": 8.4465,
                            "x2": 186.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 323.5,
                            "ty1": 325,
                            "tx2": 323.5,
                            "ty2": 324.3498,
                            "x1": 323.5,
                            "y1": 8.4465,
                            "x2": 323.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 460.5,
                            "ty1": 325,
                            "tx2": 460.5,
                            "ty2": 324.3498,
                            "x1": 460.5,
                            "y1": 8.4465,
                            "x2": 460.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 597.5,
                            "ty1": 325,
                            "tx2": 597.5,
                            "ty2": 324.3498,
                            "x1": 597.5,
                            "y1": 8.4465,
                            "x2": 597.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 734.5,
                            "ty1": 325,
                            "tx2": 734.5,
                            "ty2": 324.3498,
                            "x1": 734.5,
                            "y1": 8.4465,
                            "x2": 734.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 871.5,
                            "ty1": 325,
                            "tx2": 871.5,
                            "ty2": 324.3498,
                            "x1": 871.5,
                            "y1": 8.4465,
                            "x2": 871.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 1009.5,
                            "ty1": 325,
                            "tx2": 1009.5,
                            "ty2": 324.3498,
                            "x1": 1009.5,
                            "y1": 8.4465,
                            "x2": 1009.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 117.68388494175501,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sun",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 254.80944110456193,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Mon",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 391.9349972673689,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Tue",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 529.0605534301758,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Wed",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 666.1861095929828,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Thu",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 803.3116657557896,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Fri",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 940.4372219185966,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sat",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }],
                        "width": 959.8788931396484,
                        "left": 49.12110686035156,
                        "right": 1009,
                        "paddingLeft": 3,
                        "paddingTop": 0,
                        "paddingRight": 3,
                        "paddingBottom": 0,
                        "minIndex": 0,
                        "maxIndex": 6,
                        "min": "Sun",
                        "max": "Sat",
                        "_startPixel": 49.12110686035156,
                        "_endPixel": 1009,
                        "_reversePixels": false,
                        "_length": 959.8788931396484,
                        "_startValue": -0.5,
                        "_valueRange": 7,
                        "labelRotation": 0,
                        "minSize": {"width": 962.8788931396484, "height": 35.6502},
                        "height": 35.6502,
                        "_ticksToDraw": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "top": 324.3498,
                        "bottom": 360
                    },
                    "_yScale": {
                        "id": "y-axis-0",
                        "type": "linear",
                        "options": {
                            "display": true,
                            "position": "left",
                            "offset": false,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": false,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 14,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> linear(tickValue, index, ticks)",
                                        "tooltip": "<pre>function(tickValue, index, ticks) {\r\n\t\t\t// If we have lots of ticks, don't use the ones\r\n\t\t\tvar delta = ticks.length > 3 ? ticks[2] - ticks[1] : ticks[1] - ticks[0];\r\n\r\n\t\t\t// If we have a number like 2.5 as the delta, figure out how many decimal places we need\r\n\t\t\tif (Math.abs(delta) > 1) {\r\n\t\t\t\tif (tickValue !== Math.floor(tickValue)) {\r\n\t\t\t\t\t// not an integer\r\n\t\t\t\t\tdelta = tickValue - Math.floor(tickValue);\r\n\t\t\t\t}\r\n\t\t\t}\r\n\r\n\t\t\tvar logDelta = helpers$1.log10(Math.abs(delta));\r\n\t\t\tvar tickString = '';\r\n\r\n\t\t\tif (tickValue !== 0) {\r\n\t\t\t\tvar maxTick = Math.max(Math.abs(ticks[0]), Math.abs(ticks[ticks.length - 1]));\r\n\t\t\t\tif (maxTick < 1e-4) { // all ticks are small numbers; use scientific notation\r\n\t\t\t\t\tvar logTick = helpers$1.log10(Math.abs(tickValue));\r\n\t\t\t\t\tvar numExponential = Math.floor(logTick) - Math.floor(logDelta);\r\n\t\t\t\t\tnumExponential = Math.max(Math.min(numExponential, 20), 0);\r\n\t\t\t\t\ttickString = tickValue.toExponential(numExponential);\r\n\t\t\t\t} else {\r\n\t\t\t\t\tvar numDecimal = -1 * Math.floor(logDelta);\r\n\t\t\t\t\tnumDecimal = Math.max(Math.min(numDecimal, 20), 0); // toFixed has a max of 20 decimal places\r\n\t\t\t\t\ttickString = tickValue.toFixed(numDecimal);\r\n\t\t\t\t}\r\n\t\t\t} else {\r\n\t\t\t\ttickString = '0'; // never show decimal places for 0\r\n\t\t\t}\r\n\r\n\t\t\treturn tickString;\r\n\t\t}</pre>",
                                        "_reviveId": 46
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0",
                                "precision": 2
                            },
                            "type": "linear",
                            "id": "y-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "left",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 47
                            }
                        },
                        "maxWidth": 506,
                        "maxHeight": 360,
                        "margins": {"left": 0, "right": 0, "top": 8.4465, "bottom": 8.4465},
                        "_ticks": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "ticks": ["6000", "5000", "4000", "3000", "2000", "1000", "0"],
                        "_labelSizes": {
                            "first": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 7.0712738037109375, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 28.363906860351562,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "0": 7.0712738037109375,
                                    "1000": 24.8170166015625,
                                    "2000": 27.688308715820312,
                                    "3000": 27.845947265625,
                                    "4000": 28.296356201171875,
                                    "5000": 28.28509521484375,
                                    "6000": 28.363906860351562
                                }, "gc": ["6000", "5000", "4000", "3000", "2000", "1000", "0"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.12110686035156,
                            "ty1": 8.5,
                            "tx2": 49,
                            "ty2": 8.5,
                            "x1": 50,
                            "y1": 8.5,
                            "x2": 1009,
                            "y2": 8.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 61.5,
                            "tx2": 49,
                            "ty2": 61.5,
                            "x1": 50,
                            "y1": 61.5,
                            "x2": 1009,
                            "y2": 61.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 113.5,
                            "tx2": 49,
                            "ty2": 113.5,
                            "x1": 50,
                            "y1": 113.5,
                            "x2": 1009,
                            "y2": 113.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 166.5,
                            "tx2": 49,
                            "ty2": 166.5,
                            "x1": 50,
                            "y1": 166.5,
                            "x2": 1009,
                            "y2": 166.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 219.5,
                            "tx2": 49,
                            "ty2": 219.5,
                            "x1": 50,
                            "y1": 219.5,
                            "x2": 1009,
                            "y2": 219.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 271.5,
                            "tx2": 49,
                            "ty2": 271.5,
                            "x1": 50,
                            "y1": 271.5,
                            "x2": 1009,
                            "y2": 271.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 324.5,
                            "tx2": 49,
                            "ty2": 324.5,
                            "x1": 50,
                            "y1": 324.5,
                            "x2": 1009,
                            "y2": 324.5,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 35.12110686035156,
                            "y": 8.4465,
                            "rotation": 0,
                            "label": "6000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 61.09704999999999,
                            "rotation": 0,
                            "label": "5000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 113.7476,
                            "rotation": 0,
                            "label": "4000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 166.39815,
                            "rotation": 0,
                            "label": "3000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 219.0487,
                            "rotation": 0,
                            "label": "2000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 271.69925,
                            "rotation": 0,
                            "label": "1000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 324.3498,
                            "rotation": 0,
                            "label": "0",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }],
                        "height": 315.9033,
                        "top": 8.4465,
                        "bottom": 324.3498,
                        "paddingLeft": 0,
                        "paddingTop": 8.4465,
                        "paddingRight": 0,
                        "paddingBottom": 8.4465,
                        "min": 0,
                        "max": 6000,
                        "start": 0,
                        "end": 6000,
                        "ticksAsNumbers": [6000, 5000, 4000, 3000, 2000, 1000, 0],
                        "zeroLineIndex": 6,
                        "_startPixel": 8.4465,
                        "_endPixel": 324.3498,
                        "_reversePixels": true,
                        "_length": 315.9033,
                        "_startValue": 0,
                        "_endValue": 6000,
                        "_valueRange": 6000,
                        "labelRotation": 0,
                        "minSize": {"width": 49.12110686035156, "height": 360},
                        "width": 49.12110686035156,
                        "_ticksToDraw": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "left": 0,
                        "right": 49.12110686035156
                    },
                    "_model": {
                        "backgroundColor": "#5bc5d5",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "purchase",
                        "label": "Wed",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 544.0605534301758,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_view": {
                        "backgroundColor": "#5bc5d5",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "purchase",
                        "label": "Wed",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 544.0605534301758,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 1,
                    "_index": 4,
                    "hidden": false,
                    "_xScale": {
                        "id": "x-axis-0",
                        "type": "category",
                        "options": {
                            "display": true,
                            "position": "bottom",
                            "offset": true,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": true,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 12,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                        "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                        "_reviveId": 48
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0"
                            },
                            "type": "category",
                            "id": "x-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "bottom",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 49
                            }
                        },
                        "maxWidth": 962.8788931396484,
                        "maxHeight": 180,
                        "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                        "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                        "_labelSizes": {
                            "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 25.582687377929688,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "Sun": 21.02239990234375,
                                    "Mon": 24.107635498046875,
                                    "Tue": 20.27923583984375,
                                    "Wed": 25.582687377929688,
                                    "Thu": 20.504440307617188,
                                    "Fri": 12.64495849609375,
                                    "Sat": 18.32000732421875
                                }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.5,
                            "ty1": 325,
                            "tx2": 49.5,
                            "ty2": 324.3498,
                            "x1": 49.5,
                            "y1": 8.4465,
                            "x2": 49.5,
                            "y2": 324,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 186.5,
                            "ty1": 325,
                            "tx2": 186.5,
                            "ty2": 324.3498,
                            "x1": 186.5,
                            "y1": 8.4465,
                            "x2": 186.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 323.5,
                            "ty1": 325,
                            "tx2": 323.5,
                            "ty2": 324.3498,
                            "x1": 323.5,
                            "y1": 8.4465,
                            "x2": 323.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 460.5,
                            "ty1": 325,
                            "tx2": 460.5,
                            "ty2": 324.3498,
                            "x1": 460.5,
                            "y1": 8.4465,
                            "x2": 460.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 597.5,
                            "ty1": 325,
                            "tx2": 597.5,
                            "ty2": 324.3498,
                            "x1": 597.5,
                            "y1": 8.4465,
                            "x2": 597.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 734.5,
                            "ty1": 325,
                            "tx2": 734.5,
                            "ty2": 324.3498,
                            "x1": 734.5,
                            "y1": 8.4465,
                            "x2": 734.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 871.5,
                            "ty1": 325,
                            "tx2": 871.5,
                            "ty2": 324.3498,
                            "x1": 871.5,
                            "y1": 8.4465,
                            "x2": 871.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 1009.5,
                            "ty1": 325,
                            "tx2": 1009.5,
                            "ty2": 324.3498,
                            "x1": 1009.5,
                            "y1": 8.4465,
                            "x2": 1009.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 117.68388494175501,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sun",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 254.80944110456193,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Mon",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 391.9349972673689,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Tue",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 529.0605534301758,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Wed",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 666.1861095929828,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Thu",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 803.3116657557896,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Fri",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 940.4372219185966,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sat",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }],
                        "width": 959.8788931396484,
                        "left": 49.12110686035156,
                        "right": 1009,
                        "paddingLeft": 3,
                        "paddingTop": 0,
                        "paddingRight": 3,
                        "paddingBottom": 0,
                        "minIndex": 0,
                        "maxIndex": 6,
                        "min": "Sun",
                        "max": "Sat",
                        "_startPixel": 49.12110686035156,
                        "_endPixel": 1009,
                        "_reversePixels": false,
                        "_length": 959.8788931396484,
                        "_startValue": -0.5,
                        "_valueRange": 7,
                        "labelRotation": 0,
                        "minSize": {"width": 962.8788931396484, "height": 35.6502},
                        "height": 35.6502,
                        "_ticksToDraw": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "top": 324.3498,
                        "bottom": 360
                    },
                    "_yScale": {
                        "id": "y-axis-0",
                        "type": "linear",
                        "options": {
                            "display": true,
                            "position": "left",
                            "offset": false,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": false,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 14,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> linear(tickValue, index, ticks)",
                                        "tooltip": "<pre>function(tickValue, index, ticks) {\r\n\t\t\t// If we have lots of ticks, don't use the ones\r\n\t\t\tvar delta = ticks.length > 3 ? ticks[2] - ticks[1] : ticks[1] - ticks[0];\r\n\r\n\t\t\t// If we have a number like 2.5 as the delta, figure out how many decimal places we need\r\n\t\t\tif (Math.abs(delta) > 1) {\r\n\t\t\t\tif (tickValue !== Math.floor(tickValue)) {\r\n\t\t\t\t\t// not an integer\r\n\t\t\t\t\tdelta = tickValue - Math.floor(tickValue);\r\n\t\t\t\t}\r\n\t\t\t}\r\n\r\n\t\t\tvar logDelta = helpers$1.log10(Math.abs(delta));\r\n\t\t\tvar tickString = '';\r\n\r\n\t\t\tif (tickValue !== 0) {\r\n\t\t\t\tvar maxTick = Math.max(Math.abs(ticks[0]), Math.abs(ticks[ticks.length - 1]));\r\n\t\t\t\tif (maxTick < 1e-4) { // all ticks are small numbers; use scientific notation\r\n\t\t\t\t\tvar logTick = helpers$1.log10(Math.abs(tickValue));\r\n\t\t\t\t\tvar numExponential = Math.floor(logTick) - Math.floor(logDelta);\r\n\t\t\t\t\tnumExponential = Math.max(Math.min(numExponential, 20), 0);\r\n\t\t\t\t\ttickString = tickValue.toExponential(numExponential);\r\n\t\t\t\t} else {\r\n\t\t\t\t\tvar numDecimal = -1 * Math.floor(logDelta);\r\n\t\t\t\t\tnumDecimal = Math.max(Math.min(numDecimal, 20), 0); // toFixed has a max of 20 decimal places\r\n\t\t\t\t\ttickString = tickValue.toFixed(numDecimal);\r\n\t\t\t\t}\r\n\t\t\t} else {\r\n\t\t\t\ttickString = '0'; // never show decimal places for 0\r\n\t\t\t}\r\n\r\n\t\t\treturn tickString;\r\n\t\t}</pre>",
                                        "_reviveId": 50
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0",
                                "precision": 2
                            },
                            "type": "linear",
                            "id": "y-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "left",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 51
                            }
                        },
                        "maxWidth": 506,
                        "maxHeight": 360,
                        "margins": {"left": 0, "right": 0, "top": 8.4465, "bottom": 8.4465},
                        "_ticks": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "ticks": ["6000", "5000", "4000", "3000", "2000", "1000", "0"],
                        "_labelSizes": {
                            "first": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 7.0712738037109375, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 28.363906860351562,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "0": 7.0712738037109375,
                                    "1000": 24.8170166015625,
                                    "2000": 27.688308715820312,
                                    "3000": 27.845947265625,
                                    "4000": 28.296356201171875,
                                    "5000": 28.28509521484375,
                                    "6000": 28.363906860351562
                                }, "gc": ["6000", "5000", "4000", "3000", "2000", "1000", "0"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.12110686035156,
                            "ty1": 8.5,
                            "tx2": 49,
                            "ty2": 8.5,
                            "x1": 50,
                            "y1": 8.5,
                            "x2": 1009,
                            "y2": 8.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 61.5,
                            "tx2": 49,
                            "ty2": 61.5,
                            "x1": 50,
                            "y1": 61.5,
                            "x2": 1009,
                            "y2": 61.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 113.5,
                            "tx2": 49,
                            "ty2": 113.5,
                            "x1": 50,
                            "y1": 113.5,
                            "x2": 1009,
                            "y2": 113.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 166.5,
                            "tx2": 49,
                            "ty2": 166.5,
                            "x1": 50,
                            "y1": 166.5,
                            "x2": 1009,
                            "y2": 166.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 219.5,
                            "tx2": 49,
                            "ty2": 219.5,
                            "x1": 50,
                            "y1": 219.5,
                            "x2": 1009,
                            "y2": 219.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 271.5,
                            "tx2": 49,
                            "ty2": 271.5,
                            "x1": 50,
                            "y1": 271.5,
                            "x2": 1009,
                            "y2": 271.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 324.5,
                            "tx2": 49,
                            "ty2": 324.5,
                            "x1": 50,
                            "y1": 324.5,
                            "x2": 1009,
                            "y2": 324.5,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 35.12110686035156,
                            "y": 8.4465,
                            "rotation": 0,
                            "label": "6000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 61.09704999999999,
                            "rotation": 0,
                            "label": "5000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 113.7476,
                            "rotation": 0,
                            "label": "4000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 166.39815,
                            "rotation": 0,
                            "label": "3000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 219.0487,
                            "rotation": 0,
                            "label": "2000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 271.69925,
                            "rotation": 0,
                            "label": "1000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 324.3498,
                            "rotation": 0,
                            "label": "0",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }],
                        "height": 315.9033,
                        "top": 8.4465,
                        "bottom": 324.3498,
                        "paddingLeft": 0,
                        "paddingTop": 8.4465,
                        "paddingRight": 0,
                        "paddingBottom": 8.4465,
                        "min": 0,
                        "max": 6000,
                        "start": 0,
                        "end": 6000,
                        "ticksAsNumbers": [6000, 5000, 4000, 3000, 2000, 1000, 0],
                        "zeroLineIndex": 6,
                        "_startPixel": 8.4465,
                        "_endPixel": 324.3498,
                        "_reversePixels": true,
                        "_length": 315.9033,
                        "_startValue": 0,
                        "_endValue": 6000,
                        "_valueRange": 6000,
                        "labelRotation": 0,
                        "minSize": {"width": 49.12110686035156, "height": 360},
                        "width": 49.12110686035156,
                        "_ticksToDraw": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "left": 0,
                        "right": 49.12110686035156
                    },
                    "_model": {
                        "backgroundColor": "#5bc5d5",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "purchase",
                        "label": "Thu",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 681.1861095929828,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_view": {
                        "backgroundColor": "#5bc5d5",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "purchase",
                        "label": "Thu",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 681.1861095929828,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 1,
                    "_index": 5,
                    "hidden": false,
                    "_xScale": {
                        "id": "x-axis-0",
                        "type": "category",
                        "options": {
                            "display": true,
                            "position": "bottom",
                            "offset": true,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": true,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 12,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                        "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                        "_reviveId": 52
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0"
                            },
                            "type": "category",
                            "id": "x-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "bottom",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 53
                            }
                        },
                        "maxWidth": 962.8788931396484,
                        "maxHeight": 180,
                        "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                        "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                        "_labelSizes": {
                            "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 25.582687377929688,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "Sun": 21.02239990234375,
                                    "Mon": 24.107635498046875,
                                    "Tue": 20.27923583984375,
                                    "Wed": 25.582687377929688,
                                    "Thu": 20.504440307617188,
                                    "Fri": 12.64495849609375,
                                    "Sat": 18.32000732421875
                                }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.5,
                            "ty1": 325,
                            "tx2": 49.5,
                            "ty2": 324.3498,
                            "x1": 49.5,
                            "y1": 8.4465,
                            "x2": 49.5,
                            "y2": 324,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 186.5,
                            "ty1": 325,
                            "tx2": 186.5,
                            "ty2": 324.3498,
                            "x1": 186.5,
                            "y1": 8.4465,
                            "x2": 186.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 323.5,
                            "ty1": 325,
                            "tx2": 323.5,
                            "ty2": 324.3498,
                            "x1": 323.5,
                            "y1": 8.4465,
                            "x2": 323.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 460.5,
                            "ty1": 325,
                            "tx2": 460.5,
                            "ty2": 324.3498,
                            "x1": 460.5,
                            "y1": 8.4465,
                            "x2": 460.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 597.5,
                            "ty1": 325,
                            "tx2": 597.5,
                            "ty2": 324.3498,
                            "x1": 597.5,
                            "y1": 8.4465,
                            "x2": 597.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 734.5,
                            "ty1": 325,
                            "tx2": 734.5,
                            "ty2": 324.3498,
                            "x1": 734.5,
                            "y1": 8.4465,
                            "x2": 734.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 871.5,
                            "ty1": 325,
                            "tx2": 871.5,
                            "ty2": 324.3498,
                            "x1": 871.5,
                            "y1": 8.4465,
                            "x2": 871.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 1009.5,
                            "ty1": 325,
                            "tx2": 1009.5,
                            "ty2": 324.3498,
                            "x1": 1009.5,
                            "y1": 8.4465,
                            "x2": 1009.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 117.68388494175501,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sun",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 254.80944110456193,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Mon",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 391.9349972673689,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Tue",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 529.0605534301758,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Wed",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 666.1861095929828,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Thu",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 803.3116657557896,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Fri",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 940.4372219185966,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sat",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }],
                        "width": 959.8788931396484,
                        "left": 49.12110686035156,
                        "right": 1009,
                        "paddingLeft": 3,
                        "paddingTop": 0,
                        "paddingRight": 3,
                        "paddingBottom": 0,
                        "minIndex": 0,
                        "maxIndex": 6,
                        "min": "Sun",
                        "max": "Sat",
                        "_startPixel": 49.12110686035156,
                        "_endPixel": 1009,
                        "_reversePixels": false,
                        "_length": 959.8788931396484,
                        "_startValue": -0.5,
                        "_valueRange": 7,
                        "labelRotation": 0,
                        "minSize": {"width": 962.8788931396484, "height": 35.6502},
                        "height": 35.6502,
                        "_ticksToDraw": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "top": 324.3498,
                        "bottom": 360
                    },
                    "_yScale": {
                        "id": "y-axis-0",
                        "type": "linear",
                        "options": {
                            "display": true,
                            "position": "left",
                            "offset": false,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": false,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 14,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> linear(tickValue, index, ticks)",
                                        "tooltip": "<pre>function(tickValue, index, ticks) {\r\n\t\t\t// If we have lots of ticks, don't use the ones\r\n\t\t\tvar delta = ticks.length > 3 ? ticks[2] - ticks[1] : ticks[1] - ticks[0];\r\n\r\n\t\t\t// If we have a number like 2.5 as the delta, figure out how many decimal places we need\r\n\t\t\tif (Math.abs(delta) > 1) {\r\n\t\t\t\tif (tickValue !== Math.floor(tickValue)) {\r\n\t\t\t\t\t// not an integer\r\n\t\t\t\t\tdelta = tickValue - Math.floor(tickValue);\r\n\t\t\t\t}\r\n\t\t\t}\r\n\r\n\t\t\tvar logDelta = helpers$1.log10(Math.abs(delta));\r\n\t\t\tvar tickString = '';\r\n\r\n\t\t\tif (tickValue !== 0) {\r\n\t\t\t\tvar maxTick = Math.max(Math.abs(ticks[0]), Math.abs(ticks[ticks.length - 1]));\r\n\t\t\t\tif (maxTick < 1e-4) { // all ticks are small numbers; use scientific notation\r\n\t\t\t\t\tvar logTick = helpers$1.log10(Math.abs(tickValue));\r\n\t\t\t\t\tvar numExponential = Math.floor(logTick) - Math.floor(logDelta);\r\n\t\t\t\t\tnumExponential = Math.max(Math.min(numExponential, 20), 0);\r\n\t\t\t\t\ttickString = tickValue.toExponential(numExponential);\r\n\t\t\t\t} else {\r\n\t\t\t\t\tvar numDecimal = -1 * Math.floor(logDelta);\r\n\t\t\t\t\tnumDecimal = Math.max(Math.min(numDecimal, 20), 0); // toFixed has a max of 20 decimal places\r\n\t\t\t\t\ttickString = tickValue.toFixed(numDecimal);\r\n\t\t\t\t}\r\n\t\t\t} else {\r\n\t\t\t\ttickString = '0'; // never show decimal places for 0\r\n\t\t\t}\r\n\r\n\t\t\treturn tickString;\r\n\t\t}</pre>",
                                        "_reviveId": 54
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0",
                                "precision": 2
                            },
                            "type": "linear",
                            "id": "y-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "left",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 55
                            }
                        },
                        "maxWidth": 506,
                        "maxHeight": 360,
                        "margins": {"left": 0, "right": 0, "top": 8.4465, "bottom": 8.4465},
                        "_ticks": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "ticks": ["6000", "5000", "4000", "3000", "2000", "1000", "0"],
                        "_labelSizes": {
                            "first": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 7.0712738037109375, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 28.363906860351562,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "0": 7.0712738037109375,
                                    "1000": 24.8170166015625,
                                    "2000": 27.688308715820312,
                                    "3000": 27.845947265625,
                                    "4000": 28.296356201171875,
                                    "5000": 28.28509521484375,
                                    "6000": 28.363906860351562
                                }, "gc": ["6000", "5000", "4000", "3000", "2000", "1000", "0"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.12110686035156,
                            "ty1": 8.5,
                            "tx2": 49,
                            "ty2": 8.5,
                            "x1": 50,
                            "y1": 8.5,
                            "x2": 1009,
                            "y2": 8.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 61.5,
                            "tx2": 49,
                            "ty2": 61.5,
                            "x1": 50,
                            "y1": 61.5,
                            "x2": 1009,
                            "y2": 61.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 113.5,
                            "tx2": 49,
                            "ty2": 113.5,
                            "x1": 50,
                            "y1": 113.5,
                            "x2": 1009,
                            "y2": 113.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 166.5,
                            "tx2": 49,
                            "ty2": 166.5,
                            "x1": 50,
                            "y1": 166.5,
                            "x2": 1009,
                            "y2": 166.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 219.5,
                            "tx2": 49,
                            "ty2": 219.5,
                            "x1": 50,
                            "y1": 219.5,
                            "x2": 1009,
                            "y2": 219.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 271.5,
                            "tx2": 49,
                            "ty2": 271.5,
                            "x1": 50,
                            "y1": 271.5,
                            "x2": 1009,
                            "y2": 271.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 324.5,
                            "tx2": 49,
                            "ty2": 324.5,
                            "x1": 50,
                            "y1": 324.5,
                            "x2": 1009,
                            "y2": 324.5,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 35.12110686035156,
                            "y": 8.4465,
                            "rotation": 0,
                            "label": "6000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 61.09704999999999,
                            "rotation": 0,
                            "label": "5000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 113.7476,
                            "rotation": 0,
                            "label": "4000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 166.39815,
                            "rotation": 0,
                            "label": "3000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 219.0487,
                            "rotation": 0,
                            "label": "2000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 271.69925,
                            "rotation": 0,
                            "label": "1000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 324.3498,
                            "rotation": 0,
                            "label": "0",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }],
                        "height": 315.9033,
                        "top": 8.4465,
                        "bottom": 324.3498,
                        "paddingLeft": 0,
                        "paddingTop": 8.4465,
                        "paddingRight": 0,
                        "paddingBottom": 8.4465,
                        "min": 0,
                        "max": 6000,
                        "start": 0,
                        "end": 6000,
                        "ticksAsNumbers": [6000, 5000, 4000, 3000, 2000, 1000, 0],
                        "zeroLineIndex": 6,
                        "_startPixel": 8.4465,
                        "_endPixel": 324.3498,
                        "_reversePixels": true,
                        "_length": 315.9033,
                        "_startValue": 0,
                        "_endValue": 6000,
                        "_valueRange": 6000,
                        "labelRotation": 0,
                        "minSize": {"width": 49.12110686035156, "height": 360},
                        "width": 49.12110686035156,
                        "_ticksToDraw": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "left": 0,
                        "right": 49.12110686035156
                    },
                    "_model": {
                        "backgroundColor": "#5bc5d5",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "purchase",
                        "label": "Fri",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 818.3116657557896,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_view": {
                        "backgroundColor": "#5bc5d5",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "purchase",
                        "label": "Fri",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 818.3116657557896,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 1,
                    "_index": 6,
                    "hidden": false,
                    "_xScale": {
                        "id": "x-axis-0",
                        "type": "category",
                        "options": {
                            "display": true,
                            "position": "bottom",
                            "offset": true,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": true,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 12,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                        "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                        "_reviveId": 56
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0"
                            },
                            "type": "category",
                            "id": "x-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "bottom",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 57
                            }
                        },
                        "maxWidth": 962.8788931396484,
                        "maxHeight": 180,
                        "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                        "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                        "_labelSizes": {
                            "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 25.582687377929688,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "Sun": 21.02239990234375,
                                    "Mon": 24.107635498046875,
                                    "Tue": 20.27923583984375,
                                    "Wed": 25.582687377929688,
                                    "Thu": 20.504440307617188,
                                    "Fri": 12.64495849609375,
                                    "Sat": 18.32000732421875
                                }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.5,
                            "ty1": 325,
                            "tx2": 49.5,
                            "ty2": 324.3498,
                            "x1": 49.5,
                            "y1": 8.4465,
                            "x2": 49.5,
                            "y2": 324,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 186.5,
                            "ty1": 325,
                            "tx2": 186.5,
                            "ty2": 324.3498,
                            "x1": 186.5,
                            "y1": 8.4465,
                            "x2": 186.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 323.5,
                            "ty1": 325,
                            "tx2": 323.5,
                            "ty2": 324.3498,
                            "x1": 323.5,
                            "y1": 8.4465,
                            "x2": 323.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 460.5,
                            "ty1": 325,
                            "tx2": 460.5,
                            "ty2": 324.3498,
                            "x1": 460.5,
                            "y1": 8.4465,
                            "x2": 460.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 597.5,
                            "ty1": 325,
                            "tx2": 597.5,
                            "ty2": 324.3498,
                            "x1": 597.5,
                            "y1": 8.4465,
                            "x2": 597.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 734.5,
                            "ty1": 325,
                            "tx2": 734.5,
                            "ty2": 324.3498,
                            "x1": 734.5,
                            "y1": 8.4465,
                            "x2": 734.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 871.5,
                            "ty1": 325,
                            "tx2": 871.5,
                            "ty2": 324.3498,
                            "x1": 871.5,
                            "y1": 8.4465,
                            "x2": 871.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 1009.5,
                            "ty1": 325,
                            "tx2": 1009.5,
                            "ty2": 324.3498,
                            "x1": 1009.5,
                            "y1": 8.4465,
                            "x2": 1009.5,
                            "y2": 324,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 117.68388494175501,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sun",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 254.80944110456193,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Mon",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 391.9349972673689,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Tue",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 529.0605534301758,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Wed",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 666.1861095929828,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Thu",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 803.3116657557896,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Fri",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }, {
                            "x": 940.4372219185966,
                            "y": 336.3498,
                            "rotation": 0,
                            "label": "Sat",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 8.4465,
                            "textAlign": "center"
                        }],
                        "width": 959.8788931396484,
                        "left": 49.12110686035156,
                        "right": 1009,
                        "paddingLeft": 3,
                        "paddingTop": 0,
                        "paddingRight": 3,
                        "paddingBottom": 0,
                        "minIndex": 0,
                        "maxIndex": 6,
                        "min": "Sun",
                        "max": "Sat",
                        "_startPixel": 49.12110686035156,
                        "_endPixel": 1009,
                        "_reversePixels": false,
                        "_length": 959.8788931396484,
                        "_startValue": -0.5,
                        "_valueRange": 7,
                        "labelRotation": 0,
                        "minSize": {"width": 962.8788931396484, "height": 35.6502},
                        "height": 35.6502,
                        "_ticksToDraw": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                            "value": "Mon",
                            "major": false,
                            "label": "Mon",
                            "_index": 1
                        }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                            "value": "Wed",
                            "major": false,
                            "label": "Wed",
                            "_index": 3
                        }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                            "value": "Fri",
                            "major": false,
                            "label": "Fri",
                            "_index": 5
                        }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                        "top": 324.3498,
                        "bottom": 360
                    },
                    "_yScale": {
                        "id": "y-axis-0",
                        "type": "linear",
                        "options": {
                            "display": true,
                            "position": "left",
                            "offset": false,
                            "gridLines": {
                                "display": true,
                                "color": "#F0F2F5",
                                "lineWidth": 1,
                                "drawBorder": true,
                                "drawOnChartArea": true,
                                "drawTicks": false,
                                "tickMarkLength": 10,
                                "zeroLineWidth": 1,
                                "zeroLineColor": "rgba(0,0,0,0.25)",
                                "zeroLineBorderDash": [8, 4],
                                "zeroLineBorderDashOffset": 0,
                                "offsetGridLines": false,
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            },
                            "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                            "ticks": {
                                "beginAtZero": true,
                                "minRotation": 0,
                                "maxRotation": 50,
                                "mirror": false,
                                "padding": 14,
                                "reverse": false,
                                "display": true,
                                "autoSkip": true,
                                "autoSkipPadding": 0,
                                "labelOffset": 0,
                                "callback": {
                                    "_custom": {
                                        "type": "function",
                                        "display": "<span style=\"opacity:.5;\">function</span> linear(tickValue, index, ticks)",
                                        "tooltip": "<pre>function(tickValue, index, ticks) {\r\n\t\t\t// If we have lots of ticks, don't use the ones\r\n\t\t\tvar delta = ticks.length > 3 ? ticks[2] - ticks[1] : ticks[1] - ticks[0];\r\n\r\n\t\t\t// If we have a number like 2.5 as the delta, figure out how many decimal places we need\r\n\t\t\tif (Math.abs(delta) > 1) {\r\n\t\t\t\tif (tickValue !== Math.floor(tickValue)) {\r\n\t\t\t\t\t// not an integer\r\n\t\t\t\t\tdelta = tickValue - Math.floor(tickValue);\r\n\t\t\t\t}\r\n\t\t\t}\r\n\r\n\t\t\tvar logDelta = helpers$1.log10(Math.abs(delta));\r\n\t\t\tvar tickString = '';\r\n\r\n\t\t\tif (tickValue !== 0) {\r\n\t\t\t\tvar maxTick = Math.max(Math.abs(ticks[0]), Math.abs(ticks[ticks.length - 1]));\r\n\t\t\t\tif (maxTick < 1e-4) { // all ticks are small numbers; use scientific notation\r\n\t\t\t\t\tvar logTick = helpers$1.log10(Math.abs(tickValue));\r\n\t\t\t\t\tvar numExponential = Math.floor(logTick) - Math.floor(logDelta);\r\n\t\t\t\t\tnumExponential = Math.max(Math.min(numExponential, 20), 0);\r\n\t\t\t\t\ttickString = tickValue.toExponential(numExponential);\r\n\t\t\t\t} else {\r\n\t\t\t\t\tvar numDecimal = -1 * Math.floor(logDelta);\r\n\t\t\t\t\tnumDecimal = Math.max(Math.min(numDecimal, 20), 0); // toFixed has a max of 20 decimal places\r\n\t\t\t\t\ttickString = tickValue.toFixed(numDecimal);\r\n\t\t\t\t}\r\n\t\t\t} else {\r\n\t\t\t\ttickString = '0'; // never show decimal places for 0\r\n\t\t\t}\r\n\r\n\t\t\treturn tickString;\r\n\t\t}</pre>",
                                        "_reviveId": 58
                                    }
                                },
                                "minor": {},
                                "major": {},
                                "lineHeight": 1.5,
                                "fontFamily": "'Poppins', sans-serif",
                                "fontSize": 11.262,
                                "fontColor": "#9397A0",
                                "precision": 2
                            },
                            "type": "linear",
                            "id": "y-axis-0"
                        },
                        "ctx": "[object CanvasRenderingContext2D]",
                        "chart": {
                            "_custom": {
                                "type": "component-definition",
                                "display": "<i>Unknown Component</i>",
                                "tooltip": "Component definition"
                            }
                        },
                        "hidden": false,
                        "fullWidth": false,
                        "position": "left",
                        "weight": 0,
                        "_layers": {
                            "_custom": {
                                "type": "function",
                                "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                "_reviveId": 59
                            }
                        },
                        "maxWidth": 506,
                        "maxHeight": 360,
                        "margins": {"left": 0, "right": 0, "top": 8.4465, "bottom": 8.4465},
                        "_ticks": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "ticks": ["6000", "5000", "4000", "3000", "2000", "1000", "0"],
                        "_labelSizes": {
                            "first": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "last": {"width": 7.0712738037109375, "height": 16.893, "offset": 8.4465},
                            "widest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465},
                            "highest": {"width": 28.363906860351562, "height": 16.893, "offset": 8.4465}
                        },
                        "_maxLabelLines": 0,
                        "longestLabelWidth": 28.363906860351562,
                        "longestTextCache": {
                            "normal 11.262px 'Poppins', sans-serif": {
                                "data": {
                                    "0": 7.0712738037109375,
                                    "1000": 24.8170166015625,
                                    "2000": 27.688308715820312,
                                    "3000": 27.845947265625,
                                    "4000": 28.296356201171875,
                                    "5000": 28.28509521484375,
                                    "6000": 28.363906860351562
                                }, "gc": ["6000", "5000", "4000", "3000", "2000", "1000", "0"]
                            }
                        },
                        "_gridLineItems": [{
                            "tx1": 49.12110686035156,
                            "ty1": 8.5,
                            "tx2": 49,
                            "ty2": 8.5,
                            "x1": 50,
                            "y1": 8.5,
                            "x2": 1009,
                            "y2": 8.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 61.5,
                            "tx2": 49,
                            "ty2": 61.5,
                            "x1": 50,
                            "y1": 61.5,
                            "x2": 1009,
                            "y2": 61.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 113.5,
                            "tx2": 49,
                            "ty2": 113.5,
                            "x1": 50,
                            "y1": 113.5,
                            "x2": 1009,
                            "y2": 113.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 166.5,
                            "tx2": 49,
                            "ty2": 166.5,
                            "x1": 50,
                            "y1": 166.5,
                            "x2": 1009,
                            "y2": 166.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 219.5,
                            "tx2": 49,
                            "ty2": 219.5,
                            "x1": 50,
                            "y1": 219.5,
                            "x2": 1009,
                            "y2": 219.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 271.5,
                            "tx2": 49,
                            "ty2": 271.5,
                            "x1": 50,
                            "y1": 271.5,
                            "x2": 1009,
                            "y2": 271.5,
                            "width": 1,
                            "color": "#F0F2F5",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }, {
                            "tx1": 49.12110686035156,
                            "ty1": 324.5,
                            "tx2": 49,
                            "ty2": 324.5,
                            "x1": 50,
                            "y1": 324.5,
                            "x2": 1009,
                            "y2": 324.5,
                            "width": 1,
                            "color": "rgba(0,0,0,0.25)",
                            "borderDash": [8, 4],
                            "borderDashOffset": 0
                        }],
                        "_labelItems": [{
                            "x": 35.12110686035156,
                            "y": 8.4465,
                            "rotation": 0,
                            "label": "6000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 61.09704999999999,
                            "rotation": 0,
                            "label": "5000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 113.7476,
                            "rotation": 0,
                            "label": "4000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 166.39815,
                            "rotation": 0,
                            "label": "3000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 219.0487,
                            "rotation": 0,
                            "label": "2000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 271.69925,
                            "rotation": 0,
                            "label": "1000",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }, {
                            "x": 35.12110686035156,
                            "y": 324.3498,
                            "rotation": 0,
                            "label": "0",
                            "font": {
                                "family": "'Poppins', sans-serif",
                                "lineHeight": 16.893,
                                "size": 11.262,
                                "style": "normal",
                                "weight": null,
                                "string": "normal 11.262px 'Poppins', sans-serif",
                                "color": "#9397A0"
                            },
                            "textOffset": 0,
                            "textAlign": "right"
                        }],
                        "height": 315.9033,
                        "top": 8.4465,
                        "bottom": 324.3498,
                        "paddingLeft": 0,
                        "paddingTop": 8.4465,
                        "paddingRight": 0,
                        "paddingBottom": 8.4465,
                        "min": 0,
                        "max": 6000,
                        "start": 0,
                        "end": 6000,
                        "ticksAsNumbers": [6000, 5000, 4000, 3000, 2000, 1000, 0],
                        "zeroLineIndex": 6,
                        "_startPixel": 8.4465,
                        "_endPixel": 324.3498,
                        "_reversePixels": true,
                        "_length": 315.9033,
                        "_startValue": 0,
                        "_endValue": 6000,
                        "_valueRange": 6000,
                        "labelRotation": 0,
                        "minSize": {"width": 49.12110686035156, "height": 360},
                        "width": 49.12110686035156,
                        "_ticksToDraw": [{"value": 6000, "major": false, "label": "6000", "_index": 0}, {
                            "value": 5000,
                            "major": false,
                            "label": "5000",
                            "_index": 1
                        }, {"value": 4000, "major": false, "label": "4000", "_index": 2}, {
                            "value": 3000,
                            "major": false,
                            "label": "3000",
                            "_index": 3
                        }, {"value": 2000, "major": false, "label": "2000", "_index": 4}, {
                            "value": 1000,
                            "major": false,
                            "label": "1000",
                            "_index": 5
                        }, {"value": 0, "major": false, "label": "0", "_index": 6}],
                        "left": 0,
                        "right": 49.12110686035156
                    },
                    "_model": {
                        "backgroundColor": "#5bc5d5",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "purchase",
                        "label": "Sat",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 955.4372219185966,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_view": {
                        "backgroundColor": "#5bc5d5",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "datasetLabel": "purchase",
                        "label": "Sat",
                        "horizontal": false,
                        "base": 324.3498,
                        "x": 955.4372219185966,
                        "y": 324.3498,
                        "width": 30
                    },
                    "_start": null
                }],
                "dataset": null,
                "controller": {
                    "chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "index": 1,
                    "_type": "bar",
                    "_data": [0, 0, 0, 0, 0, 0, 0],
                    "_config": {
                        "categoryPercentage": 0.8,
                        "barPercentage": 0.5,
                        "label": "purchase",
                        "barThickness": 30,
                        "borderWidth": 1,
                        "backgroundColor": "#5bc5d5"
                    },
                    "_cachedDataOpts": {
                        "backgroundColor": "#5bc5d5",
                        "borderColor": "rgba(0,0,0,0.1)",
                        "borderSkipped": "bottom",
                        "borderWidth": 1,
                        "barPercentage": 0.5,
                        "barThickness": 30,
                        "categoryPercentage": 0.8
                    },
                    "_ruler": {
                        "pixels": [117.68388494175501, 254.80944110456193, 391.9349972673689, 529.0605534301758, 666.1861095929828, 803.3116657557896, 940.4372219185966],
                        "start": 49.12110686035156,
                        "end": 1009,
                        "stackCount": 2,
                        "scale": {
                            "id": "x-axis-0",
                            "type": "category",
                            "options": {
                                "display": true,
                                "position": "bottom",
                                "offset": true,
                                "gridLines": {
                                    "display": true,
                                    "color": "#F0F2F5",
                                    "lineWidth": 1,
                                    "drawBorder": true,
                                    "drawOnChartArea": true,
                                    "drawTicks": false,
                                    "tickMarkLength": 10,
                                    "zeroLineWidth": 1,
                                    "zeroLineColor": "rgba(0,0,0,0.25)",
                                    "zeroLineBorderDash": [8, 4],
                                    "zeroLineBorderDashOffset": 0,
                                    "offsetGridLines": true,
                                    "borderDash": [8, 4],
                                    "borderDashOffset": 0
                                },
                                "scaleLabel": {"display": false, "labelString": "", "padding": {"top": 4, "bottom": 4}},
                                "ticks": {
                                    "beginAtZero": true,
                                    "minRotation": 0,
                                    "maxRotation": 50,
                                    "mirror": false,
                                    "padding": 12,
                                    "reverse": false,
                                    "display": true,
                                    "autoSkip": true,
                                    "autoSkipPadding": 0,
                                    "labelOffset": 0,
                                    "callback": {
                                        "_custom": {
                                            "type": "function",
                                            "display": "<span style=\"opacity:.5;\">function</span> values(value)",
                                            "tooltip": "<pre>function(value) {\r\n\t\t\treturn helpers$1.isArray(value) ? value : '' + value;\r\n\t\t}</pre>",
                                            "_reviveId": 60
                                        }
                                    },
                                    "minor": {},
                                    "major": {},
                                    "lineHeight": 1.5,
                                    "fontFamily": "'Poppins', sans-serif",
                                    "fontSize": 11.262,
                                    "fontColor": "#9397A0"
                                },
                                "type": "category",
                                "id": "x-axis-0"
                            },
                            "ctx": "[object CanvasRenderingContext2D]",
                            "chart": {
                                "_custom": {
                                    "type": "component-definition",
                                    "display": "<i>Unknown Component</i>",
                                    "tooltip": "Component definition"
                                }
                            },
                            "hidden": false,
                            "fullWidth": false,
                            "position": "bottom",
                            "weight": 0,
                            "_layers": {
                                "_custom": {
                                    "type": "function",
                                    "display": "<span style=\"opacity:.5;\">function</span> _layers()",
                                    "tooltip": "<pre>function() {\r\n\t\tvar me = this;\r\n\t\tvar opts = me.options;\r\n\t\tvar tz = opts.ticks && opts.ticks.z || 0;\r\n\t\tvar gz = opts.gridLines && opts.gridLines.z || 0;\r\n\r\n\t\tif (!me._isVisible() || tz === gz || me.draw !== me._draw) {\r\n\t\t\t// backward compatibility: draw has been overridden by custom scale\r\n\t\t\treturn [{\r\n\t\t\t\tz: tz,\r\n\t\t\t\tdraw: function() {\r\n\t\t\t\t\tme.draw.apply(me, arguments);\r\n\t\t\t\t}\r\n\t\t\t}];\r\n\t\t}\r\n\r\n\t\treturn [{\r\n\t\t\tz: gz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawGrid.apply(me, arguments);\r\n\t\t\t\tme._drawTitle.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}, {\r\n\t\t\tz: tz,\r\n\t\t\tdraw: function() {\r\n\t\t\t\tme._drawLabels.apply(me, arguments);\r\n\t\t\t}\r\n\t\t}];\r\n\t}</pre>",
                                    "_reviveId": 61
                                }
                            },
                            "maxWidth": 962.8788931396484,
                            "maxHeight": 180,
                            "margins": {"left": 49.12110686035156, "right": 3, "top": 0, "bottom": 0},
                            "_ticks": [{"value": "Sun", "major": false, "label": "Sun", "_index": 0}, {
                                "value": "Mon",
                                "major": false,
                                "label": "Mon",
                                "_index": 1
                            }, {"value": "Tue", "major": false, "label": "Tue", "_index": 2}, {
                                "value": "Wed",
                                "major": false,
                                "label": "Wed",
                                "_index": 3
                            }, {"value": "Thu", "major": false, "label": "Thu", "_index": 4}, {
                                "value": "Fri",
                                "major": false,
                                "label": "Fri",
                                "_index": 5
                            }, {"value": "Sat", "major": false, "label": "Sat", "_index": 6}],
                            "ticks": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                            "_labelSizes": {
                                "first": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465},
                                "last": {"width": 18.32000732421875, "height": 16.893, "offset": 8.4465},
                                "widest": {"width": 25.582687377929688, "height": 16.893, "offset": 8.4465},
                                "highest": {"width": 21.02239990234375, "height": 16.893, "offset": 8.4465}
                            },
                            "_maxLabelLines": 0,
                            "longestLabelWidth": 25.582687377929688,
                            "longestTextCache": {
                                "normal 11.262px 'Poppins', sans-serif": {
                                    "data": {
                                        "Sun": 21.02239990234375,
                                        "Mon": 24.107635498046875,
                                        "Tue": 20.27923583984375,
                                        "Wed": 25.582687377929688,
                                        "Thu": 20.504440307617188,
                                        "Fri": 12.64495849609375,
                                        "Sat": 18.32000732421875
                                    }, "gc": ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                                }
                            },
                            "_gridLineItems": [{
                                "tx1": 49.5,
                                "ty1": 325,
                                "tx2": 49.5,
                                "ty2": 324.3498,
                                "x1": 49.5,
                                "y1": 8.4465,
                                "x2": 49.5,
                                "y2": 324,
                                "width": 1,
                                "color": "rgba(0,0,0,0.25)",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }, {
                                "tx1": 186.5,
                                "ty1": 325,
                                "tx2": 186.5,
                                "ty2": 324.3498,
                                "x1": 186.5,
                                "y1": 8.4465,
                                "x2": 186.5,
                                "y2": 324,
                                "width": 1,
                                "color": "#F0F2F5",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }, {
                                "tx1": 323.5,
                                "ty1": 325,
                                "tx2": 323.5,
                                "ty2": 324.3498,
                                "x1": 323.5,
                                "y1": 8.4465,
                                "x2": 323.5,
                                "y2": 324,
                                "width": 1,
                                "color": "#F0F2F5",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }, {
                                "tx1": 460.5,
                                "ty1": 325,
                                "tx2": 460.5,
                                "ty2": 324.3498,
                                "x1": 460.5,
                                "y1": 8.4465,
                                "x2": 460.5,
                                "y2": 324,
                                "width": 1,
                                "color": "#F0F2F5",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }, {
                                "tx1": 597.5,
                                "ty1": 325,
                                "tx2": 597.5,
                                "ty2": 324.3498,
                                "x1": 597.5,
                                "y1": 8.4465,
                                "x2": 597.5,
                                "y2": 324,
                                "width": 1,
                                "color": "#F0F2F5",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }, {
                                "tx1": 734.5,
                                "ty1": 325,
                                "tx2": 734.5,
                                "ty2": 324.3498,
                                "x1": 734.5,
                                "y1": 8.4465,
                                "x2": 734.5,
                                "y2": 324,
                                "width": 1,
                                "color": "#F0F2F5",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }, {
                                "tx1": 871.5,
                                "ty1": 325,
                                "tx2": 871.5,
                                "ty2": 324.3498,
                                "x1": 871.5,
                                "y1": 8.4465,
                                "x2": 871.5,
                                "y2": 324,
                                "width": 1,
                                "color": "#F0F2F5",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }, {
                                "tx1": 1009.5,
                                "ty1": 325,
                                "tx2": 1009.5,
                                "ty2": 324.3498,
                                "x1": 1009.5,
                                "y1": 8.4465,
                                "x2": 1009.5,
                                "y2": 324,
                                "width": 1,
                                "color": "#F0F2F5",
                                "borderDash": [8, 4],
                                "borderDashOffset": 0
                            }],
                            "_labelItems": [{
                                "x": 117.68388494175501,
                                "y": 336.3498,
                                "rotation": 0,
                                "label": "Sun",
                                "font": {
                                    "family": "'Poppins', sans-serif",
                                    "lineHeight": 16.893,
                                    "size": 11.262,
                                    "style": "normal",
                                    "weight": null,
                                    "string": "normal 11.262px 'Poppins', sans-serif",
                                    "color": "#9397A0"
                                },
                                "textOffset": 8.4465,
                                "textAlign": "center"
                            }, {
                                "x": 254.80944110456193,
                                "y": 336.3498,
                                "rotation": 0,
                                "label": "Mon",
                                "font": {
                                    "family": "'Poppins', sans-serif",
                                    "lineHeight": 16.893,
                                    "size": 11.262,
                                    "style": "normal",
                                    "weight": null,
                                    "string": "normal 11.262px 'Poppins', sans-serif",
                                    "color": "#9397A0"
                                },
                                "textOffset": 8.4465,
                                "textAlign": "center"
                            }, {
                                "x": 391.9349972673689,
                                "y": 336.3498,
                                "rotation": 0,
                                "label": "Tue",
                                "font": {
                                    "family": "'Poppins', sans-serif",
                                    "lineHeight": 16.893,
                                    "size": 11.262,
                                    "style": "normal",
                                    "weight": null,
                                    "string": "normal 11.262px 'Poppins', sans-serif",
                                    "color": "#9397A0"
                                },
                                "textOffset": 8.4465,
                                "textAlign": "center"
                            }, {
                                "x": 529.0605534301758,
                                "y": 336.3498,
                                "rotation": 0,
                                "label": "Wed",
                                "font": {
                                    "family": "'Poppins', sans-serif",
                                    "lineHeight": 16.893,
                                    "size": 11.262,
                                    "style": "normal",
                                    "weight": null,
                                    "string": "normal 11.262px 'Poppins', sans-serif",
                                    "color": "#9397A0"
                                },
                                "textOffset": 8.4465,
                                "textAlign": "center"
                            }, {
                                "x": 666.1861095929828,
                                "y": 336.3498,
                                "rotation": 0,
                                "label": "Thu",
                                "font": {
                                    "family": "'Poppins', sans-serif",
                                    "lineHeight": 16.893,
                                    "size": 11.262,
                                    "style": "normal",
                                    "weight": null,
                                    "string": "normal 11.262px 'Poppins', sans-serif",
                                    "color": "#9397A0"
                                },
                                "textOffset": 8.4465,
                                "textAlign": "center"
                            }, {
                                "x": 803.3116657557896,
                                "y": 336.3498,
                                "rotation": 0,
                                "label": "Fri",
                                "font": {
                                    "family": "'Poppins', sans-serif",
                                    "lineHeight": 16.893,
                                    "size": 11.262,
                                    "style": "normal",
                                    "weight": null,
                                    "string": "normal 11.262px 'Poppins', sans-serif",
                                    "color": "#9397A0"
                                },
                                "textOffset": 8.4465,
                                "textAlign": "center"
                            }, {
                                "x": 940.4372219185966,
                                "y": 336.3498,
                                "rotation": 0,
                                "label": "Sat",
                                "font": {
                                    "family": "'Poppins', sans-serif",
                                    "lineHeight": 16.893,
                                    "size": 11.262,
                                    "style": "normal",
                                    "weight": null,
                                    "string": "normal 11.262px 'Poppins', sans-serif",
                                    "color": "#9397A0"
                                },
                                "textOffset": 8.4465,
                                "textAlign": "center"
                            }],
                            "width": 959.8788931396484,
                            "left": 49.12110686035156,
                            "right": 1009,
                            "paddingLeft": 3,
                            "paddingTop": 0,
                            "paddingRight": 3,
                            "paddingBottom": 0,
                            "minIndex": 0,
                            "maxIndex": 6,
                            "min": "Sun",
                            "max": "Sat",
                            "_startPixel": 49.12110686035156,
                            "_endPixel": 1009,
                            "_reversePixels": false,
                            "_length": 959.8788931396484,
                            "_startValue": -0.5,
                            "_valueRange": 7,
                            "labelRotation": 0,
                            "minSize": {"width": 962.8788931396484, "height": 35.6502},
                            "height": 35.6502,
                            "_ticksToDraw": [{
                                "value": "Sun",
                                "major": false,
                                "label": "Sun",
                                "_index": 0
                            }, {"value": "Mon", "major": false, "label": "Mon", "_index": 1}, {
                                "value": "Tue",
                                "major": false,
                                "label": "Tue",
                                "_index": 2
                            }, {"value": "Wed", "major": false, "label": "Wed", "_index": 3}, {
                                "value": "Thu",
                                "major": false,
                                "label": "Thu",
                                "_index": 4
                            }, {"value": "Fri", "major": false, "label": "Fri", "_index": 5}, {
                                "value": "Sat",
                                "major": false,
                                "label": "Sat",
                                "_index": 6
                            }],
                            "top": 324.3498,
                            "bottom": 360
                        }
                    }
                },
                "hidden": null,
                "xAxisID": "x-axis-0",
                "yAxisID": "y-axis-0",
                "order": 0,
                "index": 1,
                "bar": true,
                "$filler": null
            }
        }
    }]
}

export const stockSummaryData = {
    "options": {
        "cols": [{"name": "Stock Alert", "key": "name", "class": "text-primary"}, {
            "name": "Quantity",
            "key": "amount",
            "class": "text-danger",
            "alignment": "right"
        }]
    },
    "data": [
        {
            "name": "<div class=\"text-primary\">Cotton women full pant</div><small class=\"text-muted\">#21438056</small>",
            "amount": "<span class=\"text-success\">14</span>"
        }, {
            "name": "<div class=\"text-primary\">Full jeans pant</div><small class=\"text-muted\">#21884380</small>",
            "amount": "<span class=\"text-success\">21</span>"
        }, {
            "name": "<div class=\"text-primary\">T Shirt for man-Red-m</div><small class=\"text-muted\">#8266242588</small>",
            "amount": "<span class=\"text-success\">24</span>"
        }, {
            "name": "<div class=\"text-primary\">Cotton Short Sleeve T-shirt-L-white</div><small class=\"text-muted\">#8091567543</small>",
            "amount": "<span class=\"text-success\">57</span>"
        }, {
            "name": "<div class=\"text-primary\">Hat</div><small class=\"text-muted\">#21431156</small>",
            "amount": "<span class=\"text-success\">73</span>"
        }
    ]
}

export const topCustomer = {
    "options": {
        "cols": [{"name": "Customer name", "key": "name", "class": "text-primary"}, {
            "name": "Total amount",
            "key": "amount",
            "class": "",
            "alignment": "right"
        }]
    },
    "data": [
        {
            "name": "<div class=\"text-primary\">Walk-in -customer</div><small class=\"text-muted\">#walkincustomer@gmail.com</small>",
            "amount": "<span class=\"text-success\">$1500.00</span>"
        }, {
            "name": "<div class=\"text-primary\">Marlin Macejkovic</div><small class=\"text-muted\">#marquardt.dan@yahoo.com</small>",
            "amount": "<span class=\"text-success\">$1250.00</span>"
        }, {
            "name": "<div class=\"text-primary\">Alfonso Koelpin</div><small class=\"text-muted\">#zachariah.bauch@hills.info</small>",
            "amount": "<span class=\"text-success\">$970.00</span>"
        }, {
            "name": "<div class=\"text-primary\">Florence Becker</div><small class=\"text-muted\">#julianne01@deckow.info</small>",
            "amount": "<span class=\"text-success\">$950.00</span>"
        }, {
            "name": "<div class=\"text-primary\">Amely Brekke</div><small class=\"text-muted\">#colin02@yahoo.com</small>",
            "amount": "<span class=\"text-success\">$350.00</span>"
        }
    ]
}

export const topSellingData = {
    "labels": ["Hat", "Sporty Track pants", "Three quarter pant", "Cotton Short Sleeve T-shirt", "Cotton women full pant"],
    "dataSet": [{
        "barThickness": 30,
        "borderWidth": 0,
        "backgroundColor": ["#00cc4a", "#54caf2", "#bb6bdb", "#f28319", "#808080"],
        "data": ["1", "1", "1", "1", "1"],
        "_meta": {
            "30": {
                "type": "doughnut",
                "data": [{
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 0,
                    "_index": 0,
                    "hidden": false,
                    "_options": {
                        "backgroundColor": "#00cc4a",
                        "borderColor": "#fff",
                        "borderWidth": 0,
                        "borderAlign": "center"
                    },
                    "_model": {
                        "backgroundColor": "#00cc4a",
                        "borderColor": "#fff",
                        "borderWidth": 0,
                        "borderAlign": "center",
                        "x": 230.5,
                        "y": 180,
                        "startAngle": -1.5707963267948966,
                        "endAngle": -0.3141592653589793,
                        "circumference": 1.2566370614359172,
                        "outerRadius": 180,
                        "innerRadius": 90,
                        "label": "Hat"
                    },
                    "_view": {
                        "backgroundColor": "#00cc4a",
                        "borderColor": "#fff",
                        "borderWidth": 0,
                        "borderAlign": "center",
                        "x": 230.5,
                        "y": 180,
                        "startAngle": -1.5707963267948966,
                        "endAngle": -0.3141592653589793,
                        "circumference": 1.2566370614359172,
                        "outerRadius": 180,
                        "innerRadius": 90,
                        "label": "Hat"
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 0,
                    "_index": 1,
                    "hidden": false,
                    "_options": {
                        "backgroundColor": "#54caf2",
                        "borderColor": "#fff",
                        "borderWidth": 0,
                        "borderAlign": "center"
                    },
                    "_model": {
                        "backgroundColor": "#54caf2",
                        "borderColor": "#fff",
                        "borderWidth": 0,
                        "borderAlign": "center",
                        "x": 230.5,
                        "y": 180,
                        "startAngle": -0.3141592653589793,
                        "endAngle": 0.9424777960769379,
                        "circumference": 1.2566370614359172,
                        "outerRadius": 180,
                        "innerRadius": 90,
                        "label": "Sporty Track pants"
                    },
                    "_view": {
                        "backgroundColor": "#54caf2",
                        "borderColor": "#fff",
                        "borderWidth": 0,
                        "borderAlign": "center",
                        "x": 230.5,
                        "y": 180,
                        "startAngle": -0.3141592653589793,
                        "endAngle": 0.9424777960769379,
                        "circumference": 1.2566370614359172,
                        "outerRadius": 180,
                        "innerRadius": 90,
                        "label": "Sporty Track pants"
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 0,
                    "_index": 2,
                    "hidden": false,
                    "_options": {
                        "backgroundColor": "#bb6bdb",
                        "borderColor": "#fff",
                        "borderWidth": 0,
                        "borderAlign": "center"
                    },
                    "_model": {
                        "backgroundColor": "#bb6bdb",
                        "borderColor": "#fff",
                        "borderWidth": 0,
                        "borderAlign": "center",
                        "x": 230.5,
                        "y": 180,
                        "startAngle": 0.9424777960769379,
                        "endAngle": 2.199114857512855,
                        "circumference": 1.2566370614359172,
                        "outerRadius": 180,
                        "innerRadius": 90,
                        "label": "Three quarter pant"
                    },
                    "_view": {
                        "backgroundColor": "#bb6bdb",
                        "borderColor": "#fff",
                        "borderWidth": 0,
                        "borderAlign": "center",
                        "x": 230.5,
                        "y": 180,
                        "startAngle": 0.9424777960769379,
                        "endAngle": 2.199114857512855,
                        "circumference": 1.2566370614359172,
                        "outerRadius": 180,
                        "innerRadius": 90,
                        "label": "Three quarter pant"
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 0,
                    "_index": 3,
                    "hidden": false,
                    "_options": {
                        "backgroundColor": "#f28319",
                        "borderColor": "#fff",
                        "borderWidth": 0,
                        "borderAlign": "center"
                    },
                    "_model": {
                        "backgroundColor": "#f28319",
                        "borderColor": "#fff",
                        "borderWidth": 0,
                        "borderAlign": "center",
                        "x": 230.5,
                        "y": 180,
                        "startAngle": 2.199114857512855,
                        "endAngle": 3.4557519189487724,
                        "circumference": 1.2566370614359172,
                        "outerRadius": 180,
                        "innerRadius": 90,
                        "label": "Cotton Short Sleeve T-shirt"
                    },
                    "_view": {
                        "backgroundColor": "#f28319",
                        "borderColor": "#fff",
                        "borderWidth": 0,
                        "borderAlign": "center",
                        "x": 230.5,
                        "y": 180,
                        "startAngle": 2.199114857512855,
                        "endAngle": 3.4557519189487724,
                        "circumference": 1.2566370614359172,
                        "outerRadius": 180,
                        "innerRadius": 90,
                        "label": "Cotton Short Sleeve T-shirt"
                    },
                    "_start": null
                }, {
                    "_chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "_datasetIndex": 0,
                    "_index": 4,
                    "hidden": false,
                    "_options": {
                        "backgroundColor": "#808080",
                        "borderColor": "#fff",
                        "borderWidth": 0,
                        "borderAlign": "center"
                    },
                    "_model": {
                        "backgroundColor": "#808080",
                        "borderColor": "#fff",
                        "borderWidth": 0,
                        "borderAlign": "center",
                        "x": 230.5,
                        "y": 180,
                        "startAngle": 3.4557519189487724,
                        "endAngle": 4.71238898038469,
                        "circumference": 1.2566370614359172,
                        "outerRadius": 180,
                        "innerRadius": 90,
                        "label": "Cotton women full pant"
                    },
                    "_view": {
                        "backgroundColor": "#808080",
                        "borderColor": "#fff",
                        "borderWidth": 0,
                        "borderAlign": "center",
                        "x": 230.5,
                        "y": 180,
                        "startAngle": 3.4557519189487724,
                        "endAngle": 4.71238898038469,
                        "circumference": 1.2566370614359172,
                        "outerRadius": 180,
                        "innerRadius": 90,
                        "label": "Cotton women full pant"
                    },
                    "_start": null
                }],
                "dataset": null,
                "controller": {
                    "chart": {
                        "_custom": {
                            "type": "component-definition",
                            "display": "<i>Unknown Component</i>",
                            "tooltip": "Component definition"
                        }
                    },
                    "index": 0,
                    "_type": "doughnut",
                    "_data": ["1", "1", "1", "1", "1"],
                    "_config": {
                        "barThickness": 30,
                        "borderWidth": 0,
                        "backgroundColor": ["#00cc4a", "#54caf2", "#bb6bdb", "#f28319", "#808080"]
                    },
                    "_cachedDataOpts": null,
                    "outerRadius": 180,
                    "innerRadius": 90
                },
                "hidden": null,
                "xAxisID": null,
                "yAxisID": null,
                "order": 0,
                "index": 0,
                "total": 5,
                "$filler": null
            }
        }
    }]
}
